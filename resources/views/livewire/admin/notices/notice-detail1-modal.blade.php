<div class="space-y-4">
    @if($notice)
    <!-- 기본 정보 -->
    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">

        <div class="">

            <div>
                제목 : {{ $notice->title }}
                <span class="mt-1">
                    @if($notice->is_published)
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        발행됨
                    </span>
                    @else
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        미발행
                    </span>
                    @endif
                </span>
            </div>

            <div>
                <p class="mt-1 text-sm text-gray-900 dark:text-white">작성자 : {{ $notice->author->name ?? 'N/A' }}
                </p>
            </div>

            @if($notice->is_published && $notice->published_at)
            <div>
                <p class="mt-1 text-sm text-gray-900 dark:text-white">발행일시: {{
                    $notice->published_at->format('Y-m-d H:i:s') }}</p>
            </div>
            @endif

            <div>
                <p class="mt-1 text-sm text-gray-900 dark:text-white">작성일 : {{
                    $notice->created_at->format('Y-m-d H:i:s') }}</p>
            </div>
            <div>
                <p class="mt-1 text-sm text-gray-900 dark:text-white">마지막 업데이트 : {{
                    $notice->updated_at->format('Y-m-d H:i:s') }}</p>
            </div>

        </div>
    </div>

    <!-- 내용 -->
    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
        <div class="text-sm text-gray-900 dark:text-white whitespace-pre-wrap">{{ $notice->content }}</div>
    </div>

    <!-- 첨부파일 -->
    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-md font-medium text-gray-900 dark:text-white">첨부파일</h3>
            @if($notice->attachments && $notice->attachments->count() > 0)
            @can('update', $notice)
            <x-dynamic-modal-trigger text="관리" type="link" variant="primary"
                component="admin.notices.notice-attachment-modal" key-id="notice-attachments-{{ $notice->id }}"
                :params="['noticeId' => $notice->id, 'modalKeyId' => 'notice-attachments-'.$notice->id]"
                title="첨부파일 관리" width="600" height="750" :single="true" />
            @endcan
            @endif
        </div>
        @if($notice->attachments && $notice->attachments->count() > 0)
        <div class="space-y-2">
            @foreach($notice->attachments as $attachment)
            <div
                class="flex items-center justify-between p-2 bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-600">
                <div class="flex items-center space-x-2 flex-1 min-w-0">
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 flex-shrink-0" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank"
                        class="text-sm text-blue-600 dark:text-blue-400 hover:underline truncate">
                        {{ $attachment->original_name }}
                    </a>
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                        ({{ number_format($attachment->file_size / 1024, 2) }} KB)
                    </span>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-sm text-gray-500 dark:text-gray-400">첨부파일이 없습니다.</p>
        @endif
    </div>

    <!-- 액션 버튼 -->
    <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-600">
        @can('update', $notice)
        <x-primary-button type="button" wire:click="editNotice">
            수정
        </x-primary-button>
        @endcan
        <x-secondary-button type="button" @click="Livewire.dispatch('dynamic-modal:close', [{ modalId: 'dm-{{ $modalKeyId }}' }])">닫기
        </x-secondary-button>
    </div>
    @else
    <div class="text-center py-8">
        <p class="text-gray-500 dark:text-gray-400">공지사항 정보를 불러오는 중...</p>
    </div>
    @endif
</div>

