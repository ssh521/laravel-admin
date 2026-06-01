<div 
    @if($parentModalId)
    x-data
    x-init="
        const handleModalClosed = (event) => {
            if (event.detail.modalId === '{{ $parentModalId }}') {
                @this.call('handleCloseModal', { modalId: '{{ $parentModalId }}' });
            }
        };
        window.addEventListener('closed-modal', handleModalClosed);
        $watch('$wire.showModal', (value) => {
            if (!value) {
                window.removeEventListener('closed-modal', handleModalClosed);
            }
        });
    "
    @endif
>
    @if($showModal)
        @if($parentModalId)
            {{-- draggable-modal 안에 있을 때는 자체 모달 UI 사용 안 함 --}}
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    {{ $mode === 'create' ? '새 공지사항 추가' : ($mode === 'view' ? '공지사항 보기' : '공지사항 편집') }}
                </h2>
        @else
            {{-- 독립적으로 사용될 때는 자체 모달 UI 사용 --}}
            <div class="fixed inset-0 z-40 flex items-center justify-center">
                <div class="absolute inset-0 bg-black/50" wire:click="close"></div>
                <div class="relative z-50 w-full max-w-2xl bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 max-h-[90vh] overflow-y-auto">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        {{ $mode === 'create' ? '새 공지사항 추가' : ($mode === 'view' ? '공지사항 보기' : '공지사항 편집') }}
                    </h2>
        @endif

                <div class="space-y-4">
                    @if($mode === 'view' && $author)
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg mb-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">작성자</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $author->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">작성일</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $createdAt ? $createdAt->format('Y-m-d H:i:s') : '-' }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">제목</label>
                        @if($mode === 'view')
                            <p class="w-full px-3 py-2 rounded bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600">{{ $title }}</p>
                        @else
                            <input type="text" class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white" wire:model.defer="title">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">내용</label>
                        @if($mode === 'view')
                            <div class="w-full px-3 py-2 rounded bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600 min-h-[200px] whitespace-pre-wrap">{{ $content }}</div>
                        @else
                            <textarea rows="10" class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white" wire:model.defer="content"></textarea>
                            @error('content')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">발행 상태</label>
                        @if($mode === 'view')
                            @if($isPublished)
                                <span class="inline-flex items-center px-3 py-1 text-sm font-medium bg-green-100 text-green-800 rounded-full">
                                    발행됨
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 text-sm font-medium bg-red-100 text-red-800 rounded-full">
                                    미발행
                                </span>
                            @endif
                        @else
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" class="rounded border-gray-300 dark:border-gray-600" wire:model.defer="isPublished">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">발행하기</span>
                            </label>
                            @error('is_published')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    @if($isPublished)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">발행일시</label>
                        @if($mode === 'view')
                            <p class="w-full px-3 py-2 rounded bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600">
                                {{ $publishedAt ? \Carbon\Carbon::parse($publishedAt)->format('Y-m-d H:i') : '-' }}
                            </p>
                        @else
                            <input type="datetime-local" class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white" wire:model.defer="publishedAt">
                            @error('published_at')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>
                    @endif

                    {{-- 첨부파일 섹션 --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">첨부파일</label>
                            @if($noticeId && $mode !== 'view')
                                <x-dynamic-modal-trigger
                                    text="첨부 관리"
                                    component="admin.notices.notice-attachment-modal"
                                    key-id="notice-attachments-{{ $noticeId }}"
                                    :params="['noticeId' => $noticeId, 'modalKeyId' => 'notice-attachments-'.$noticeId]"
                                    title="첨부파일 관리"
                                    width="900"
                                    height="600"
                                    :single="true"
                                    variant="secondary"
                                />
                            @endif
                        </div>
                        
                        {{-- 기존 첨부파일 목록 --}}
                        @if(count($attachments) > 0)
                        <div class="mb-3 space-y-2">
                            @foreach($attachments as $attachment)
                            <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded border border-gray-200 dark:border-gray-600">
                                <div class="flex items-center space-x-2 flex-1 min-w-0">
                                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <a href="{{ asset('storage/' . $attachment['file_path']) }}" target="_blank" class="text-sm text-blue-600 dark:text-blue-400 hover:underline truncate">
                                        {{ $attachment['original_name'] }}
                                    </a>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        ({{ number_format($attachment['file_size'] / 1024, 2) }} KB)
                                    </span>
                                </div>
                                @if($mode !== 'view')
                                <button wire:click="removeAttachment({{ $attachment['id'] }})" type="button" class="ml-2 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @endif

                        {{-- 새 파일 업로드 --}}
                        @if($mode !== 'view')
                        <div>
                            <input type="file" wire:model="uploadedFiles" multiple class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-gray-700 dark:file:text-gray-300">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">최대 10MB까지 업로드 가능합니다.</p>
                            @error('uploadedFiles.*')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            {{-- 업로드된 파일 미리보기 --}}
                            @if(count($uploadedFiles) > 0)
                            <div class="mt-3 space-y-2">
                                @foreach($uploadedFiles as $index => $file)
                                <div class="flex items-center justify-between p-2 bg-blue-50 dark:bg-blue-900/20 rounded border border-blue-200 dark:border-blue-800">
                                    <div class="flex items-center space-x-2 flex-1 min-w-0">
                                        <svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <span class="text-sm text-gray-700 dark:text-gray-300 truncate">{{ $file->getClientOriginalName() }}</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            ({{ number_format($file->getSize() / 1024, 2) }} KB)
                                        </span>
                                    </div>
                                    <button wire:click="removeUploadedFile({{ $index }})" type="button" class="ml-2 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-2">
                    <button class="px-4 py-2 rounded border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300" wire:click.stop="close">
                        {{ $mode === 'view' ? '닫기' : '취소' }}
                    </button>
                    @if($mode!=='view')
                        <button class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700" wire:click.stop="save">저장</button>
                    @endif
                </div>
            @if($parentModalId)
                </div>
            @else
                </div>
            </div>
            @endif
    @endif
</div>
