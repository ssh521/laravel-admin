<?php

namespace Ssh521\LaravelAdmin\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Ssh521\LaravelAdmin\Actions\Notice\CreateNoticeAction;
use Ssh521\LaravelAdmin\Actions\Notice\DeleteNoticeAction;
use Ssh521\LaravelAdmin\Actions\Notice\DeleteNoticeAttachmentAction;
use Ssh521\LaravelAdmin\Actions\Notice\GetNoticeDetailAction;
use Ssh521\LaravelAdmin\Actions\Notice\GetNoticeListAction;
use Ssh521\LaravelAdmin\Actions\Notice\ToggleNoticePublishStatusAction;
use Ssh521\LaravelAdmin\Actions\Notice\UpdateNoticeAction;
use Ssh521\LaravelAdmin\Actions\Notice\UpdateNoticeAttachmentAction;
use Ssh521\LaravelAdmin\Actions\Notice\UploadNoticeAttachmentAction;
use Ssh521\LaravelAdmin\Actions\Notice\UploadNoticeEditorImageAction;
use Ssh521\LaravelAdmin\DTOs\Notice\CreateNoticeDTO;
use Ssh521\LaravelAdmin\DTOs\Notice\DeleteNoticeAttachmentDTO;
use Ssh521\LaravelAdmin\DTOs\Notice\DeleteNoticeDTO;
use Ssh521\LaravelAdmin\DTOs\Notice\GetNoticeDetailDTO;
use Ssh521\LaravelAdmin\DTOs\Notice\GetNoticeListDTO;
use Ssh521\LaravelAdmin\DTOs\Notice\ToggleNoticePublishStatusDTO;
use Ssh521\LaravelAdmin\DTOs\Notice\UpdateNoticeAttachmentDTO;
use Ssh521\LaravelAdmin\DTOs\Notice\UpdateNoticeDTO;
use Ssh521\LaravelAdmin\DTOs\Notice\UploadNoticeAttachmentDTO;
use Ssh521\LaravelAdmin\DTOs\Notice\UploadNoticeEditorImageDTO;
use Ssh521\LaravelAdmin\Models\AdminNotice;
use Ssh521\LaravelAdmin\Models\AdminNoticeAttachment;
use Ssh521\LaravelAdmin\Validators\Notice\CreateNoticeValidator;
use Ssh521\LaravelAdmin\Validators\Notice\DeleteNoticeAttachmentValidator;
use Ssh521\LaravelAdmin\Validators\Notice\DeleteNoticeValidator;
use Ssh521\LaravelAdmin\Validators\Notice\GetNoticeDetailValidator;
use Ssh521\LaravelAdmin\Validators\Notice\GetNoticeListValidator;
use Ssh521\LaravelAdmin\Validators\Notice\ToggleNoticePublishStatusValidator;
use Ssh521\LaravelAdmin\Validators\Notice\UpdateNoticeAttachmentValidator;
use Ssh521\LaravelAdmin\Validators\Notice\UpdateNoticeValidator;
use Ssh521\LaravelAdmin\Validators\Notice\UploadNoticeAttachmentValidator;
use Ssh521\LaravelAdmin\Validators\Notice\UploadNoticeEditorImageValidator;

final class NoticeService
{
    public function __construct(
        private readonly CreateNoticeAction $createNoticeAction,
        private readonly UpdateNoticeAction $updateNoticeAction,
        private readonly DeleteNoticeAction $deleteNoticeAction,
        private readonly GetNoticeListAction $getNoticeListAction,
        private readonly GetNoticeDetailAction $getNoticeDetailAction,
        private readonly UploadNoticeAttachmentAction $uploadNoticeAttachmentAction,
        private readonly DeleteNoticeAttachmentAction $deleteNoticeAttachmentAction,
        private readonly UpdateNoticeAttachmentAction $updateNoticeAttachmentAction,
        private readonly ToggleNoticePublishStatusAction $toggleNoticePublishStatusAction,
        private readonly UploadNoticeEditorImageAction $uploadNoticeEditorImageAction,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function createNotice(array $data): AdminNotice
    {
        $validated = CreateNoticeValidator::validate($data);
        $dto = CreateNoticeDTO::make($validated);

        return $this->createNoticeAction->execute($dto);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateNotice(array $data): AdminNotice
    {
        $validated = UpdateNoticeValidator::validate($data);
        $dto = UpdateNoticeDTO::make($validated);

        return $this->updateNoticeAction->execute($dto);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function deleteNotice(array $data): bool
    {
        $validated = DeleteNoticeValidator::validate($data);
        $dto = DeleteNoticeDTO::make($validated);

        return $this->deleteNoticeAction->execute($dto);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function getNoticeList(array $data): LengthAwarePaginator
    {
        // DTO 에서 문자열만 받음. 숫자가 들어오면 문자열도 변경함.
        if (array_key_exists('search', $data) && $data['search'] !== null && is_scalar($data['search'])) {
            $data['search'] = (string) $data['search'];
        }

        $validated = GetNoticeListValidator::validate($data);
        $dto = GetNoticeListDTO::make($validated);

        return $this->getNoticeListAction->execute($dto);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function getNoticeDetail(array $data): AdminNotice
    {
        $validated = GetNoticeDetailValidator::validate($data);
        $dto = GetNoticeDetailDTO::make($validated);

        return $this->getNoticeDetailAction->execute($dto);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function uploadNoticeAttachment(array $data): AdminNoticeAttachment
    {
        $validated = UploadNoticeAttachmentValidator::validate($data);
        $dto = UploadNoticeAttachmentDTO::make($validated);

        return $this->uploadNoticeAttachmentAction->execute($dto);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function uploadNoticeEditorImage(array $data): string
    {
        $validated = UploadNoticeEditorImageValidator::validate($data);
        $dto = UploadNoticeEditorImageDTO::make($validated);

        return $this->uploadNoticeEditorImageAction->execute($dto);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function deleteNoticeAttachment(array $data): bool
    {
        $validated = DeleteNoticeAttachmentValidator::validate($data);
        $dto = DeleteNoticeAttachmentDTO::make($validated);

        return $this->deleteNoticeAttachmentAction->execute($dto);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateNoticeAttachment(array $data): AdminNoticeAttachment
    {
        $validated = UpdateNoticeAttachmentValidator::validate($data);
        $dto = UpdateNoticeAttachmentDTO::make($validated);

        return $this->updateNoticeAttachmentAction->execute($dto);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function togglePublishStatus(array $data): AdminNotice
    {
        $validated = ToggleNoticePublishStatusValidator::validate($data);
        $dto = ToggleNoticePublishStatusDTO::make($validated);

        return $this->toggleNoticePublishStatusAction->execute($dto);
    }
}
