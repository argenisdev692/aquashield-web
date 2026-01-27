{{-- resources/views/components/select-input-2.blade.php --}}
@props([
    'name',
    'label',
    'model', // Expects the Livewire property name (e.g., 'service_category_id')
    'options', // Collection or Array of options
    'valueField' => 'id', // Property/key in $options for the option's value attribute
    'textField' => 'name', // Property/key in $options for the option's displayed text
    'placeholder' => 'Select an option', // Text for the initial default option
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

    <select id="{{ $name }}" name="{{ $name }}" {{-- Bind directly to the Livewire property passed via 'model' --}}
        wire:model.defer="{{ $model }}" {{ $required ? 'required' : '' }} {{-- Merge classes and handle error styling --}}
        {{ $attributes->merge([
            'class' =>
                'mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white' .
                ($error ? ' border-red-500 dark:border-red-500' : ''),
        ]) }}>
        {{-- Placeholder Option --}}
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif

        {{-- Iterate through options --}}
        @if (!empty($options))
            @foreach ($options as $option)
                {{-- Use data_get for robust access to value/text fields from objects or arrays --}}
                @php
                    $value = data_get($option, $valueField);
                    $text = data_get($option, $textField);
                @endphp
                <option value="{{ $value }}">{{ $text }}</option>
            @endforeach
        @endif

    </select>

    {{-- Display the error message if it exists --}}
    @if ($error)
        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $error }}</p>
    @endif
</div>
