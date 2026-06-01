<x-guest-layout>
    <x-authentication-card>

        <x-slot name="logo">
            <x-site-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        @session('status')
            <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                {{ $value }}
            </div>
        @endsession

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            </div>

            <div class="block mt-4">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" />
                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                <x-button class="ms-4">
                    {{ __('Log in') }}
                </x-button>
            </div>
        </form>

        @if (Route::has('kakao.redirect'))
            <div class="mt-6">
                <a href="{{ route('kakao.redirect') }}" class="inline-flex items-center w-full justify-center gap-2 rounded-md bg-yellow-400 px-4 py-2 font-semibold text-black shadow hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                        <path d="M12 3C6.477 3 2 6.335 2 10.451c0 2.5 1.73 4.68 4.33 5.936-.152.57-.552 2.064-.632 2.386-.099.398.146.392.307.285.126-.083 2.005-1.363 2.817-1.914.392.057.796.087 1.21.087 5.523 0 10-3.335 10-7.48C20.032 6.335 17.523 3 12 3z"/>
                    </svg>
                    카카오로 로그인
                </a>
            </div>
        @endif

        @if (Route::has('register'))
            <x-slot name="footer">
                <div class="flex items-center justify-center mt-4">
                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Don\'t have an account?') }}</span>
                    <a href="{{ route('register') }}" class="text-sm text-purple-600 dark:text-purple-400 hover:text-purple-900 dark:hover:text-purple-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 dark:focus:ring-offset-gray-800 ml-1">
                        {{ __('Sign up') }}
                    </a>
                </div>
            </x-slot>
        @endif
    </x-authentication-card>
</x-guest-layout>
