<?php

namespace App\Rules;

use App\Models\Category;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidCategoryParent implements ValidationRule
{
    public function __construct(
        private ?Category $category,
        private ?string $type 
    ) {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value) {
            return;
        }

        $parent = Category::find($value);

        if(! $parent){
            return;
        }

        if ($this->category && ! $this->category->isValidParent($parent)) {
            $fail('این دسته‌بندی نمی‌تواند والد انتخاب‌شده باشد.');
        }

        if($this->type !== null && $parent->type !== $this->type) {
            $fail('دسته‌بندی والد باید از نوع مشابه باشد.');
        }
    }
}