<?php

namespace Ssh521\LaravelAdmin\Validators\Role;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

final class UpdateRoleValidator
{
    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     *
     * @throws ValidationException
     */
    public static function validate(array $data): array
    {
        $roleId = $data['roleId'] ?? null;

        $validator = Validator::make($data, [
            'roleId' => ['required', 'integer', 'exists:roles,id'],
            'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')->ignore($roleId)],
            'permissionIds' => ['nullable', 'array'],
            'permissionIds.*' => ['integer', 'exists:permissions,id'],
        ]);

        return $validator->validate();
    }
}
