<?php

namespace Ssh521\LaravelAdmin\Validators\User;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Ssh521\LaravelAdmin\Support\ResolvesUserModel;

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
        $table = app(ResolvesUserModel::class)->table();

        $validator = Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique($table, 'email')],
            'password' => ['required', 'string', 'min:8'],
        ]);

        return $validator->validate();
    }
}
