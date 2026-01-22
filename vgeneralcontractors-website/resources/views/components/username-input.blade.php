@props(['name', 'label', 'model', 'mode', 'error' => null])

<div class="mb-4">
    <label for="{{ $name }}"
        class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">{{ $label }}</label>
    <div class="relative">
        @if ($mode === 'store')
            <input type="text" id="{{ $name }}" disabled
                placeholder="Will be automatically generated from name and lastname"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline bg-gray-100">
            <div class="mt-1 text-xs text-gray-500 italic">
                Example: If name is "John Doe", username will be something like "johnd123"
            </div>
        @else
            <input type="text" id="{{ $name }}" x-model="{{ $model }}"
                @input="$wire.set('{{ $name }}', $event.target.value); validateUsername($event.target.value);"
                @blur="checkUsernameAvailability($event.target.value)"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                :class="{ 'border-red-500': errors.{{ $name }} }">
            <div class="mt-1 text-xs text-gray-500 italic">
                Username must be at least 7 characters and contain at least 2 numbers
            </div>
            @if ($error)
                <div class="text-red-500 text-xs mt-1">{{ $error }}</div>
            @endif
        @endif
    </div>
</div>
