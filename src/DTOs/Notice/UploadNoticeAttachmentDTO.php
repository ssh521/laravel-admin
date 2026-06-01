<?php

namespace Ssh521\LaravelAdmin\DTOs\Notice;

use Illuminate\Http\UploadedFile;

final readonly class UploadNoticeAttachmentDTO
{
    public function __construct(
        public int $noticeId,
        public UploadedFile $file,
        public string $originalName,
        public int $fileSize,
        public ?string $mimeType = null,
    ) {}

    public static function make(array $data): self
    {
        return new self(
            noticeId: $data['notice_id'],
            file: $data['file'],
            originalName: $data['original_name'],
            fileSize: $data['file_size'],
            mimeType: $data['mime_type'] ?? null,
        );
    }
}
