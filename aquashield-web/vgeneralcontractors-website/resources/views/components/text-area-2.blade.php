{{-- resources/views/components/text-area-2.blade.php --}}
@props([
    'name',
    'label',
    'model', // Expects the Livewire property name (e.g., 'description')
    'rows' => 4,
    'error' => null,
    'required' => false,
])

<div>
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        {{ $label }}
        {{-- Show asterisk if required --}}
        @if ($required)
            <span class="text-red-500">*</span>
        @endif
    </label>
    <textarea id="{{ $name }}" name="{{ $name }}" rows="{{ $rows }}" {{-- Bind directly to the Livewire property passed via 'model' --}}
        {{-- Using defer is often good practice for textareas to avoid excessive updates --}} wire:model.defer="{{ $model }}" {{-- Add required attribute for basic HTML5 validation/accessibility --}} {{ $required ? 'required' : '' }}
        {{-- Merge other attributes passed to the component --}}
        {{ $attributes->merge([
            'class' =>
                'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white' .
                ($error ? ' border-red-500 dark:border-red-500' : ''),
        ]) }}></textarea>

    {{-- Display the error message if it exists --}}
    @if ($error)
        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $error }}</p>
    @endif
</div>
