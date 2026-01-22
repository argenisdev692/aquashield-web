@props(['name', 'label', 'model', 'error' => null])

<div class="mb-4">
    <label for="{{ $name }}"
        class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">{{ $label }}:</label>
    <input type="email" x-model="{{ $model }}"
        @input="$wire.set('{{ $name }}', $event.target.value); validateEmail($event.target.value);"
        @blur="checkEmailAvailability($event.target.value)" id="{{ $name }}"
        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline"
        :class="{ 'border-red-500': errors.{{ $name }} }" placeholder="Enter {{ $label }}">
    @if ($error)
        <div class="text-red-500 text-xs mt-1">{{ $error }}</div>
    @endif
</div>
