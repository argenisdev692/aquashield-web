@props(['name', 'label', 'model', 'options' => [], 'error' => null, 'required' => false])

<div>
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        {{ $label }} {{ $required ? '' : '' }}
    </label>
    <select id="{{ $name }}" name="{{ $name }}"
        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white {{ $error ? 'border-red-500' : '' }}"
        x-model="{{ $model }}" wire:model.defer="{{ $name }}" {{ $required ? 'required' : '' }}>
        <option value="">Select an option</option>
        @foreach ($options as $value => $label)
            <option value="{{ $value }}">{{ $label }}</option>
        @endforeach
    </select>
    @if ($error)
        <p class="mt-2 text-sm text-red-600">{{ $error }}</p>
    @endif
</div>
