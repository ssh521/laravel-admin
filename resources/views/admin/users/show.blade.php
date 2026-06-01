<x-layouts.admin title="사용자 정보">

    <x-slot name="header">

        <x-admin-header>
            <x-slot name="navigation">
                <a href="{{ route('home') }}">HOME</a>
                - <span class="cursor-pointer" @click="open = !open">관리자메뉴</span>
                @can('viewAny', Ssh521\LaravelAdmin\Models\User::class)
                - <a href="{{ route('admin.users.index') }}">회원 리스트</a>
                @endcan

            </x-slot>
            <x-slot name="description">
                {{ __('User Information') }}
            </x-slot>
        </x-admin-header>

    </x-slot>

    <div class="w-full md:max-w-5xl mx-auto">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl p-6">
            <div class="p-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">

                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-medium">
                            {{ __('User Information') }}
                        </h3>
                        <p class="mt-1 text-sm">
                            {{ __('사용자 정보확인') }}
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                            ← {{ __('Back') }}
                        </a>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name Field -->
                        <div>
                            <x-input-label :value="__('Name')" />
                            <div class="mt-2">
                                <div class="font-bold text-lg ml-1 mt-2">{{ $user->name }}</div>
                            </div>
                        </div>

                        <!-- Email Field -->
                        <div>
                            <x-input-label :value="__('Email')" />
                            <div class="mt-2">
                                <div class="font-bold text-lg ml-1 mt-2">{{ $user->email }}</div>
                            </div>
                        </div>
                    </div>

                    <x-section-border />

                    <!-- Roles Section -->
                    <div>
                        <h4 class="text-md font-medium mb-4">{{ __('Roles') }}</h4>
                        <div class="mt-2">
                            <div class="mt-2 flex flex-wrap gap-2">
                                @if(!empty($user->getRoleNames()))
                                    @foreach($user->getRoleNames() as $role)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                            {{ $role }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('No roles assigned') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <x-section-border />

                    <!-- Permissions Section -->
                    <div>
                        <h4 class="text-md font-medium mb-4">{{ __('Permissions') }}</h4>
                        <div class="mt-2">
                            <div class="mt-2 flex flex-wrap gap-2">
                                @if(!empty($user->getPermissionNames()))
                                    @foreach($user->getPermissionNames() as $permission)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                            {{ $permission }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('No permissions assigned') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <x-section-border />

                    <!-- Delete Button -->
                    <div class="flex items-center justify-end py-4">
                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this user?') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-700 dark:bg-red-600 dark:hover:bg-red-800 rounded-md text-white font-medium transition-colors">
                                {{ __('Delete User') }}
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

</x-layouts.admin>