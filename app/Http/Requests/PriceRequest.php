<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PriceRequest extends FormRequest
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
            'price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:100',
            'inventory' => 'required|integer|min:0',
            'attribute_values' => 'required|array|min:1',
            'attribute_values.*' => 'exists:attribute_values,id',
        ];
    }
}
