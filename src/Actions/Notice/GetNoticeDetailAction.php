<?php

namespace Ssh521\LaravelAdmin\Actions\Notice;

use Ssh521\LaravelAdmin\DTOs\Notice\GetNoticeDetailDTO;
use Ssh521\LaravelAdmin\Models\AdminNotice;

final class GetNoticeDetailAction
{
    public function execute(GetNoticeDetailDTO $dto): AdminNotice
    {
        return AdminNotice::with(['author', 'attachments'])->findOrFail($dto->noticeId);
    }
}
