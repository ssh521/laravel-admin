<?php

namespace Ssh521\LaravelAdmin\Actions\Notice;

use Ssh521\LaravelAdmin\DTOs\Notice\UpdateNoticeAttachmentDTO;
use Ssh521\LaravelAdmin\Models\AdminNoticeAttachment;

final class UpdateNoticeAttachmentAction
{
    public function execute(UpdateNoticeAttachmentDTO $dto): AdminNoticeAttachment
    {
        $attachment = AdminNoticeAttachment::findOrFail($dto->attachmentId);

        $attachment->update([
            'original_name' => $dto->originalName,
            'sort_order' => $dto->sortOrder,
        ]);

        return $attachment->refresh();
    }
}
