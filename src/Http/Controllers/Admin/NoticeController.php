<?php

namespace Ssh521\LaravelAdmin\Http\Controllers\Admin;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Ssh521\LaravelAdmin\Http\Controllers\Controller;
use Ssh521\LaravelAdmin\Models\AdminNotice;
use Ssh521\LaravelAdmin\Models\AdminNoticeAttachment;
use Ssh521\LaravelAdmin\Services\NoticeService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class NoticeController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly NoticeService $noticeService,
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', AdminNotice::class);

        return view('admin.notices.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', AdminNotice::class);

        return view('admin.notices.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', AdminNotice::class);

        $notice = $this->noticeService->createNotice([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'author_id' => auth()->id(),
            'is_published' => $request->boolean('is_published'),
            'published_at' => $request->input('published_at'),
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments', []) as $file) {
                if (! $file || ! $file->isValid()) {
                    continue;
                }

                $this->noticeService->uploadNoticeAttachment([
                    'notice_id' => $notice->id,
                    'file' => $file,
                    'original_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ]);
            }
        }

        return redirect()->route('admin.notices.index')
            ->with('success', '공지사항이 생성되었습니다.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AdminNotice $notice)
    {
        $this->authorize('view', $notice);

        $notice = $this->noticeService->getNoticeDetail([
            'notice_id' => $notice->id,
        ]);

        return view('admin.notices.show', compact('notice'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AdminNotice $notice)
    {
        $this->authorize('update', $notice);

        $notice = $this->noticeService->getNoticeDetail([
            'notice_id' => $notice->id,
        ]);

        return view('admin.notices.edit', compact('notice'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AdminNotice $notice)
    {
        $this->authorize('update', $notice);

        $notice = $this->noticeService->updateNotice([
            'notice_id' => $notice->id,
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'is_published' => $request->boolean('is_published'),
            'published_at' => $request->input('published_at'),
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments', []) as $file) {
                if (! $file || ! $file->isValid()) {
                    continue;
                }

                $this->noticeService->uploadNoticeAttachment([
                    'notice_id' => $notice->id,
                    'file' => $file,
                    'original_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ]);
            }
        }

        return redirect()->route('admin.notices.index')
            ->with('success', '공지사항이 수정되었습니다.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AdminNotice $notice)
    {
        $this->authorize('delete', $notice);

        $this->noticeService->deleteNotice([
            'notice_id' => $notice->id,
        ]);

        return redirect()->route('admin.notices.index')
            ->with('success', '공지사항이 삭제되었습니다.');
    }

    public function downloadAttachment(AdminNoticeAttachment $attachment): BinaryFileResponse
    {
        $this->authorize('view', $attachment->notice);

        if (! Storage::disk('public')->exists($attachment->file_path)) {
            abort(404, '파일을 찾을 수 없습니다.');
        }

        return response()->download(
            Storage::disk('public')->path($attachment->file_path),
            $attachment->original_name
        );
    }

    public function deleteAttachment(AdminNoticeAttachment $attachment)
    {
        $this->authorize('update', $attachment->notice);

        $this->noticeService->deleteNoticeAttachment([
            'attachment_id' => $attachment->id,
        ]);

        return redirect()->back()
            ->with('success', '첨부파일이 삭제되었습니다.');
    }

    public function uploadImage(Request $request): JsonResponse
    {
        $this->authorize('uploadImage', AdminNotice::class);

        $file = $request->file('image');

        if (! $file || ! $file->isValid()) {
            return response()->json([
                'success' => false,
                'message' => '유효하지 않은 파일입니다.',
            ], 422);
        }

        $path = $this->noticeService->uploadNoticeEditorImage([
            'uploader_id' => (int) auth()->id(),
            'file' => $file,
            'original_name' => $file->getClientOriginalName(),
            'file_size' => (int) $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ]);

        return response()->json([
            'success' => true,
            'url' => Storage::disk('public')->url($path),
            'filename' => basename($path),
        ]);
    }
}
