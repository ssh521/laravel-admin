<?php

namespace Ssh521\LaravelAdmin\DTOs\Notice;

final readonly class UpdateNoticeAttachmentDTO
{
    public function __construct(
        public int $attachmentId,
        public string $originalName,
        public int $sortOrder,
    ) {}

    public static function make(array $data): self
    {
        return new self(
            attachmentId: $data['attachment_id'],
            originalName: $data['original_name'],
            sortOrder: $data['sort_order'],
        );
    }
}
