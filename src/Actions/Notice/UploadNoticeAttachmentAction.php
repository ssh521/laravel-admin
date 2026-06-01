<?php

namespace Ssh521\LaravelAdmin\Actions\Notice;

use Ssh521\LaravelAdmin\DTOs\Notice\UploadNoticeAttachmentDTO;
use Ssh521\LaravelAdmin\Models\AdminNoticeAttachment;

final class UploadNoticeAttachmentAction
{
    public function execute(UploadNoticeAttachmentDTO $dto): AdminNoticeAttachment
    {
        // 파일 저장 경로 생성
        $directory = 'notices/'.$dto->noticeId;
        $fileName = time().'_'.uniqid().'_'.preg_replace('/[^a-zA-Z0-9._-]/', '_', $dto->originalName);
        $filePath = $dto->file->storeAs($directory, $fileName, 'public');

        // sort_order 계산 (기존 최대값 + 1, 없으면 0)
        $maxSortOrder = AdminNoticeAttachment::where('notice_id', $dto->noticeId)->max('sort_order');

        // 데이터베이스에 저장
        return AdminNoticeAttachment::create([
            'notice_id' => $dto->noticeId,
            'original_name' => $dto->originalName,
            'file_path' => $filePath,
            'file_size' => $dto->fileSize,
            'mime_type' => $dto->mimeType ?? $dto->file->getMimeType(),
            'sort_order' => ($maxSortOrder ?? 0) + 1,
        ]);
    }
}
