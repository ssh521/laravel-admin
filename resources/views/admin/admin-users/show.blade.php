<x-laravel-admin::layouts.admin title="관리자 계정 정보">
    <x-slot name="header">
        <x-laravel-admin::admin-header>
            <x-slot name="navigation">
                <a href="{{ route('home') }}">HOME</a>
                - <a href="{{ route('admin.index') }}">관리자 홈</a>
                - <a href="{{ route('admin.admin-users.index') }}">관리자 계정</a>
            </x-slot>
            <x-slot name="description">
                {{ __('Admin User Information') }}
            </x-slot>
        </x-laravel-admin::admin-header>
    </x-slot>

    <div class="w-full md:max-w-5xl mx-auto">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-medium">{{ $adminUser->name }}</h3>
                    <p class="mt-1 text-sm">{{ $adminUser->email }}</p>
                </div>
                <a href="{{ route('admin.admin-users.edit', $adminUser->getKey()) }}" class="text-blue-600 hover:text-blue-800">{{ __('Edit') }}</a>
            </div>

            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-laravel-admin::input-label :value="__('Name')" />
                        <div class="mt-2 font-bold">{{ $adminUser->name }}</div>
                    </div>
                    <div>
                        <x-laravel-admin::input-label :value="__('Email')" />
                        <div class="mt-2 font-bold">{{ $adminUser->email }}</div>
                    </div>
                </div>

                <x-laravel-admin::section-border />

                <div>
                    <h4 class="text-md font-medium mb-4">{{ __('Roles') }}</h4>
                    <div class="flex flex-wrap gap-2">
                        @forelse ($adminUser->getRoleNames() as $role)
                            <span class="inline-flex items-center rounded-full bg-gray-100 dark:bg-gray-700 px-3 py-1 text-sm">{{ $role }}</span>
                        @empty
                            <span class="text-sm text-gray-500">{{ __('No roles assigned') }}</span>
                        @endforelse
                    </div>
                </div>

                <x-laravel-admin::section-border />

                <div>
                    <x-laravel-admin::input-label :value="__('Email Verification Status')" />
                    <div class="mt-2">
                        @if ($adminUser->email_verified_at)
                            <span class="text-green-600 dark:text-green-400">{{ __('Verified') }}: {{ $adminUser->email_verified_at->format('Y-m-d H:i:s') }}</span>
                        @else
                            <span class="text-red-600 dark:text-red-400">{{ __('Not Verified') }}</span>
                        @endif
                    </div>
                </div>

                <div class="flex justify-end">
                    <form action="{{ route('admin.admin-users.destroy', $adminUser->getKey()) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this admin user?') }}');">
                        @csrf
                        @method('DELETE')
                        <x-laravel-admin::danger-button type="submit">
                            {{ __('Delete Admin User') }}
                        </x-laravel-admin::danger-button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-laravel-admin::layouts.admin>
