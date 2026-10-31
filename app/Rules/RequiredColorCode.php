<?php

namespace App\Rules;

use App\Models\Attribute;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class RequiredColorCode implements ValidationRule
{
    public function __construct(private int|string|null $attributeId)
    {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $this->attributeId) return;

        $attributeModel = Attribute::find($this->attributeId);
        if ($attributeModel?->type !== 'color') return;

        if (empty($value)) {
            $fail('کد رنگ برای این ویژگی الزامی است.');
        } elseif (! preg_match('/\A#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})\z/', (string) $value)) {
            $fail('کد رنگ باید با قالب #RGB یا #RRGGBB وارد شود.');
        }
    }
}
