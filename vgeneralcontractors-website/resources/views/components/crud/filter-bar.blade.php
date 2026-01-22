@props([
    'entityName' => 'Item',
    'showSearchBar' => true,
    'showInactiveToggle' => true,
    'showPerPage' => true,
    'showExport' => true,
    'showDateRange' => true,
    'perPageOptions' => [5, 10, 15, 25, 50],
    'defaultPerPage' => 10,
    'addButtonId' => 'addEntityBtn',
    'searchId' => 'searchInput',
    'searchPlaceholder' => 'Search...',
    'showDeletedId' => 'showDeleted',
    'showDeletedLabel' => 'Show Inactive Items',
    'perPageId' => 'perPage',
    'createButtonId' => 'createBtn',
    'addNewLabel' => 'Add New',
    'exportId' => 'exportSelect',
    'exportLabel' => 'Export Data',
    'dateRangeStartId' => 'dateRangeStart',
    'dateRangeEndId' => 'dateRangeEnd',
    'clearDatesId' => 'clearDates',
    'dateRangeLabel' => 'Date Range',
    'startDateLabel' => 'Start Date',
    'endDateLabel' => 'End Date',
    'clearDatesLabel' => 'Clear Dates',
    'managerName' => 'crudManager',
])



<div class="glassmorphism-filter-container">
    <!-- Main Filter Bar - Siempre Visible -->
    <div class="flex flex-col sm:flex-row justify-between items-center gap-3 p-4">
        <!-- Izquierda: Search Input y Filtros -->

        <div class="flex flex-col w-full sm:flex-row sm:flex-1 sm:max-w-md items-center gap-4">
            <!-- gap-4 para mayor separación -->
            <!-- Search Input -->
            @if ($showSearchBar)
                <div class="relative flex-1 w-full">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" viewBox="0 0 24 24" class="w-5 h-5 text-gray-200 drop-shadow-md">
                            <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </span>
                    <input type="text" id="{{ $searchId }}" placeholder="{{ $searchPlaceholder }}"
                        class="pl-10 pr-4 py-2.5 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent w-full text-sm text-white bg-black/50 border-white/10 backdrop-blur-sm placeholder-gray-400 transition-all duration-200 text-center sm:text-left placeholder:text-center sm:placeholder:text-left">
                </div>
            @endif

            <!-- Filtros Avanzados Toggle Button - Debajo del search en mobile, a la derecha en desktop -->
            @if ($showInactiveToggle || $showPerPage || $showExport || $showDateRange)
                <div class="w-full sm:w-auto">
                    <button id="toggleFilters" type="button"
                        class="inline-flex items-center justify-center px-3 py-2.5 border border-white/10 rounded-lg shadow-sm bg-black/50 text-white hover:bg-black/60 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all duration-200 backdrop-blur-sm w-full sm:w-auto mb-3 sm:mb-0">
                        <span class="flex items-center justify-center w-full">
                            <svg id="filterIcon" class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z" />
                            </svg>
                            <span id="filterText" class="text-sm font-medium">{{ __('filters') }}</span>
                            <svg id="chevronIcon" class="w-4 h-4 ml-1.5 transform transition-transform duration-200"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </span>
                    </button>
                </div>
            @endif
        </div>

        <!-- Derecha: Botón Crear -->
        <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto justify-end">
            <!-- Create Button -->
            <x-crud.button-create :id="$createButtonId" :label="$addNewLabel" :entity-name="$entityName" class="w-full sm:w-auto" />
        </div>
    </div>

    <!-- Collapsible Advanced Filters Section -->
    @if ($showInactiveToggle || $showPerPage || $showExport || $showDateRange)
        <div id="advancedFilters" class="hidden glassmorphism-filter-advanced">
            <div class="p-4">
                <!-- Responsive Grid Layout -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mb-4">

                    <!-- Date Range Section - Full width on mobile, spans 2 columns on larger screens -->
                    @if ($showDateRange)
                        <div class="md:col-span-2 lg:col-span-2 xl:col-span-2 flex flex-col justify-start h-full">
                            <div class="space-y-3 w-full">
                                <label
                                    class="flex items-center gap-1 text-sm font-medium text-gray-300 justify-center text-center md:justify-start md:text-left w-full">
                                    📅 {{ __('date_range') }}
                                </label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <!-- Start Date -->
                                    <div class="relative">
                                        <label for="{{ $dateRangeStartId }}"
                                            class="sr-only">{{ __('start_date') }}</label>
                                        <div class="relative">
                                            <input type="text" id="{{ $dateRangeStartId }}" name="date_range_start"
                                                placeholder="{{ __('start_date') }}" readonly
                                                class="w-full pl-10 pr-4 py-2.5 text-sm text-white bg-black/50 border border-white/10 rounded-lg backdrop-blur-sm placeholder-gray-400 cursor-pointer hover:bg-black/60 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 text-center sm:text-center md:text-left placeholder:text-center sm:placeholder:text-center md:placeholder:text-left glassmorphic-date-input">
                                            <!-- Calendar Icon -->
                                            <span
                                                class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </span>
                                            <!-- Clear Button -->
                                            <button type="button" id="{{ $dateRangeStartId }}_clear"
                                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-white transition-colors duration-200 opacity-0 pointer-events-none">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- End Date -->
                                    <div class="relative">
                                        <label for="{{ $dateRangeEndId }}" class="sr-only">{{ __('end_date') }}</label>
                                        <div class="relative">
                                            <input type="text" id="{{ $dateRangeEndId }}" name="date_range_end"
                                                placeholder="{{ __('end_date') }}" readonly
                                                class="w-full pl-10 pr-4 py-2.5 text-sm text-white bg-black/50 border border-white/10 rounded-lg backdrop-blur-sm placeholder-gray-400 cursor-pointer hover:bg-black/60 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 text-center sm:text-center md:text-left placeholder:text-center sm:placeholder:text-center md:placeholder:text-left glassmorphic-date-input">
                                            <!-- Calendar Icon -->
                                            <span
                                                class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </span>
                                            <!-- Clear Button -->
                                            <button type="button" id="{{ $dateRangeEndId }}_clear"
                                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-white transition-colors duration-200 opacity-0 pointer-events-none">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <!-- Clear Dates Button - Compacto -->
                                <div class="flex justify-center">
                                    <button type="button" id="{{ $clearDatesId }}"
                                        class="px-2 py-1 text-xs font-medium text-gray-400 bg-black/20 border border-white/10 rounded-md hover:bg-black/40 hover:text-white focus:outline-none focus:ring-1 focus:ring-purple-500 focus:border-transparent transition-all duration-200 backdrop-blur-sm">
                                        🗑️ {{ $clearDatesLabel }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Export Options (moved up) -->
                    @if ($showExport)
                        <div class="flex flex-col justify-start h-full w-full">
                            <label for="{{ $exportId }}"
                                class="flex items-center gap-1 text-sm font-medium text-gray-300 mb-3 justify-center text-center md:justify-start md:text-left w-full">
                                📋 {{ __('export_data') }}
                            </label>
                            <div class="relative w-full">
                                <select id="{{ $exportId }}"
                                    class="border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm py-2.5 px-3 w-full text-white bg-black/50 border-white/10 backdrop-blur-sm appearance-none cursor-pointer hover:bg-black/60 transition-all duration-200 text-center sm:text-center md:text-left glassmorphic-select">
                                    <option value="" disabled selected
                                        class="text-center md:text-left glassmorphic-option">
                                        {{ __('choose_format') }}</option>
                                    <option value="pdf" class="text-center md:text-left glassmorphic-option">📄
                                        {{ __('pdf_report') }}
                                    </option>
                                    <option value="excel" class="text-center md:text-left glassmorphic-option">📊
                                        {{ __('excel') }}
                                    </option>
                                </select>
                                <!-- Dropdown Arrow -->
                                <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg fill="none" stroke="currentColor" stroke-linecap="round"
                                        stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24"
                                        class="w-4 h-4 text-gray-400">
                                        <path d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </span>
                            </div>
                        </div>
                    @endif

                    <!-- Per Page Selector -->
                    @if ($showPerPage)
                        <div class="flex flex-col justify-start w-full">
                            <label for="{{ $perPageId }}"
                                class="text-sm font-medium text-gray-300 mb-3 text-center md:text-left w-full">📄
                                {{ __('items_per_page') }}</label>
                            <select id="{{ $perPageId }}"
                                class="border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm py-2.5 px-3 text-white bg-black/50 border-white/10 backdrop-blur-sm transition-all duration-200 hover:bg-black/60 text-center sm:text-center md:text-left w-full glassmorphic-select">
                                @foreach ($perPageOptions as $option)
                                    <option value="{{ $option }}"
                                        class="text-center md:text-left glassmorphic-option"
                                        {{ $option == $defaultPerPage ? 'selected' : '' }}>
                                        {{ $option }} {{ __('per_page') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <!-- Status Toggle Only, no label (moved down) -->
                    @if ($showInactiveToggle)
                        <div class="flex flex-col justify-start h-full w-full lg:w-auto">
                            <!-- Espaciador para alinear con otros labels -->
                            <div class="h-6 mb-3"></div>
                            <div class="w-full flex justify-center md:justify-start">
                                <x-crud.toggle-show-deleted :id="$showDeletedId" :label="$showDeletedLabel" :manager-name="$managerName"
                                    class="mx-auto md:ml-0" />
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Clear Filters Button - Ahora centrado en mobile, a la derecha en desktop -->
                <div
                    class="pt-6 border-t border-white/10 flex justify-center sm:justify-end sticky bottom-0 bg-black/80 backdrop-filter backdrop-blur-md pb-4">
                    <button id="clearFilters" type="button"
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-white bg-red-500/30 hover:bg-red-500/40 border border-red-400/30 rounded-lg shadow-lg backdrop-blur-md transition-all duration-200 hover:shadow-red-500/20 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-red-500/50 focus:ring-offset-2 focus:ring-offset-transparent w-full sm:w-auto">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span class="font-medium">{{ __('clear_filters') }}</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
    /* Premium Glassmorphism Filter Bar - Matching Table Design */
    .glassmorphism-filter-container {
        position: relative;
        margin: 1rem 0 2.5rem 0;
        /* Added extra margin-bottom for spacing below filter bar */
        border-radius: 20px;

        /* Crystal Glass Background with Premium Transparency - Same as table */
        background: rgba(0, 0, 0, 0.78);

        /* Premium Purple Box Shadow System - Exactly same as table */
        box-shadow:
            0 8px 32px 0 rgba(138, 43, 226, 0.25),
            0 16px 64px 0 rgba(128, 0, 255, 0.18),
            0 4px 16px 0 rgba(75, 0, 130, 0.3),
            0 2px 8px 0 rgba(147, 51, 234, 0.2),
            inset 0 1px 0 rgba(255, 255, 255, 0.15),
            inset 0 -1px 0 rgba(255, 255, 255, 0.08);

        /* Advanced Blur Effects - Same as table */
        backdrop-filter: blur(20px) saturate(1.3);
        -webkit-backdrop-filter: blur(20px) saturate(1.3);

        /* Refined Border for Glass Effect - Same as table */
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-top: 1px solid rgba(255, 255, 255, 0.25);

        /* Enhanced Animation - Same as table */
        animation: fadeInUp 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Shimmer effect overlay - Same as table */
    .glassmorphism-filter-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        border-radius: 20px;
        background: linear-gradient(135deg,
                rgba(255, 255, 255, 0.1) 0%,
                rgba(255, 255, 255, 0.05) 25%,
                transparent 50%,
                rgba(138, 43, 226, 0.08) 75%,
                rgba(128, 0, 255, 0.12) 100%);
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.4s ease;
    }

    /* Hover effects - Same as table */
    .glassmorphism-filter-container:hover {
        transform: translateY(-3px);
        box-shadow:
            0 12px 48px 0 rgba(138, 43, 226, 0.35),
            0 24px 80px 0 rgba(128, 0, 255, 0.25),
            0 6px 24px 0 rgba(75, 0, 130, 0.4),
            0 3px 12px 0 rgba(147, 51, 234, 0.3),
            inset 0 1px 0 rgba(255, 255, 255, 0.2),
            inset 0 -1px 0 rgba(255, 255, 255, 0.1);
    }

    .glassmorphism-filter-container:hover::before {
        opacity: 1;
    }

    /* Advanced Filters Section - Enhanced styling */
    .glassmorphism-filter-advanced {
        border-top: 1px solid rgba(255, 255, 255, 0.15);
        background: rgba(0, 0, 0, 0.82);

        /* Enhanced Purple Box Shadow for Advanced Section */
        box-shadow:
            0 6px 24px 0 rgba(138, 43, 226, 0.22),
            0 12px 48px 0 rgba(128, 0, 255, 0.15),
            0 2px 12px 0 rgba(75, 0, 130, 0.25),
            0 1px 6px 0 rgba(147, 51, 234, 0.18),
            inset 0 1px 0 rgba(255, 255, 255, 0.12),
            inset 0 -1px 0 rgba(255, 255, 255, 0.06);

        /* Enhanced Blur Effects */
        backdrop-filter: blur(16px) saturate(1.2);
        -webkit-backdrop-filter: blur(16px) saturate(1.2);

        /* Premium Glass Border */
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        border-left: 1px solid rgba(255, 255, 255, 0.1);
        border-right: 1px solid rgba(255, 255, 255, 0.1);
        border-bottom-left-radius: 20px;
        border-bottom-right-radius: 20px;

        position: relative;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Shimmer effect for advanced filters */
    .glassmorphism-filter-advanced::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        border-radius: 0 0 20px 20px;
        background: linear-gradient(135deg,
                rgba(255, 255, 255, 0.08) 0%,
                rgba(255, 255, 255, 0.03) 25%,
                transparent 50%,
                rgba(138, 43, 226, 0.06) 75%,
                rgba(128, 0, 255, 0.1) 100%);
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.4s ease;
    }

    .glassmorphism-filter-advanced:hover::after {
        opacity: 1;
    }

    /* Enhanced Flatpickr Styles */
    .flatpickr-calendar {
        background: rgba(0, 0, 0, 0.8) !important;
        backdrop-filter: blur(20px) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        border-radius: 12px !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 30px rgba(147, 51, 234, 0.1) !important;
    }

    .flatpickr-calendar:before,
    .flatpickr-calendar:after {
        display: none !important;
    }

    .flatpickr-months {
        background: transparent !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
    }

    .flatpickr-month {
        background: transparent !important;
        color: white !important;
    }

    .flatpickr-current-month .flatpickr-monthDropdown-months {
        background: rgba(0, 0, 0, 0.8) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        color: white !important;
    }

    .flatpickr-current-month .numInputWrapper {
        color: white !important;
    }

    .flatpickr-current-month input.cur-year {
        background: transparent !important;
        color: white !important;
    }

    .flatpickr-weekdays {
        background: transparent !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
    }

    .flatpickr-weekday {
        background: transparent !important;
        color: rgba(255, 255, 255, 0.7) !important;
        font-weight: 500 !important;
    }

    .flatpickr-days {
        background: transparent !important;
    }

    .flatpickr-day {
        background: transparent !important;
        color: white !important;
        border: 1px solid transparent !important;
        border-radius: 6px !important;
        margin: 1px !important;
    }

    .flatpickr-day:hover {
        background: rgba(147, 51, 234, 0.2) !important;
        border-color: rgba(147, 51, 234, 0.4) !important;
    }

    .flatpickr-day.selected,
    .flatpickr-day.startRange,
    .flatpickr-day.endRange {
        background: rgba(147, 51, 234, 0.6) !important;
        border-color: rgba(147, 51, 234, 0.8) !important;
        color: white !important;
    }

    .flatpickr-day.inRange {
        background: rgba(147, 51, 234, 0.2) !important;
        border-color: rgba(147, 51, 234, 0.3) !important;
        color: white !important;
    }

    .flatpickr-day.today {
        border-color: rgba(147, 51, 234, 0.6) !important;
        color: rgba(147, 51, 234, 1) !important;
    }

    .flatpickr-day.today:hover {
        background: rgba(147, 51, 234, 0.2) !important;
        color: white !important;
    }

    .flatpickr-day.disabled {
        color: rgba(255, 255, 255, 0.3) !important;
    }

    .flatpickr-prev-month,
    .flatpickr-next-month {
        color: white !important;
        fill: white !important;
    }

    .flatpickr-prev-month:hover,
    .flatpickr-next-month:hover {
        color: rgba(147, 51, 234, 1) !important;
        fill: rgba(147, 51, 234, 1) !important;
    }

    /* Clear button visibility */
    .date-input-active .opacity-0 {
        opacity: 1 !important;
        pointer-events: auto !important;
    }

    /* Glassmorphic Select and Option Styles */
    .glassmorphic-select {
        /* Enhanced background with deeper transparency */
        background: rgba(0, 0, 0, 0.85) !important;

        /* Text color gray for both dark and light mode */
        color: rgba(156, 163, 175, 1) !important;
        /* text-gray-400 */

        /* Premium box shadow with purple accents */
        box-shadow:
            0 4px 16px 0 rgba(138, 43, 226, 0.15),
            0 8px 32px 0 rgba(128, 0, 255, 0.1),
            0 2px 8px 0 rgba(75, 0, 130, 0.2),
            inset 0 1px 0 rgba(255, 255, 255, 0.1),
            inset 0 -1px 0 rgba(255, 255, 255, 0.05);

        /* Enhanced blur effects */
        backdrop-filter: blur(16px) saturate(1.2);
        -webkit-backdrop-filter: blur(16px) saturate(1.2);

        /* Refined border */
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        border-top: 1px solid rgba(255, 255, 255, 0.2) !important;
    }

    .glassmorphic-select:hover {
        background: rgba(0, 0, 0, 0.9) !important;
        box-shadow:
            0 6px 24px 0 rgba(138, 43, 226, 0.2),
            0 12px 48px 0 rgba(128, 0, 255, 0.15),
            0 3px 12px 0 rgba(75, 0, 130, 0.25),
            inset 0 1px 0 rgba(255, 255, 255, 0.15),
            inset 0 -1px 0 rgba(255, 255, 255, 0.08);
        transform: translateY(-1px);
    }

    .glassmorphic-select:focus {
        background: rgba(0, 0, 0, 0.9) !important;
        box-shadow:
            0 8px 32px 0 rgba(138, 43, 226, 0.3),
            0 16px 64px 0 rgba(128, 0, 255, 0.2),
            0 4px 16px 0 rgba(75, 0, 130, 0.3),
            0 0 20px 0 rgba(147, 51, 234, 0.25),
            inset 0 1px 0 rgba(255, 255, 255, 0.2),
            inset 0 -1px 0 rgba(255, 255, 255, 0.1);
    }

    /* Enhanced Option Styles */
    .glassmorphic-option {
        /* Dark glassmorphic background */
        background: rgba(0, 0, 0, 0.95) !important;
        color: rgba(255, 255, 255, 0.9) !important;

        /* Enhanced border and padding */
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        padding: 8px 12px !important;
        margin: 2px 0 !important;

        /* Subtle shadow for depth */
        box-shadow:
            0 2px 8px 0 rgba(0, 0, 0, 0.3),
            0 1px 4px 0 rgba(138, 43, 226, 0.1),
            inset 0 1px 0 rgba(255, 255, 255, 0.08);

        /* Smooth transitions */
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }

    .glassmorphic-option:hover {
        background: rgba(138, 43, 226, 0.2) !important;
        color: rgba(255, 255, 255, 1) !important;
        border-color: rgba(138, 43, 226, 0.4) !important;
        box-shadow:
            0 4px 16px 0 rgba(138, 43, 226, 0.2),
            0 2px 8px 0 rgba(128, 0, 255, 0.15),
            inset 0 1px 0 rgba(255, 255, 255, 0.15);
        transform: translateY(-1px) !important;
    }

    .glassmorphic-option:checked,
    .glassmorphic-option:selected {
        background: rgba(138, 43, 226, 0.3) !important;
        color: rgba(255, 255, 255, 1) !important;
        border-color: rgba(138, 43, 226, 0.6) !important;
        box-shadow:
            0 4px 16px 0 rgba(138, 43, 226, 0.25),
            0 2px 8px 0 rgba(128, 0, 255, 0.2),
            inset 0 1px 0 rgba(255, 255, 255, 0.2),
            inset 0 -1px 0 rgba(255, 255, 255, 0.1);
        font-weight: 500 !important;
    }

    .glassmorphic-option:disabled {
        background: rgba(0, 0, 0, 0.7) !important;
        color: rgba(255, 255, 255, 0.4) !important;
        border-color: rgba(255, 255, 255, 0.05) !important;
        cursor: not-allowed !important;
        box-shadow: none !important;
    }

    /* Browser-specific dropdown styling */
    .glassmorphic-select option {
        background: rgba(0, 0, 0, 0.95) !important;
        color: rgba(255, 255, 255, 0.9) !important;
    }

    .glassmorphic-select option:hover {
        background: rgba(138, 43, 226, 0.2) !important;
        color: rgba(255, 255, 255, 1) !important;
    }

    .glassmorphic-select option:checked {
        background: rgba(138, 43, 226, 0.3) !important;
        color: rgba(255, 255, 255, 1) !important;
    }

    /* Glassmorphic Date Input Styles - Matching Select Elements */
    .glassmorphic-date-input {
        /* Enhanced background with deeper transparency - Same as selects */
        background: rgba(0, 0, 0, 0.85) !important;

        /* Premium box shadow with purple accents - Same as selects */
        box-shadow:
            0 4px 16px 0 rgba(138, 43, 226, 0.15),
            0 8px 32px 0 rgba(128, 0, 255, 0.1),
            0 2px 8px 0 rgba(75, 0, 130, 0.2),
            inset 0 1px 0 rgba(255, 255, 255, 0.1),
            inset 0 -1px 0 rgba(255, 255, 255, 0.05);

        /* Enhanced blur effects - Same as selects */
        backdrop-filter: blur(16px) saturate(1.2);
        -webkit-backdrop-filter: blur(16px) saturate(1.2);

        /* Refined border - Same as selects */
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        border-top: 1px solid rgba(255, 255, 255, 0.2) !important;
    }

    .glassmorphic-date-input:hover {
        background: rgba(0, 0, 0, 0.9) !important;
        box-shadow:
            0 6px 24px 0 rgba(138, 43, 226, 0.2),
            0 12px 48px 0 rgba(128, 0, 255, 0.15),
            0 3px 12px 0 rgba(75, 0, 130, 0.25),
            inset 0 1px 0 rgba(255, 255, 255, 0.15),
            inset 0 -1px 0 rgba(255, 255, 255, 0.08);
        transform: translateY(-1px);
    }

    .glassmorphic-date-input:focus {
        background: rgba(0, 0, 0, 0.9) !important;
        box-shadow:
            0 8px 32px 0 rgba(138, 43, 226, 0.3),
            0 16px 64px 0 rgba(128, 0, 255, 0.2),
            0 4px 16px 0 rgba(75, 0, 130, 0.3),
            0 0 20px 0 rgba(147, 51, 234, 0.25),
            inset 0 1px 0 rgba(255, 255, 255, 0.2),
            inset 0 -1px 0 rgba(255, 255, 255, 0.1);
    }

    /* Enhanced Animations - Same as table */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Mobile Responsive - Same as table */
    @media (max-width: 768px) {
        .glassmorphism-filter-container {
            margin: 0.5rem 0;
            border-radius: 16px;
        }

        .glassmorphism-filter-container::before {
            border-radius: 16px;
        }

        .glassmorphism-filter-advanced {
            border-bottom-left-radius: 16px;
            border-bottom-right-radius: 16px;
            overflow-x: hidden;
            max-width: 100%;
            padding: 0 0.5rem;
            min-height: 200px;
            /* Asegurar altura mínima */
        }

        .glassmorphism-filter-advanced::after {
            border-radius: 0 0 16px 16px;
        }

        /* Asegurar que los contenedores no se desborden */
        .glassmorphism-filter-advanced .grid {
            grid-template-columns: 1fr;
            width: 100%;
        }

        /* Mejorar visibilidad de botones en mobile */
        #clearFilters {
            width: 100%;
            justify-content: center;
            margin-bottom: 0.5rem;
        }
    }

    /* Dark mode enhancements - Same as table */
    @media (prefers-color-scheme: dark) {
        .glassmorphism-filter-container {
            background: rgba(0, 0, 0, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .glassmorphism-filter-advanced {
            background: rgba(0, 0, 0, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.12);
        }

        .glassmorphic-select {
            color: rgba(156, 163, 175, 1) !important;
            /* text-gray-400 for dark mode */
        }
    }

    /* Light mode enhancements */
    @media (prefers-color-scheme: light) {
        .glassmorphic-select {
            color: rgba(107, 114, 128, 1) !important;
            /* text-gray-500 for light mode */
        }
    }

    /* Ensure gray color is applied regardless of color scheme */
    .glassmorphic-select {
        color: rgba(156, 163, 175, 1) !important;
        /* Default gray color */
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get manager name from component props
        const managerName = '{{ $managerName }}';

        // Initialize date range functionality
        initializeDateRangeFilters();

        // Toggle Advanced Filters (existing code)
        const toggleButton = document.getElementById('toggleFilters');
        const advancedFilters = document.getElementById('advancedFilters');
        const chevronIcon = document.getElementById('chevronIcon');
        const filterText = document.getElementById('filterText');

        if (toggleButton && advancedFilters) {
            toggleButton.addEventListener('click', function() {
                const isHidden = advancedFilters.classList.contains('hidden');

                if (isHidden) {
                    // Show filters
                    advancedFilters.classList.remove('hidden');
                    advancedFilters.style.maxHeight = '0px';
                    advancedFilters.style.overflow = 'hidden';
                    advancedFilters.style.transition = 'max-height 0.3s ease-out';
                    filterText.textContent = @json(__('hide_filters'));

                    // Animate in
                    requestAnimationFrame(() => {
                        // Agregar padding extra para móviles
                        const extraPadding = window.innerWidth < 768 ? 100 : 50;
                        advancedFilters.style.maxHeight = (advancedFilters.scrollHeight +
                            extraPadding) + 'px';
                    });

                    // Update button appearance
                    chevronIcon.style.transform = 'rotate(180deg)';
                    filterText.textContent = 'Hide Filters';
                    toggleButton.classList.add('bg-purple-600/20', 'border-purple-500/30');
                } else {
                    // Hide filters
                    advancedFilters.style.maxHeight = '0px';
                    filterText.textContent = @json(__('filters'));
                    setTimeout(() => {
                        advancedFilters.classList.add('hidden');
                        advancedFilters.style.removeProperty('max-height');
                        advancedFilters.style.removeProperty('overflow');
                        advancedFilters.style.removeProperty('transition');
                    }, 300);

                    // Update button appearance
                    chevronIcon.style.transform = 'rotate(0deg)';
                    filterText.textContent = 'Filters';
                    toggleButton.classList.remove('bg-purple-600/20', 'border-purple-500/30');
                }
            });
        }

        // Manejar el redimensionamiento de la ventana
        window.addEventListener('resize', function() {
            const advancedFilters = document.getElementById('advancedFilters');
            if (advancedFilters && !advancedFilters.classList.contains('hidden')) {
                // Solo ajustar si los filtros están visibles
                const extraPadding = window.innerWidth < 768 ? 100 : 50;
                advancedFilters.style.maxHeight = (advancedFilters.scrollHeight + extraPadding) + 'px';
            }
        });

        // Enhanced Clear Filters Functionality
        const clearFiltersBtn = document.getElementById('clearFilters');
        if (clearFiltersBtn) {
            clearFiltersBtn.addEventListener('click', function() {
                // Reset search input
                const searchInput = document.getElementById('{{ $searchId }}');
                if (searchInput) searchInput.value = '';

                // Reset per page selector
                const perPageSelect = document.getElementById('{{ $perPageId }}');
                if (perPageSelect) perPageSelect.selectedIndex = 0;

                // Reset export dropdown
                const exportSelect = document.getElementById('{{ $exportId }}');
                if (exportSelect) exportSelect.selectedIndex = 0;

                // Reset show deleted toggle
                const showDeletedToggle = document.getElementById('{{ $showDeletedId }}');
                if (showDeletedToggle && showDeletedToggle.type === 'checkbox') {
                    showDeletedToggle.checked = false;
                }

                // Reset date range pickers
                if (startDatePicker) {
                    startDatePicker.clear();
                    startDatePicker.set('maxDate', null);
                }
                if (endDatePicker) {
                    endDatePicker.clear();
                    endDatePicker.set('minDate', null);
                }

                // Remove active states
                startDateInput?.classList.remove('date-input-active');
                endDateInput?.classList.remove('date-input-active');

                // Show success message
                showClearNotification();
            });
        }

        // Export Functionality (existing code)
        const exportSelect = document.getElementById('{{ $exportId }}');
        if (exportSelect) {
            exportSelect.addEventListener('change', function() {
                const selectedValue = this.value;

                if (selectedValue) {
                    // Add loading state
                    const originalHTML = this.innerHTML;
                    this.innerHTML = '<option value="" disabled selected>' +
                        @json(__('exporting')) + '</option>';
                    this.disabled = true;

                    // Simulate export process
                    setTimeout(() => {
                        // Reset dropdown
                        this.innerHTML = originalHTML;
                        this.disabled = false;
                        this.selectedIndex = 0;

                        // Show success message
                        showExportNotification(selectedValue);
                        handleExport(selectedValue);
                    }, 1000);
                }
            });
        }

        // Notification Functions (existing code)
        function showExportNotification(format) {
            let message = '';
            if (format === 'pdf') {
                message = @json(__('pdf_export_completed'));
            } else if (format === 'excel') {
                message = @json(__('excel_export_completed'));
            } else {
                message = format.toUpperCase() + ' ' + @json(__('export_completed_successfully'));
            }
            showNotification(message, 'success');
        }

        function showClearNotification() {
            showNotification(@json(__('filters_cleared')), 'info');
        }

        function showNotification(message, type = 'success') {
            const bgColor = type === 'success' ? 'bg-green-500' : 'bg-blue-500';
            const notification = document.createElement('div');
            notification.className =
                `fixed top-4 right-4 ${bgColor} text-white px-4 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 max-w-sm`;
            notification.textContent = message;

            document.body.appendChild(notification);

            // Animate in
            requestAnimationFrame(() => {
                notification.style.transform = 'translateY(0)';
                notification.style.opacity = '1';
            });

            // Remove notification after 3 seconds
            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateY(-100%)';
                setTimeout(() => {
                    if (document.body.contains(notification)) {
                        document.body.removeChild(notification);
                    }
                }, 300);
            }, 3000);
        }

        function handleExport(format) {
            console.log(`Exporting data as ${format}`);

            // Get the manager instance using the manager name
            const managerName = '{{ $managerName }}';
            const manager = window[managerName];

            if (!manager) {
                console.error(`Manager ${managerName} not found`);
                return;
            }

            // Get current filters from the manager
            const currentFilters = {
                search: manager.searchTerm || '',
                showDeleted: manager.showDeleted || false,
                startDate: document.getElementById('{{ $dateRangeStartId }}')?.value || '',
                endDate: document.getElementById('{{ $dateRangeEndId }}')?.value || '',
                perPage: document.getElementById('{{ $perPageId }}')?.value || 10
            };

            // Build URL with filters
            let exportUrl = '';
            if (format === 'excel' && manager.routes?.exportExcel) {
                exportUrl = manager.routes.exportExcel;
            } else if (format === 'pdf' && manager.routes?.exportPdf) {
                exportUrl = manager.routes.exportPdf;
            } else {
                console.error(`Export route not found for format: ${format}`);
                return;
            }

            // Add filters as query parameters
            const params = new URLSearchParams();
            if (currentFilters.search) params.append('search', currentFilters.search);
            if (currentFilters.showDeleted) params.append('show_deleted', '1');
            if (currentFilters.startDate) params.append('start_date', currentFilters.startDate);
            if (currentFilters.endDate) params.append('end_date', currentFilters.endDate);
            if (currentFilters.perPage) params.append('per_page', currentFilters.perPage);

            const finalUrl = exportUrl + (params.toString() ? '?' + params.toString() : '');

            // Trigger download
            window.open(finalUrl, '_blank');
        }

        // Initialize Date Range Functionality
        function initializeDateRangeFilters() {
            const startDateInput = document.getElementById('{{ $dateRangeStartId }}');
            const endDateInput = document.getElementById('{{ $dateRangeEndId }}');

            if (!startDateInput || !endDateInput) {
                console.warn('Date range input elements not found');
                return;
            }

            // Initialize Flatpickr for start date
            const startDatePicker = flatpickr(startDateInput, {
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'F j, Y',
                allowInput: false,
                clickOpens: true,
                placeholder: '{{ __('start_date') }}',
                onChange: function(selectedDates, dateStr, instance) {
                    handleDateFilterChange();
                    toggleClearButtonVisibility();
                }
            });

            // Initialize Flatpickr for end date
            const endDatePicker = flatpickr(endDateInput, {
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'F j, Y',
                allowInput: false,
                clickOpens: true,
                placeholder: '{{ __('end_date') }}',
                onChange: function(selectedDates, dateStr, instance) {
                    handleDateFilterChange();
                    toggleClearButtonVisibility();
                }
            });

            // Handle date filter changes
            function handleDateFilterChange() {
                const startDate = startDatePicker.selectedDates[0];
                const endDate = endDatePicker.selectedDates[0];

                // Validate date range
                if (startDate && endDate && endDate < startDate) {
                    showNotification('{{ __('end_date_cannot_be_earlier') }}', 'error');
                    endDatePicker.clear();
                    return;
                }

                // Update date picker constraints
                if (startDate) {
                    endDatePicker.set('minDate', startDate);
                } else {
                    endDatePicker.set('minDate', null);
                }

                if (endDate) {
                    startDatePicker.set('maxDate', endDate);
                } else {
                    startDatePicker.set('maxDate', null);
                }

                // Apply filters to CrudManager
                if (window[managerName] && typeof window[managerName].applyDateFilters === 'function') {
                    const startDateStr = startDate ? startDatePicker.formatDate(startDate, 'Y-m-d') : '';
                    const endDateStr = endDate ? endDatePicker.formatDate(endDate, 'Y-m-d') : '';

                    console.log('Applying date filters via CrudManager:', {
                        startDateStr,
                        endDateStr
                    });
                    window[managerName].applyDateFilters(startDateStr, endDateStr);
                } else {
                    console.warn(`CrudManager ${managerName} not found or doesn't support date filters`);
                }
            }

            // Clear dates functionality
            const clearDatesBtn = document.getElementById('{{ $clearDatesId }}');
            if (clearDatesBtn) {
                clearDatesBtn.addEventListener('click', function() {
                    startDatePicker.clear();
                    endDatePicker.clear();
                    startDatePicker.set('maxDate', null);
                    endDatePicker.set('minDate', null);

                    // Clear filters in CrudManager
                    if (window[managerName] && typeof window[managerName].clearDateFilters ===
                        'function') {
                        window[managerName].clearDateFilters();
                    }

                    toggleClearButtonVisibility();
                    showNotification('{{ __('date_filters_cleared') }}', 'info');
                });
            }

            // Individual clear buttons
            const startClearBtn = document.getElementById('{{ $dateRangeStartId }}_clear');
            const endClearBtn = document.getElementById('{{ $dateRangeEndId }}_clear');

            if (startClearBtn) {
                startClearBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    startDatePicker.clear();
                    startDatePicker.set('maxDate', null);
                    handleDateFilterChange();
                    toggleClearButtonVisibility();
                });
            }

            if (endClearBtn) {
                endClearBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    endDatePicker.clear();
                    endDatePicker.set('minDate', null);
                    handleDateFilterChange();
                    toggleClearButtonVisibility();
                });
            }

            // Toggle clear button visibility
            function toggleClearButtonVisibility() {
                const hasStartDate = startDateInput.value && startDateInput.value.trim() !== '';
                const hasEndDate = endDateInput.value && endDateInput.value.trim() !== '';

                // Individual clear buttons
                if (startClearBtn) {
                    startClearBtn.style.display = hasStartDate ? 'block' : 'none';
                }
                if (endClearBtn) {
                    endClearBtn.style.display = hasEndDate ? 'block' : 'none';
                }

                // Main clear button
                if (clearDatesBtn) {
                    clearDatesBtn.style.display = (hasStartDate || hasEndDate) ? 'block' : 'none';
                }
            }

            // Initial setup
            toggleClearButtonVisibility();
        }

        // Initialize date range if elements exist
        if (document.getElementById('{{ $dateRangeStartId }}')) {
            console.log('Initializing date range filters for manager:', managerName);
        } else {
            console.warn('Date range elements not found - skipping initialization');
        }
    });
</script>
