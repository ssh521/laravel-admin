<?php

namespace Ssh521\LaravelAdmin\Livewire\Admin\Notices;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\On;
use Livewire\Component;
use Ssh521\LaravelAdmin\Services\NoticeService;

class NoticeDetail1Modal extends Component
{
    use AuthorizesRequests;

    public $notice = null;

    public ?string $modalKeyId = null;

    private NoticeService $noticeService;

    public function boot(NoticeService $noticeService): void
    {
        $this->noticeService = $noticeService;
    }

    public function mount(?int $noticeId = null, ?string $modalKeyId = null): void
    {
        $this->modalKeyId = $modalKeyId;

        if ($noticeId) {
            $this->loadNotice($noticeId);
        }
    }

    public function loadNotice(int $noticeId): void
    {
        $this->notice = $this->noticeService->getNoticeDetail(['notice_id' => $noticeId]);

        if (! $this->notice) {
            return;
        }

        $this->authorize('view', $this->notice);
    }

    public function editNotice(): void
    {
        if (! $this->notice) {
            return;
        }

        $this->authorize('update', $this->notice);

        $this->redirect(route('admin.notices.edit', $this->notice->id));
    }

    public function resetNotice(): void
    {
        $this->notice = null;
    }

    #[On('admin-notices:notice:saved')]
    public function refreshNotice(): void
    {
        if ($this->notice && $this->notice->id) {
            $this->loadNotice($this->notice->id);
        }
    }

    public function render()
    {
        return view('livewire.admin.notices.notice-detail1-modal');
    }
}
