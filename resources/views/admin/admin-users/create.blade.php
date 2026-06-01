<x-laravel-admin::layouts.admin title="관리자 계정 등록">
    <x-slot name="header">
        <x-laravel-admin::admin-header>
            <x-slot name="navigation">
                <a href="{{ route('home') }}">HOME</a>
                - <a href="{{ route('admin.index') }}">관리자 홈</a>
                - <a href="{{ route('admin.admin-users.index') }}">관리자 계정</a>
            </x-slot>
            <x-slot name="description">
                {{ __('Create Admin User') }}
            </x-slot>
        </x-laravel-admin::admin-header>
    </x-slot>

    <div class="w-full md:max-w-5xl mx-auto">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-medium">{{ __('Admin User Information') }}</h3>
                    <p class="mt-1 text-sm">{{ __('새로운 관리자 계정을 등록합니다.') }}</p>
                </div>
                <a href="{{ route('admin.admin-users.index') }}" class="text-blue-600 hover:text-blue-800">← {{ __('Back') }}</a>
            </div>

            <form action="{{ route('admin.admin-users.store') }}" method="POST" class="space-y-6">
                @csrf

                @include('admin.admin-users.partials.form', [
                    'adminUser' => null,
                    'roles' => $roles,
                    'submitLabel' => __('Create Admin User'),
                ])
            </form>
        </div>
    </div>
</x-laravel-admin::layouts.admin>
