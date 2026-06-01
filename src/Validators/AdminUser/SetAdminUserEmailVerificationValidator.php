<?php

namespace Ssh521\LaravelAdmin\Validators\AdminUser;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Ssh521\LaravelAdmin\Validators\AdminUser\Concerns\ResolvesAdminUserTable;

final class SetAdminUserEmailVerificationValidator
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
            'adminUserId' => ['required', 'integer', Rule::exists(self::adminUsersTable(), 'id')],
            'isVerified' => ['required', 'boolean'],
        ]);

        return $validator->validate();
    }
}
