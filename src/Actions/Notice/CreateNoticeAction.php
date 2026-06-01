<?php

namespace Ssh521\LaravelAdmin\Actions\Notice;

use Ssh521\LaravelAdmin\DTOs\Notice\CreateNoticeDTO;
use Ssh521\LaravelAdmin\Models\AdminNotice;

final class CreateNoticeAction
{
    public function execute(CreateNoticeDTO $dto): AdminNotice
    {
        return AdminNotice::create([
            'title' => $dto->title,
            'content' => $dto->content,
            'author_id' => $dto->authorId,
            'is_published' => $dto->isPublished,
            'published_at' => $dto->publishedAt ? now()->parse($dto->publishedAt) : ($dto->isPublished ? now() : null),
        ]);
    }
}
