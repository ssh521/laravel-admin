<?php

namespace Ssh521\LaravelAdmin\DTOs\Notice;

final readonly class UpdateNoticeDTO
{
    public function __construct(
        public int $noticeId,
        public string $title,
        public string $content,
        public bool $isPublished = false,
        public ?string $publishedAt = null,
    ) {}

    public static function make(array $data): self
    {
        return new self(
            noticeId: $data['notice_id'],
            title: $data['title'],
            content: $data['content'],
            isPublished: $data['is_published'] ?? false,
            publishedAt: $data['published_at'] ?? null,
        );
    }
}
