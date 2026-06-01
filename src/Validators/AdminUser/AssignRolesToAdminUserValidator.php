<?php

namespace Ssh521\LaravelAdmin\Validators\AdminUser;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Ssh521\LaravelAdmin\Validators\AdminUser\Concerns\ResolvesAdminUserTable;

final class AssignRolesToAdminUserValidator
{
    use ResolvesAdminUserTable;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     *
     * @throws ValidationException
     */
    public static function validate(array $data): array
    {
        $guardName = config('laravel-admin.auth.guard', 'laravel_admin');

        $validator = Validator::make($data, [
            'adminUserId' => ['required', 'integer', Rule::exists(self::adminUsersTable(), 'id')],
            'roleIds' => ['present', 'array'],
            'roleIds.*' => ['integer', Rule::exists('roles', 'id')->where('guard_name', $guardName)],
        ]);

        return $validator->validate();
    }
}
