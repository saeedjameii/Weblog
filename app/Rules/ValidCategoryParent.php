<?php

namespace App\Rules;

use App\Models\Category;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidCategoryParent implements ValidationRule
{
    public function __construct(
        private ?Category $category
    ) {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $this->category || ! $value) {
            return;
        }

        $parent = Category::find($value);

        if ($parent && ! $this->category->isValidParent($parent)) {
            $fail('این دسته‌بندی نمی‌تواند والد انتخاب‌شده باشد.');
        }
    }
}