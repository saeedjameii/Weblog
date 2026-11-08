<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Rules\ValidJalaliDate;
use Illuminate\Foundation\Http\FormRequest;
use Morilog\Jalali\Jalalian;

class SignUpRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $email = $this->input('email');

        if (is_string($email)) {
            $this->merge(['email' => User::normalizeEmail($email)]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|max:20',
            'last_name' => 'required|max:20',
            'email' => 'required|email|max:255|unique:users',
            'phone_number' => 'nullable|size:11|regex:/^09[0-9]{9}$/|unique:users',
            'birth_date' => ['nullable', 'regex:/^[0-9]{4}\/[0-9]{2}\/[0-9]{2}$/', new ValidJalaliDate],
            'national_code' => 'nullable|size:10|regex:/^[0-9]{10}$/|unique:users',
            'password' => 'required|min:8|confirmed',
        ];
    }

    protected function passedValidation(): void
    {
        if ($this->filled('birth_date')) {
            $this->merge([
                'birth_date' => Jalalian::fromFormat('Y/m/d', $this->birth_date)->toCarbon()->format('Y-m-d'),
            ]);
        }
    }
}