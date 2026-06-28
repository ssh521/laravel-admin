<?php

namespace Ssh521\LaravelAdmin\Validators\User;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Ssh521\LaravelAdmin\Support\ResolvesUserModel;

final class UpdateUserValidator
{
    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     *
     * @throws ValidationException
     */
    public static function validate(array $data): array
    {
        $userId = $data['userId'] ?? null;
        $table = app(ResolvesUserModel::class)->table();

        $validator = Validator::make($data, [
            'userId' => ['required', 'integer', Rule::exists($table, 'id')],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique($table, 'email')->ignore($userId)],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        return $validator->validate();
    }
}
