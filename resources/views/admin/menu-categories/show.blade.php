<x-laravel-admin::layouts.admin title="메뉴 카테고리 상세">
    <x-slot name="header">
        <x-laravel-admin::admin-header>
            <x-slot name="navigation">
                <a href="{{ route('home') }}">HOME</a>
                - <a href="{{ route('admin.index') }}">관리자 홈</a>
                - <a href="{{ route('admin.menu-categories.index') }}">메뉴 카테고리 관리</a>
            </x-slot>
            <x-slot name="description">
                {{ __('Menu Category Detail') }}
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
                        {{ __('메뉴 카테고리 상세 정보를 확인합니다.') }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('admin.menu-categories.index') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                        ← {{ __('Back to Menu Categories') }}
                    </a>
                </div>
            </div>

            <x-laravel-admin::session-messages />

            <div class="space-y-6">
                <!-- Category Basic Information -->
                <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-6">
                            <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">{{ __('Basic Information') }}</h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-laravel-admin::label for="category_id" :value="__('ID')" />
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $menuCategory->id }}</p>
                                </div>

                                <div>
                                    <x-laravel-admin::label for="category_name" :value="__('Category Name')" />
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white font-medium">{{ $menuCategory->name }}</p>
                                </div>

                                <div>
                                    <x-laravel-admin::label for="sort_order" :value="__('Sort Order')" />
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $menuCategory->sort_order }}</p>
                                </div>

                                <div>
                                    <x-laravel-admin::label for="status" :value="__('Status')" />
                                    <p class="mt-1">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $menuCategory->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400' }}">
                                            {{ $menuCategory->is_active ? __('활성') : __('비활성') }}
                                        </span>
                                    </p>
                                </div>

                                <div>
                                    <x-laravel-admin::label for="created_at" :value="__('Created At')" />
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $menuCategory->created_at->format('Y-m-d H:i:s') }}</p>
                                </div>

                                <div>
                                    <x-laravel-admin::label for="updated_at" :value="__('Updated At')" />
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $menuCategory->updated_at->format('Y-m-d H:i:s') }}</p>
                                </div>
                </div>
            </div>

            <x-laravel-admin::section-border />

            <!-- Allowed Roles Section -->
            <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-6">
                            <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">{{ __('Allowed Roles') }} ({{ $menuCategory->roles->count() }})</h4>

                            @if($menuCategory->roles->count() > 0)
                                <div class="flex flex-wrap gap-2">
                                    @foreach($menuCategory->roles as $role)
                                        <span class="px-3 py-1 text-sm bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400 rounded-full">
                                            {{ $role->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                <p class="text-gray-500 dark:text-gray-400 text-sm">{{ __('이 카테고리에 허용된 역할이 없습니다.') }}</p>
            @endif
        </div>

        <x-laravel-admin::section-border />

        <!-- Connected Menus Section -->
        <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-6">
                            <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">{{ __('Connected Menus') }} ({{ $menuCategory->menus->count() }})</h4>

                            @if($menuCategory->menus->count() > 0)
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                        <thead class="bg-gray-50 dark:bg-gray-600">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    {{ __('Menu Name') }}
                                                </th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    {{ __('Link') }}
                                                </th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    {{ __('Status') }}
                                                </th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    {{ __('Actions') }}
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-gray-700 divide-y divide-gray-200 dark:divide-gray-600">
                                            @foreach($menuCategory->menus as $menu)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $menu->name }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                        {{ $menu->link_file }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $menu->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400' }}">
                                                            {{ $menu->is_active ? __('활성') : __('비활성') }}
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                        <a href="{{ route('admin.menus.show', $menu) }}"
                                                           class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">{{ __('View') }}</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-8">
                <p class="text-gray-500 dark:text-gray-400">{{ __('이 카테고리에 연결된 메뉴가 없습니다.') }}</p>
            </div>
        @endif
    </div>

    <!-- Action Buttons -->
    <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-600">
                            @can('update', $menuCategory)
                            <a href="{{ route('admin.menu-categories.edit', $menuCategory) }}"
                               class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-400 focus:bg-blue-700 dark:focus:bg-blue-400 active:bg-blue-800 dark:active:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-50 transition ease-in-out duration-150"
                               style="color: white !important;">
                                {{ __('Edit Category') }}
                            </a>
                            @endcan

                            <a href="{{ route('admin.menu-categories.roles', $menuCategory) }}"
                               class="inline-flex items-center px-4 py-2 bg-green-600 dark:bg-green-500 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-green-700 dark:hover:bg-green-400 focus:bg-green-700 dark:focus:bg-green-400 active:bg-green-800 dark:active:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-50 transition ease-in-out duration-150"
                               style="color: white !important;">
                                {{ __('Permission Management') }}
                            </a>

                            @can('delete', $menuCategory)
                            <form action="{{ route('admin.menu-categories.destroy', $menuCategory) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-red-600 dark:bg-red-500 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-red-700 dark:hover:bg-red-400 focus:bg-red-700 dark:focus:bg-red-400 active:bg-red-800 dark:active:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-50 transition ease-in-out duration-150"
                                        style="color: white !important;"
                                        onclick="return confirm('{{ __('정말 삭제하시겠습니까?') }}')">
                                    {{ __('Delete Category') }}
                                </button>
    </form>
    @endcan
    </div>
        </div>
    </div>
</x-laravel-admin::layouts.admin>