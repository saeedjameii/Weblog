<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Morilog\Jalali\Jalalian;

class ValidJalaliPublishDate implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (
            !is_string($value) ||
            !preg_match('/^[0-9]{4}\/[0-9]{2}\/[0-9]{2}$/', $value)
        ) {
            $fail('فرمت تاریخ انتشار معتبر نیست. لطفاً از فرمت YYYY/MM/DD استفاده کنید.');
            return;
        }

        try {
            $jalaliDate = Jalalian::fromFormat('Y/m/d', $value);

            if ($jalaliDate->format('Y/m/d') !== $value) {
                $fail('تاریخ انتشار وارد شده معتبر نیست.');
            }
        } catch (\Throwable $e) {
            $fail('تاریخ انتشار وارد شده معتبر نیست. لطفاً از فرمت YYYY/MM/DD استفاده کنید.');
        }
    }
}
