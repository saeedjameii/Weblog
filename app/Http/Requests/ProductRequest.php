<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ProductRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'unit' => 'required|string|max:50',
            'weight' => 'required|numeric|min:0',
            'categories' => 'required|array|min:1',
            'categories.*' => ['required', 'integer', 'distinct', Rule::exists('categories', 'id')->where(fn ($query) => $query->where('type', 'product'))],
            'prices' => [$this->isMethod('post') ? 'required' : 'sometimes', 'array', 'min:1'],
            ...PriceRequest::nestedRules('prices.*.'),
            'images'   => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        if (is_array($this->input('prices'))) {
            $validator->after(fn (Validator $validator) => PriceRequest::validatePrices($validator, $this->input('prices')));
        }
    }
}
