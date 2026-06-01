@props(['name', 'disabled' => false])

<select 
    name="{{ $name }}"
    {{ $disabled ? 'disabled' : '' }} 
    {!! $attributes->merge(['class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 dark:focus:ring-indigo-500 dark:focus:border-indigo-500']) !!}
>
    {{ $slot }}
</select>

