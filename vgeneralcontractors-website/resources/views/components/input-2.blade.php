{{-- resources/views/components/input-2.blade.php --}}
@props([
    'name',
    'label',
    'model', // Expects the Livewire property name (e.g., 'title')
    'type' => 'text', // Default input type
    'error' => null,
    'required' => false,
])

<div>
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        {{ $label }}
        @if ($required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" {{-- Bind directly to the Livewire property passed via 'model'.
             Using .defer is often good for text inputs to reduce network requests.
             This binding ensures the value from Livewire is displayed. --}}
        wire:model.defer="{{ $model }}" {{ $required ? 'required' : '' }} {{-- Merge attributes: Allows passing placeholder, autocomplete, Alpine directives etc.
             Also handles base classes and error styling. --}}
        {{ $attributes->merge([
            'class' =>
                'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white' .
                ($error ? ' border-red-500 dark:border-red-500' : ''),
        ]) }}>

    {{-- Display the error message if it exists --}}
    @if ($error)
        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $error }}</p>
    @endif
</div>
