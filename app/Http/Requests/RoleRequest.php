<?php

namespace App\Http\Requests;

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
        $rules = [
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ];

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $role = $this->route('role');
            $createRolePermissionId = Permission::where('name', 'create-role')->value('id');

            $rules['name'] = 'required|string|max:250|unique:roles,name,' . $role->id;
            $rules['permissions.*'] = [
                'exists:permissions,id',
                Rule::notIn([$createRolePermissionId]),
            ];
        }

        return $rules;
    }
}
