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
                $fail('تاریخ تولد وارد شده معتبر نیست. لطفاً از فرمت YYYY/MM/DD استفاده کنید.');
                return;
            }

            if ($jalaliDate->toCarbon()->isFuture()) {
                $fail('تاریخ وارد شده نمی‌تواند مربوط به آینده باشد.');
                return;
            }

        } catch (\Throwable $e) {
            $fail('تاریخ تولد وارد شده معتبر نیست. لطفاً از فرمت YYYY/MM/DD استفاده کنید (مثلاً ۱۳۷۰/۰۶/۱۲).');
        }
    }
}