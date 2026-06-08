<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ __('Admin Login') }} - {{ config('app.name', 'Laravel') }}</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <script>
        (function() {
            const theme = window.localStorage?.getItem('theme') || null;
            const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            const isDark = theme === 'dark' || theme === 'system' && prefersDark || (!theme && prefersDark);

            document.documentElement.classList.toggle('dark', isDark);
        })();
    </script>

    @include('laravel-admin::partials.assets')
</head>
<body class="min-h-screen bg-[#E7E7D6] font-sans antialiased text-[#222222] dark:bg-gray-900 dark:text-gray-100">
    @php
        $loginRoute = $loginRoute ?? config('laravel-admin.route_name_prefix', 'admin.').'login';
    @endphp

    <main class="flex min-h-screen items-center justify-center px-4 py-10">
        <section class="w-full max-w-[420px] border border-[#d8d8d0] bg-white p-2 dark:border-gray-700 dark:bg-gray-900">
            <div class="border border-[#d9d9d9] bg-white px-6 py-8 dark:border-gray-700 dark:bg-gray-800">
                <div class="mb-8 text-center">
                    <div class="mb-4 flex justify-center">
                        <x-laravel-admin::admin.site-logo size="w-16 h-16" class="dark:text-purple-200" />
                    </div>
                    <h1 class="text-[26px] font-bold leading-none">
                        {{ __('관리자 로그인') }}
                    </h1>
                </div>

                @if ($errors->any())
                    <div class="mb-4 border border-red-300 bg-red-50 px-3 py-2 text-[13px] text-red-700 dark:border-red-700 dark:bg-red-950/40 dark:text-red-200">
                        <ul class="list-inside list-disc space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @session('status')
                    <div class="mb-4 border border-green-300 bg-green-50 px-3 py-2 text-[13px] text-green-700 dark:border-green-700 dark:bg-green-950/40 dark:text-green-200">
                        {{ $value }}
                    </div>
                @endsession

                <form method="POST" action="{{ route($loginRoute) }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="email" class="mb-1 block text-[14px] font-semibold">
                            {{ __('Email') }}
                        </label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="username"
                            class="admin-focus-border h-[34px] w-full rounded-sm border border-[#7d7d7d] bg-white px-2 text-[14px] text-[#111111] outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        >
                    </div>

                    <div>
                        <label for="password" class="mb-1 block text-[14px] font-semibold">
                            {{ __('Password') }}
                        </label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            class="admin-focus-border h-[34px] w-full rounded-sm border border-[#7d7d7d] bg-white px-2 text-[14px] text-[#111111] outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        >
                    </div>

                    <label for="remember_me" class="flex items-center gap-2 text-[14px] text-gray-700 dark:text-gray-300">
                        <input
                            id="remember_me"
                            type="checkbox"
                            name="remember"
                            class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                        >
                        <span>{{ __('Remember me') }}</span>
                    </label>

                    <button type="submit" class="h-[34px] w-full cursor-pointer rounded-sm border border-[#7d7d7d] bg-[#eeeeee] px-4 text-[14px] font-semibold text-[#222222] hover:bg-[#e3e3e3] dark:bg-gray-700 dark:text-gray-100">
                        {{ __('Log in') }}
                    </button>
                </form>
            </div>
        </section>
    </main>
</body>
</html>
