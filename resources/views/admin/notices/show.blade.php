<x-layouts.admin title="공지사항 정보">

    <x-slot name="header">

        <x-admin-header>
            <x-slot name="navigation">
                <a href="{{ route('home') }}">HOME</a>
                - <span class="cursor-pointer" @click="open = !open">관리자메뉴</span>
                @can('viewAny', Ssh521\LaravelAdmin\Models\AdminNotice::class)
                - <a href="{{ route('admin.notices.index') }}">공지사항 리스트</a>
                @endcan

            </x-slot>
            <x-slot name="description">
                {{ __('Notice Information') }}
            </x-slot>
        </x-admin-header>

    </x-slot>

    <div class="w-full md:max-w-5xl mx-auto">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl p-6">
            <div class="p-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">

                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-medium">
                            {{ __('Notice Information') }}
                        </h3>
                        <p class="mt-1 text-sm">
                            {{ __('공지사항 정보확인') }}
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('admin.notices.index') }}"
                            class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                            ← {{ __('Back') }}
                        </a>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Title Field -->
                        <div>
                            <x-input-label :value="__('Title')" />
                            <div class="mt-2">
                                <div class="font-bold text-lg ml-1 mt-2">{{ $notice->title }}</div>
                            </div>
                        </div>

                        <!-- Author Field -->
                        <div>
                            <x-input-label :value="__('Author')" />
                            <div class="mt-2">
                                <div class="font-bold text-lg ml-1 mt-2">{{ $notice->author->name ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>

                    <x-section-border />

                    <!-- Content Field -->
                    <div>
                        <x-input-label :value="__('Content')" />
                        <div class="mt-2">
                            <div class="whitespace-pre-wrap text-gray-900 dark:text-white p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                                {{ $notice->content }}
                            </div>
                        </div>
                    </div>

                    <x-section-border />

                    <!-- Attachments -->
                    @if($notice->attachments->count() > 0)
                    <div>
                        <x-input-label :value="__('Attachments')" />
                        <div class="mt-2 space-y-2">
                            @foreach($notice->attachments as $attachment)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                                <div class="flex items-center space-x-3 flex-1 min-w-0">
                                    <svg class="w-6 h-6 text-gray-500 dark:text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <x-section-border />
                    @endif

                    <!-- Publication Status -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label :value="__('Publication Status')" />
                            <div class="mt-2">
                                @if($notice->is_published)
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                    {{ __('Published') }}
                                </span>
                                @else
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                                    {{ __('Not Published') }}
                                </span>
                                @endif
                            </div>
                        </div>

                        @if($notice->published_at)
                        <div>
                            <x-input-label :value="__('Published At')" />
                            <div class="mt-2">
                                <div class="font-bold text-lg ml-1 mt-2">
                                    {{ $notice->published_at->format('Y-m-d H:i:s') }}
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <x-section-border />

                    <!-- Dates -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label :value="__('Created At')" />
                            <div class="mt-2">
                                <div class="text-sm text-gray-600 dark:text-gray-400 ml-1 mt-2">
                                    {{ $notice->created_at->format('Y-m-d H:i:s') }}
                                </div>
                            </div>
                        </div>

                        <div>
                            <x-input-label :value="__('Updated At')" />
                            <div class="mt-2">
                                <div class="text-sm text-gray-600 dark:text-gray-400 ml-1 mt-2">
                                    {{ $notice->updated_at->format('Y-m-d H:i:s') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <x-section-border />

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-4 py-4">
                        @can('update', $notice)
                        <a href="{{ route('admin.notices.edit', $notice) }}"
                            class="px-4 py-2 bg-blue-500 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-800 rounded-md text-white font-medium transition-colors">
                            {{ __('Edit') }}
                        </a>
                        @endcan

                        @can('delete', $notice)
                        <form action="{{ route('admin.notices.destroy', $notice) }}" method="POST"
                            onsubmit="return confirm('{{ __('Are you sure you want to delete this notice?') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="px-4 py-2 bg-red-500 hover:bg-red-700 dark:bg-red-600 dark:hover:bg-red-800 rounded-md text-white font-medium transition-colors">
                                {{ __('Delete') }}
                            </button>
                        </form>
                        @endcan
                    </div>

                </div>
            </div>
        </div>
    </div>

</x-layouts.admin>

