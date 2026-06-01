<x-layouts.admin title="공지사항 등록">

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
                {{ __('Create New Notice') }}
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
                        {{ __('새로운 공지사항을 등록합니다.') }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('admin.notices.index') }}" class="text-blue-600 hover:text-blue-800">
                        ← {{ __('Back') }}
                    </a>
                </div>
            </div>

            <form action="{{ route('admin.notices.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 gap-6">
                    <!-- Title Field -->
                    <div>
                        <x-input id="title" name="title" type="text" class="mt-1 block w-full lg:max-w-3xl" :value="old('title')"
                            placeholder="제목을 입력하세요" autocomplete="off" />
                        <x-input-error-message class="mt-2" :messages="$errors->get('title')" />
                    </div>
                </div>

                <!-- Content Field -->
                <div>
                    <textarea id="content" name="content" rows="15"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white"
                        placeholder="내용을 입력하세요">{{ old('content') }}</textarea>
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
                                    old('is_published')=='1' ? 'checked' : '' }}
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <label for="is_published" class="ms-2 text-sm font-medium">
                                    {{ __('Publish Notice') }}
                                </label>
                            </div>
                        </div>
                        <p class="mt-1 text-xs">
                            {{ __('체크박스를 선택하면 공지사항이 발행됩니다.') }}
                        </p>
                    </div>

                    <div id="published_at_field" style="display: none;">
                        <x-label for="published_at" :value="__('Published At')" />
                        <x-input id="published_at" name="published_at" type="datetime-local" class="mt-1 block w-full"
                            :value="old('published_at')" />
                        <x-input-error-message class="mt-2" :messages="$errors->get('published_at')" />
                    </div>
                </div>

                <!-- Attachments Section -->
                <div>
                    <x-input-label :value="__('Attachments')" />
                    <div class="mt-2">
                        <input type="file" name="attachments[]" multiple
                            class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-gray-700 dark:file:text-gray-300">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">최대 10MB까지 업로드 가능합니다. 여러 파일을 선택할 수 있습니다.</p>
                        <x-input-error-message class="mt-2" :messages="$errors->get('attachments.*')" />
                    </div>
                </div>

                <div class="flex items-center justify-end">
                    <x-primary-button>
                        {{ __('Create Notice') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const isPublishedCheckbox = document.getElementById('is_published');
            const publishedAtField = document.getElementById('published_at_field');

            function togglePublishedAtField() {
                if (isPublishedCheckbox.checked) {
                    publishedAtField.style.display = 'block';
                    // 현재 날짜/시간을 기본값으로 설정
                    if (!document.getElementById('published_at').value) {
                        const now = new Date();
                        const year = now.getFullYear();
                        const month = String(now.getMonth() + 1).padStart(2, '0');
                        const day = String(now.getDate()).padStart(2, '0');
                        const hours = String(now.getHours()).padStart(2, '0');
                        const minutes = String(now.getMinutes()).padStart(2, '0');
                        document.getElementById('published_at').value = `${year}-${month}-${day}T${hours}:${minutes}`;
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

