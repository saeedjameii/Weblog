<?php

namespace App\Http\Requests;

use App\Rules\RequiredColorCode;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AttributeValueRequest extends FormRequest
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
        return [
            'attribute_id' => 'required|integer|exists:attributes,id',
            'value' => 'required|string|max:100',
            'color_code' => ['nullable', 'string', new RequiredColorCode($this->attribute_id)],
        ];
    }
}
