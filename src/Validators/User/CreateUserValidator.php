<?php

namespace Ssh521\LaravelAdmin\Validators\User;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

final class CreateUserValidator
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        return $validator->validate();
    }
}
