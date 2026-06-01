@props(['heading', 'subheading' => null])

<div {{ $attributes->merge(['class' => 'flex w-full flex-col gap-4']) }}>
    <div class="flex flex-col gap-1">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
            {{ $heading }}
        </h2>

        @if ($subheading)
            <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ $subheading }}
            </p>
        @endif
    </div>

    <div class="w-full rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
        {{ $slot }}
    </div>
</div>
