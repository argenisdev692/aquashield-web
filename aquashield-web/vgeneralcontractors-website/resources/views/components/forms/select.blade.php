@props([
    'label',
    'name',
    'id',
    'required' => false,
    'options' => [],
    'value' => '',
    'errorId' => null,
    'class' =>
        'mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-900 dark:text-gray-100 dark:bg-gray-700',
    'labelClass' => 'block text-sm font-medium text-gray-700 dark:text-gray-300',
    'errorClass' => 'text-red-500 text-xs italic mt-1 hidden',
])

<div class="mb-4">
    <label for="{{ $id }}" class="{{ $labelClass }}">
        {{ $label }} @if ($required)
            <span class="text-red-500">*</span>
        @endif
    </label>
    <select name="{{ $name }}" id="{{ $id }}" @if ($required) required @endif
        class="{{ $class }}" {{ $attributes }}>
        <option value="">Select {{ $label }}</option>
        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" {{ old($name, $value) == $optionValue ? 'selected' : '' }}>
                {{ $optionLabel }}
            </option>
        @endforeach
    </select>
    @if ($errorId)
        <span id="{{ $errorId }}" class="{{ $errorClass }}"></span>
    @endif
</div>
