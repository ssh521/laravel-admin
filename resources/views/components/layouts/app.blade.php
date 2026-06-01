@props(['title' => ''])

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title && $title !== 'Page Title' ? $title . ' - ' : '' }}{{ config('app.name', 'Laravel') }}</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Dark mode FOUC prevention -->
    <script>
        (function() {
            const applyTheme = () => {
                const theme = localStorage.getItem('theme');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                const isDark = theme === 'dark' || (!theme && prefersDark);

                document.documentElement.classList.toggle('dark', isDark);
            };

            applyTheme();
            document.addEventListener('livewire:navigated', applyTheme);
        })();
    </script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')

    <!-- Styles -->
    @livewireStyles
</head>
<body class="font-sans antialiased bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 h-full">

    <x-laravel-admin::banner />

    <!-- Layout Start -->
    <div x-data="{
            sidebarOpen: false,
            _desktopSidebarCollapsed: (() => {
                const collapsed = localStorage.getItem('DesktopSidebarCollapsed');
                if (collapsed !== null) {
                    return collapsed === 'true';
                }

                const legacyCollapsed = localStorage.getItem('fluxDesktopSidebarCollapsed');
                if (legacyCollapsed !== null) {
                    localStorage.setItem('DesktopSidebarCollapsed', legacyCollapsed);
                    localStorage.removeItem('fluxDesktopSidebarCollapsed');

                    return legacyCollapsed === 'true';
                }

                const open = localStorage.getItem('DesktopSidebarOpen');
                if (open !== null) {
                    return open === 'false';
                }

                const legacyOpen = localStorage.getItem('fluxDesktopSidebarOpen');
                if (legacyOpen !== null) {
                    localStorage.setItem('DesktopSidebarOpen', legacyOpen);
                    localStorage.removeItem('fluxDesktopSidebarOpen');

                    return legacyOpen === 'false';
                }

                return false;
            })(),
            get desktopSidebarCollapsed() {
                return this._desktopSidebarCollapsed && window.matchMedia('(min-width: 1024px)').matches;
            },
            set desktopSidebarCollapsed(value) {
                this._desktopSidebarCollapsed = value;
            },
        }" x-init="$watch('_desktopSidebarCollapsed', value => {
            localStorage.setItem('DesktopSidebarCollapsed', value);
            localStorage.setItem('DesktopSidebarOpen', (! value).toString());
        })" class="h-full flex flex-col min-h-screen md:h-dvh overflow-hidden">

        <div class="shrink-0">
            @include('partials.header')
        </div>

        <!-- Content 시작-->
        <!-- 좌우로 배치됨 -->
        <div class="flex flex-1 min-h-0 overflow-hidden">

            {{-- Mobile sidebar backdrop --}}
            <div x-cloak x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-50 bg-black/30 lg:hidden"
                x-on:click="sidebarOpen = false" style="display: none;"></div>


            {{-- Desktop Sidebar --}}
            <aside
                class="relative z-50 hidden h-full lg:flex lg:flex-col overflow-x-visible transition-[width,box-shadow] duration-200 ease-out"
                x-bind:class="desktopSidebarCollapsed ? 'lg:w-16 lg:shadow-none' : 'lg:w-64 lg:shadow-sm lg:shadow-gray-200/60 dark:lg:shadow-gray-950/30'">
                @include('partials.sidebar')
            </aside>

            {{-- Mobile Drawer Sidebar --}}
            <aside
                class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-950 border-r border-gray-200 dark:border-gray-800 lg:hidden dvh-screen"
                x-cloak x-show="sidebarOpen" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="-translate-x-6 opacity-0" x-transition:enter-end="translate-x-0 opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="translate-x-0 opacity-100" x-transition:leave-end="-translate-x-6 opacity-0"
                style="display: none;">
                <div class="h-full">
                    @include('partials.sidebar')
                </div>
            </aside>


            <!-- Main 유일한 스크롤 영역 -->
            <main
                class="relative flex-1 min-h-0 overflow-auto mt-2 lg:mt-0 scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100 dark:scrollbar-thumb-gray-600 dark:scrollbar-track-gray-800">

                <header class="bg-white dark:bg-gray-900 sticky top-0 z-10 pb-4">
                    <div class="flex items-center gap-3 px-4">

                        <div class="flex items-center gap-2">

                            {{-- sidebarOpen 스코프가 정의되어 있지 않으면 숨김 --}}
                            <button type="button" class="lg:hidden block"
                                x-bind:class="typeof sidebarOpen === 'undefined' ? 'hidden' : ''"
                                x-on:click="sidebarOpen = true; desktopSidebarCollapsed = false"
                                aria-label="Toggle Sidebar">
                                <svg viewBox="0 0 24 24" aria-hidden="true" class="h-5 w-5" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <rect x="3" y="3" width="18" height="18" rx="2" />
                                    <path d="M9 3v18" />
                                </svg>
                            </button>

                            <div class="text-xs lg:text-xs">
                                <a href="{{ route('home') }}">HOME</a> >
                                @if (isset($header))
                                {{ $header }}
                                @else
                                {{ $title }}
                                @endif
                            </div>

                        </div>

                    </div>
                </header>

                <div class="p-4 pt-0">
                    {{ $slot }}
                </div>


                <x-laravel-admin::footer />
            </main>
            <!-- Main 끝 -->


        <!-- Content 끝-->

    </div>

    @stack('scripts')
    @livewireScripts
</body>
</html>