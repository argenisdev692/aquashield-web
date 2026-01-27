@props(['name', 'label', 'model', 'rows' => 4, 'error' => null, 'required' => false])

<div>
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        {{ $label }} {{ $required ? '' : '' }}
    </label>
    <textarea id="{{ $name }}" name="{{ $name }}" rows="{{ $rows }}"
        class="mt-1 block w-full rounded-md border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white {{ $error ? 'border-red-500' : '' }}"
        x-model="{{ $model }}" wire:model.defer="{{ $name }}" {{ $required ? 'required' : '' }}></textarea>
    @if ($error)
        <p class="mt-2 text-sm text-red-600">{{ $error }}</p>
    @endif
</div>
