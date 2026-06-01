<x-laravel-admin::layouts.admin title="메뉴 카테고리 등록">
    <x-slot name="header">
        <x-laravel-admin::admin-header>
            <x-slot name="navigation">
                <a href="{{ route('home') }}">HOME</a>
                - <a href="{{ route('admin.index') }}">관리자 홈</a>
            </x-slot>
            <x-slot name="description">
                {{ __('Create New Menu Category') }}
            </x-slot>
        </x-laravel-admin::admin-header>
    </x-slot>

    <div class="w-full lg:max-w-7xl mx-auto">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl px-6 py-2">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-medium dark:text-white">
                        {{ __('Menu Category Information') }}
                    </h3>
                    <p class="mt-1 text-sm dark:text-gray-400">
                        {{ __('새로운 메뉴 카테고리를 등록합니다.') }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('admin.menu-categories.index') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                        ← {{ __('Back to Menu Categories') }}
                    </a>
                </div>
            </div>

            <form action="{{ route('admin.menu-categories.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Category Name Field -->
                    <div>
                        <x-laravel-admin::label for="name" :value="__('Category Name')" />
                        <x-laravel-admin::input id="name" name="name" type="text" class="mt-1 block w-full"
                                     :value="old('name')" placeholder="Enter category name" autocomplete="name" required />
                        <x-laravel-admin::input-error-message class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <!-- Sort Order Field -->
                    <div>
                        <x-laravel-admin::label for="sort_order" :value="__('Sort Order')" />
                        <x-laravel-admin::input id="sort_order" name="sort_order" type="number" class="mt-1 block w-full"
                                     :value="old('sort_order', 0)" min="0" />
                        <x-laravel-admin::input-error-message class="mt-2" :messages="$errors->get('sort_order')" />
                    </div>
                </div>

                <x-laravel-admin::section-border />

                <!-- Additional Settings -->
                <div class="space-y-6">
                    <!-- Active Status -->
                    <div>
                        <label class="flex items-center">
                            <x-laravel-admin::checkbox name="is_active" value="1" :checked="old('is_active', true)" />
                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ __('활성화') }}</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-end mt-6">
                    <x-laravel-admin::primary-button>
                        {{ __('Create Category') }}
                    </x-laravel-admin::primary-button>
                </div>
            </form>
        </div>
    </div>
</x-laravel-admin::layouts.admin>