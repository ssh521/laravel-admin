<?php

namespace Ssh521\LaravelAdmin\Validators\Notice;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

final class UploadNoticeAttachmentValidator
{
    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     *
     * @throws ValidationException
     */
    public static function validate(array $data): array
    {
        $rules = [
            'notice_id' => ['required', 'integer', 'exists:admin_notices,id'],
            'original_name' => ['required', 'string', 'max:255'],
            'file_size' => ['required', 'integer', 'min:0', 'max:10485760'], // 10MB 제한
            'mime_type' => ['nullable', 'string', 'max:255'],
        ];

        // file이 UploadedFile 인스턴스인 경우
        if (isset($data['file']) && $data['file'] instanceof UploadedFile) {
            $rules['file'] = ['required', 'file', 'max:10240']; // 10MB 제한
        } else {
            $rules['file'] = ['required'];
        }

        $validator = Validator::make($data, $rules);

        return $validator->validate();
    }
}
