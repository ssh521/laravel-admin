<x-laravel-admin::layouts.admin title="관리자 계정 관리">
    <x-slot name="header">
        <x-laravel-admin::admin-header>
            <x-slot name="navigation">
                <a href="{{ route('home') }}">HOME</a>
                - <a href="{{ route('admin.index') }}">관리자 홈</a>
            </x-slot>
            <x-slot name="description">
                {{ __('Admin User List') }}
            </x-slot>
        </x-laravel-admin::admin-header>
    </x-slot>

    <div class="w-full mx-auto">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl p-6">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-6">
                <div>
                    <h3 class="text-lg font-medium">{{ __('Admin Users') }}</h3>
                    <p class="mt-1 text-sm">{{ __('패키지 전용 관리자 계정을 관리합니다.') }}</p>
                </div>
                <a href="{{ route('admin.admin-users.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest text-white dark:text-gray-800 hover:bg-gray-700 dark:hover:bg-gray-100">
                    {{ __('Create Admin User') }}
                </a>
            </div>

            <x-laravel-admin::session-messages />

            <form method="GET" action="{{ route('admin.admin-users.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-6">
                <x-laravel-admin::input name="search" type="text" :value="request('search')" placeholder="Search name or email" class="md:col-span-2" />
                <select name="role" class="border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 rounded-md shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">{{ __('All Roles') }}</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->name }}" @selected(request('role') === $role->name)>{{ $role->name }}</option>
                    @endforeach
                </select>
                <x-laravel-admin::primary-button>
                    {{ __('Search') }}
                </x-laravel-admin::primary-button>
            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase">{{ __('Name') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase">{{ __('Email') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase">{{ __('Roles') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase">{{ __('Verified') }}</th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($adminUsers as $adminUser)
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap font-medium">{{ $adminUser->name }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">{{ $adminUser->email }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse ($adminUser->getRoleNames() as $role)
                                            <span class="inline-flex items-center rounded-full bg-gray-100 dark:bg-gray-700 px-2 py-1 text-xs">{{ $role }}</span>
                                        @empty
                                            <span class="text-sm text-gray-500">{{ __('No roles') }}</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if ($adminUser->email_verified_at)
                                        <span class="text-green-600 dark:text-green-400">{{ __('Verified') }}</span>
                                    @else
                                        <span class="text-red-600 dark:text-red-400">{{ __('Not Verified') }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right">
                                    <a href="{{ route('admin.admin-users.show', $adminUser->getKey()) }}" class="text-blue-600 hover:text-blue-800">{{ __('View') }}</a>
                                    <span class="mx-1 text-gray-300">|</span>
                                    <a href="{{ route('admin.admin-users.edit', $adminUser->getKey()) }}" class="text-blue-600 hover:text-blue-800">{{ __('Edit') }}</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">{{ __('No admin users found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $adminUsers->withQueryString()->links() }}
            </div>
        </div>
    </div>
</x-laravel-admin::layouts.admin>
