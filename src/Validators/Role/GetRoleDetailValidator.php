<?php

namespace Ssh521\LaravelAdmin\Validators\Role;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

final class GetRoleDetailValidator
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
            'roleId' => ['required', 'integer', 'exists:roles,id'],
        ]);

        return $validator->validate();
    }
}
