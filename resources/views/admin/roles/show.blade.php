<x-laravel-admin::admin.layouts.admin title="역할 상세">
    <x-slot name="header">
        <x-laravel-admin::admin.admin-header>
            <x-slot name="navigation">
                <a href="{{ route('home') }}">HOME</a>
                - <a href="{{ route('admin.index') }}">관리자 홈</a>
                - <a href="{{ route('admin.roles.index') }}">역할 관리</a>
            </x-slot>
            <x-slot name="description">
                {{ __('Role Detail') }}
            </x-slot>
        </x-laravel-admin::admin.admin-header>
    </x-slot>

    <div class="w-full lg:max-w-5xl mx-auto">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl px-5 py-2">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-medium dark:text-white">
                        {{ __('Role Information') }}
                    </h3>
                    <p class="mt-1 text-sm dark:text-gray-400">
                        {{ __('역할 상세 정보를 확인합니다.') }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('admin.roles.index') }}" class="inline-flex items-center justify-center min-w-[104px] px-5 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                        ← {{ __('Back to Roles') }}
                    </a>
                </div>
            </div>

            <x-laravel-admin::admin.session-messages />

            <div class="space-y-6">
                <!-- Role Basic Information -->
                <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-6">
                            <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">{{ __('Basic Information') }}</h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-laravel-admin::admin.label for="role_name" :value="__('Role Name')" />
                                    <p class="mt-1 text-base text-gray-900 dark:text-white font-medium">{{ $role->name }}</p>
                                </div>

                                @if($role->description)
                                <div>
                                    <x-laravel-admin::admin.label for="role_description" :value="__('Description')" />
                                    <p class="mt-1 text-base text-gray-900 dark:text-white">{{ $role->description }}</p>
                                </div>
                                @endif
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                                <div>
                                    <x-laravel-admin::admin.label for="created_at" :value="__('Created At')" />
                                    <p class="mt-1 text-base text-gray-900 dark:text-white">{{ $role->created_at->format('Y-m-d H:i:s') }}</p>
                                </div>

                                <div>
                                    <x-laravel-admin::admin.label for="updated_at" :value="__('Updated At')" />
                                    <p class="mt-1 text-base text-gray-900 dark:text-white">{{ $role->updated_at->format('Y-m-d H:i:s') }}</p>
                                </div>
                </div>
            </div>

            <x-laravel-admin::admin.section-border />

            <!-- Permissions Section -->
            <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-6">
                            <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">{{ __('Assigned Permissions') }}</h4>

                            @if($role->permissions->count() > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                    @foreach($role->permissions as $permission)
                                        <div class="flex items-center">
                                            <span class="px-3 py-1 text-sm bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400 rounded-full">
                                                {{ $permission->name }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                <p class="text-gray-500 dark:text-gray-400 text-sm">{{ __('이 역할에 할당된 권한이 없습니다.') }}</p>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-600">
                            @can('update', $role)
                            <a href="{{ route('admin.roles.edit', $role) }}"
                               class="inline-flex items-center justify-center min-w-[120px] px-5 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest !text-white dark:!text-gray-800 hover:bg-gray-700 dark:hover:bg-gray-100 focus:bg-gray-700 dark:focus:bg-gray-100 active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                {{ __('Edit Role') }}
                            </a>
                            @endcan

                            @can('delete', $role)
                            <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <x-laravel-admin::admin.danger-button type="submit"
                                        onclick="return confirm('{{ __('정말 삭제하시겠습니까?') }}')">
                                    {{ __('Delete Role') }}
                                </x-laravel-admin::admin.danger-button>
    </form>
    @endcan
    </div>
        </div>
    </div>
</x-laravel-admin::admin.layouts.admin>
