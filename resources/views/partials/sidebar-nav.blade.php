{{-- Nav --}}
<nav class="flex-1 min-h-0 overflow-y-auto overflow-x-visible scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100 dark:scrollbar-thumb-gray-600 dark:scrollbar-track-gray-800 px-4">

    <div class="space-y-1 py-4 sm:py-0">
        {{-- 메뉴 --}}
        <div class="space-y-1">


            <div class="space-y-1">

                <a
                    href="{{ route('dashboard') }}"
                    wire:navigate
                    title="{{ __('Dashboard') }}"
                    class="flex items-center gap-2 rounded-md border px-2.5 py-1.5 text-xs font-medium transition-colors
                        {{ request()->routeIs('dashboard')
                            ? 'border-gray-200 bg-transparent text-gray-900 dark:border-gray-700 dark:bg-transparent dark:text-white'
                            : 'border-transparent text-gray-700 hover:border-gray-200 hover:bg-gray-50 dark:text-gray-300 dark:hover:border-gray-700 dark:hover:bg-gray-900/40' }}"
                >
                    <span class="inline-flex h-4 w-4 shrink-0 items-center justify-center text-gray-600 dark:text-gray-300">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                            <path d="M3 10.5 12 3l9 7.5"></path>
                            <path d="M5 9.5V20a1 1 0 0 0 1 1h4v-7h4v7h4a1 1 0 0 0 1-1V9.5"></path>
                        </svg>
                    </span>
                    <span>{{ __('Dashboard') }}</span>
                </a>

                @if (Route::has('test.samplepage'))
                <a
                    href="{{ route('test.samplepage') }}"
                    wire:navigate
                    title="{{ __('Sample Page') }}"
                    class="flex items-center gap-2 rounded-md border px-2.5 py-1.5 text-xs font-medium transition-colors
                        {{ request()->routeIs('test.samplepage')
                            ? 'border-gray-200 bg-transparent text-gray-900 dark:border-gray-700 dark:bg-transparent dark:text-white'
                            : 'border-transparent text-gray-700 hover:border-gray-200 hover:bg-gray-50 dark:text-gray-300 dark:hover:border-gray-700 dark:hover:bg-gray-900/40' }}"
                >
                    <span class="inline-flex h-4 w-4 shrink-0 items-center justify-center text-gray-600 dark:text-gray-300">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                            <path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                            <path d="M14 3v6h6"></path>
                            <path d="M8 13h8"></path>
                            <path d="M8 17h8"></path>
                        </svg>
                    </span>
                    <span>{{ __('Sample Page') }}</span>
                </a>
                @endif

                @if (Route::has('test.icon-list'))
                <a
                    href="{{ route('test.icon-list') }}"
                    wire:navigate
                    title="{{ __('Icon List') }}"
                    class="flex items-center gap-2 rounded-md border px-2.5 py-1.5 text-xs font-medium transition-colors
                        {{ request()->routeIs('test.icon-list')
                            ? 'border-gray-200 bg-transparent text-gray-900 dark:border-gray-700 dark:bg-transparent dark:text-white'
                            : 'border-transparent text-gray-700 hover:border-gray-200 hover:bg-gray-50 dark:text-gray-300 dark:hover:border-gray-700 dark:hover:bg-gray-900/40' }}"
                >
                    <span class="inline-flex h-4 w-4 shrink-0 items-center justify-center text-gray-600 dark:text-gray-300">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                            <rect x="3" y="3" width="7" height="7" rx="1"></rect>
                            <rect x="14" y="3" width="7" height="7" rx="1"></rect>
                            <rect x="3" y="14" width="7" height="7" rx="1"></rect>
                            <rect x="14" y="14" width="7" height="7" rx="1"></rect>
                        </svg>
                    </span>
                    <span>{{ __('Icon List') }}</span>
                </a>
                @endif

                @if (Route::has('test.components'))
                <a
                    href="{{ route('test.components') }}"
                    wire:navigate
                    title="{{ __('Components') }}"
                    class="flex items-center gap-2 rounded-md border px-2.5 py-1.5 text-xs font-medium transition-colors
                        {{ request()->routeIs('test.components')
                            ? 'border-gray-200 bg-transparent text-gray-900 dark:border-gray-700 dark:bg-transparent dark:text-white'
                            : 'border-transparent text-gray-700 hover:border-gray-200 hover:bg-gray-50 dark:text-gray-300 dark:hover:border-gray-700 dark:hover:bg-gray-900/40' }}"
                >
                    <span class="inline-flex h-4 w-4 shrink-0 items-center justify-center text-gray-600 dark:text-gray-300">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                            <path d="M12 3 2 8l10 5 10-5-10-5z"></path>
                            <path d="m2 12 10 5 10-5"></path>
                            <path d="m2 16 10 5 10-5"></path>
                        </svg>
                    </span>
                    <span>{{ __('Components') }}</span>
                </a>
                @endif

            </div>
        </div>

        {{-- Favorites 1 --}}
        <div class="space-y-1" x-data="{ open: {{ Route::has('dashboard.servers') && request()->routeIs('dashboard.servers') ? 'true' : 'false' }} }">
            <button
                type="button"
                title="Favorites"
                class="flex w-full items-center gap-2 rounded-md border border-transparent px-2.5 py-1.5 text-xs font-medium text-gray-700 transition-colors hover:border-gray-200 hover:bg-gray-50 dark:text-gray-300 dark:hover:border-gray-700 dark:hover:bg-gray-900/40"
                @click="open = !open"
            >
                <span class="inline-flex h-4 w-4 shrink-0 items-center justify-center text-gray-600 dark:text-gray-300">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                    </svg>
                </span>
                <span>Favorites</span>
                <span class="ml-auto">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </span>
            </button>

            <div x-show="open" x-collapse class="relative mt-1 space-y-1 pl-7 before:absolute before:left-4 before:top-1 before:bottom-1 before:w-px before:bg-gray-200/70 dark:before:bg-gray-700/70">
                @if (Route::has('dashboard.servers'))
                    <a
                        href="{{ route('dashboard.servers') }}"
                        wire:navigate
                        class="flex items-center gap-2 rounded-md border px-2.5 py-1.5 text-xs font-medium transition-colors
                            {{ request()->routeIs('dashboard.servers')
                                ? 'border-gray-200 bg-transparent text-gray-900 dark:border-gray-700 dark:bg-transparent dark:text-white'
                                : 'border-transparent text-gray-700 hover:border-gray-200 hover:bg-gray-50 dark:text-gray-300 dark:hover:border-gray-700 dark:hover:bg-gray-900/40' }}"
                    >
                        서버
                    </a>
                @endif
                <a href="https://laravel.yaver.com/" target="_blank" class="flex items-center gap-2 rounded-md border border-transparent px-2.5 py-1.5 text-xs font-medium text-gray-700 transition-colors hover:border-gray-200 hover:bg-gray-50 dark:text-gray-300 dark:hover:border-gray-700 dark:hover:bg-gray-900/40">
                    Laravel Yaver
                </a>
                <a href="#" class="flex items-center gap-2 rounded-md border border-transparent px-2.5 py-1.5 text-xs font-medium text-gray-700 transition-colors hover:border-gray-200 hover:bg-gray-50 dark:text-gray-300 dark:hover:border-gray-700 dark:hover:bg-gray-900/40">
                    Android app
                </a>
                <a href="#" class="flex items-center gap-2 rounded-md border border-transparent px-2.5 py-1.5 text-xs font-medium text-gray-700 transition-colors hover:border-gray-200 hover:bg-gray-50 dark:text-gray-300 dark:hover:border-gray-700 dark:hover:bg-gray-900/40">
                    Brand guidelines
                </a>
            </div>
        </div>

        {{-- Favorites 2 --}}
        <div class="space-y-1" x-data="{ open: false }">
            <button
                type="button"
                title="Favorites"
                class="flex w-full items-center gap-2 rounded-md border border-transparent px-2.5 py-1.5 text-xs font-medium text-gray-700 transition-colors hover:border-gray-200 hover:bg-gray-50 dark:text-gray-300 dark:hover:border-gray-700 dark:hover:bg-gray-900/40"
                @click="open = !open"
            >
                <span class="inline-flex h-4 w-4 shrink-0 items-center justify-center text-gray-600 dark:text-gray-300">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                    </svg>
                </span>
                <span>Favorites</span>
                <span class="ml-auto">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </span>
            </button>

            <div x-show="open" x-collapse class="relative mt-1 space-y-1 pl-7 before:absolute before:left-4 before:top-1 before:bottom-1 before:w-px before:bg-gray-200/70 dark:before:bg-gray-700/70">
                <a href="#" class="flex items-center gap-2 rounded-md border border-transparent px-2.5 py-1.5 text-xs font-medium text-gray-700 transition-colors hover:border-gray-200 hover:bg-gray-50 dark:text-gray-300 dark:hover:border-gray-700 dark:hover:bg-gray-900/40">
                    Marketing site
                </a>
                <a href="#" class="flex items-center gap-2 rounded-md border border-transparent px-2.5 py-1.5 text-xs font-medium text-gray-700 transition-colors hover:border-gray-200 hover:bg-gray-50 dark:text-gray-300 dark:hover:border-gray-700 dark:hover:bg-gray-900/40">
                    Android app
                </a>
                <a href="#" class="flex items-center gap-2 rounded-md border border-transparent px-2.5 py-1.5 text-xs font-medium text-gray-700 transition-colors hover:border-gray-200 hover:bg-gray-50 dark:text-gray-300 dark:hover:border-gray-700 dark:hover:bg-gray-900/40">
                    Brand guidelines
                </a>
            </div>
        </div>
    </div>
</nav>
