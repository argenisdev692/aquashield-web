@props(['name', 'label', 'model', 'error' => null])

<div class="mb-4">
    <label for="{{ $name }}"
        class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">{{ $label }}:</label>
    <input type="tel" x-model="{{ $model }}"
        @input="
            let value = $event.target.value.replace(/\D/g, '').substring(0, 10);
            if (value.length === 0) {
                {{ $model }} = '';
            } else if (value.length <= 3) {
                {{ $model }} = `(${value}`;
            } else if (value.length <= 6) {
                {{ $model }} = `(${value.substring(0,3)}) ${value.substring(3)}`;
            } else {
                {{ $model }} = `(${value.substring(0,3)}) ${value.substring(3,6)}-${value.substring(6)}`;
            }
            $wire.set('{{ $name }}', {{ $model }});
        "
        @blur="validatePhone({{ $model }}); if ({{ $model }}) { checkPhoneAvailability({{ $model }}); }"
        id="{{ $name }}"
        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline"
        :class="{ 'border-red-500': errors.{{ $name }} }" placeholder="(XXX) XXX-XXXX">
    <div class="text-red-500 text-xs mt-1" x-show="errors.{{ $name }}" x-text="errors.{{ $name }}">
    </div>
    @if ($error)
        <div class="text-red-500 text-xs mt-1">{{ $error }}</div>
    @endif
</div>
