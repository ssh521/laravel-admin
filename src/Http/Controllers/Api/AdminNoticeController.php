<?php

namespace Ssh521\LaravelAdmin\Http\Controllers\Api;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Ssh521\LaravelAdmin\Http\Controllers\Controller;
use Ssh521\LaravelAdmin\Http\Resources\AdminNoticeResource;
use Ssh521\LaravelAdmin\Models\AdminNotice;
use Ssh521\LaravelAdmin\Services\NoticeService;

class AdminNoticeController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly NoticeService $noticeService,
    ) {}

    /**
     * Display a listing of the notices.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', AdminNotice::class);

        $notices = $this->noticeService->getNoticeList([
            'search' => $request->input('search'),
            'is_published' => $request->input('is_published'),
            'sortField' => $request->input('sortField', 'created_at'),
            'sortDirection' => $request->input('sortDirection', 'desc'),
            'perPage' => $request->input('perPage', 10),
        ]);

        return AdminNoticeResource::collection($notices);
    }

    /**
     * Store a newly created notice in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', AdminNotice::class);

        $notice = $this->noticeService->createNotice([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'author_id' => $request->user()->id,
            'is_published' => $request->input('is_published', false),
            'published_at' => $request->input('published_at'),
        ]);

        return (new AdminNoticeResource($notice->load(['author', 'attachments'])))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified notice.
     */
    public function show(int $id): AdminNoticeResource
    {
        $notice = $this->noticeService->getNoticeDetail(['notice_id' => $id]);
        $this->authorize('view', $notice);

        return new AdminNoticeResource($notice->load(['author', 'attachments']));
    }

    /**
     * Update the specified notice in storage.
     */
    public function update(Request $request, int $id): AdminNoticeResource
    {
        $notice = $this->noticeService->getNoticeDetail(['notice_id' => $id]);
        $this->authorize('update', $notice);

        $notice = $this->noticeService->updateNotice([
            'notice_id' => $id,
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'is_published' => $request->input('is_published', false),
            'published_at' => $request->input('published_at'),
        ]);

        return new AdminNoticeResource($notice->load(['author', 'attachments']));
    }

    /**
     * Remove the specified notice from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $notice = $this->noticeService->getNoticeDetail(['notice_id' => $id]);
        $this->authorize('delete', $notice);

        $this->noticeService->deleteNotice(['notice_id' => $id]);

        return response()->json([
            'message' => 'Notice deleted successfully',
        ], 200);
    }

    /**
     * Toggle the publish status of the specified notice.
     */
    public function togglePublishStatus(int $id): AdminNoticeResource
    {
        $notice = $this->noticeService->getNoticeDetail(['notice_id' => $id]);
        $this->authorize('update', $notice);

        $notice = $this->noticeService->togglePublishStatus(['notice_id' => $id]);

        return new AdminNoticeResource($notice->load(['author', 'attachments']));
    }

    /**
     * Display a listing of published notices (public).
     */
    public function publicIndex(Request $request): AnonymousResourceCollection
    {
        $notices = $this->noticeService->getNoticeList([
            'search' => $request->input('search'),
            'is_published' => true, // 발행된 공지사항만
            'sortField' => $request->input('sortField', 'published_at'),
            'sortDirection' => $request->input('sortDirection', 'desc'),
            'perPage' => $request->input('perPage', 5),
        ]);

        return AdminNoticeResource::collection($notices);
    }

    /**
     * Display the specified published notice (public).
     */
    public function publicShow(int $id): AdminNoticeResource
    {
        $notice = $this->noticeService->getNoticeDetail(['notice_id' => $id]);

        // 발행되지 않은 공지사항은 404 반환
        if (! $notice->is_published) {
            abort(404);
        }

        return new AdminNoticeResource($notice->load(['author', 'attachments']));
    }
}
