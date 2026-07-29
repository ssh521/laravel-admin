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
        $guard = config('laravel-admin.auth.guard', 'laravel_admin');

        $validator = Validator::make($data, [
            'roleId' => [
                'required',
                'integer',
                Rule::exists('roles', 'id')->where('guard_name', $guard),
            ],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')
                    ->where('guard_name', $guard)
                    ->ignore($roleId),
            ],
            'permissionIds' => ['nullable', 'array'],
            'permissionIds.*' => [
                'integer',
                Rule::exists('permissions', 'id')->where('guard_name', $guard),
            ],
        ]);

        return $validator->validate();
    }
}
