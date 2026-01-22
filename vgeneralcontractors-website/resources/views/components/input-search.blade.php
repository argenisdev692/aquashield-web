@props(['wireModel' => 'search'])

<div class="w-full md:w-1/3">
    <label for="search" class="sr-only">{{ __('search') }}</label>
    <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                    clip-rule="evenodd"></path>
            </svg>
        </div>
        <input id="search" wire:model.live.debounce.300ms="search"
            class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-800 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:placeholder-gray-400 dark:focus:placeholder-gray-500 focus:border-blue-300 dark:focus:border-blue-600 focus:ring focus:ring-blue-200 dark:focus:ring-blue-700 focus:ring-opacity-50 sm:text-sm"
            placeholder="{{ __('search') }}" type="search">
    </div>
</div>
