@props(['disabled' => false])

<input
    {{ $disabled ? 'disabled' : '' }}
    {!! $attributes->merge([
        'class' => 'border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 rounded-md shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 dark:focus:border-indigo-500 dark:focus:ring-indigo-500 disabled:opacity-50',
    ]) !!}
>
