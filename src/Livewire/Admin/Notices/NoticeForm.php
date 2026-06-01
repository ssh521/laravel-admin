<?php

namespace Ssh521\LaravelAdmin\Livewire\Admin\Notices;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Ssh521\LaravelAdmin\Models\AdminNotice;
use Ssh521\LaravelAdmin\Services\NoticeService;

class NoticeForm extends Component
{
    use AuthorizesRequests, WithFileUploads;

    public $showModal = false;

    public $mode = 'create'; // create | view | edit

    public $parentModalId = null; // 부모 draggable-modal의 ID

    public $noticeId;

    public $title = '';

    public $content = '';

    public $isPublished = false;

    public $publishedAt = null;

    public $attachments = [];

    public $uploadedFiles = [];

    public $author = null;

    public $createdAt = null;

    public function __construct()
    {
        $this->noticeService = app(NoticeService::class);
    }

    public function mount(?int $noticeId = null, string $mode = 'create', ?string $parentModalId = null): void
    {
        // draggable-modal에서 마운트될 때 설정
        $this->parentModalId = $parentModalId;

        // mount 시점에는 showModal을 false로 설정 (open() 메서드가 호출될 때 true로 변경됨)
        $this->showModal = false;
    }

    #[On('admin-notices:notice-form:open')]
    public function open(array $payload = []): void
    {
        $this->resetValidation();
        $this->resetForm();

        $this->mode = $payload['mode'] ?? 'create';
        $noticeId = $payload['id'] ?? null;

        if ($this->mode === 'create') {
            $this->authorize('create', AdminNotice::class);
        }

        if (in_array($this->mode, ['view', 'edit'], true) && $noticeId) {
            $notice = $this->noticeService->getNoticeDetail(['notice_id' => $noticeId]);
            $this->authorize($this->mode === 'view' ? 'view' : 'update', $notice);

            $this->noticeId = $notice->id;
            $this->title = $notice->title;
            $this->content = $notice->content;
            $this->isPublished = $notice->is_published;
            $this->publishedAt = $notice->published_at?->format('Y-m-d\TH:i');
            $this->attachments = $notice->attachments->toArray();
            $this->author = $notice->author;
            $this->createdAt = $notice->created_at;
        }

        // parentModalId가 있으면 모달은 이미 열려있으므로 showModal만 true로 설정
        // 없으면 독립적으로 사용되는 경우이므로 showModal을 true로 설정
        $this->showModal = true;
    }

    public function handleCloseModal(array $payload = []): void
    {
        // 자신의 parentModalId와 일치하는 경우에만 닫기
        if (isset($payload['modalId']) && $payload['modalId'] === $this->parentModalId) {
            $this->resetNotice();
        }
    }

    public function close(): void
    {
        // 이미 닫혀있으면 중복 호출 방지
        if (! $this->showModal) {
            return;
        }

        $this->showModal = false;

        // draggable-modal이 있는 경우 모달 닫기 이벤트 디스패치
        // NoticeForm이 draggable-modal 안에 있을 때 모달도 함께 닫기
        if ($this->parentModalId) {
            $this->dispatch('close-modal', ['modalId' => $this->parentModalId]);
        }

        // 폼 리셋
        $this->resetForm();
    }

    public function save(): void
    {
        if ($this->mode === 'view') {
            return;
        }

        try {
            if ($this->mode === 'create') {
                $this->authorize('create', AdminNotice::class);
                $notice = $this->noticeService->createNotice([
                    'title' => $this->title,
                    'content' => $this->content,
                    'author_id' => Auth::id(),
                    'is_published' => $this->isPublished,
                    'published_at' => $this->publishedAt,
                ]);
                $this->noticeId = $notice->id;
            } else {
                $notice = AdminNotice::findOrFail($this->noticeId);
                $this->authorize('update', $notice);
                $this->noticeService->updateNotice([
                    'notice_id' => $this->noticeId,
                    'title' => $this->title,
                    'content' => $this->content,
                    'is_published' => $this->isPublished,
                    'published_at' => $this->publishedAt,
                ]);
            }

            // 업로드된 파일 처리
            foreach ($this->uploadedFiles as $file) {
                $this->noticeService->uploadNoticeAttachment([
                    'notice_id' => $this->noticeId,
                    'file' => $file,
                    'original_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ]);
            }

            $this->dispatch('admin-notices:notice:saved');
            $this->showModal = false;
        } catch (ValidationException $e) {
            $this->resetValidation();
            foreach ($e->errors() as $key => $messages) {
                foreach ($messages as $message) {
                    $this->addError($key, $message);
                }
            }
        }
    }

    public function removeAttachment($attachmentId): void
    {
        try {
            $this->noticeService->deleteNoticeAttachment(['attachment_id' => $attachmentId]);
            $this->attachments = array_filter($this->attachments, function ($attachment) use ($attachmentId) {
                return $attachment['id'] != $attachmentId;
            });
            $this->dispatch('admin-notices:notice:saved');
        } catch (\Exception $e) {
            $this->addError('attachment', '첨부파일 삭제 중 오류가 발생했습니다.');
        }
    }

    public function removeUploadedFile($index): void
    {
        unset($this->uploadedFiles[$index]);
        $this->uploadedFiles = array_values($this->uploadedFiles);
    }

    public function resetNotice(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $this->noticeId = null;
        $this->title = '';
        $this->content = '';
        $this->isPublished = false;
        $this->publishedAt = null;
        $this->attachments = [];
        $this->uploadedFiles = [];
        $this->author = null;
        $this->createdAt = null;
    }

    protected function loadNoticeData(int $noticeId): void
    {
        $notice = $this->noticeService->getNoticeDetail(['notice_id' => $noticeId]);

        if ($this->mode === 'view') {
            $this->authorize('view', $notice);
        } elseif ($this->mode === 'edit') {
            $this->authorize('update', $notice);
        }

        $this->noticeId = $notice->id;
        $this->title = $notice->title;
        $this->content = $notice->content;
        $this->isPublished = $notice->is_published;
        $this->publishedAt = $notice->published_at?->format('Y-m-d\TH:i');
        $this->attachments = $notice->attachments->toArray();
    }

    public function render()
    {
        return view('livewire.admin.notices.notice-form');
    }

    private NoticeService $noticeService;
}
