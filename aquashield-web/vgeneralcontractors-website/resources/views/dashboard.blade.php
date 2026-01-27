<x-app-layout>
    {{-- Commenting out Jetstream header to avoid duplicate titles --}}
    {{-- <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('dashboard') }}
        </h2>
    </x-slot> --}}

    <x-welcome title="{{ __('dashboard_title') }}" subtitle="{{ __('dashboard_subtitle') }}" />
</x-app-layout>
