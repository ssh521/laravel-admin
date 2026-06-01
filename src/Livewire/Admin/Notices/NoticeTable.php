<?php

namespace Ssh521\LaravelAdmin\Livewire\Admin\Notices;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Ssh521\LaravelAdmin\Models\AdminNotice;
use Ssh521\LaravelAdmin\Services\NoticeService;

class NoticeTable extends Component
{
    use AuthorizesRequests, WithPagination;

    private NoticeService $noticeService;

    public function __construct()
    {
        $this->noticeService = app(NoticeService::class);
    }

    public $search = '';

    public $isPublished = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'isPublished' => ['except' => null],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingIsPublished(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        // 권한 검사: 공지사항 목록 조회 권한이 있는지 확인
        $this->authorize('viewAny', AdminNotice::class);

        $notices = $this->noticeService->getNoticeList([
            'search' => $this->search,
            'is_published' => $this->isPublished,
            'sortField' => 'created_at',
            'sortDirection' => 'desc',
            'perPage' => 15,
        ]);

        return view('livewire.admin.notices.notice-table', [
            'notices' => $notices,
            'canCreate' => Gate::allows('create', AdminNotice::class),
            'canEdit' => function ($notice) {
                return Gate::allows('update', $notice);
            },
            'canDelete' => function ($notice) {
                return Gate::allows('delete', $notice);
            },
        ]);
    }

    public function create(): void
    {
        $this->authorize('create', AdminNotice::class);
        $this->dispatch('admin-notices:notice-form:open', ['mode' => 'create']);
    }

    public function showNoticeDetailModal(int $noticeId): void
    {
        $this->dispatch('admin-notices:notice-detail-modal:open', [
            'noticeId' => $noticeId,
        ]);
    }

    public function view($id): void
    {
        // 먼저 모델을 가져와서 권한 검사
        $notice = AdminNotice::findOrFail($id);
        $this->authorize('view', $notice);

        // 상세 정보가 필요하면 Service 호출
        $noticeDetail = $this->noticeService->getNoticeDetail(['notice_id' => $id]);
        $this->dispatch('admin-notices:notice-form:open', ['mode' => 'view', 'id' => $noticeDetail->id]);

        // 모달 열기 이벤트 디스패치
        $this->js("
            window.dispatchEvent(new CustomEvent('open-modal', { 
                detail: { modalId: 'notice-view-modal' } 
            }));
        ");
    }

    public function edit($id): void
    {
        $notice = $this->noticeService->getNoticeDetail(['notice_id' => $id]);
        $this->authorize('update', $notice);
        $this->dispatch('admin-notices:notice-form:open', ['mode' => 'edit', 'id' => $notice->id]);
    }

    public function delete($id): void
    {
        $notice = AdminNotice::findOrFail($id);
        $this->authorize('delete', $notice);
        $this->noticeService->deleteNotice(['notice_id' => $id]);
        $this->resetPage();
    }

    public function togglePublishStatus($id): void
    {
        $notice = AdminNotice::findOrFail($id);
        $this->authorize('update', $notice);
        $this->noticeService->togglePublishStatus(['notice_id' => $id]);
        $this->dispatch('notification:show', message: '공지사항 발행 상태가 변경되었습니다.', type: 'success');
    }

    #[On('admin-notices:notice:saved')]
    public function handleNoticeSaved(): void
    {
        // 공지사항이 저장된 후 목록 새로고침 (현재 페이지 유지)
        // Livewire가 자동으로 컴포넌트를 다시 렌더링하여 최신 데이터를 표시
    }
}
