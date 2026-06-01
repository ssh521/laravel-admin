<nav x-data="{ open: false }" x-on:keydown.escape.window="open = false" class="relative z-50 bg-white dark:bg-gray-900">

    <!-- Primary Navigation Menu -->
    <div class="mx-auto h-full px-4 py-0 lg:py-2">
        <div class="flex justify-between items-center h-header-bar">

            <div class="flex w-40">
                <!-- Logo -->
                <div class="flex items-center shrink-0">
                    <a href="{{ route('home') }}" class="flex items-center gap-1">
                        <x-site-logo size="w-logo-header h-logo-header" />
                        <span class="text-brand-header text-purple-700 dark:text-purple-200">야버닷컴</span>
                    </a>
                    <x-dark-mode-toggle class="hidden lg:block" />
                </div>     

            </div>

            <div class="flex items-center">
            </div>
            
            <div class="flex items-center justify-end w-40">


                @include('partials.header-auth')

            </div>

        </div>
    </div>

</nav>