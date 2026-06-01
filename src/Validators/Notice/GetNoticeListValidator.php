<?php

namespace Ssh521\LaravelAdmin\Validators\Notice;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

final class GetNoticeListValidator
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
            'is_published' => ['nullable', 'boolean'],
            'sortField' => ['nullable', 'string', 'in:title,created_at,updated_at,published_at'],
            'sortDirection' => ['nullable', 'string', 'in:asc,desc'],
            'perPage' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        return $validator->validate();
    }
}
