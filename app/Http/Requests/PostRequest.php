<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Morilog\Jalali\Jalalian;
use App\Rules\ValidJalaliPublishDate;

class PostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalize digits to Latin before validation.
     */
    protected function prepareForValidation(): void
    {
        $merged = [];
        if ($this->has('published_date') && $this->published_date) {
            $merged['published_date'] = $this->toLatinDigits($this->published_date);
        }
        if ($this->has('published_time') && $this->published_time) {
            $merged['published_time'] = $this->toLatinDigits($this->published_time);
        }
        if (!empty($merged)) {
            $this->merge($merged);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'action'         => ['required', Rule::in(['draft', 'published'])],
            'title'          => 'required|string|max:255',
            'description'    => 'required|string',
            'categories'     => 'required|array',
            'categories.*'   => ['required', Rule::exists('categories', 'id')->where(fn($query) => $query->where('type', 'post'))],
            'images'         => 'nullable|array|max:5',
            'images.*'       => 'image|mimes:jpg,jpeg,png,webp|max:5120',
            'published_date' => [
                'nullable',
                new ValidJalaliPublishDate(),
            ],
            'published_time' => [
                'nullable',
                'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/',
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'published_date' => 'تاریخ انتشار',
            'published_time' => 'ساعت انتشار',
        ];
    }

    protected function passedValidation(): void
    {
        if ($this->filled('published_date')) {
            $time = $this->filled('published_time') ? $this->published_time : now()->format('H:i');
            $jalaliString = $this->published_date . ' ' . $time;
            try {
                $carbonDate = Jalalian::fromFormat('Y/m/d H:i', $jalaliString)->toCarbon();
                $this->merge(['published_at' => $carbonDate]);
            } catch (\Throwable $e) {
                $this->merge(['published_at' => null]);
            }
        } else {
            $this->merge(['published_at' => null]);
        }
    }

    private function toLatinDigits(?string $value): ?string
    {
        if (!$value) return null;
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $arabic  = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $latin   = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        return str_replace($arabic, $latin, str_replace($persian, $latin, trim($value)));
    }
}
