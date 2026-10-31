<?php

namespace App\Http\Requests;

use App\Models\AttributeValue;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

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
        return self::nestedRules();
    }

    public static function nestedRules(string $prefix = ''): array
    {
        return [
            $prefix.'price' => ['required', 'numeric', 'min:0'],
            $prefix.'discount' => ['nullable', 'numeric', 'min:0', 'max:100'],
            $prefix.'inventory' => ['required', 'integer', 'min:0'],
            $prefix.'attribute_values' => ['required', 'array', 'min:1'],
            $prefix.'attribute_values.*' => ['required', 'integer', 'exists:attribute_values,id'],
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
        $allValueIds = collect($prices)->flatMap(fn ($price) => $price['attribute_values'] ?? [])->unique();
        $valuesById = AttributeValue::query()->whereIn('id', $allValueIds)->get(['id', 'attribute_id'])->keyBy('id');
        $seenCombinations = [];

        foreach ($prices as $index => $price) {
            $values = collect($price['attribute_values'] ?? [])->map(fn ($id) => (int) $id);
            $attributeIds = $values->map(fn ($id) => $valuesById->get($id)?->attribute_id);
            $field = ($root !== '' ? $root.'.'.$index.'.' : '').'attribute_values';

            // Incomplete rows already receive required errors from the rules
            // above. They are not a meaningful attribute combination and must
            // not collide with one another as an empty combination.
            if ($values->isEmpty() || $values->contains(0)) {
                continue;
            }

            if ($attributeIds->contains(null) || $attributeIds->unique()->count() !== $attributeIds->count()) {
                $validator->errors()->add($field, 'برای هر ویژگی فقط یک مقدار معتبر انتخاب کنید.');
            }

            $combination = $values->sort()->implode(':');
            if (isset($seenCombinations[$combination])) {
                $validator->errors()->add($field, 'این ترکیب مقدارهای ویژگی قبلاً برای همین محصول وارد شده است.');
            }
            $seenCombinations[$combination] = true;
        }
    }
}
