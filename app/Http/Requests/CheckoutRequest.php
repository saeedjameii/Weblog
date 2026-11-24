<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('api')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'recipient_name' => 'required|string|min:3|max:100',
            'recipient_phone' => 'required|string|regex:/^09[0-9]{9}$/',
            'province' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'address' => 'required|string|min:10',
            'postal_code' => 'required|string|regex:/^[0-9]{10}$/',
            'note' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'recipient_name.required' => 'نام گیرنده الزامی است.',
            'recipient_name.string' => 'نام گیرنده باید یک رشته باشد.',
            'recipient_name.min' => 'نام گیرنده باید حداقل 3 کاراکتر باشد.',
            'recipient_name.max' => 'نام گیرنده نباید بیشتر از 100 کاراکتر باشد.',

            'recipient_phone.required' => 'شماره تلفن گیرنده الزامی است.',
            'recipient_phone.string' => 'شماره تلفن گیرنده باید یک رشته باشد.',
            'recipient_phone.regex' => 'شماره تلفن گیرنده باید با 09 شروع شود و شامل 11 رقم باشد.',

            'province.required' => 'استان الزامی است.',
            'province.string' => 'استان باید یک رشته باشد.',
            'province.max' => 'استان نباید بیشتر از 100 کاراکتر باشد.',

            'city.required' => 'شهر الزامی است.',
            'city.string' => 'شهر باید یک رشته باشد.',
            'city.max' => 'شهر نباید بیشتر از 100 کاراکتر باشد.',

            'address.required' => 'آدرس الزامی است.',
            'address.string' => 'آدرس باید یک رشته باشد.',
            'address.min' => 'آدرس باید حداقل 10 کاراکتر باشد.',

            'postal_code.required' => 'کد پستی الزامی است.',
            'postal_code.string' => 'کد پستی باید یک رشته باشد.',
            'postal_code.regex' => 'کد پستی باید شامل 10 رقم باشد.',

            'note.string' => 'یادداشت باید یک رشته باشد.',
            'note.max' => 'یادداشت نباید بیشتر از 500 کاراکتر باشد.',
        ];
    }
}
