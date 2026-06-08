<x-laravel-admin::admin.layouts.admin title="관리자 계정 등록">
    <x-slot name="header">
        <x-laravel-admin::admin.admin-header>
            <x-slot name="navigation">
                <a href="{{ route('home') }}">HOME</a>
                - <a href="{{ route('admin.index') }}">관리자 홈</a>
                - <a href="{{ route('admin.admin-users.index') }}">관리자 계정</a>
            </x-slot>
            <x-slot name="description">
                {{ __('Create Admin User') }}
            </x-slot>
        </x-laravel-admin::admin.admin-header>
    </x-slot>

    <div class="mx-auto w-full max-w-5xl bg-white px-2 py-2 dark:bg-gray-900 dark:border-gray-700">
        <div class="min-h-[450px] bg-white px-6 py-8 sm:px-12 lg:px-16 dark:bg-gray-800 dark:border-gray-700">
            <div class="mb-8">
                <h1 class="text-[22px] font-bold leading-none text-[#222222] dark:text-gray-100">{{ __('Admin User Information') }}</h1>
            </div>

            <form action="{{ route('admin.admin-users.store') }}" method="POST">
                @csrf

                @include('admin.admin-users.partials.form', [
                    'adminUser' => null,
                    'roles' => $roles,
                    'submitLabel' => __('등록하기'),
                ])
            </form>
        </div>
    </div>
</x-laravel-admin::admin.layouts.admin>
