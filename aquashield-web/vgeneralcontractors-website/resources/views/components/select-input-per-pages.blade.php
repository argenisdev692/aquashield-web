@props(['options' => [], 'name', 'id' => null, 'label' => null, 'wireModel' => null])

<div class="w-full {{ isset($attributes['class']) ? $attributes['class'] : '' }}">
    @if ($label)
        <label for="{{ $id ?? $name }}"
            class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">{{ $label }}:</label>
    @endif
    <select id="{{ $id ?? $name }}" name="{{ $name }}" {{ $wireModel ? "wire:model.live=\"$wireModel\"" : '' }}
        {{ $attributes->except(['class']) }}
        class="block w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring focus:ring-blue-200 dark:focus:ring-blue-700 focus:ring-opacity-50 focus:border-blue-300 dark:focus:border-blue-600 sm:text-sm">
        {{ $slot }}
    </select>
</div>
