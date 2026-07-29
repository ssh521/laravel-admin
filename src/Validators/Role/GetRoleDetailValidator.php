<?php

namespace Ssh521\LaravelAdmin\Validators\Role;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
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
        $guard = config('laravel-admin.auth.guard', 'laravel_admin');

        $validator = Validator::make($data, [
            'roleId' => [
                'required',
                'integer',
                Rule::exists('roles', 'id')->where('guard_name', $guard),
            ],
        ]);

        return $validator->validate();
    }
}
