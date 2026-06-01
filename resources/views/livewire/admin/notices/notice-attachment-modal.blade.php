<div class="p-6 space-y-4">
    <div class="flex items-center justify-between">
        <div class="text-sm text-gray-600 dark:text-gray-300">
            공지사항 #{{ $noticeId }} 첨부파일 관리
        </div>
        <button type="button"
            class="px-3 py-1.5 rounded border border-gray-300 dark:border-gray-600 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700"
            wire:click="refreshAttachments">
            새로고침
        </button>
    </div>

    @error('attachment')
        <p class="text-sm text-red-600">{{ $message }}</p>
    @enderror

    <div class="space-y-3">
        @forelse($attachments as $attachment)
            <div class="p-3 rounded border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                <div class="flex items-start gap-3">
                    <div class="flex-1 space-y-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.notices.attachments.download', $attachment['id']) }}" target="_blank"
                                class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline truncate">
                                {{ $attachment['original_name'] }}
                            </a>
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                ({{ number_format(($attachment['file_size'] ?? 0) / 1024, 2) }} KB)
                            </span>
                            @if(!empty($attachment['created_at']))
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    • {{ $attachment['created_at'] }}
                                </span>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-6 gap-3">
                            <div class="md:col-span-4">
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    표시 파일명
                                </label>
                                <input type="text"
                                    class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white text-sm"
                                    wire:model.defer="edited.{{ $attachment['id'] }}.original_name">
                                @error('original_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    정렬
                                </label>
                                <input type="number" min="0"
                                    class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white text-sm"
                                    wire:model.defer="edited.{{ $attachment['id'] }}.sort_order">
                                @error('sort_order')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-2">
                        <button type="button"
                            class="px-3 py-2 rounded bg-indigo-600 dark:bg-indigo-500 text-white text-sm hover:bg-indigo-700 dark:hover:bg-indigo-400"
                            wire:click="saveAttachment({{ $attachment['id'] }})">
                            저장
                        </button>
                        <button type="button"
                            class="px-3 py-2 rounded bg-red-600 dark:bg-red-500 text-white text-sm hover:bg-red-700 dark:hover:bg-red-400"
                            wire:click="deleteAttachment({{ $attachment['id'] }})"
                            onclick="return confirm('정말 삭제하시겠습니까?');">
                            삭제
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-6 rounded border border-dashed border-gray-300 dark:border-gray-700 text-sm text-gray-500 dark:text-gray-400">
                첨부파일이 없습니다.
            </div>
        @endforelse
    </div>

    <div class="pt-4 border-t border-gray-200 dark:border-gray-700 space-y-2">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            새 파일 업로드
        </label>
        <input type="file" wire:model="uploadedFiles" multiple
            class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-gray-700 dark:file:text-gray-300">
        <p class="text-xs text-gray-500 dark:text-gray-400">최대 10MB까지 업로드 가능합니다.</p>
        @error('uploadedFiles')
            <p class="text-sm text-red-600">{{ $message }}</p>
        @enderror
        @error('uploadedFiles.*')
            <p class="text-sm text-red-600">{{ $message }}</p>
        @enderror
        <div wire:loading wire:target="uploadedFiles" class="text-xs text-gray-500 dark:text-gray-400">
            파일 업로드 준비 중...
        </div>

        <div class="flex justify-end gap-2 pt-2">
            <button type="button"
                class="px-4 py-2 rounded bg-blue-600 dark:bg-blue-500 text-white text-sm hover:bg-blue-700 dark:hover:bg-blue-400"
                wire:click="uploadFiles"
                wire:loading.attr="disabled"
                wire:target="uploadedFiles,uploadFiles">
                업로드
            </button>
            <button type="button"
                class="px-4 py-2 rounded border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 text-sm hover:bg-gray-50 dark:hover:bg-gray-700"
                wire:click="close">
                닫기
            </button>
        </div>
    </div>
</div>
