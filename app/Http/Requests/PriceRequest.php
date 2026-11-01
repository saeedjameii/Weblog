<?php

namespace App\Http\Requests;

use App\Models\AttributeValue;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class PriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return self::nestedRules();
    }

    public static function nestedRules(string $prefix = ''): array
    {
        return [
            $prefix.'price' => ['required', 'numeric', 'min:0'],
            $prefix.'discount' => ['nullable', 'numeric', 'min:0', 'max:100'],
            $prefix.'inventory' => ['required', 'integer', 'min:0'],
            $prefix.'attribute_values' => ['nullable', 'array'],
            $prefix.'attribute_values.*' => ['nullable', 'integer', 'exists:attribute_values,id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $prices = $this->input('prices');
            if (is_array($prices)) {
                self::validatePrices($validator, $prices);
                return;
            }

            self::validatePrices($validator, [$this->all()], '');
        });
    }

    public static function validatePrices(Validator $validator, array $prices, string $root = 'prices'): void
    {
        $allValueIds = collect($prices)->flatMap(fn ($price) => $price['attribute_values'] ?? [])->filter(fn ($id) => !empty($id))->unique();

        $valuesById = AttributeValue::query()->whereIn('id', $allValueIds)->get(['id', 'attribute_id'])->keyBy('id');
        $seenCombinations = [];

        foreach ($prices as $index => $price) {
            $field = ($root !== '' ? $root.'.'.$index.'.' : '').'attribute_values';

            if ((!isset($price['price']) || $price['price'] === '') && (!isset($price['inventory']) || $price['inventory'] === '')) {
                continue;
            }

            $values = collect($price['attribute_values'] ?? [])->filter(fn ($id) => !empty($id) && (int) $id > 0)->map(fn ($id) => (int) $id);

            if ($values->isNotEmpty()) {
                $attributeIds = $values->map(fn ($id) => $valuesById->get($id)?->attribute_id);

                if ($attributeIds->contains(null) || $attributeIds->unique()->count() !== $attributeIds->count()) {
                    $validator->errors()->add($field, 'برای هر ویژگی فقط یک مقدار معتبر انتخاب کنید.');
                }
            }

            $combination = $values->isEmpty() ? '__NO_ATTRIBUTES__' : $values->sort()->implode(':');

            if (isset($seenCombinations[$combination])) {
                if ($combination === '__NO_ATTRIBUTES__') {
                    $validator->errors()->add($field, 'نمی‌توانید بیش از یک قیمت بدون ویژگی ثبت کنید. سایر قیمت‌ها باید دارای ویژگی مشخص و متمایز باشند.');
                } else {
                    $validator->errors()->add($field, 'این ترکیب مقدارهای ویژگی قبلاً برای همین محصول وارد شده است.');
                }
            }

            $seenCombinations[$combination] = true;
        }
    }
}
