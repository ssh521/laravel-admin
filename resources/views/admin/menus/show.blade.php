<x-layouts.admin title="메뉴 상세">
    <x-slot name="header">
        <x-admin-header>
            <x-slot name="navigation">
                <a href="{{ route('home') }}">HOME</a>
                - <a href="{{ route('admin.index') }}">관리자 홈</a>
                - <a href="{{ route('admin.menus.index') }}">메뉴 관리</a>
            </x-slot>
            <x-slot name="description">
                {{ __('Menu Detail') }}
            </x-slot>
        </x-admin-header>
    </x-slot>

    <div class="w-full lg:max-w-7xl mx-auto">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl px-6 py-2">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-medium dark:text-white">
                        {{ __('Menu Information') }}
                    </h3>
                    <p class="mt-1 text-sm dark:text-gray-400">
                        {{ __('메뉴 상세 정보를 확인합니다.') }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('admin.menus.index') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                        ← {{ __('Back to Menus') }}
                    </a>                            
                </div>
            </div>

            <x-session-messages />

            <div class="space-y-6">
                <!-- Menu Basic Information -->
                <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-6">
                    <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">{{ __('Basic Information') }}</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-label for="menu_id" :value="__('ID')" />
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $menu->id }}</p>
                        </div>
                        
                        <div>
                            <x-label for="menu_name" :value="__('Menu Name')" />
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                @if($menu->icon)
                                    <i class="{{ $menu->icon }} mr-2 text-gray-400 dark:text-gray-500"></i>
                                @endif
                                {{ $menu->name }}
                            </p>
                        </div>

                        <div>
                            <x-label for="category" :value="__('Category')" />
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $menu->category ? $menu->category->name : '-' }}</p>
                        </div>

                        <div>
                            <x-label for="parent_menu" :value="__('Parent Menu')" />
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $menu->parent ? $menu->parent->name : '-' }}</p>
                        </div>

                        <div>
                            <x-label for="route_name" :value="__('Route Name')" />
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                @if($menu->route_name)
                                    <span class="text-blue-600 dark:text-blue-400 font-medium">{{ $menu->route_name }}</span>
                                @else
                                    <span class="text-gray-400 dark:text-gray-500">-</span>
                                @endif
                            </p>
                        </div>

                        <div>
                            <x-label for="route_parameters" :value="__('Route Parameters')" />
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $menu->route_parameters ?: '-' }}</p>
                        </div>

                        <div>
                            <x-label for="direct_url" :value="__('Direct URL')" />
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $menu->url ?: '-' }}</p>
                        </div>

                        <div>
                            <x-label for="target" :value="__('Target')" />
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $menu->target ?: '-' }}</p>
                        </div>

                        <div>
                            <x-label for="sort_order" :value="__('Sort Order')" />
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $menu->sort_order }}</p>
                        </div>

                        <div>
                            <x-label for="status" :value="__('Status')" />
                            <p class="mt-1">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $menu->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400' }}">
                                    {{ $menu->is_active ? __('활성') : __('비활성') }}
                                </span>
                            </p>
                        </div>

                        <div>
                            <x-label for="external_link" :value="__('External Link')" />
                            <p class="mt-1">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $menu->is_external ? 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                    {{ $menu->is_external ? __('예') : __('아니오') }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div>
                            <x-label for="created_at" :value="__('Created At')" />
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $menu->created_at->format('Y-m-d H:i:s') }}</p>
                        </div>
                        
                        <div>
                            <x-label for="updated_at" :value="__('Updated At')" />
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $menu->updated_at->format('Y-m-d H:i:s') }}</p>
                        </div>
                    </div>
                    
                    @if($menu->description)
                        <div class="mt-6">
                            <x-label for="description" :value="__('Description')" />
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $menu->description }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <x-section-border />

                <!-- Child Menus Section -->
                @if($menu->children->count() > 0)
                <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-6">
                    <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">{{ __('Child Menus') }} ({{ $menu->children->count() }})</h4>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                            <thead class="bg-gray-50 dark:bg-gray-600">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Menu Name') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Route/URL') }}
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
                                @foreach($menu->children as $childMenu)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $childMenu->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            @if($childMenu->route_name)
                                                <span class="text-blue-600 dark:text-blue-400 font-medium">{{ $childMenu->route_name }}</span>
                                            @else
                                                <span class="text-gray-600 dark:text-gray-400">{{ $childMenu->url }}</span>
                                            @endif
                                            @if($childMenu->is_external)
                                                <span class="ml-1 text-xs bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400 px-1 rounded">{{ __('외부') }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $childMenu->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400' }}">
                                                {{ $childMenu->is_active ? __('활성') : __('비활성') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('admin.menus.show', $childMenu) }}" 
                                               class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">{{ __('View') }}</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-600">
                @can('update', $menu)
                <a href="{{ route('admin.menus.edit', $menu) }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-600 focus:bg-blue-700 dark:focus:bg-blue-600 active:bg-blue-900 dark:active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-50 transition ease-in-out duration-150"
                   style="color: white !important;">
                    {{ __('Edit Menu') }}
                </a>
                @endcan

                @can('delete', $menu)
                <form action="{{ route('admin.menus.destroy', $menu) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-red-600 dark:bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 dark:hover:bg-red-400 active:bg-red-700 dark:active:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-50 transition ease-in-out duration-150"
                            onclick="return confirm('{{ __('정말 삭제하시겠습니까?') }}')">
                        {{ __('Delete Menu') }}
                    </button>
                </form>
                @endcan
            </div>
        </div>
    </div>
</x-layouts.admin> 