@props(['name', 'label', 'model', 'error' => null])

<div class="mb-4">
    <label for="{{ $name }}"
        class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">{{ $label }}:</label>
    <input type="text" x-model="{{ $model }}"
        @input="
            // Solo permitir letras y espacios
            $event.target.value = $event.target.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
            // Capitalizar primera letra de cada palabra
            let words = $event.target.value.toLowerCase().split(' ');
            words = words.map(word => word.charAt(0).toUpperCase() + word.slice(1));
            $event.target.value = words.join(' ');
            {{ $model }} = $event.target.value;
            $wire.set('{{ $name }}', $event.target.value);
        "
        @blur="validateField('{{ $name }}')" id="{{ $name }}"
        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-800 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-300 dark:focus:border-blue-600"
        :class="{ 'border-red-500': errors.{{ $name }} }" placeholder="{{ $label }}">
    <div class="text-red-500 text-xs mt-1" x-show="errors.{{ $name }}" x-text="errors.{{ $name }}">
    </div>
    @if ($error)
        <div class="text-red-500 text-xs mt-1">{{ $error }}</div>
    @endif
</div>
