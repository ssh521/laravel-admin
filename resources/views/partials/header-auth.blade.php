@guest
<div class="ms-3 flex items-center gap-3">
    @if (Route::has('login'))
    <x-laravel-admin::nav-link href="{{ route('login') }}" :active="request()->routeIs('login')" class="text-sm">
        {{ __('Log in') }}
    </x-laravel-admin::nav-link>
    @endif

    @if (Route::has('register'))
    <x-laravel-admin::nav-link href="{{ route('register') }}" :active="request()->routeIs('register')" class="text-sm">
        {{ __('Register') }}
    </x-laravel-admin::nav-link>
    @endif
</div>
@endguest

@auth
<!-- Teams Dropdown -->
@if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
<div class="ms-3 relative">
    <x-laravel-admin::dropdown align="right" width="60">
        <x-slot name="trigger">
            <span class="inline-flex rounded-md">
                <button type="button"
                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150">
                    {{ Auth::user()->currentTeam->name }}

                    <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                    </svg>
                </button>
            </span>
        </x-slot>

        <x-slot name="content">
            <div class="w-60">
                <!-- Team Management -->
                <div class="block px-4 py-2 text-xs text-gray-400">
                    {{ __('Manage Team') }}
                </div>

                <!-- Team Settings -->
                <x-laravel-admin::dropdown-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}">
                    {{ __('Team Settings') }}
                </x-laravel-admin::dropdown-link>

                @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                <x-laravel-admin::dropdown-link href="{{ route('teams.create') }}">
                    {{ __('Create New Team') }}
                </x-laravel-admin::dropdown-link>
                @endcan

                <!-- Team Switcher -->
                @if (Auth::user()->allTeams()->count() > 1)
                <div class="border-t border-gray-200 dark:border-gray-600"></div>

                <div class="block px-4 py-2 text-xs text-gray-400">
                    {{ __('Switch Teams') }}
                </div>

                @foreach (Auth::user()->allTeams() as $team)
                <x-laravel-admin::switchable-team :team="$team" />
                @endforeach
                @endif
            </div>
        </x-slot>
    </x-laravel-admin::dropdown>
</div>
@endif

<!-- Settings Dropdown -->
<div class="ms-3 relative">
    <x-laravel-admin::dropdown align="right" width="48">
        <x-slot name="trigger">
            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
            <button
                class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                <img class="size-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}"
                    alt="{{ Auth::user()->name }}" />
            </button>
            @else
            <span class="inline-flex rounded-md">
                <button type="button"
                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150">
                    {{ Auth::user()->name }}

                    <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>
            </span>
            @endif
        </x-slot>

        <x-slot name="content">

            <!-- 관리자 메뉴 -->
            @if(auth()->user() && auth()->user()->hasRole(['Super Admin', 'Admin']))
            <!-- Admin Management -->
            <div class="block px-4 py-2 text-xs text-gray-400">
                {{ __('Admin Management') }}
            </div>

            <x-laravel-admin::dropdown-link href="{{ route('admin.index') }}" :active="request()->routeIs('admin.index')">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="w-4 h-4 mr-2 inline -mt-1">
                    <circle cx="12" cy="12" r="3" />
                    <path
                        d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33h.09A1.65 1.65 0 0 0 11 3.09V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51h.09a1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82v.09a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z" />
                </svg>
                {{ __('관리자 메뉴') }}
            </x-laravel-admin::dropdown-link>

            @endif

            <div class="border-t border-gray-200 dark:border-gray-600"></div>

            <!-- Admin Management -->
            <div class="block px-4 py-2 text-xs text-gray-400">
                {{ __('User Dashboard') }}
            </div>

            <!-- Dashboard -->
            <x-laravel-admin::dropdown-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="w-4 h-4 mr-2 inline -mt-1">
                    <circle cx="12" cy="12" r="3" />
                    <path
                        d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06-.06a2 2 0 0 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33h.09A1.65 1.65 0 0 0 11 3.09V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51h.09a1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82v.09a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z" />
                </svg>
                {{ __('Dashboard') }}
            </x-laravel-admin::dropdown-link>

            <div class="border-t border-gray-200 dark:border-gray-600"></div>

            <!-- Account Management -->
            <div class="block px-4 py-2 text-xs text-gray-400">
                {{ __('Manage Account') }}
            </div>

            <x-laravel-admin::dropdown-link href="{{ route('profile.show') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="w-4 h-4 mr-2 inline -mt-1">
                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                {{ __('Profile') }}
            </x-laravel-admin::dropdown-link>

            <div class="border-t border-gray-200 dark:border-gray-600"></div>

            <!-- Authentication -->
            @php
                $adminLogoutRoute = config('laravel-admin.route_name_prefix', 'admin.').'logout';
                $logoutRoute = Route::has($adminLogoutRoute) ? $adminLogoutRoute : 'logout';
            @endphp

            <form method="POST" action="{{ route($logoutRoute) }}" x-data>
                @csrf

                <x-laravel-admin::dropdown-link href="{{ route($logoutRoute) }}" @click.prevent="$root.submit();">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="w-4 h-4 mr-2 inline -mt-1">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16,17 21,12 16,7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                    {{ __('Log Out') }}
                </x-laravel-admin::dropdown-link>
            </form>


        </x-slot>
    </x-laravel-admin::dropdown>
</div>
@endauth
