<?php

namespace Ssh521\LaravelAdmin\DTOs\Notice;

final readonly class ToggleNoticePublishStatusDTO
{
    public function __construct(
        public int $noticeId,
    ) {}

    public static function make(array $data): self
    {
        return new self(
            noticeId: $data['notice_id'],
        );
    }
}
