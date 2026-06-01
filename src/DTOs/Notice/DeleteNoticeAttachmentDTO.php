<?php

namespace Ssh521\LaravelAdmin\DTOs\Notice;

final readonly class DeleteNoticeAttachmentDTO
{
    public function __construct(
        public int $attachmentId,
    ) {}

    public static function make(array $data): self
    {
        return new self(
            attachmentId: $data['attachment_id'],
        );
    }
}
