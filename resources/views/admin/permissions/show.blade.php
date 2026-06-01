<x-layouts.admin title="권한 상세">
    <x-slot name="header">
        <x-admin-header>
            <x-slot name="navigation">
                <a href="{{ route('home') }}">HOME</a>
                - <a href="{{ route('admin.index') }}">관리자 홈</a>
                - <a href="{{ route('admin.permissions.index') }}">권한 관리</a>
            </x-slot>
            <x-slot name="description">
                {{ __('Permission Detail') }}
            </x-slot>
        </x-admin-header>
    </x-slot>

    <div class="w-full lg:max-w-7xl mx-auto">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl px-6 py-2">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-medium dark:text-white">
                        {{ __('Permission Information') }}
                    </h3>
                    <p class="mt-1 text-sm dark:text-gray-400">
                        {{ __('권한 상세 정보를 확인합니다.') }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('admin.permissions.index') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                        ← {{ __('Back to Permissions') }}
                    </a>                            
                </div>
            </div>

            <x-session-messages />

            <div class="space-y-6">
                <!-- Permission Basic Information -->
                <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-6">
                            <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">{{ __('Basic Information') }}</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-label for="permission_name" :value="__('Permission Name')" />
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white font-medium">{{ $permission->name }}</p>
                                </div>
                                
                                @if($permission->description)
                                <div>
                                    <x-label for="permission_description" :value="__('Description')" />
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $permission->description }}</p>
                                </div>
                                @endif
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                                <div>
                                    <x-label for="created_at" :value="__('Created At')" />
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $permission->created_at->format('Y-m-d H:i:s') }}</p>
                                </div>
                                
                                <div>
                                    <x-label for="updated_at" :value="__('Updated At')" />
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $permission->updated_at->format('Y-m-d H:i:s') }}</p>
                                </div>
                </div>
            </div>

            <x-section-border />

            <!-- Assigned Roles Section -->
            <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-6">
                            <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">{{ __('Assigned Roles') }}</h4>
                            
                            @if($permission->roles && $permission->roles->count() > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                    @foreach($permission->roles as $role)
                                        <div class="flex items-center">
                                            <span class="px-3 py-1 text-sm bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400 rounded-full">
                                                {{ $role->name }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                <p class="text-gray-500 dark:text-gray-400 text-sm">{{ __('이 권한이 할당된 역할이 없습니다.') }}</p>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-600">
                            @can('update', $permission)
                            <a href="{{ route('admin.permissions.edit', $permission) }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-600 focus:bg-blue-700 dark:focus:bg-blue-600 active:bg-blue-900 dark:active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-50 transition ease-in-out duration-150"
                               style="color: white !important;">
                                {{ __('Edit Permission') }}
                            </a>
                            @endcan

                            @can('delete', $permission)
                            <form action="{{ route('admin.permissions.destroy', $permission) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 bg-red-600 dark:bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 dark:hover:bg-red-400 active:bg-red-700 dark:active:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-50 transition ease-in-out duration-150"
                                        onclick="return confirm('{{ __('정말 삭제하시겠습니까?') }}')">
                                    {{ __('Delete Permission') }}
                                </button>
    </form>
    @endcan
    </div>
        </div>
    </div>
</x-layouts.admin> 