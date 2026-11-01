<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (is_array($this->prices)) {
            $filteredPrices = array_values(array_filter($this->prices, function ($item) {
                $hasPrice = isset($item['price']) && $item['price'] !== '' && $item['price'] !== null;
                $hasInventory = isset($item['inventory']) && $item['inventory'] !== '' && $item['inventory'] !== null;
                $hasAttributes = !empty(array_filter($item['attribute_values'] ?? []));

                return $hasPrice || $hasInventory || $hasAttributes;
            }));

            $this->merge([
                'prices' => !empty($filteredPrices) ? $filteredPrices : $this->prices
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'name'         => 'required|string|max:255',
            'description'  => 'required|string',
            'unit'         => 'required|string|max:50',
            'weight'       => 'required|numeric|min:0',
            'categories'   => 'required|array|min:1',
            'categories.*' => ['required', 'integer', 'distinct', Rule::exists('categories', 'id')->where(fn ($query) => $query->where('type', 'product'))],
            'prices'       => [$this->isMethod('post') ? 'required' : 'sometimes', 'array', 'min:1'],
            ...PriceRequest::nestedRules('prices.*.'),
            'images'       => 'nullable|array|max:5',
            'images.*'     => 'image|mimes:jpg,jpeg,png,webp|max:5120',
        ];
    }

    public function attributes(): array
    {
        return [
            'name'               => 'نام محصول',
            'description'        => 'توضیحات',
            'unit'               => 'واحد اندازه‌گیری',
            'weight'             => 'وزن',
            'categories'         => 'دسته‌بندی‌ها',
            'prices.*.price'     => 'قیمت اصلی',
            'prices.*.inventory' => 'موجودی انبار',
            'prices.*.discount'  => 'درصد تخفیف',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        if (is_array($this->input('prices'))) {
            $validator->after(fn (Validator $validator) => PriceRequest::validatePrices($validator, $this->input('prices')));
        }
    }
}