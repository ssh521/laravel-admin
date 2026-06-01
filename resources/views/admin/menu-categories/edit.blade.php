<x-layouts.admin title="메뉴 카테고리 수정">
    <x-slot name="header">
        <x-admin-header>
            <x-slot name="navigation">
                <a href="{{ route('home') }}">HOME</a>
                - <a href="{{ route('admin.index') }}">관리자 홈</a>
            </x-slot>
            <x-slot name="description">
                {{ __('Edit Menu Category') }}
            </x-slot>
        </x-admin-header>
    </x-slot>

    <div class="w-full lg:max-w-7xl mx-auto">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl px-6 py-2">
            <x-session-messages />

            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-medium dark:text-white">
                        {{ __('Edit Menu Category Information') }}
                    </h3>
                    <p class="mt-1 text-sm dark:text-gray-400">
                        {{ __('메뉴 카테고리 정보를 수정합니다.') }}
                    </p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.menu-categories.roles', $menuCategory) }}"
                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-green-700">
                        {{ __('Permission Management') }}
                    </a>
                    <a href="{{ route('admin.menu-categories.index') }}"
                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                        ← {{ __('Back to Menu Categories') }}
                    </a>
                </div>
            </div>

            <form action="{{ route('admin.menu-categories.update', $menuCategory) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Category Name Field -->
                    <div>
                        <x-label for="name" :value="__('Category Name')" />
                        <x-input id="name" name="name" type="text" class="mt-1 block w-full"
                            :value="old('name', $menuCategory->name)" placeholder="Enter category name"
                            autocomplete="name" required />
                        <x-input-error-message class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <!-- Sort Order Field -->
                    <div>
                        <x-label for="sort_order" :value="__('Sort Order')" />
                        <x-input id="sort_order" name="sort_order" type="number" class="mt-1 block w-full"
                            :value="old('sort_order', $menuCategory->sort_order)" min="0" />
                        <x-input-error-message class="mt-2" :messages="$errors->get('sort_order')" />
                    </div>
                </div>

                <x-section-border />

                <!-- Additional Settings -->
                <div class="space-y-6">
                    <!-- Active Status -->
                    <div>
                        <label class="flex items-center">
                            <x-checkbox name="is_active" value="1"
                                :checked="old('is_active', $menuCategory->is_active)" />
                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ __('활성화') }}</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-end mt-6">
                    <x-primary-button>
                        {{ __('Update Category') }}
                    </x-primary-button>
                </div>
            </form>

            @can('delete', $menuCategory)
            <form action="{{ route('admin.menu-categories.destroy', $menuCategory) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                    onclick="return confirm('정말 삭제하시겠습니까?')">{{ __('Delete') }}</button>
            </form>
            @endcan
        </div>
    </div>
</x-layouts.admin>