<?php

namespace Ssh521\LaravelAdmin\DTOs\Notice;

final readonly class CreateNoticeDTO
{
    public function __construct(
        public string $title,
        public string $content,
        public int $authorId,
        public bool $isPublished = false,
        public ?string $publishedAt = null,
    ) {}

    public static function make(array $data): self
    {
        return new self(
            title: $data['title'],
            content: $data['content'],
            authorId: $data['author_id'],
            isPublished: $data['is_published'] ?? false,
            publishedAt: $data['published_at'] ?? null,
        );
    }
}
