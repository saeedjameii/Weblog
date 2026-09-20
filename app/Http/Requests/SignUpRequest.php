<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Override;

class SignUpRequest extends FormRequest
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
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|max:20',
            'last_name' => 'required|max:20',
            'email' => 'required|email|unique:users',
            'phone_number' => 'required|size:11|regex:/^09[0-9]{9}$/|unique:users',
            'birth_date' => 'required|regex:/^[0-9]{4}\/[0-9]{2}\/[0-9]{2}$/',
            'national_code' => 'required|size:10|regex:/^[0-9]{10}$/|unique:users',
            'password' => 'required|min:8|confirmed',
        ];
    }

}
