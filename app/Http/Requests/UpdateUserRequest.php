<?php

namespace App\Http\Requests;

use App\Rules\ValidJalaliDate;
use Illuminate\Foundation\Http\FormRequest;
use Morilog\Jalali\Jalalian;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $userId = $this->route('user')->id;

        return [
            'first_name' => 'required|max:20',
            'last_name' => 'required|max:20',
            'email' => 'required|email|unique:users,email,' . $userId,
            'phone_number' => 'required|size:11|regex:/^09[0-9]{9}$/|unique:users,phone_number,' . $userId,
            'birth_date' => ['required', 'regex:/^[0-9]{4}\/[0-9]{2}\/[0-9]{2}$/', new ValidJalaliDate],
            'national_code' => 'required|size:10|regex:/^[0-9]{10}$/|unique:users,national_code,' . $userId,
            'password' => 'nullable|min:8|confirmed',
        ];
    }

    protected function passedValidation(): void
    {
        $this->merge([
            'birth_date' => Jalalian::fromFormat('Y/m/d', $this->birth_date)->toCarbon()->format('Y-m-d'),
        ]);
    }
}

