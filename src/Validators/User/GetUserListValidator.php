<?php

namespace Ssh521\LaravelAdmin\Validators\User;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

final class GetUserListValidator
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
            'search' => ['nullable', 'string'],
            'sortField' => ['nullable', 'string', 'in:name,email,created_at'],
            'sortDirection' => ['nullable', 'string', 'in:asc,desc'],
            'perPage' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        return $validator->validate();
    }
}
