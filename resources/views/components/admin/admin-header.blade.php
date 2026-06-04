<div class="grid grid-cols-[1fr_auto_1fr] items-end">
    <div class="flex items-end">
        <h2 class="text-sm leading-tight">
            {{ $navigation }}
        </h2>
    </div>
    <div class="flex items-center justify-center">
        <a href="{{ route('home') }}"><x-laravel-admin::admin.site-logo size="w-16 h-16" class="dark:text-purple-200" /></a>
    </div>
    <div class="flex items-end justify-end">
        <h2 class="text-sm leading-tight">
            {{ $description }}
        </h2>
    </div>
</div> 