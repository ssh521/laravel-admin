<div class="flex w-full flex-col gap-4">
    <div class="flex flex-col gap-1">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
            {{ __('Manage Account') }}
        </h1>
        <p class="text-sm text-gray-600 dark:text-gray-400">
            {{ __('Profile Information') }}
        </p>
    </div>

    <nav class="flex flex-wrap gap-6 border-b border-gray-200 pb-2 dark:border-gray-700">
        <x-nav-link href="{{ route('settings.profile') }}" :active="request()->routeIs('settings.profile')">
            {{ __('Profile') }}
        </x-nav-link>
        <x-nav-link href="{{ route('settings.password') }}" :active="request()->routeIs('settings.password')">
            {{ __('Update Password') }}
        </x-nav-link>
        <x-nav-link href="{{ route('settings.appearance') }}" :active="request()->routeIs('settings.appearance')">
            {{ __('Appearance') }}
        </x-nav-link>
    </nav>
</div>
