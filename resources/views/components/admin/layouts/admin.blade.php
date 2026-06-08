@props(['title' => 'Page Title'])

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ? $title . ' - ' : '' }}{{ config('app.name', 'Laravel') }}</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">

    <!-- Dark mode FOUC prevention -->
    <script>
        (function() {
            const applyTheme = () => {
                let theme = null;

                try {
                    theme = window.localStorage?.getItem('theme') || null;
                } catch (error) {
                }

                const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                const isDark = theme === 'dark' || theme === 'system' && prefersDark || (!theme && prefersDark);

                document.documentElement.classList.toggle('dark', isDark);
            };

            applyTheme();
            window.matchMedia?.('(prefers-color-scheme: dark)').addEventListener('change', applyTheme);
            document.addEventListener('livewire:navigated', applyTheme);
        })();
    </script>

    <!-- Fonts -->
    <!-- <link rel="preconnect" href="https://fonts.bunny.net"> -->
    <!-- <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" /> -->

    <!-- Scripts -->
    @include('laravel-admin::partials.assets')

    <!-- Styles -->
    @livewireStyles
</head>
<body class="font-sans antialiased bg-[#E7E7D6] dark:bg-gray-900 h-full min-h-screen">
    <x-laravel-admin::admin.banner />

    <div x-data="{
            isMobileMenuOpen: false,
            isDesktop: localStorage.getItem('adminSidebarOpen') === 'true' || false
        }" x-init="
            $watch('isDesktop', value => localStorage.setItem('adminSidebarOpen', value));

            // 단축키 이벤트 리스너 추가
            document.addEventListener('keydown', function(e) {
                // Ctrl/Cmd + B: 사이드바 토글
                if ((e.ctrlKey || e.metaKey) && e.key === 'b') {
                    e.preventDefault();
                    isDesktop = !isDesktop;
                }
                // ESC: 모바일 메뉴 닫기
                if (e.key === 'Escape' && open) {
                    open = false;
                }
            });
        ">

        <livewire:admin.left-menu-mobile />
        <livewire:admin.header-nav />

        <div class="min-h-screen flex pt-16">

            <!-- 왼쪽 사이드바 -->
            <div x-show="isDesktop" class="hidden lg:block w-64 shadow-lg border-r border-gray-200 dark:border-gray-700"
                x-data="sidebarBackground" :style="backgroundStyle">
                <!-- 관리자 메뉴 네비게이션 -->
                <nav class="h-full overflow-y-auto">
                    <livewire:admin.left-menu />
                </nav>
            </div>

            <!-- 메인 콘텐츠 영역 -->
            <div class="flex-1 flex flex-col mt-2 mx-0 sm:mx-2 md:mx-4 lg:mx-6">

                <!-- Page Heading -->
                @if (isset($header))
                <header class="px-2 sm:px-0">
                    <div class="mb-3">
                        {{ $header }}
                    </div>
                    <hr class="border-[#663601] dark:border-gray-700 mb-4">
                </header>
                @else
                @endif


                <!-- Page Content -->
                <main class="border-[0px] p-[0px] border-gray-200 dark:border-gray-700 mb-2">
                    <div id="page-content" class="border-[0px] border-gray-200 dark:border-gray-700">
                        {{ $slot }}
                    </div>
                </main>

            </div>
        </div>
    </div>

    <x-laravel-admin::admin.client-notification />

    @livewireScripts
</body>
</html>
