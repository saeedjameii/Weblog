<?php

namespace App\Http\Requests;

use App\Enums\UserLevel;
use App\Models\Permission;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleRequest extends FormRequest
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
        $role = $this->route('role');
        $createRolePermissionId = Permission::where('name', 'create-role')->value('id');

        return [
            'name' => [
                'required',
                'string',
                'max:250',
                Rule::unique('roles', 'name')->ignore($role?->id),
                Rule::notIn(array_column(UserLevel::cases(), 'value')),
            ],
            'permissions' => 'nullable|array',
            'permissions.*' => [
                'exists:permissions,id',
                Rule::notIn(array_filter([$createRolePermissionId])),
            ],
        ];
    }
}