<?php

namespace Ssh521\LaravelAdmin\Validators\AdminUser;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Ssh521\LaravelAdmin\Validators\AdminUser\Concerns\ResolvesAdminUserTable;

final class CreateAdminUserValidator
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
        $validator = Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(self::adminUsersTable(), 'email')],
            'password' => ['required', 'string', 'min:8'],
            'isVerified' => ['nullable', 'boolean'],
        ]);

        return $validator->validate();
    }
}
