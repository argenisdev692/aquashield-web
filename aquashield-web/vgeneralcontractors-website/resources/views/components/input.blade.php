@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge([
    'class' =>
        'border-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-gray-800 dark:focus:border-gray-800 focus:ring-gray-800 dark:focus:ring-gray-800 rounded-md shadow-sm',
]) !!}>
