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

    <script>
        (function() {
            const theme = localStorage.getItem('theme');
            if (theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    @vite(config('laravel-admin.vite', ['resources/vendor/laravel-admin/admin.css', 'resources/vendor/laravel-admin/admin.js']))
    <link href="https://cdn.jsdelivr.net/npm/jarallax@2/dist/jarallax.css" rel="stylesheet" />

    @stack('head')

    @livewireStyles
</head>

<body class="h-dvh bg-gray-50 text-gray-900">

    {{-- 전체 화면 컨테이너: 모바일 주소창 변화 대응 --}}
    <div class="dvh-full flex flex-col overflow-hidden">
            {{ $slot }}
    </div>

    @stack('scripts')

    @livewireScripts
</body>
</html>
