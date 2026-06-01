<x-layouts.app title="대시보드">

    <x-slot name="header">
        {{ __('Dashboard') }}
    </x-slot>

    <div class="space-y-6">
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                {{ __('Welcome back') }}, {{ $user->name }}!
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ $user->email }}
            </p>
        </div>

        @can('viewDashboard', 'admin')
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">관리자</h3>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">관리자 페이지로 이동합니다.</p>
            <a
                href="{{ route('admin.index') }}"
                class="mt-4 inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
            >
                관리자 홈
            </a>
        </div>
        @endcan

    </div>

</x-layouts.app>
