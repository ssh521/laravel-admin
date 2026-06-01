<?php

namespace Ssh521\LaravelAdmin\Livewire\Admin\Notices;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\On;
use Livewire\Component;
use Ssh521\LaravelAdmin\Services\NoticeService;

class NoticeDetailModal extends Component
{
    use AuthorizesRequests;

    public $notice = null;

    private NoticeService $noticeService;

    public function boot(NoticeService $noticeService): void
    {
        $this->noticeService = $noticeService;
    }

    #[On('admin-notices:notice-detail-modal:open')]
    public function open(array $payload = []): void
    {
        $noticeId = $payload['noticeId'] ?? null;

        if (! $noticeId) {
            return;
        }

        $this->notice = $this->noticeService->getNoticeDetail(['notice_id' => $noticeId]);

        if (! $this->notice) {
            return;
        }

        $this->authorize('view', $this->notice);

        // window.dispatchEvent를 사용하여 모달 열기로 해야함. 이벤트.window 에 적용될려면 js 로 해야함.
        $this->js("
            window.dispatchEvent(new CustomEvent('open-modal', { 
                detail: { modalId: 'notice-detail-modal' } 
            }));
        ");
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

    public function render()
    {
        return view('livewire.admin.notices.notice-detail-modal');
    }
}
