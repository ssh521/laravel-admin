<?php

namespace Ssh521\LaravelAdmin\Actions\Notice;

use Illuminate\Support\Facades\Storage;
use Ssh521\LaravelAdmin\DTOs\Notice\DeleteNoticeAttachmentDTO;
use Ssh521\LaravelAdmin\Models\AdminNoticeAttachment;

final class DeleteNoticeAttachmentAction
{
    public function execute(DeleteNoticeAttachmentDTO $dto): bool
    {
        $attachment = AdminNoticeAttachment::findOrFail($dto->attachmentId);

        // 파일 삭제
        if (Storage::disk('public')->exists($attachment->file_path)) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        // 데이터베이스에서 삭제
        $attachment->delete();

        return true;
    }
}
