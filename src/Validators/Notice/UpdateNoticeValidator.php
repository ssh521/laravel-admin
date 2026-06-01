<?php

namespace Ssh521\LaravelAdmin\Validators\Notice;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

final class UpdateNoticeValidator
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
            'notice_id' => ['required', 'integer', 'exists:admin_notices,id'],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'is_published' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ]);

        return $validator->validate();
    }
}
