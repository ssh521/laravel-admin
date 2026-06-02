<x-laravel-admin::guest-layout>
    <x-laravel-admin::authentication-card>
        <x-slot name="logo">
            <x-laravel-admin::site-logo />
        </x-slot>

        <x-laravel-admin::validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="block">
                <x-laravel-admin::label for="email" value="{{ __('Email') }}" />
                <x-laravel-admin::input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-laravel-admin::label for="password" value="{{ __('Password') }}" />
                <x-laravel-admin::input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-laravel-admin::label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                <x-laravel-admin::input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-laravel-admin::button>
                    {{ __('Reset Password') }}
                </x-laravel-admin::button>
            </div>
        </form>
    </x-laravel-admin::authentication-card>
</x-laravel-admin::guest-layout>
