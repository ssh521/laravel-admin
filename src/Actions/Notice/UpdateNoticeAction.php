<?php

namespace Ssh521\LaravelAdmin\Actions\Notice;

use Ssh521\LaravelAdmin\DTOs\Notice\UpdateNoticeDTO;
use Ssh521\LaravelAdmin\Models\AdminNotice;

final class UpdateNoticeAction
{
    public function execute(UpdateNoticeDTO $dto): AdminNotice
    {
        $notice = AdminNotice::findOrFail($dto->noticeId);

        $notice->title = $dto->title;
        $notice->content = $dto->content;
        $notice->is_published = $dto->isPublished;
        $notice->published_at = $dto->publishedAt ? now()->parse($dto->publishedAt) : ($dto->isPublished ? now() : null);

        $notice->save();

        return $notice;
    }
}
