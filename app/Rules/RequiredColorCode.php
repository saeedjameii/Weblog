<?php

namespace App\Rules;

use App\Models\Attribute;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class RequiredColorCode implements ValidationRule
{
    public function __construct(
        private ?int $attributeId
    ) {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if($this->attributeId){
            $attributeModel = Attribute::find($this->attributeId);
            if($attributeModel && $attributeModel->type == 'color' && empty($value)){
                $fail('کد رنگ برای این ویژگی الزامی است.');
            }
        }
    }
}