<div class="w-full">
    <!-- Search Form Only -->
    <div class="max-w-xl mx-auto">
        <div class="flex flex-col sm:flex-row gap-2 relative">
            <input wire:model.live.debounce.300ms="query" type="text" placeholder="{{ __('search_articles') }}"
                class="flex-grow px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 transition-shadow text-gray-800">
            @if ($query)
                <button wire:click="clearSearch" class="absolute right-20 top-3 text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            @endif
            <button
                class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                <span class="flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    {{ __('search') }}
                </span>
            </button>
        </div>
    </div>

    <!-- Loading indicator -->
    <div wire:loading class="flex justify-center my-4">
        <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-yellow-500"></div>
    </div>
</div>
