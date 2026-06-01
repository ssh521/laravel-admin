<x-layouts.admin title="공지사항 수정">
    <x-slot name="header">
        <x-admin-header>
            <x-slot name="navigation">
                <a href="{{ route('home') }}">HOME</a>
                - <a href="{{ route('admin.index') }}">관리자 홈</a>
                @can('viewAny', Ssh521\LaravelAdmin\Models\AdminNotice::class)
                - <a href="{{ route('admin.notices.index') }}">공지사항 리스트</a>
                @endcan

            </x-slot>
            <x-slot name="description">
                {{ __('Edit Notice') }}
            </x-slot>
        </x-admin-header>
    </x-slot>

    <div class="w-full mx-auto">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl p-6">

            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-medium">
                        {{ __('Notice Information') }}
                    </h3>
                    <p class="mt-1 text-sm">
                        {{ __('내용을 수정합니다.') }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('admin.notices.index') }}" class="text-blue-600 hover:text-blue-800">
                        ← {{ __('Back') }}
                    </a>
                </div>
            </div>

            <form action="{{ route('admin.notices.update', $notice) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-6">
                    <!-- Title Field -->
                    <div>
                        <x-input id="title" name="title" type="text" class="mt-1 block w-full lg:max-w-3xl"
                            :value="old('title', $notice->title)" placeholder="공지사항 제목을 입력하세요"
                            autocomplete="off" />
                        <x-input-error-message class="mt-2" :messages="$errors->get('title')" />
                    </div>
                </div>

                <!-- Content Field -->
                <div>
                    <textarea id="content" name="content" rows="15"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white"
                        placeholder="공지사항 내용을 입력하세요">{{ old('content', $notice->content) }}</textarea>
                    <div id="notice-editor" class="mt-3"></div>
                    <x-input-error-message class="mt-2" :messages="$errors->get('content')" />
                </div>

                <!-- Publication Status -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label :value="__('Publication Status')" />
                        <div class="mt-2 flex items-center space-x-4">
                            <div class="flex items-center">
                                <input id="is_published" name="is_published" type="checkbox" value="1" {{
                                    old('is_published', $notice->is_published ? '1' : null) == '1' ? 'checked' : '' }}
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500
                                dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700
                                dark:border-gray-600">
                                <label for="is_published" class="ms-2 text-sm font-medium">
                                    {{ __('Publish Notice') }}
                                </label>
                            </div>
                            @if($notice->is_published && $notice->published_at)
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                {{ __('Published on') }}: {{ $notice->published_at->format('Y-m-d H:i:s') }}
                            </span>
                            @else
                            <span class="text-sm text-red-600 dark:text-red-400">
                                {{ __('Not Published') }}
                            </span>
                            @endif
                        </div>
                        <p class="mt-1 text-xs">
                            {{ __('체크박스를 선택하면 공지사항이 발행됩니다.') }}
                        </p>
                    </div>

                    <div id="published_at_field">
                        <x-label for="published_at" :value="__('Published At')" />
                        <x-input id="published_at" name="published_at" type="datetime-local" class="mt-1 block w-full"
                            :value="old('published_at', $notice->published_at ? $notice->published_at->format('Y-m-d\TH:i') : '')" />
                        <x-input-error-message class="mt-2" :messages="$errors->get('published_at')" />
                    </div>
                </div>

                <!-- Attachments Section -->
                <div>
                    <x-input-label :value="__('Attachments')" />
                    
                    {{-- 기존 첨부파일 목록 --}}
                    @if($notice->attachments->count() > 0)
                    <div class="mt-2 mb-4 space-y-2">
                        @foreach($notice->attachments as $attachment)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                            <div class="flex items-center space-x-3 flex-1 min-w-0">
                                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <div class="flex-1 min-w-0">
                                    <a href="{{ route('admin.notices.attachments.download', $attachment) }}" target="_blank" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline truncate block">
                                        {{ $attachment->original_name }}
                                    </a>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ $attachment->formatted_size }} • {{ $attachment->created_at->format('Y-m-d H:i') }}
                                    </p>
                                </div>
                            </div>
                            <form action="{{ route('admin.notices.attachments.delete', $attachment) }}" method="POST" class="inline" onsubmit="return confirm('정말 삭제하시겠습니까?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="ml-2 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    {{-- 새 파일 업로드 --}}
                    <div class="mt-2">
                        <input type="file" name="attachments[]" multiple
                            class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-gray-700 dark:file:text-gray-300">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">최대 10MB까지 업로드 가능합니다. 여러 파일을 선택할 수 있습니다.</p>
                        <x-input-error-message class="mt-2" :messages="$errors->get('attachments.*')" />
                    </div>
                </div>

                <div class="flex items-center justify-end mt-6">
                    <x-primary-button>
                        {{ __('Update Notice') }}
                    </x-primary-button>
                </div>
            </form>

            <div class="mt-6">
                @can('delete', $notice)
                <form action="{{ route('admin.notices.destroy', $notice) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                        onclick="return confirm('정말 삭제하시겠습니까?')">{{ __('Delete') }}</button>
                </form>
                @endcan
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const isPublishedCheckbox = document.getElementById('is_published');
            const publishedAtField = document.getElementById('published_at_field');

            function togglePublishedAtField() {
                if (isPublishedCheckbox.checked) {
                    publishedAtField.style.display = 'block';
                    // 발행일시가 없으면 현재 날짜/시간을 기본값으로 설정
                    const publishedAtInput = document.getElementById('published_at');
                    if (!publishedAtInput.value) {
                        const now = new Date();
                        const year = now.getFullYear();
                        const month = String(now.getMonth() + 1).padStart(2, '0');
                        const day = String(now.getDate()).padStart(2, '0');
                        const hours = String(now.getHours()).padStart(2, '0');
                        const minutes = String(now.getMinutes()).padStart(2, '0');
                        publishedAtInput.value = `${year}-${month}-${day}T${hours}:${minutes}`;
                    }
                } else {
                    publishedAtField.style.display = 'none';
                }
            }

            isPublishedCheckbox.addEventListener('change', togglePublishedAtField);
            togglePublishedAtField(); // 초기 상태 설정
        });
    </script>

    @include('admin.notices._toastui-editor')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            window.initToastUiEditorForTextarea?.({
                editorElementId: 'notice-editor',
                textareaId: 'content',
                formSelector: 'form',
                height: '600px',
                initialEditType: 'wysiwyg',
                imageUploadUrl: '{{ route('admin.notices.upload-image') }}',
            });
        });
    </script>
</x-layouts.admin>

