<?php

namespace Ssh521\LaravelAdmin\Validators\AdminUser;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Ssh521\LaravelAdmin\Validators\AdminUser\Concerns\ResolvesAdminUserTable;

final class UpdateAdminUserValidator
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
        $adminUserId = $data['adminUserId'] ?? null;

        $validator = Validator::make($data, [
            'adminUserId' => ['required', 'integer', Rule::exists(self::adminUsersTable(), 'id')],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(self::adminUsersTable(), 'email')->ignore($adminUserId)],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        return $validator->validate();
    }
}
