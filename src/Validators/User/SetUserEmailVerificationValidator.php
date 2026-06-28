<?php

namespace Ssh521\LaravelAdmin\Validators\User;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Ssh521\LaravelAdmin\Support\ResolvesUserModel;

final class SetUserEmailVerificationValidator
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
            'userId' => ['required', 'integer', Rule::exists($table, 'id')],
            'isVerified' => ['required', 'boolean'],
        ]);

        return $validator->validate();
    }
}
