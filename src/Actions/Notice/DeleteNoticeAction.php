<?php

namespace Ssh521\LaravelAdmin\Actions\Notice;

use Ssh521\LaravelAdmin\DTOs\Notice\DeleteNoticeDTO;
use Ssh521\LaravelAdmin\Models\AdminNotice;

final class DeleteNoticeAction
{
    public function execute(DeleteNoticeDTO $dto): bool
    {
        $notice = AdminNotice::findOrFail($dto->noticeId);
        $notice->delete();

        return true;
    }
}
