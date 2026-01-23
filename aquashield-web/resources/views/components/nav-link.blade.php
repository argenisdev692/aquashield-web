@props(['active'])

@php
    $classes =
        $active ?? false
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-gray-800 dark:border-gray-800 text-sm font-medium leading-5 text-gray-900 dark:text-gray-100 focus:outline-none focus:border-gray-800 transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-300 hover:border-gray-800 dark:hover:border-gray-800 focus:outline-none focus:text-gray-800 dark:focus:text-gray-300 focus:border-gray-800 dark:focus:border-gray-800 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
