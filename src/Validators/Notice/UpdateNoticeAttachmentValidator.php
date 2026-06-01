<?php

namespace Ssh521\LaravelAdmin\Validators\Notice;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

final class UpdateNoticeAttachmentValidator
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
            'attachment_id' => ['required', 'integer', 'exists:admin_notice_attachments,id'],
            'original_name' => ['required', 'string', 'max:255'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);

        return $validator->validate();
    }
}
