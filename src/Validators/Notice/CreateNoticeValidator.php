<?php

namespace Ssh521\LaravelAdmin\Validators\Notice;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

final class CreateNoticeValidator
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
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'author_id' => ['required', 'integer', 'exists:users,id'],
            'is_published' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ]);

        return $validator->validate();
    }
}
