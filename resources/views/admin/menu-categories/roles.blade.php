<x-laravel-admin::admin.layouts.admin title="메뉴 카테고리 역할 관리">
    <x-slot name="header">
        <x-laravel-admin::admin.admin-header>
            <x-slot name="navigation">
                <a href="{{ route('home') }}">HOME</a>
                - <a href="{{ route('admin.index') }}">관리자 홈</a>
                - <a href="{{ route('admin.menu-categories.index') }}">메뉴 카테고리 관리</a>
            </x-slot>
            <x-slot name="description">
                {{ __('Menu Category Role Management') }}
            </x-slot>
        </x-laravel-admin::admin.admin-header>
    </x-slot>

    <div class="w-full lg:max-w-5xl mx-auto">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl px-5 py-2">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-medium dark:text-white">
                        {{ __('Role Management') }} - [{{ $menuCategory->name }}]
                    </h3>
                    <p class="mt-1 text-sm dark:text-gray-400">
                        {{ __('메뉴 카테고리에 허용할 역할을 선택합니다.') }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('admin.menu-categories.show', $menuCategory) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                        ← {{ __('Back to Category') }}
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="p-4 mb-6 text-sm text-green-800 dark:text-green-400 rounded-lg bg-green-50 dark:bg-green-900/20"
                    role="alert">
                    <span class="font-medium">성공</span> {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('admin.menu-categories.roles.update', $menuCategory) }}" method="POST" class="space-y-6">
                @csrf

                <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-6">
                    <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">{{ __('Select Allowed Roles') }}</h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($roles as $role)
                            <label class="flex items-center">
                                <x-laravel-admin::admin.checkbox name="roles[]" value="{{ $role->id }}" :checked="in_array($role->id, $allowedRoleIds)" />
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $role->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center justify-end mt-6">
                    <x-laravel-admin::admin.primary-button>
                        {{ __('Save Changes') }}
                    </x-laravel-admin::admin.primary-button>
                </div>
            </form>
        </div>
    </div>
</x-laravel-admin::admin.layouts.admin>