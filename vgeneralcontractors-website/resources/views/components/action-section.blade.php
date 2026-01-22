<div {{ $attributes->merge(['class' => 'md:grid md:grid-cols-3 md:gap-6']) }}>
    <x-section-title>
        <x-slot name="title">{{ $title }}</x-slot>
        <x-slot name="description">{{ $description }}</x-slot>
    </x-section-title>

    <div class="mt-6 md:mt-0 md:col-span-2">
        <div class="px-6 py-6 sm:p-6 bg-white dark:bg-gray-800 shadow rounded-lg sm:rounded-lg">
            {{ $content }}
        </div>
    </div>
</div>
