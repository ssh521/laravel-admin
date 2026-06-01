<?php

namespace Ssh521\LaravelAdmin\Validators\User;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

final class AssignRolesToUserValidator
{
    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     *
     * @throws ValidationException
     */
    public static function validate(array $data): array
    {
        $validator = Validator::make($data, [
            'userId' => ['required', 'integer', 'exists:users,id'],
            'roleIds' => ['required', 'array'],
            'roleIds.*' => ['integer', 'exists:roles,id'],
        ]);

        return $validator->validate();
    }
}
