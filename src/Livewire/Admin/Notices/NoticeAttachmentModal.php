<?php

namespace Ssh521\LaravelAdmin\Livewire\Admin\Notices;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;
use Ssh521\LaravelAdmin\Models\AdminNotice;
use Ssh521\LaravelAdmin\Services\NoticeService;

class NoticeAttachmentModal extends Component
{
    use AuthorizesRequests, WithFileUploads;

    public int $noticeId;

    public ?string $modalKeyId = null;

    /**
     * @var array<int, array<string, mixed>>
     */
    public array $attachments = [];

    /**
     * @var array<int, array{original_name: string, sort_order: int}>
     */
    public array $edited = [];

    public array $uploadedFiles = [];

    public function __construct()
    {
        $this->noticeService = app(NoticeService::class);
    }

    public function mount(int $noticeId, ?string $modalKeyId = null): void
    {
        $this->noticeId = $noticeId;
        $this->modalKeyId = $modalKeyId;

        $notice = $this->noticeService->getNoticeDetail(['notice_id' => $noticeId]);
        $this->authorize('update', $notice);

        $this->syncFromNotice($notice);
    }

    public function refreshAttachments(): void
    {
        $notice = $this->noticeService->getNoticeDetail(['notice_id' => $this->noticeId]);
        $this->authorize('update', $notice);

        $this->syncFromNotice($notice);
    }

    public function saveAttachment(int $attachmentId): void
    {
        $this->resetErrorBag();

        $notice = $this->noticeService->getNoticeDetail(['notice_id' => $this->noticeId]);
        $this->authorize('update', $notice);

        $payload = $this->edited[$attachmentId] ?? null;
        if (! is_array($payload)) {
            return;
        }

        try {
            $this->noticeService->updateNoticeAttachment([
                'attachment_id' => $attachmentId,
                'original_name' => $payload['original_name'] ?? '',
                'sort_order' => $payload['sort_order'] ?? 0,
            ]);

            $this->refreshAttachments();
            $this->dispatch('admin-notices:notice:saved');
        } catch (ValidationException $e) {
            foreach ($e->errors() as $key => $messages) {
                foreach ($messages as $message) {
                    $this->addError($key, $message);
                }
            }
        }
    }

    public function deleteAttachment(int $attachmentId): void
    {
        $this->resetErrorBag();

        $notice = $this->noticeService->getNoticeDetail(['notice_id' => $this->noticeId]);
        $this->authorize('update', $notice);

        try {
            $this->noticeService->deleteNoticeAttachment(['attachment_id' => $attachmentId]);

            $this->refreshAttachments();
            $this->dispatch('admin-notices:notice:saved');
        } catch (\Exception) {
            $this->addError('attachment', '첨부파일 삭제 중 오류가 발생했습니다.');
        }
    }

    public function uploadFiles(): void
    {
        $this->resetErrorBag();

        $notice = $this->noticeService->getNoticeDetail(['notice_id' => $this->noticeId]);
        $this->authorize('update', $notice);

        if (count($this->uploadedFiles) === 0) {
            $this->addError('uploadedFiles', '업로드할 파일을 선택해주세요.');

            return;
        }

        try {
            foreach ($this->uploadedFiles as $file) {
                $this->noticeService->uploadNoticeAttachment([
                    'notice_id' => $this->noticeId,
                    'file' => $file,
                    'original_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ]);
            }

            $this->uploadedFiles = [];
            $this->refreshAttachments();
            $this->dispatch('admin-notices:notice:saved');
        } catch (ValidationException $e) {
            foreach ($e->errors() as $key => $messages) {
                foreach ($messages as $message) {
                    $this->addError($key, $message);
                }
            }
        }
    }

    public function close(): void
    {
        if (! $this->modalKeyId) {
            return;
        }

        $this->dispatch('dynamic-modal:close', ['modalId' => 'dm-'.$this->modalKeyId]);
    }

    public function render()
    {
        return view('livewire.admin.notices.notice-attachment-modal');
    }

    private function syncFromNotice(AdminNotice $notice): void
    {
        $this->attachments = $notice->attachments
            ->map(fn ($attachment) => [
                'id' => $attachment->id,
                'original_name' => $attachment->original_name,
                'file_path' => $attachment->file_path,
                'file_size' => $attachment->file_size,
                'sort_order' => $attachment->sort_order,
                'created_at' => $attachment->created_at?->format('Y-m-d H:i'),
            ])
            ->values()
            ->all();

        $this->edited = [];
        foreach ($this->attachments as $attachment) {
            $this->edited[$attachment['id']] = [
                'original_name' => $attachment['original_name'],
                'sort_order' => $attachment['sort_order'],
            ];
        }
    }

    private NoticeService $noticeService;
}
