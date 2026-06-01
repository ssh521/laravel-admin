<x-laravel-admin::layouts.admin title="메뉴 수정">
    <x-slot name="header">
        <x-laravel-admin::admin-header>
            <x-slot name="navigation">
                <a href="{{ route('home') }}">HOME</a>
                - <a href="{{ route('admin.index') }}">관리자 홈</a>
            </x-slot>
            <x-slot name="description">
                {{ __('Edit Menu') }}
            </x-slot>
        </x-laravel-admin::admin-header>
    </x-slot>

    <div class="w-full lg:max-w-7xl mx-auto">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl px-6 py-2">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-medium dark:text-white">
                        {{ __('Edit Menu Information') }}
                    </h3>
                    <p class="mt-1 text-sm dark:text-gray-400">
                        {{ __('메뉴 정보를 수정합니다.') }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('admin.menus.index') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                        ← {{ __('Back to Menus') }}
                    </a>
                </div>
            </div>

            <form action="{{ route('admin.menus.update', $menu) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Menu Name Field -->
                    <div class="md:col-span-2">
                        <x-laravel-admin::label for="name" :value="__('Menu Name')" />
                        <x-laravel-admin::input id="name" name="name" type="text" class="mt-1 block w-full"
                                     :value="old('name', $menu->name)" placeholder="Enter menu name" autocomplete="name" required />
                        <x-laravel-admin::input-error-message class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <!-- Route Name Field -->
                    <div>
                        <x-laravel-admin::label for="route_name" :value="__('Route Name')" />
                        <x-laravel-admin::input id="route_name" name="route_name" type="text" class="mt-1 block w-full"
                                     :value="old('route_name', $menu->route_name)" placeholder="예: users.index" />
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('라우트 이름을 입력하거나 비워두고 직접 URL을 입력하세요.') }}</p>
                        <x-laravel-admin::input-error-message class="mt-2" :messages="$errors->get('route_name')" />
                    </div>

                    <!-- Route Parameters Field -->
                    <div>
                        <x-laravel-admin::label for="route_parameters" :value="__('Route Parameters (JSON)')" />
                        <x-laravel-admin::input id="route_parameters" name="route_parameters" type="text" class="mt-1 block w-full"
                                     :value="old('route_parameters', $menu->route_parameters)" placeholder='예: {"id": 1}' />
                        <x-laravel-admin::input-error-message class="mt-2" :messages="$errors->get('route_parameters')" />
                    </div>

                    <!-- Direct URL Field -->
                    <div>
                        <x-laravel-admin::label for="url" :value="__('Direct URL')" />
                        <x-laravel-admin::input id="url" name="url" type="text" class="mt-1 block w-full"
                                     :value="old('url', $menu->getRawUrlAttribute())" placeholder="예: /admin/users" />
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('라우트 이름이 없을 때 사용할 직접 URL') }}</p>
                        <x-laravel-admin::input-error-message class="mt-2" :messages="$errors->get('url')" />
                    </div>

                    <!-- Target Field -->
                    <div>
                        <x-laravel-admin::label for="target" :value="__('Target')" />
                        <select id="target" name="target"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-600 focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 rounded-md shadow-sm dark:bg-gray-700 dark:text-white">
                            <option value="">{{ __('선택하세요') }}</option>
                            <option value="_self" {{ old('target', $menu->target) == '_self' ? 'selected' : '' }}>{{ __('현재 창') }}</option>
                            <option value="_blank" {{ old('target', $menu->target) == '_blank' ? 'selected' : '' }}>{{ __('새 창') }}</option>
                            <option value="_parent" {{ old('target', $menu->target) == '_parent' ? 'selected' : '' }}>{{ __('부모 창') }}</option>
                            <option value="_top" {{ old('target', $menu->target) == '_top' ? 'selected' : '' }}>{{ __('최상위 창') }}</option>
                        </select>
                        <x-laravel-admin::input-error-message class="mt-2" :messages="$errors->get('target')" />
                    </div>

                    <!-- Category Field -->
                    <div>
                        <x-laravel-admin::label for="category_id" :value="__('Category')" />
                        <select id="category_id" name="category_id"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-600 focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 rounded-md shadow-sm dark:bg-gray-700 dark:text-white">
                            <option value="">{{ __('선택하세요') }}</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $menu->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-laravel-admin::input-error-message class="mt-2" :messages="$errors->get('category_id')" />
                    </div>

                    <!-- Parent Menu Field -->
                    <div>
                        <x-laravel-admin::label for="parent_id" :value="__('Parent Menu')" />
                        <select id="parent_id" name="parent_id"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-600 focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 rounded-md shadow-sm dark:bg-gray-700 dark:text-white">
                            <option value="">{{ __('선택하세요') }}</option>
                            @foreach($parentMenus as $parentMenu)
                                <option value="{{ $parentMenu->id }}" {{ old('parent_id', $menu->parent_id) == $parentMenu->id ? 'selected' : '' }}>
                                    {{ $parentMenu->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-laravel-admin::input-error-message class="mt-2" :messages="$errors->get('parent_id')" />
                    </div>

                    <!-- Sort Order Field -->
                    <div>
                        <x-laravel-admin::label for="sort_order" :value="__('Sort Order')" />
                        <x-laravel-admin::input id="sort_order" name="sort_order" type="number" class="mt-1 block w-full"
                                     :value="old('sort_order', $menu->sort_order)" min="0" />
                        <x-laravel-admin::input-error-message class="mt-2" :messages="$errors->get('sort_order')" />
                    </div>

                    <!-- Icon Field -->
                    <div>
                        <x-laravel-admin::label for="icon" :value="__('Icon (FontAwesome Class)')" />
                        <x-laravel-admin::input id="icon" name="icon" type="text" class="mt-1 block w-full"
                                     :value="old('icon', $menu->icon)" placeholder="fas fa-home" />
                        <x-laravel-admin::input-error-message class="mt-2" :messages="$errors->get('icon')" />
                    </div>
                </div>

                <x-laravel-admin::section-border />

                <!-- Additional Settings -->
                <div class="space-y-6">
                    <!-- Active Status -->
                    <div>
                        <label class="flex items-center">
                            <x-laravel-admin::checkbox name="is_active" value="1" :checked="old('is_active', $menu->is_active)" />
                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ __('활성화') }}</span>
                        </label>
                    </div>

                    <!-- Description Field -->
                    <div>
                        <x-laravel-admin::label for="description" :value="__('Description')" />
                        <textarea id="description" name="description" rows="3"
                                  class="mt-1 block w-full border-gray-300 dark:border-gray-600 focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 rounded-md shadow-sm dark:bg-gray-700 dark:text-white">{{ old('description', $menu->description) }}</textarea>
                        <x-laravel-admin::input-error-message class="mt-2" :messages="$errors->get('description')" />
                    </div>
                </div>

                <div class="flex items-center justify-end mt-6">
                    <x-laravel-admin::primary-button>
                        {{ __('Update Menu') }}
                    </x-laravel-admin::primary-button>
                </div>
            </form>

            @can('delete', $menu)
            <form action="{{ route('admin.menus.destroy', $menu) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                    onclick="return confirm('정말 삭제하시겠습니까?')">삭제</button>
            </form>
            @endcan
        </div>
    </div>
</x-laravel-admin::layouts.admin>