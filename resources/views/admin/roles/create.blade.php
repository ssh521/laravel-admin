<x-layouts.admin title="역할 등록">
    <x-slot name="header">
        <x-admin-header>
            <x-slot name="navigation">
                <a href="{{ route('home') }}">HOME</a>
                - <a href="{{ route('admin.index') }}">Admin Home</a>
            </x-slot>
            <x-slot name="description">
                {{ __('Create New Role') }}
            </x-slot>
        </x-admin-header>
    </x-slot>

    <div class="w-full lg:max-w-7xl mx-auto">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl px-6 py-2">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-medium dark:text-white">
                        {{ __('Role Information') }}
                    </h3>
                    <p class="mt-1 text-sm dark:text-gray-400">
                        {{ __('Register a new role.') }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('admin.roles.index') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                        ← {{ __('Back to Roles') }}
                    </a>                            
                </div>
            </div>

            <form action="{{ route('admin.roles.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Role Name Field -->
                    <div>
                        <x-label for="name" :value="__('Role Name')" />
                        <x-input id="name" name="name" type="text" class="mt-1 block w-full" 
                                     :value="old('name')" placeholder="{{ __('Enter role name') }}" autocomplete="name" />
                        @if ($errors->has('name'))
                            <x-input-error-message :messages="['Please enter a role name!']" />
                        @endif
                    </div>

                    <!-- Role Description Field -->
                    <div>
                        <x-label for="description" :value="__('Description')" />
                        <x-input id="description" name="description" type="text" class="mt-1 block w-full" 
                                     :value="old('description')" placeholder="{{ __('Enter role description') }}" />
                        <x-input-error-message class="mt-2" :messages="$errors->get('description')" />
                    </div>
                </div>

                <x-section-border />

                <!-- Permissions Section -->
                <div>
                    <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">{{ __('Assign Permissions') }}</h4>
                    <x-input-label for="permissions_multiple" :value="__('Select Permissions')" />
                    <div class="mt-2 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($permissions as $permission)
                            <div class="flex items-center">
                                <input id="permission_{{ $permission->id }}" name="permissions[]" type="checkbox"
                                       value="{{ $permission->id }}" {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <label for="permission_{{ $permission->id }}"
                                       class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                    {{ $permission->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    @if ($errors->has('permissions'))
                        <x-input-error-message class="mt-2" :messages="['Please select at least one permission!']" />
                    @endif
                </div>

                <div class="flex items-center justify-end mt-6">
                    <x-primary-button>
                        {{ __('Create Role') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>