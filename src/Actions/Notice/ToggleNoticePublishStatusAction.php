<?php

namespace Ssh521\LaravelAdmin\Actions\Notice;

use Ssh521\LaravelAdmin\DTOs\Notice\ToggleNoticePublishStatusDTO;
use Ssh521\LaravelAdmin\Models\AdminNotice;

final class ToggleNoticePublishStatusAction
{
    public function execute(ToggleNoticePublishStatusDTO $dto): AdminNotice
    {
        $notice = AdminNotice::findOrFail($dto->noticeId);

        $notice->is_published = ! $notice->is_published;
        $notice->published_at = $notice->is_published ? now() : null;

        $notice->save();

        return $notice;
    }
}
