<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Morilog\Jalali\Jalalian;

class ValidJalaliDate implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            $jalaliDate = Jalalian::fromFormat('Y/m/d', $value);

            if ($jalaliDate->format('Y/m/d') !== $value) {
                throw new \InvalidArgumentException('Invalid Jalali date after normalization.');
            }
        } catch (\Throwable $e) {
            $fail('تاریخ تولد وارد شده معتبر نیست. لطفاً از فرمت YYYY/MM/DD استفاده کنید (مثلاً ۱۴۰۵/۰۶/۱۲).');
        }
    }
}