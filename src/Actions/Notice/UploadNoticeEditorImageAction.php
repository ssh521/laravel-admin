<?php

namespace Ssh521\LaravelAdmin\Actions\Notice;

use Ssh521\LaravelAdmin\DTOs\Notice\UploadNoticeEditorImageDTO;

final class UploadNoticeEditorImageAction
{
    public function execute(UploadNoticeEditorImageDTO $dto): string
    {
        $directory = 'notices/editor-images/'.$dto->uploaderId;

        $fileName = time().'_'.uniqid().'_'.preg_replace('/[^a-zA-Z0-9._-]/', '_', $dto->originalName);

        return $dto->file->storeAs($directory, $fileName, 'public');
    }
}
