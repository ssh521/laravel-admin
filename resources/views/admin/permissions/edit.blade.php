<x-laravel-admin::layouts.admin title="권한 수정">
    <x-slot name="header">
        <x-laravel-admin::admin-header>
            <x-slot name="navigation">
                <a href="{{ route('home') }}">HOME</a>
                - <a href="{{ route('admin.index') }}">Admin Home</a>
            </x-slot>
            <x-slot name="description">
                {{ __('Edit Permission') }}
            </x-slot>
        </x-laravel-admin::admin-header>
    </x-slot>

    <div class="w-full lg:max-w-7xl mx-auto">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl px-6 py-2">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-medium dark:text-white">
                        {{ __('Edit Permission Information') }}
                    </h3>
                    <p class="mt-1 text-sm dark:text-gray-400">
                        {{ __('Edit permission information.') }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('admin.permissions.index') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                        ← {{ __('Back to Permissions') }}
                    </a>
                </div>
            </div>

            <form action="{{ route('admin.permissions.update', $permission) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Permission Name Field -->
                    <div>
                        <x-laravel-admin::label for="name" :value="__('Permission Name')" />
                        <x-laravel-admin::input id="name" name="name" type="text" class="mt-1 block w-full"
                                     :value="old('name', $permission->name)" placeholder="{{ __('Enter permission name') }}" autocomplete="name" />
                        @if ($errors->has('name'))
                            <x-laravel-admin::input-error-message :messages="['Please enter a permission name!']" />
                        @endif
                    </div>

                    <!-- Permission Description Field -->
                    <div>
                        <x-laravel-admin::label for="description" :value="__('Description')" />
                        <x-laravel-admin::input id="description" name="description" type="text" class="mt-1 block w-full"
                                     :value="old('description', $permission->description ?? '')" placeholder="{{ __('Enter permission description') }}" />
                        <x-laravel-admin::input-error-message class="mt-2" :messages="$errors->get('description')" />
                    </div>
                </div>

                <div class="flex items-center justify-end mt-6">
                    <x-laravel-admin::primary-button>
                        {{ __('Update Permission') }}
                    </x-laravel-admin::primary-button>
                </div>
            </form>

            @can('delete', $permission)
            <form action="{{ route('admin.permissions.destroy', $permission) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                    onclick="return confirm('{{ __('정말 삭제하시겠습니까?') }}')">{{ __('Delete') }}</button>
            </form>
            @endcan
        </div>
    </div>
</x-laravel-admin::layouts.admin>