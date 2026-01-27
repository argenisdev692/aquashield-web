<x-app-layout>
    {{-- Commenting out Jetstream header to avoid duplicate headers --}}
    {{-- <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Appointments Management') }}
        </h2>
    </x-slot> --}}

    {{-- Dark background container with consistent styling --}}
    <div class="min-h-screen ">
        {{-- Enhanced Animated Header Section --}}
        <div class="p-4 sm:p-6">
            <div class="animated-header-card rounded-2xl shadow-2xl mb-8 overflow-hidden">
                <div class="animated-gradient-header px-8 py-6 relative">
                    {{-- Floating particles background --}}
                    <div class="absolute inset-0 overflow-hidden pointer-events-none">
                        <div class="absolute top-10 left-20 w-2 h-2 bg-white/20 rounded-full animate-float-slow"></div>
                        <div class="absolute top-20 right-32 w-1 h-1 bg-purple-200/30 rounded-full animate-float-medium">
                        </div>
                        <div class="absolute bottom-16 left-40 w-3 h-3 bg-blue-200/20 rounded-full animate-float-fast">
                        </div>
                        <div class="absolute bottom-8 right-20 w-2 h-2 bg-white/15 rounded-full animate-float-slow">
                        </div>
                        <div
                            class="absolute top-32 left-60 w-1.5 h-1.5 bg-emerald-200/25 rounded-full animate-float-medium">
                        </div>
                        <div
                            class="absolute bottom-32 right-60 w-2.5 h-2.5 bg-indigo-200/20 rounded-full animate-float-fast">
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between relative z-10">
                        <div class="mb-4 sm:mb-0 w-full">
                            <h1 class="text-2xl xs:text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-2 tracking-tight text-center sm:text-left"
                                style="text-shadow: 0 4px 8px rgba(0, 0, 0, 0.4), 0 2px 4px rgba(0, 0, 0, 0.3);">
                                {{ __('appointments_management_title') }}
                            </h1>

                            {{-- Animated subtitle with marquee effect --}}
                            <div
                                class="bg-white/10 backdrop-blur-md rounded-lg px-2 py-2 border border-white/20 max-w-full sm:max-w-md mx-auto sm:mx-0 text-center">
                                <div class="marquee-container overflow-hidden w-full">
                                    <div
                                        class="marquee-text animate-marquee whitespace-nowrap text-purple-100/90 text-xs xs:text-sm sm:text-sm font-medium text-center">
                                        ✨ {{ __('appointments_management_subtitle') }} • 🚀
                                        {{ __('efficient_management') }} • 💼
                                        {{ __('professional_organization') }} • 📊 {{ __('complete_tracking') }} •
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Animated icon section --}}
                        <div class="flex items-center space-x-4">
                            {{-- Primary animated icon --}}
                            <div class="relative">
                                <div
                                    class="w-14 h-14 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center animate-pulse-soft border border-white/30">
                                    <svg class="w-7 h-7 text-white animate-bounce-subtle" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                        </path>
                                    </svg>
                                </div>
                                {{-- Animated ring --}}
                                <div class="absolute inset-0 border-2 border-white/30 rounded-xl animate-ping-slow">
                                </div>
                            </div>

                            {{-- Create appointment button --}}
                            <a href="{{ route('appointments.create') }}"
                                class="inline-flex items-center px-6 py-3 bg-white/20 backdrop-blur-md border border-white/30 rounded-xl font-bold text-base text-white uppercase tracking-wide hover:bg-white/30 focus:bg-white/30 active:bg-white/40 focus:outline-none focus:ring-2 focus:ring-white/50 focus:ring-offset-2 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4">
                                    </path>
                                </svg>
                                {{ __('create_appointment') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto py-2 sm:py-4 md:py-2 lg:py-2 px-4 sm:px-6 lg:px-8">
            <!-- Success and error messages -->
            <div id="alertContainer"></div>
            @if (session()->has('message'))
                <x-alerts.success :message="session('message')" />
            @endif
            @if (session()->has('error'))
                <x-alerts.error :message="session('error')" />
            @endif

            <!-- Main container -->
            <div class="glassmorphism-container shadow-xl rounded-lg">
                <div class="p-6">
                    <!-- Filter and action bar -->
                    <div
                        class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 space-y-4 md:space-y-0">
                        <!-- Search input -->
                        <div class="w-full md:w-1/2 lg:w-2/5">
                            <x-crud.input-search id="searchInput" placeholder="{{ __('search_appointments') }}" />
                        </div>

                        <div
                            class="flex flex-col sm:flex-row items-center w-full md:w-auto space-y-3 sm:space-y-0 sm:space-x-4">
                            <!-- Toggle to show inactive appointments -->
                            <x-crud.toggle-deleted id="showDeleted" label="{{ __('show_inactive_appointments') }}" />

                            <!-- Per page dropdown -->
                            <x-select-input-per-pages name="perPage" id="perPage" class="sm:w-32">
                                <option value="5">5 {{ __('per_page') }}</option>
                                <option value="10" selected>10 {{ __('per_page') }}</option>
                                <option value="15">15 {{ __('per_page') }}</option>
                                <option value="25">25 {{ __('per_page') }}</option>
                                <option value="50">50 {{ __('per_page') }}</option>
                            </x-select-input-per-pages>
                        </div>
                    </div>

                    <!-- Date range filters -->
                    <div class="flex flex-col sm:flex-row sm:flex-wrap sm:items-end gap-3 py-5 mb-4">
                        <div class="w-full sm:w-auto">
                            <label for="start_date"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('start_date') }}</label>
                            <input type="date" id="start_date" name="start_date"
                                class="w-full sm:w-44 md:w-48 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                        </div>
                        <div class="w-full sm:w-auto">
                            <label for="end_date"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('end_date') }}</label>
                            <input type="date" id="end_date" name="end_date"
                                class="w-full sm:w-44 md:w-48 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                        </div>
                        <div class="w-full sm:w-auto">
                            <label for="status_lead_filter"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('lead_status') }}</label>
                            <select id="status_lead_filter" name="status_lead_filter"
                                class="w-full sm:w-44 md:w-48 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="">{{ __('all_statuses') }}</option>
                                <option value="New">{{ __('new_status') }}</option>
                                <option value="Called">{{ __('called_status') }}</option>
                                <option value="Pending">{{ __('pending_status') }}</option>
                                <option value="Declined">{{ __('declined_status') }}</option>
                            </select>
                        </div>
                        <button id="clearDateFilters" type="button"
                            class="w-full sm:w-auto px-4 py-2.5 bg-gray-900/20 hover:bg-gray-800/30 text-white/90 hover:text-white text-sm rounded-lg backdrop-blur-md border border-gray-600/30 hover:border-gray-500/50 transition-all duration-300 ease-in-out transform hover:scale-105 shadow-lg hover:shadow-xl font-medium tracking-wide">
                            <i class="fas fa-times-circle mr-1 opacity-70"></i>
                            {{ __('clear') }}
                        </button>
                        <div class="w-full sm:ml-auto flex flex-col sm:flex-row gap-2 sm:space-x-2 sm:gap-0">
                            <button id="sendRejectionBtn" disabled
                                class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-800 focus:outline-none focus:border-red-800 focus:ring focus:ring-red-200 disabled:opacity-25">
                                <span class="mr-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </span>
                                {{ __('send_rejection') }}
                            </button>
                            <button id="exportToExcel"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring focus:ring-blue-200 disabled:opacity-25">
                                <span class="mr-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                </span>
                                {{ __('excel_export') }}
                            </button>
                        </div>
                    </div>

                    <!-- Appointments table -->
                    <div class="overflow-x-auto glassmorphism-container table-container animate-fadeInUp">
                        <table id="appointmentsTable"
                            class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 glassmorphism-table"
                            style="border-collapse: separate; border-spacing: 0;">
                            <thead class="glassmorphism-header animate-shimmer">
                                <tr class="glassmorphism-header-row">
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-purple-300 uppercase tracking-wider glassmorphism-th">
                                        <input type="checkbox" id="selectAll" class="glassmorphism-checkbox">
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-purple-300 uppercase tracking-wider cursor-pointer sort-header glassmorphism-th"
                                        data-field="first_name">
                                        {{ __('name') }}
                                        <span class="sort-icon"></span>
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-purple-300 uppercase tracking-wider cursor-pointer sort-header glassmorphism-th"
                                        data-field="email">
                                        {{ __('email') }}
                                        <span class="sort-icon"></span>
                                    </th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-purple-300 uppercase tracking-wider glassmorphism-th">
                                        {{ __('phone') }}
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-purple-300 uppercase tracking-wider cursor-pointer sort-header glassmorphism-th"
                                        data-field="inspection_date">
                                        {{ __('inspection_date') }}
                                        <span class="sort-icon"></span>
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-purple-300 uppercase tracking-wider cursor-pointer sort-header glassmorphism-th"
                                        data-field="inspection_time">
                                        {{ __('inspection_time') }}
                                        <span class="sort-icon"></span>
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-purple-300 uppercase tracking-wider cursor-pointer sort-header glassmorphism-th"
                                        data-field="insurance_property">
                                        {{ __('insurance') }}
                                        <span class="sort-icon"></span>
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-purple-300 uppercase tracking-wider cursor-pointer sort-header glassmorphism-th"
                                        data-field="status_lead">
                                        {{ __('status_lead') }}
                                        <span class="sort-icon"></span>
                                    </th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-purple-300 uppercase tracking-wider glassmorphism-th">
                                        {{ __('inspection_status') }}
                                    </th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-purple-300 uppercase tracking-wider glassmorphism-th">
                                        {{ __('actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="glassmorphism-body divide-y divide-purple-300/20">
                                <tr id="loadingRow">
                                    <td colspan="10" class="px-6 py-4 text-center">
                                        <svg class="animate-spin h-5 w-5 mr-3 text-blue-500 inline-block"
                                            viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        {{ __('loading_appointments') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div id="pagination" class="mt-4 flex justify-between items-center"></div>
                </div>
            </div>
        </div>

        <!-- Rejection Notification Modal -->
        <div id="rejectionModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title"
            role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div
                    class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100"
                                    id="modal-title">
                                    {{ __('send_rejection_notification') }}
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                    {{ __('select_reason_rejecting') }}
                                </p>

                                <div class="mt-4">
                                    <div class="flex items-start mb-3">
                                        <div class="flex items-center h-5">
                                            <input id="reason_no_contact" name="rejection_reason" type="radio"
                                                value="no_contact"
                                                class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="reason_no_contact"
                                                class="font-medium text-gray-700 dark:text-gray-300">{{ __('unable_to_contact') }}</label>
                                        </div>
                                    </div>

                                    <div class="flex items-start mb-3">
                                        <div class="flex items-center h-5">
                                            <input id="reason_no_insurance" name="rejection_reason" type="radio"
                                                value="no_insurance"
                                                class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="reason_no_insurance"
                                                class="font-medium text-gray-700 dark:text-gray-300">{{ __('no_property_insurance') }}</label>
                                        </div>
                                    </div>

                                    <div class="flex items-start mb-3">
                                        <div class="flex items-center h-5">
                                            <input id="reason_other_option" name="rejection_reason" type="radio"
                                                value="other"
                                                class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="reason_other_option"
                                                class="font-medium text-gray-700 dark:text-gray-300">{{ __('other_reason') }}</label>
                                        </div>
                                    </div>

                                    <div id="other_reason_container" class="mt-4 hidden">
                                        <label for="reason_other"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('specify_other_reason') }}</label>
                                        <textarea id="reason_other" name="reason_other" rows="3"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" id="sendRejectionNotification"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            {{ __('send_notification') }}
                        </button>
                        <button type="button" id="cancelRejection"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                            {{ __('cancel') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <style>
            /* Modern Dark Crystal Index 2025 with Purple Accents - Enhanced Version */
            .glassmorphism-container {
                position: relative;
                margin: 1rem 0;
                border-radius: 16px;
                padding: 1.5rem;
                overflow: hidden;

                /* Dark Crystal Background with enhanced glassmorphism */
                background: linear-gradient(135deg,
                        rgba(17, 17, 17, 0.95) 0%,
                        rgba(30, 30, 30, 0.92) 50%,
                        rgba(20, 20, 20, 0.95) 100%);

                /* Elegant Border with glow effect */
                border: 1px solid rgba(139, 69, 190, 0.3);

                /* Advanced Shadow System */
                box-shadow:
                    0 8px 32px rgba(139, 69, 190, 0.15),
                    0 4px 16px rgba(0, 0, 0, 0.4),
                    inset 0 1px 0 rgba(255, 255, 255, 0.1),
                    0 0 0 1px rgba(139, 69, 190, 0.1);

                /* Enhanced Blur */
                backdrop-filter: blur(15px);
                -webkit-backdrop-filter: blur(15px);

                /* Smooth Animation */
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);

                /* Animated border */
                background-clip: padding-box;
            }

            .glassmorphism-container::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 2px;
                background: linear-gradient(90deg,
                        transparent 0%,
                        rgba(139, 69, 190, 0.8) 25%,
                        rgba(168, 85, 247, 0.9) 50%,
                        rgba(139, 69, 190, 0.8) 75%,
                        transparent 100%);
                opacity: 0.8;
                animation: pulseGlow 3s ease-in-out infinite;
            }

            .glassmorphism-container::after {
                content: '';
                position: absolute;
                top: -2px;
                left: -2px;
                right: -2px;
                bottom: -2px;
                background: linear-gradient(45deg,
                        rgba(139, 69, 190, 0.2),
                        rgba(168, 85, 247, 0.3),
                        rgba(139, 69, 190, 0.2));
                border-radius: 18px;
                z-index: -1;
                opacity: 0;
                transition: opacity 0.3s ease;
            }

            .glassmorphism-container:hover {
                transform: translateY(-2px);
                border-color: rgba(168, 85, 247, 0.5);
                box-shadow:
                    0 12px 40px rgba(139, 69, 190, 0.25),
                    0 6px 20px rgba(0, 0, 0, 0.5),
                    inset 0 1px 0 rgba(255, 255, 255, 0.15),
                    0 0 30px rgba(139, 69, 190, 0.3);
            }

            .glassmorphism-container:hover::after {
                opacity: 1;
            }

            /* Enhanced Table Styling with Advanced Glassmorphism */
            .glassmorphism-container table {
                background: transparent !important;
                border-radius: 12px;
                overflow: hidden;
                border-collapse: separate !important;
                border-spacing: 0 !important;
            }

            /* Correct Glassmorphism Header Styles from generic-advanced-table */
            .glassmorphism-header {
                /* Premium Glass Header Background */
                background: rgba(0, 0, 0, 0.85);

                /* Enhanced Purple Shadow for Header */
                box-shadow:
                    0 2px 16px 0 rgba(138, 43, 226, 0.15),
                    0 4px 24px 0 rgba(128, 0, 255, 0.1),
                    0 1px 6px 0 rgba(75, 0, 130, 0.2),
                    inset 0 1px 0 rgba(255, 255, 255, 0.1);

                /* Advanced Blur for Header */
                backdrop-filter: blur(12px) saturate(1.1);
                -webkit-backdrop-filter: blur(12px) saturate(1.1);

                border-bottom: 1px solid rgba(255, 255, 255, 0.15);
                border-radius: 16px 16px 0 0;
                position: relative;
                transition: all 0.3s ease;
                /* Mejorado el aislamiento del overflow para el shimmer */
                overflow: hidden;
                isolation: isolate;
                /* Crear nuevo contexto de apilamiento */
            }

            /* Shimmer animated effect for table header */
            .glassmorphism-header::after {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                /* Cambiado para usar con transform */
                width: 40%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.18), transparent);
                animation: shimmer-header 2.2s infinite;
                pointer-events: none;
                z-index: 1;
                /* Reducido de 2 a 1 para no interferir con scroll */
                /* Aislamiento del shimmer solo al header */
                clip-path: inset(0 0 0 0);
                will-change: transform;
                transform: translateX(-100%) translateZ(0);
                /* Force hardware acceleration */
            }

            .glassmorphism-header::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: linear-gradient(135deg,
                        rgba(255, 255, 255, 0.12) 0%,
                        rgba(255, 255, 255, 0.06) 25%,
                        transparent 50%,
                        rgba(138, 43, 226, 0.08) 75%,
                        rgba(128, 0, 255, 0.12) 100%);
                border-radius: 16px 16px 0 0;
                pointer-events: none;
                opacity: 0;
                transition: opacity 0.3s ease;
            }

            .glassmorphism-header:hover::before {
                opacity: 1;
            }

            .glassmorphism-th {
                padding: 1rem 1.5rem;
                text-align: center;
                font-size: 0.75rem;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                color: rgba(255, 255, 255, 0.85);
                border-right: none;
                position: relative;
                transition: all 0.3s ease;
            }

            .glassmorphism-th:last-child {
                border-right: none;
            }

            .glassmorphism-th:hover {
                background: rgba(255, 255, 255, 0.12);
                color: rgba(255, 255, 255, 0.98);
                box-shadow:
                    0 2px 8px 0 rgba(138, 43, 226, 0.2),
                    0 4px 16px 0 rgba(128, 0, 255, 0.1);
            }

            /* Header animations */
            @keyframes shimmer-header {
                0% {
                    transform: translateX(-100%);
                }

                100% {
                    transform: translateX(100%);
                }
            }

            .glassmorphism-container table tbody tr {
                background: rgba(30, 30, 30, 0.7) !important;
                border-bottom: 1px solid rgba(139, 69, 190, 0.2) !important;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                animation: fadeInUp 0.6s ease-out forwards;
                opacity: 0;
                transform: translateY(20px);
            }

            .glassmorphism-container table tbody tr:nth-child(odd) {
                animation-delay: 0.1s;
            }

            .glassmorphism-container table tbody tr:nth-child(even) {
                animation-delay: 0.2s;
            }

            .glassmorphism-container table tbody tr:hover {
                background: rgba(139, 69, 190, 0.15) !important;
                transform: translateY(-1px) scale(1.002);
                box-shadow: 0 4px 12px rgba(139, 69, 190, 0.2);
            }

            .glassmorphism-container table tbody td {
                color: #f1f5f9 !important;
                border-color: rgba(139, 69, 190, 0.2) !important;
                padding: 1rem 0.75rem;
                font-weight: 500;
            }

            /* Fix for text colors in table cells */
            .glassmorphism-container table tbody td,
            .glassmorphism-container table tbody td * {
                color: #f1f5f9 !important;
            }

            /* Override dark text colors specifically */
            .glassmorphism-container .text-gray-900,
            .glassmorphism-container .dark\\:text-gray-100 {
                color: #f1f5f9 !important;
            }

            /* Enhanced Input and Button Styling */
            .glassmorphism-container input,
            .glassmorphism-container select,
            .glassmorphism-container textarea {
                background: rgba(40, 40, 40, 0.8) !important;
                border: 1px solid rgba(139, 69, 190, 0.3) !important;
                color: #ffffff !important;
                border-radius: 8px !important;
                padding: 0.75rem 1rem !important;
                transition: all 0.3s ease !important;
                font-weight: 500;
            }

            .glassmorphism-container input::placeholder {
                color: rgba(255, 255, 255, 0.6) !important;
            }

            .glassmorphism-container input:focus,
            .glassmorphism-container select:focus,
            .glassmorphism-container textarea:focus {
                border-color: rgba(168, 85, 247, 0.6) !important;
                box-shadow: 0 0 0 3px rgba(139, 69, 190, 0.2) !important;
                background: rgba(50, 50, 50, 0.9) !important;
            }

            /* Enhanced Checkbox Styling */
            .glassmorphism-container input[type="checkbox"],
            .appointment-checkbox,
            #selectAll {
                appearance: none !important;
                -webkit-appearance: none !important;
                width: 1.25rem !important;
                height: 1.25rem !important;
                border: 2px solid rgba(139, 69, 190, 0.6) !important;
                border-radius: 0.375rem !important;
                background: rgba(40, 40, 40, 0.8) !important;
                cursor: pointer !important;
                position: relative !important;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
                padding: 0 !important;
                margin: 0 auto !important;
                display: block !important;
            }

            .glassmorphism-container input[type="checkbox"]:hover,
            .appointment-checkbox:hover,
            #selectAll:hover {
                border-color: rgba(168, 85, 247, 0.8) !important;
                background: rgba(50, 50, 60, 0.9) !important;
                transform: scale(1.05) !important;
                box-shadow: 0 0 10px rgba(139, 69, 190, 0.3) !important;
            }

            .glassmorphism-container input[type="checkbox"]:checked,
            .appointment-checkbox:checked,
            #selectAll:checked {
                background: linear-gradient(135deg, rgba(139, 69, 190, 0.9), rgba(168, 85, 247, 1)) !important;
                border-color: rgba(168, 85, 247, 1) !important;
            }

            .glassmorphism-container input[type="checkbox"]:checked::after,
            .appointment-checkbox:checked::after,
            #selectAll:checked::after {
                content: "✓" !important;
                position: absolute !important;
                top: 50% !important;
                left: 50% !important;
                transform: translate(-50%, -50%) !important;
                color: #ffffff !important;
                font-size: 0.875rem !important;
                font-weight: bold !important;
                line-height: 1 !important;
            }

            /* Row click interaction styles */
            .glassmorphism-container tbody tr {
                cursor: pointer !important;
                transition: all 0.3s ease !important;
                border-radius: 8px !important;
                margin: 2px 0 !important;
            }

            .glassmorphism-container tbody tr:hover {
                background: rgba(139, 69, 190, 0.1) !important;
                transform: translateY(-1px) !important;
                box-shadow: 0 4px 8px rgba(139, 69, 190, 0.2) !important;
                border-radius: 8px !important;
            }

            .glassmorphism-container tbody tr:hover td {
                background: transparent !important;
            }

            .glassmorphism-container tbody tr.selected {
                background: linear-gradient(135deg, rgba(139, 69, 190, 0.2), rgba(168, 85, 247, 0.15)) !important;
                border-left: 4px solid rgba(168, 85, 247, 0.8) !important;
            }

            /* Enhanced glassmorphism action buttons - Preserve individual colors */
            .glassmorphism-container .action-button,
            .glassmorphism-container a[href*="edit"],
            .glassmorphism-container .delete-btn,
            .glassmorphism-container .restore-btn,
            .glassmorphism-container .share-location {
                /* Don't override background colors - let individual buttons maintain their colors */
                backdrop-filter: blur(12px) !important;
                -webkit-backdrop-filter: blur(12px) !important;
                border: 1px solid rgba(255, 255, 255, 0.1) !important;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
                position: relative !important;
                overflow: hidden !important;
            }

            /* Preserve specific button colors */
            .glassmorphism-container a[href*="edit"] {
                /* Keep blue colors */
                background: rgba(59, 130, 246, 0.4) !important;
                box-shadow: 0 4px 16px rgba(59, 130, 246, 0.2) !important;
            }

            .glassmorphism-container a[href*="edit"]:hover {
                background: rgba(59, 130, 246, 0.6) !important;
                box-shadow: 0 8px 24px rgba(59, 130, 246, 0.4) !important;
            }

            .glassmorphism-container .share-location:not([data-no-coords]) {
                /* Keep green colors */
                background: rgba(34, 197, 94, 0.4) !important;
                box-shadow: 0 4px 16px rgba(34, 197, 94, 0.2) !important;
            }

            .glassmorphism-container .share-location:not([data-no-coords]):hover {
                background: rgba(34, 197, 94, 0.6) !important;
                box-shadow: 0 8px 24px rgba(34, 197, 94, 0.4) !important;
            }

            .glassmorphism-container .restore-btn {
                /* Keep emerald colors */
                background: rgba(16, 185, 129, 0.4) !important;
                box-shadow: 0 4px 16px rgba(16, 185, 129, 0.2) !important;
            }

            .glassmorphism-container .restore-btn:hover {
                background: rgba(16, 185, 129, 0.6) !important;
                box-shadow: 0 8px 24px rgba(16, 185, 129, 0.4) !important;
            }

            .glassmorphism-container .delete-btn {
                /* Keep red colors */
                background: rgba(239, 68, 68, 0.4) !important;
                box-shadow: 0 4px 16px rgba(239, 68, 68, 0.2) !important;
            }

            .glassmorphism-container .delete-btn:hover {
                background: rgba(239, 68, 68, 0.6) !important;
                box-shadow: 0 8px 24px rgba(239, 68, 68, 0.4) !important;
            }

            .glassmorphism-container .share-location[data-no-coords] {
                /* Keep gray colors for disabled */
                background: rgba(107, 114, 128, 0.3) !important;
                box-shadow: 0 4px 16px rgba(107, 114, 128, 0.1) !important;
            }

            .glassmorphism-container .action-button::before,
            .glassmorphism-container a[href*="edit"]::before,
            .glassmorphism-container .delete-btn::before,
            .glassmorphism-container .restore-btn::before,
            .glassmorphism-container .share-location::before {
                content: '' !important;
                position: absolute !important;
                top: 0 !important;
                left: 0 !important;
                right: 0 !important;
                bottom: 0 !important;
                background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), transparent) !important;
                opacity: 0 !important;
                transition: opacity 0.3s ease !important;
            }

            .glassmorphism-container .action-button:hover::before,
            .glassmorphism-container a[href*="edit"]:hover::before,
            .glassmorphism-container .delete-btn:hover::before,
            .glassmorphism-container .restore-btn:hover::before,
            .glassmorphism-container .share-location:hover::before {
                opacity: 1 !important;
            }

            /* Enhanced Button Styling */
            .glassmorphism-container .btn,
            .glassmorphism-container button {
                border-radius: 8px !important;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
                font-weight: 600 !important;
            }

            .glassmorphism-container button:hover {
                transform: translateY(-2px) !important;
                box-shadow: 0 6px 16px rgba(139, 69, 190, 0.25) !important;
            }

            /* Enhanced Label Styling */
            .glassmorphism-container label {
                color: #e2e8f0 !important;
                font-weight: 600 !important;
                margin-bottom: 0.5rem !important;
            }

            /* Enhanced Pagination Styling */
            .glassmorphism-container .pagination {
                background: rgba(40, 40, 40, 0.8) !important;
                border-radius: 12px !important;
                padding: 1rem !important;
            }

            /* Pagination text styling - ensure white text */
            .glassmorphism-container #pagination,
            .glassmorphism-container #pagination *,
            .glassmorphism-container .pagination-info,
            .glassmorphism-container .pagination-text {
                color: rgba(255, 255, 255, 0.95) !important;
            }

            /* Record count text styling */
            .glassmorphism-container .record-info,
            .glassmorphism-container .showing-records,
            .glassmorphism-container .total-records {
                color: rgba(255, 255, 255, 0.9) !important;
                font-weight: 500 !important;
                text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3) !important;
            }

            .glassmorphism-container .pagination button {
                background: rgba(139, 69, 190, 0.2) !important;
                border: 1px solid rgba(139, 69, 190, 0.3) !important;
                color: #ffffff !important;
                margin: 0 0.25rem !important;
                border-radius: 6px !important;
                padding: 0.5rem 1rem !important;
            }

            .glassmorphism-container .pagination button:hover {
                background: rgba(168, 85, 247, 0.4) !important;
                border-color: rgba(168, 85, 247, 0.6) !important;
            }

            .glassmorphism-container .pagination .active {
                background: linear-gradient(135deg, rgba(139, 69, 190, 0.8), rgba(168, 85, 247, 0.9)) !important;
                border-color: rgba(168, 85, 247, 0.8) !important;
            }

            /* Enhanced Search and Filter Styling */
            .glassmorphism-container .search-container {
                background: rgba(30, 30, 30, 0.6) !important;
                border-radius: 12px !important;
                padding: 1.5rem !important;
                border: 1px solid rgba(139, 69, 190, 0.2) !important;
                margin-bottom: 1.5rem !important;
            }

            /* Enhanced Toggle Styling - Preserve switch appearance */
            .glassmorphism-container .toggle-switch {
                background: rgba(40, 40, 40, 0.8) !important;
                border: 1px solid rgba(139, 69, 190, 0.3) !important;
            }

            .glassmorphism-container .toggle-switch:checked {
                background: linear-gradient(135deg, rgba(139, 69, 190, 0.8), rgba(168, 85, 247, 0.9)) !important;
            }

            /* Preserve native toggle/switch styling */
            .glassmorphism-container #showDeleted+div {
                /* Preserve the switch track styling from component */
                background: rgba(75, 85, 99, 0.8) !important;
                border: 1px solid rgba(139, 69, 190, 0.3) !important;
                backdrop-filter: blur(8px) !important;
            }

            .glassmorphism-container #showDeleted:checked+div {
                background: rgba(37, 99, 235, 0.8) !important;
                border-color: rgba(37, 99, 235, 1) !important;
                box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2) !important;
            }

            /* Toggle label styling */
            .glassmorphism-container label[for="showDeleted"]+span,
            .glassmorphism-container .toggle-label {
                color: rgba(255, 255, 255, 0.9) !important;
                font-weight: 500 !important;
            }

            /* Override any dark text in toggle area */
            .glassmorphism-container .flex.items-center span,
            .glassmorphism-container div:has(#showDeleted) span {
                color: rgba(255, 255, 255, 0.9) !important;
                font-weight: 500 !important;
                text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3) !important;
            }

            /* Ensure all pagination and record info text is white */
            .glassmorphism-container div:contains("Mostrando"),
            .glassmorphism-container div:contains("Registros"),
            .glassmorphism-container .pagination-container,
            .glassmorphism-container .pagination-container * {
                color: rgba(255, 255, 255, 0.95) !important;
            }

            /* Enhanced Modal Styling */
            .glassmorphism-container .modal {
                background: rgba(20, 20, 20, 0.95) !important;
                border: 1px solid rgba(139, 69, 190, 0.3) !important;
                border-radius: 16px !important;
                backdrop-filter: blur(20px) !important;
            }

            /* Enhanced Action Buttons */
            .glassmorphism-container .action-button {
                background: rgba(139, 69, 190, 0.2) !important;
                border: 1px solid rgba(139, 69, 190, 0.4) !important;
                color: #ffffff !important;
                border-radius: 8px !important;
                padding: 0.5rem 1rem !important;
                transition: all 0.3s ease !important;
            }

            .glassmorphism-container .action-button:hover {
                background: rgba(168, 85, 247, 0.4) !important;
                border-color: rgba(168, 85, 247, 0.6) !important;
                transform: translateY(-2px) !important;
                box-shadow: 0 4px 12px rgba(139, 69, 190, 0.25) !important;
            }

            /* Enhanced Table Container */
            .glassmorphism-container .table-container {
                background: rgba(25, 25, 25, 0.8) !important;
                border: 1px solid rgba(139, 69, 190, 0.3) !important;
                border-radius: 12px !important;
                overflow: hidden;
                box-shadow:
                    0 4px 16px rgba(0, 0, 0, 0.3),
                    inset 0 1px 0 rgba(255, 255, 255, 0.1) !important;
            }

            /* Animated gradient background */
            .animated-gradient-header {
                background: linear-gradient(135deg, #1e3c72 0%, #2a5298 25%, #1e3c72 50%, #6a11cb 75%, #2575fc 100%);
                background-size: 300% 300%;
                animation: gradientShift 8s ease infinite;
                position: relative;
            }

            .animated-header-card {
                border: 2px solid #34d399;
                /* border-emerald-400 */
                box-shadow: 0 0 30px rgba(16, 185, 129, 0.4), 0 20px 40px rgba(0, 0, 0, 0.3);
            }

            .animated-gradient-header::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: linear-gradient(45deg,
                        rgba(255, 255, 255, 0.1) 0%,
                        rgba(255, 255, 255, 0.05) 50%,
                        rgba(255, 255, 255, 0.1) 100%);
                pointer-events: none;
            }

            @keyframes gradientShift {
                0% {
                    background-position: 0% 50%;
                }

                50% {
                    background-position: 100% 50%;
                }

                100% {
                    background-position: 0% 50%;
                }
            }

            /* Marquee animation */
            .marquee-container {
                width: 100%;
                max-width: 400px;
            }

            .marquee-text {
                display: inline-block;
                animation: marquee 20s linear infinite;
            }

            @keyframes marquee {
                0% {
                    transform: translateX(100%);
                }

                100% {
                    transform: translateX(-100%);
                }
            }

            /* Floating animations */
            @keyframes float-slow {

                0%,
                100% {
                    transform: translateY(0px) rotate(0deg);
                }

                50% {
                    transform: translateY(-10px) rotate(180deg);
                }
            }

            @keyframes float-medium {

                0%,
                100% {
                    transform: translateY(0px) rotate(0deg);
                }

                50% {
                    transform: translateY(-15px) rotate(90deg);
                }
            }

            @keyframes float-fast {

                0%,
                100% {
                    transform: translateY(0px) rotate(0deg);
                }

                50% {
                    transform: translateY(-20px) rotate(270deg);
                }
            }

            /* New animations for glassmorphic effects */
            @keyframes shimmer {
                0% {
                    transform: translateX(-100%);
                }

                100% {
                    transform: translateX(100%);
                }
            }

            @keyframes glow {
                0% {
                    opacity: 0.3;
                    box-shadow: 0 0 5px rgba(139, 69, 190, 0.3);
                }

                100% {
                    opacity: 0.8;
                    box-shadow: 0 0 15px rgba(139, 69, 190, 0.6);
                }
            }

            @keyframes slideInFromTop {
                0% {
                    transform: translateY(-20px);
                    opacity: 0;
                }

                100% {
                    transform: translateY(0);
                    opacity: 1;
                }
            }

            @keyframes fadeInUp {
                0% {
                    transform: translateY(30px);
                    opacity: 0;
                }

                100% {
                    transform: translateY(0);
                    opacity: 1;
                }
            }

            @keyframes pulseGlow {

                0%,
                100% {
                    box-shadow: 0 0 20px rgba(139, 69, 190, 0.3);
                }

                50% {
                    box-shadow: 0 0 30px rgba(139, 69, 190, 0.5);
                }
            }

            /* Text Color Fixes */
            .glassmorphism-container table td,
            .glassmorphism-container table td *,
            .glassmorphism-container tbody td,
            .glassmorphism-container tbody td *,
            .appointments-table td,
            .appointments-table td *,
            .glassmorphism-container .text-sm {
                color: rgba(255, 255, 255, 0.95) !important;
            }

            .glassmorphism-container table td a,
            .glassmorphism-container table td button,
            .glassmorphism-container td .badge,
            .glassmorphism-container td .btn {
                color: rgba(255, 255, 255, 0.9) !important;
            }

            /* Table Overflow Fix */
            .glassmorphism-container .table-responsive,
            .table-container {
                overflow-x: auto !important;
                overflow-y: visible !important;
                max-width: 100% !important;
                scrollbar-width: thin !important;
                scrollbar-color: rgba(139, 69, 190, 0.6) rgba(40, 40, 40, 0.3) !important;
            }

            .glassmorphism-container .table-responsive::-webkit-scrollbar {
                height: 8px !important;
                width: 8px !important;
            }

            .glassmorphism-container .table-responsive::-webkit-scrollbar-track {
                background: rgba(40, 40, 40, 0.3) !important;
                border-radius: 4px !important;
            }

            .glassmorphism-container .table-responsive::-webkit-scrollbar-thumb {
                background: linear-gradient(135deg, rgba(139, 69, 190, 0.6), rgba(168, 85, 247, 0.8)) !important;
                border-radius: 4px !important;
            }

            .glassmorphism-container .table-responsive::-webkit-scrollbar-thumb:hover {
                background: linear-gradient(135deg, rgba(139, 69, 190, 0.8), rgba(168, 85, 247, 1)) !important;
            }

            .glassmorphism-container table {
                min-width: 100% !important;
                white-space: nowrap !important;
            }

            /* Enhanced styling for inactive appointments */
            .glassmorphism-container .bg-red-50\/20 {
                background: linear-gradient(135deg, rgba(254, 202, 202, 0.15), rgba(252, 165, 165, 0.1)) !important;
                backdrop-filter: blur(10px) !important;
                border-left: 4px solid rgba(239, 68, 68, 0.6) !important;
                position: relative !important;
            }

            .glassmorphism-container .bg-red-50\/20::before {
                content: "" !important;
                position: absolute !important;
                top: 0 !important;
                left: 0 !important;
                right: 0 !important;
                bottom: 0 !important;
                background: linear-gradient(90deg, rgba(239, 68, 68, 0.1) 0%, transparent 100%) !important;
                pointer-events: none !important;
            }

            /* Improved text visibility for inactive appointments */
            .glassmorphism-container .bg-red-50\/20 td,
            .glassmorphism-container .bg-red-50\/20 td * {
                color: rgba(255, 255, 255, 0.8) !important;
                text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3) !important;
            }

            /* Enhanced checkbox styling for inactive rows */
            .glassmorphism-container .bg-red-50\/20 input[type="checkbox"] {
                border-color: rgba(239, 68, 68, 0.6) !important;
                background: rgba(60, 60, 60, 0.8) !important;
            }

            .glassmorphism-container .bg-red-50\/20 input[type="checkbox"]:checked {
                background: linear-gradient(135deg, rgba(239, 68, 68, 0.8), rgba(220, 38, 38, 1)) !important;
                border-color: rgba(220, 38, 38, 1) !important;
            }

            /* Row hover effects for inactive appointments */
            .glassmorphism-container .bg-red-50\/20:hover {
                background: linear-gradient(135deg, rgba(254, 202, 202, 0.25), rgba(252, 165, 165, 0.2)) !important;
                transform: translateX(2px) !important;
                transition: all 0.3s ease !important;
            }

            /* Enhanced Table Cell Styling */
            .glassmorphism-container table td {
                padding: 1rem 1.5rem !important;
                text-align: center !important;
                color: rgba(255, 255, 255, 0.9) !important;
                font-size: 0.875rem !important;
                border-right: none !important;
                transition: all 0.3s ease !important;
                background: transparent !important;
            }

            .glassmorphism-container table td:last-child {
                border-right: none !important;
            }

            /* Action buttons container styling */
            .glassmorphism-container table td div.flex {
                background: transparent !important;
                gap: 0.5rem !important;
            }

            .animate-float-slow {
                animation: float-slow 6s ease-in-out infinite;
            }

            .animate-float-medium {
                animation: float-medium 4s ease-in-out infinite;
            }

            .animate-float-fast {
                animation: float-fast 3s ease-in-out infinite;
            }

            .animate-shimmer {
                position: relative;
                overflow: hidden;
            }

            .animate-fadeInUp {
                animation: fadeInUp 0.8s ease-out;
            }

            .animate-pulseGlow {
                animation: pulseGlow 3s ease-in-out infinite;
            }

            /* Pulse animations */
            @keyframes pulse-soft {

                0%,
                100% {
                    transform: scale(1);
                    opacity: 1;
                }

                50% {
                    transform: scale(1.05);
                    opacity: 0.8;
                }
            }

            .animate-pulse-soft {
                animation: pulse-soft 3s ease-in-out infinite;
            }

            /* Bounce subtle animation */
            @keyframes bounce-subtle {

                0%,
                100% {
                    transform: translateY(0);
                    animation-timing-function: cubic-bezier(0.8, 0, 1, 1);
                }

                50% {
                    transform: translateY(-5px);
                    animation-timing-function: cubic-bezier(0, 0, 0.2, 1);
                }
            }

            .animate-bounce-subtle {
                animation: bounce-subtle 2s infinite;
            }

            /* Ping slow animation */
            @keyframes ping-slow {
                0% {
                    transform: scale(1);
                    opacity: 1;
                }

                75%,
                100% {
                    transform: scale(2);
                    opacity: 0;
                }
            }

            .animate-ping-slow {
                animation: ping-slow 3s cubic-bezier(0, 0, 0.2, 1) infinite;
            }

            /* Enhanced hover effects for header elements */
            .animated-header-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 0 40px rgba(16, 185, 129, 0.5), 0 25px 50px rgba(0, 0, 0, 0.4);
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            /* Responsive adjustments */
            @media (max-width: 640px) {
                .marquee-container {
                    max-width: 280px;
                }

                .marquee-text {
                    font-size: 0.75rem;
                }

                .animated-gradient-header {
                    padding: 1rem;
                }
            }
        </style>


        @push('scripts')
            <script>
                $(document).ready(function() {
                    // Recuperar estado del toggle de localStorage antes de inicializar el manager
                    const showDeletedState = localStorage.getItem('showDeleted') === 'true';
                    console.log('Estado inicial de showDeleted:', showDeletedState);

                    // Make the manager globally accessible
                    window.appointmentManager = new CrudManager({
                        entityName: 'Appointment',
                        entityNamePlural: 'Appointments',
                        routes: {
                            index: "{{ secure_url(route('appointments.index', [], false)) }}",
                            store: "{{ secure_url(route('appointments.store', [], false)) }}",
                            edit: "{{ secure_url(route('appointments.edit', ':id', false)) }}",
                            update: "{{ secure_url(route('appointments.update', ':id', false)) }}",
                            destroy: "{{ secure_url(route('appointments.destroy', ':id', false)) }}",
                            restore: "{{ secure_url(route('appointments.restore', ':id', false)) }}",
                            checkName: "{{ secure_url(route('appointments.check-email', [], false)) }}",
                            sendRejection: "{{ secure_url(route('appointments.send-rejection', [], false)) }}"
                        },
                        tableSelector: '#appointmentsTable',
                        searchSelector: '#searchInput',
                        perPageSelector: '#perPage',
                        showDeletedSelector: '#showDeleted',
                        paginationSelector: '#pagination',
                        alertSelector: '#alertContainer',
                        // Date filter selectors
                        startDateSelector: '#start_date',
                        endDateSelector: '#end_date',
                        clearDateFilterSelector: '#clearDateFilters',
                        statusLeadFilterSelector: '#status_lead_filter',
                        idField: 'uuid',
                        searchFields: ['first_name', 'last_name', 'email', 'status_lead', 'phone'],
                        // Establecer el valor inicial basado en localStorage
                        showDeleted: showDeletedState,
                        tableHeaders: [{
                                field: 'checkbox',
                                name: '',
                                sortable: false,
                                getter: (entity) =>
                                    entity.deleted_at ? '' :
                                    `<input type="checkbox" class="appointment-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" data-id="${entity.uuid}">`
                            },
                            {
                                field: 'first_name',
                                name: 'Name',
                                sortable: true,
                                getter: (entity) => `${entity.first_name} ${entity.last_name}`
                            },
                            {
                                field: 'email',
                                name: 'Email',
                                sortable: true
                            },
                            {
                                field: 'phone',
                                name: 'Phone',
                                sortable: false
                            },
                            {
                                field: 'inspection_date',
                                name: 'Inspection Date',
                                sortable: true,
                                getter: (entity) => entity.inspection_date ? new Date(entity.inspection_date)
                                    .toLocaleDateString() : 'N/A'
                            },
                            {
                                field: 'inspection_time',
                                name: 'Inspection Time',
                                sortable: true,
                                getter: (entity) => entity.inspection_time ? new Date(
                                        `2000-01-01T${entity.inspection_time}`)
                                    .toLocaleTimeString([], {
                                        hour: '2-digit',
                                        minute: '2-digit'
                                    }) : 'N/A'
                            },
                            {
                                field: 'insurance_property',
                                name: 'Insurance',
                                sortable: true,
                                getter: (entity) => {
                                    if (entity.insurance_property === true) {
                                        return '<span class="badge status-badge yes">Yes</span>';
                                    } else {
                                        return '<span class="badge status-badge no">No</span>';
                                    }
                                }
                            },
                            {
                                field: 'status_lead',
                                name: 'Status',
                                sortable: true,
                                getter: (entity) => {
                                    const statusMap = {
                                        'New': '<span class="badge status-badge new">New</span>',
                                        'Called': '<span class="badge status-badge called">Called</span>',
                                        'Pending': '<span class="badge status-badge pending">Pending</span>',
                                        'Declined': '<span class="badge status-badge declined">Declined</span>'
                                    };
                                    return entity.status_lead ? statusMap[entity.status_lead] || entity
                                        .status_lead : 'N/A';
                                }
                            },
                            {
                                field: 'inspection_status',
                                name: 'Inspection Status',
                                sortable: true,
                                getter: (entity) => {
                                    const statusMap = {
                                        'Confirmed': '<span class="badge status-badge called">Confirmed</span>',
                                        'Completed': '<span class="badge status-badge new">Completed</span>',
                                        'Pending': '<span class="badge status-badge pending">Pending</span>',
                                        'Declined': '<span class="badge status-badge declined">Declined</span>'
                                    };
                                    return entity.inspection_status ? statusMap[entity.inspection_status] ||
                                        entity
                                        .inspection_status : 'N/A';
                                }
                            },
                            {
                                field: 'actions',
                                name: 'Actions',
                                sortable: false,
                                getter: (appointment) => {
                                    const editUrl = `/appointments/${appointment.uuid}/edit`;

                                    let actionsHtml = `
                                <div class="flex justify-center space-x-2">
                                    <a href="${editUrl}" class="inline-flex items-center justify-center w-9 h-9 text-white rounded-md border border-white/10 bg-blue-500/40 backdrop-blur-md shadow-lg shadow-blue-500/20 hover:bg-blue-600/60 hover:shadow-xl hover:shadow-blue-500/40 transition-all duration-200 transform hover:scale-105" title="Edit Appointment">
                                         <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                             <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                         </svg>
                                    </a>`;

                                    // Botón para compartir ubicación - Se agrega independientemente del estado de borrado
                                    if (appointment.latitude && appointment.longitude) {
                                        const address =
                                            `${appointment.address || ''}, ${appointment.city || ''}`;
                                        actionsHtml += `
                                    <button data-id="${appointment.uuid}" data-lat="${appointment.latitude}" data-lng="${appointment.longitude}" data-address="${address}" class="share-location inline-flex items-center justify-center w-9 h-9 text-white rounded-md border border-white/10 bg-green-500/40 backdrop-blur-md shadow-lg shadow-green-500/20 hover:bg-green-600/60 hover:shadow-xl hover:shadow-green-500/40 transition-all duration-200 transform hover:scale-105" title="Share Location">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </button>`;
                                    } else {
                                        // Si no hay coordenadas, aún mostrar el botón pero con un comportamiento diferente
                                        actionsHtml += `
                                    <button data-id="${appointment.uuid}" data-no-coords="true" class="share-location inline-flex items-center justify-center w-9 h-9 text-white rounded-md border border-white/20 bg-gray-500/30 backdrop-blur-md shadow-lg shadow-gray-500/10 cursor-not-allowed opacity-60" title="No Location Available">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"/>
                                        </svg>
                                    </button>`;
                                    }

                                    if (appointment.deleted_at) {
                                        actionsHtml += `
                                    <button data-id="${appointment.uuid}" class="restore-btn inline-flex items-center justify-center w-9 h-9 text-white rounded-md border border-white/10 bg-emerald-500/40 backdrop-blur-md shadow-lg shadow-emerald-500/20 hover:bg-emerald-600/60 hover:shadow-xl hover:shadow-emerald-500/40 transition-all duration-200 transform hover:scale-105" title="Restore Appointment">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                    </button>`;
                                    } else {
                                        actionsHtml += `
                                    <button data-id="${appointment.uuid}" class="delete-btn inline-flex items-center justify-center w-9 h-9 text-white rounded-md border border-white/10 bg-red-500/40 backdrop-blur-md shadow-lg shadow-red-500/20 hover:bg-red-600/60 hover:shadow-xl hover:shadow-red-500/40 transition-all duration-200 transform hover:scale-105" title="Delete Appointment">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>`;
                                    }

                                    actionsHtml += `</div>`;
                                    return actionsHtml;
                                }
                            }
                        ],
                        validationFields: [{
                                name: 'first_name',
                                errorMessage: 'Please enter a valid first name.'
                            },
                            {
                                name: 'last_name',
                                errorMessage: 'Please enter a valid last name.'
                            },
                            {
                                name: 'email',
                                validation: {
                                    url: "{{ route('appointments.check-email') }}",
                                    delay: 500,
                                    minLength: 5,
                                    errorMessage: '{{ __('This email is already taken.') }}',
                                    successMessage: '{{ __('Email is available') }}'
                                },
                                errorMessage: '{{ __('Please choose a different email.') }}'
                            },
                            {
                                name: 'phone',
                                errorMessage: 'Please enter a valid phone number.'
                            },
                            {
                                name: 'inspection_date',
                                errorMessage: 'Please enter a valid inspection date.'
                            },
                            {
                                name: 'status_lead',
                                errorMessage: 'Please select a valid lead status.'
                            }
                        ],
                        defaultSortField: 'inspection_date',
                        defaultSortDirection: 'desc',
                        // Configuración de identificador de entidad para mostrar en modales de confirmación
                        entityConfig: {
                            identifierField: 'email',
                            displayName: 'appointment',
                            fallbackFields: ['first_name', 'last_name', 'phone'],
                            detailFormat: function(entity) {
                                // Mostrar nombre completo y email para mejor identificación
                                const fullName = `${entity.first_name || ''} ${entity.last_name || ''}`.trim();
                                const emailPart = entity.email ? `(${entity.email})` : '';
                                return fullName ? `${fullName} ${emailPart}` : entity.email || entity
                                    .first_name || 'this element';
                            }
                        },
                        // Configuración de traducciones personalizadas para appointments
                        translations: {
                            confirmDelete: "Are you sure?",
                            deleteMessage: "Do you want to delete this appointment?",
                            confirmRestore: "Restore appointment?",
                            restoreMessage: "Do you want to restore this appointment?",
                            yesDelete: "Yes, delete",
                            yesRestore: "Yes, restore",
                            cancel: "Cancel",
                            deletedSuccessfully: "deleted successfully",
                            restoredSuccessfully: "restored successfully",
                            errorDeleting: "Error deleting appointment",
                            errorRestoring: "Error restoring appointment"
                        }
                    });

                    // Debug: Verificar configuración del entityConfig
                    console.log('AppointmentManager entityConfig:', window.appointmentManager.entityConfig);
                    console.log('AppointmentManager translations:', window.appointmentManager.translations);

                    // Add statusLeadFilter property to appointmentManager
                    window.appointmentManager.statusLeadFilter = '';

                    // Extend the original loadEntities method
                    const originalLoadEntities = window.appointmentManager.loadEntities;
                    window.appointmentManager.loadEntities = function(page = 1) {
                        // Set current page
                        this.currentPage = page;

                        // Show loading state
                        $(this.tableSelector + ' #loadingRow').show();
                        $(this.tableSelector + ' tr:not(#loadingRow)').remove();

                        // Prepare request data
                        const requestData = {
                            page: this.currentPage,
                            per_page: this.perPage,
                            sort_field: this.sortField,
                            sort_direction: this.sortDirection,
                            search: this.searchTerm,
                            show_deleted: this.showDeleted ? "true" : "false",
                        };

                        // Add date filters if they exist
                        if (this.startDate) {
                            requestData.start_date = this.startDate;
                        }

                        if (this.endDate) {
                            requestData.end_date = this.endDate;
                        }

                        // Add status_lead_filter if it exists
                        if (this.statusLeadFilter) {
                            requestData.status_lead_filter = this.statusLeadFilter;
                        }

                        // Make AJAX request
                        $.ajax({
                            url: this.routes.index,
                            type: 'GET',
                            dataType: 'json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                'Accept': 'application/json'
                            },
                            data: requestData,
                            success: (response) => {
                                this.renderAppointmentsTable(response);
                                this.renderPagination(response);
                            },
                            error: (xhr) => {
                                console.error(`Error loading ${this.entityNamePlural}:`, xhr.responseText);

                                // Show error message in table
                                $(this.tableSelector).html(`
                                <tr>
                                    <td colspan="${this.tableHeaders.length}" class="px-6 py-4 text-center text-sm text-red-500">
                                        Error loading ${this.entityNamePlural}. Please check the console for details.
                                    </td>
                                </tr>
                            `);
                            },
                            complete: () => {
                                $(this.tableSelector + ' #loadingRow').hide();
                            }
                        });
                    };

                    // Custom render table method for appointments
                    window.appointmentManager.renderAppointmentsTable = function(data) {
                        const self = this;
                        const entities = data.data;
                        let html = "";

                        if (entities.length === 0) {
                            html =
                                `<tr><td colspan="10" class="px-6 py-4 text-center text-sm text-gray-500">{{ __('no_appointments_found_matching_criteria') }}</td></tr>`;
                        } else {
                            entities.forEach((entity) => {
                                const isDeleted = entity.deleted_at !== null;
                                const rowClass = isDeleted ?
                                    "deleted-row appointment-deleted" : "";

                                html += `<tr class="${rowClass}">`;

                                // Use the table headers to render each cell
                                self.tableHeaders.forEach((header) => {
                                    if (header.field === 'checkbox') {
                                        const checkboxHtml = header.getter ? header.getter(entity) : '';
                                        html +=
                                            `<td class="px-4 py-3 text-center">${checkboxHtml}</td>`;
                                    } else if (header.field === 'actions') {
                                        const actionsHtml = header.getter ? header.getter(entity) : '';
                                        html +=
                                            `<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">${actionsHtml}</td>`;
                                    } else {
                                        let value = header.getter ? header.getter(entity) : entity[
                                            header.field];
                                        html += `<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100 text-center">
                                        ${value}
                                        ${header.field === "first_name" && isDeleted ? '<span class="ml-2 text-xs text-red-500 dark:text-red-400">(Inactive)</span>' : ""}
                                    </td>`;
                                    }
                                });

                                html += `</tr>`;
                            });
                        }

                        // Replace table content
                        $(self.tableSelector).html(html);

                        // Don't attach edit-btn event handlers since we're using direct links
                        // But still attach delete and restore handlers using custom methods
                        $(self.tableSelector + " .delete-btn").off('click').on("click", function(e) {
                            e.preventDefault();
                            const id = $(this).data("id");
                            self.deleteAppointment(id);
                        });

                        $(self.tableSelector + " .restore-btn").off('click').on("click", function(e) {
                            e.preventDefault();
                            const id = $(this).data("id");
                            self.restoreAppointment(id);
                        });
                    };

                    // Custom delete method for appointments with entity information
                    window.appointmentManager.deleteAppointment = async function(id) {
                        console.log('DELETE: Starting deleteAppointment for ID:', id);

                        try {
                            // Find the appointment in current data
                            let appointment = null;
                            console.log('DELETE: Current data available:', this.currentData);

                            if (this.currentData && this.currentData.data) {
                                appointment = this.currentData.data.find(item => item[this.idField] === id);
                                console.log('DELETE: Found appointment:', appointment);
                            }

                            let entityInfo = '';
                            let modalTitle = 'Are you sure?';
                            let modalHtml = '';

                            if (appointment) {
                                const fullName = `${appointment.first_name || ''} ${appointment.last_name || ''}`
                                    .trim();
                                const email = appointment.email || '';
                                console.log('DELETE: Full name:', fullName);
                                console.log('DELETE: Email:', email);

                                // Crear contenido HTML más robusto
                                modalTitle = 'Delete Appointment';
                                modalHtml = '<div style="text-align: left; padding: 20px 0;">';
                                modalHtml +=
                                    '<p style="margin-bottom: 15px; color: #e5e7eb;">You are about to delete the following appointment:</p>';

                                if (fullName) {
                                    modalHtml +=
                                        `<p style="margin-bottom: 10px;"><strong style="color: #f59e0b;">Name:</strong> <span style="color: #ffffff;">${fullName}</span></p>`;
                                }

                                if (email) {
                                    modalHtml +=
                                        `<p style="margin-bottom: 15px;"><strong style="color: #f59e0b;">Email:</strong> <span style="color: #60a5fa !important; font-weight: 600 !important; text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2) !important;">${email}</span></p>`;
                                }

                                modalHtml +=
                                    '<p style="color: #ef4444; font-weight: 600;">This action cannot be undone.</p>';
                                modalHtml += '</div>';

                                entityInfo = fullName + (email ? ` (${email})` : '');
                            } else {
                                modalHtml =
                                    '<div style="text-align: center; padding: 20px 0;"><p style="color: #e5e7eb;">You are about to delete this appointment.</p><p style="color: #ef4444; font-weight: 600;">This action cannot be undone.</p></div>';
                                entityInfo = 'this appointment';
                                console.log('DELETE: No appointment found, using fallback');
                            }

                            console.log('DELETE: Final entityInfo:', entityInfo);
                            console.log('DELETE: Modal HTML:', modalHtml);

                            const result = await Swal.fire({
                                title: modalTitle,
                                html: modalHtml,
                                icon: "warning",
                                showCancelButton: true,
                                confirmButtonColor: "#d33",
                                cancelButtonColor: "#3085d6",
                                confirmButtonText: "Yes, delete",
                                cancelButtonText: "Cancel",
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                // Prevenir modificaciones del contenido después de mostrar
                                didOpen: () => {
                                    console.log('DELETE: Modal opened');
                                    // Forzar que el HTML se mantenga
                                    const htmlContainer = document.querySelector(
                                        '.swal2-html-container');
                                    if (htmlContainer) {
                                        htmlContainer.style.textAlign = 'left';
                                        console.log('DELETE: Modal HTML preserved:', htmlContainer
                                            .innerHTML);
                                    }
                                }
                            });

                            if (result.isConfirmed) {
                                const response = await $.ajax({
                                    url: this.routes.destroy.replace(':id', id),
                                    type: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                        'Accept': 'application/json'
                                    }
                                });

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'Appointment deleted successfully.',
                                    confirmButtonColor: '#10B981'
                                });

                                this.loadEntities();
                            }
                        } catch (error) {
                            console.error('Error deleting appointment:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error deleting appointment',
                                confirmButtonColor: '#EF4444'
                            });
                        }
                    };

                    // Custom restore method for appointments with entity information
                    window.appointmentManager.restoreAppointment = async function(id) {
                        console.log('RESTORE: Starting restoreAppointment for ID:', id);

                        try {
                            // Find the appointment in current data
                            let appointment = null;
                            console.log('RESTORE: Current data available:', this.currentData);

                            if (this.currentData && this.currentData.data) {
                                appointment = this.currentData.data.find(item => item[this.idField] === id);
                                console.log('RESTORE: Found appointment:', appointment);
                            }

                            let entityInfo = '';
                            let modalTitle = 'Restore Appointment';
                            let modalHtml = '';

                            if (appointment) {
                                const fullName = `${appointment.first_name || ''} ${appointment.last_name || ''}`
                                    .trim();
                                const email = appointment.email || '';
                                console.log('RESTORE: Full name:', fullName);
                                console.log('RESTORE: Email:', email);

                                // Crear contenido HTML más robusto
                                modalHtml = '<div style="text-align: left; padding: 20px 0;">';
                                modalHtml +=
                                    '<p style="margin-bottom: 15px; color: #e5e7eb;">You are about to restore the following appointment:</p>';

                                if (fullName) {
                                    modalHtml +=
                                        `<p style="margin-bottom: 10px;"><strong style="color: #f59e0b;">Name:</strong> <span style="color: #ffffff;">${fullName}</span></p>`;
                                }

                                if (email) {
                                    modalHtml +=
                                        `<p style="margin-bottom: 15px;"><strong style="color: #f59e0b;">Email:</strong> <span style="color: #60a5fa !important; font-weight: 600 !important; text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2) !important;">${email}</span></p>`;
                                }

                                modalHtml +=
                                    '<p style="color: #10b981; font-weight: 600;">This will make the appointment active again.</p>';
                                modalHtml += '</div>';

                                entityInfo = fullName + (email ? ` (${email})` : '');
                            } else {
                                modalHtml =
                                    '<div style="text-align: center; padding: 20px 0;"><p style="color: #e5e7eb;">You are about to restore this appointment.</p><p style="color: #10b981; font-weight: 600;">This will make it active again.</p></div>';
                                entityInfo = 'this appointment';
                                console.log('RESTORE: No appointment found, using fallback');
                            }

                            console.log('RESTORE: Final entityInfo:', entityInfo);
                            console.log('RESTORE: Modal HTML:', modalHtml);

                            const result = await Swal.fire({
                                title: modalTitle,
                                html: modalHtml,
                                icon: "question",
                                showCancelButton: true,
                                confirmButtonColor: "#28a745",
                                cancelButtonColor: "#6c757d",
                                confirmButtonText: "Yes, restore",
                                cancelButtonText: "Cancel",
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                // Prevenir modificaciones del contenido después de mostrar
                                didOpen: () => {
                                    console.log('RESTORE: Modal opened');
                                    // Forzar que el HTML se mantenga
                                    const htmlContainer = document.querySelector(
                                        '.swal2-html-container');
                                    if (htmlContainer) {
                                        htmlContainer.style.textAlign = 'left';
                                        console.log('RESTORE: Modal HTML preserved:', htmlContainer
                                            .innerHTML);
                                    }
                                }
                            });

                            if (result.isConfirmed) {
                                const response = await $.ajax({
                                    url: this.routes.restore.replace(':id', id),
                                    type: 'PATCH',
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                        'Accept': 'application/json'
                                    }
                                });

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Restored!',
                                    text: 'Appointment restored successfully.',
                                    confirmButtonColor: '#10B981'
                                });

                                this.loadEntities();
                            }
                        } catch (error) {
                            console.error('Error restoring appointment:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error restoring appointment',
                                confirmButtonColor: '#EF4444'
                            });
                        }
                    };

                    // Add event listeners for delete and restore buttons using custom methods
                    $(document).on('click', '.delete-btn', function() {
                        const id = $(this).data('id');
                        console.log('Delete button clicked for ID:', id);
                        window.appointmentManager.deleteAppointment(id);
                    });

                    $(document).on('click', '.restore-btn', function() {
                        const id = $(this).data('id');
                        console.log('Restore button clicked for ID:', id);
                        window.appointmentManager.restoreAppointment(id);
                    });

                    // Add event listener for status lead filter
                    $('#status_lead_filter').on('change', function() {
                        // Update the statusLeadFilter property
                        window.appointmentManager.statusLeadFilter = $(this).val();
                        // Reset to first page when changing filter
                        window.appointmentManager.currentPage = 1;
                        // Load entities with new filter
                        window.appointmentManager.loadEntities();
                    });

                    // Add event listeners for date filters to update properties
                    $('#start_date').on('change', function() {
                        window.appointmentManager.startDate = $(this).val();
                        window.appointmentManager.currentPage = 1; // Reset to first page
                        window.appointmentManager.loadEntities();
                    });

                    $('#end_date').on('change', function() {
                        window.appointmentManager.endDate = $(this).val();
                        window.appointmentManager.currentPage = 1; // Reset to first page
                        window.appointmentManager.loadEntities();
                    });

                    // Initialize loading of entities
                    window.appointmentManager.loadEntities();

                    // Update the clear filters button to also clear status filter
                    $('#clearDateFilters').on('click', function() {
                        $('#start_date, #end_date').val('');
                        $('#status_lead_filter').val('');
                        window.appointmentManager.startDate = '';
                        window.appointmentManager.endDate = '';
                        window.appointmentManager.statusLeadFilter = '';
                        window.appointmentManager.loadEntities();
                    });

                    // Handle export to Excel with status filter
                    function exportAppointmentsToExcel() {
                        // Show loading indicator
                        const originalButtonContent = $('#exportToExcel').html();
                        $('#exportToExcel').html(`
                    <svg class="animate-spin h-4 w-4 mr-2 text-white inline-block" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Exporting...
                `).prop('disabled', true);

                        // Gather filter parameters
                        const searchValue = $(window.appointmentManager.searchSelector).val();
                        const showDeleted = $(window.appointmentManager.showDeletedSelector).is(':checked') ? 'true' :
                            'false';
                        const startDate = window.appointmentManager.startDate;
                        const endDate = window.appointmentManager.endDate;
                        const statusLeadFilter = window.appointmentManager.statusLeadFilter;
                        const sortField = window.appointmentManager.sortField;
                        const sortDirection = window.appointmentManager.sortDirection;

                        // Create the URL with query parameters
                        const exportUrl = new URL(window.appointmentManager.routes.index, window.location.origin);
                        exportUrl.searchParams.append('export', 'excel');
                        exportUrl.searchParams.append('search', searchValue);
                        exportUrl.searchParams.append('show_deleted', showDeleted);
                        if (startDate) exportUrl.searchParams.append('start_date', startDate);
                        if (endDate) exportUrl.searchParams.append('end_date', endDate);
                        if (statusLeadFilter) exportUrl.searchParams.append('status_lead_filter', statusLeadFilter);
                        exportUrl.searchParams.append('sort_field', sortField);
                        exportUrl.searchParams.append('sort_direction', sortDirection);

                        // Ensure the button resets after a max time (fallback)
                        const resetTimeout = setTimeout(function() {
                            $('#exportToExcel').html(originalButtonContent).prop('disabled', false);
                        }, 10000); // 10 seconds timeout as fallback

                        try {
                            // Use fetch API instead of iframe for better control
                            fetch(exportUrl.toString())
                                .then(response => {
                                    clearTimeout(resetTimeout);

                                    if (!response.ok) {
                                        throw new Error('Export failed');
                                    }

                                    // Check content disposition to confirm it's a file download
                                    const contentDisposition = response.headers.get('content-disposition');
                                    if (!contentDisposition || !contentDisposition.includes('attachment')) {
                                        throw new Error('Invalid response format');
                                    }

                                    return response.blob();
                                })
                                .then(blob => {
                                    // Create download link
                                    const url = window.URL.createObjectURL(blob);
                                    const a = document.createElement('a');
                                    const filename = 'appointments_export_' + new Date().toISOString().slice(0, 10) +
                                        '.xlsx';

                                    a.href = url;
                                    a.download = filename;
                                    document.body.appendChild(a);
                                    a.click();

                                    // Cleanup
                                    window.URL.revokeObjectURL(url);
                                    a.remove();

                                    // Reset button and show success message
                                    $('#exportToExcel').html(originalButtonContent).prop('disabled', false);

                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Export Successful',
                                        text: 'Your appointments have been exported to Excel',
                                        confirmButtonColor: '#3B82F6'
                                    });
                                })
                                .catch(error => {
                                    console.error('Export error:', error);

                                    // Reset button and show error message
                                    $('#exportToExcel').html(originalButtonContent).prop('disabled', false);

                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Export Failed',
                                        text: 'There was an error exporting to Excel. Please try again.',
                                        confirmButtonColor: '#3B82F6'
                                    });
                                });
                        } catch (error) {
                            // Handle any unexpected errors
                            clearTimeout(resetTimeout);
                            console.error('Unexpected export error:', error);

                            // Reset button and show error message
                            $('#exportToExcel').html(originalButtonContent).prop('disabled', false);

                            Swal.fire({
                                icon: 'error',
                                title: 'Export Failed',
                                text: 'There was an unexpected error. Please try again.',
                                confirmButtonColor: '#3B82F6'
                            });
                        }
                    }

                    // Replace the handle export to Excel
                    $('#exportToExcel').on('click', function() {
                        exportAppointmentsToExcel();
                    });

                    // Selected appointments tracking
                    let selectedAppointments = [];

                    // Handle select all checkbox
                    $(document).on('change', '#selectAll', function() {
                        const isChecked = $(this).prop('checked');
                        $('.appointment-checkbox').prop('checked', isChecked);

                        // Update selected appointments array
                        selectedAppointments = isChecked ?
                            $('.appointment-checkbox').map(function() {
                                return $(this).data('id');
                            }).get() : [];

                        // Enable/disable rejection button
                        updateRejectionButtonState();
                    });

                    // Handle individual checkbox changes
                    $(document).on('change', '.appointment-checkbox', function() {
                        const id = $(this).data('id');

                        if ($(this).prop('checked')) {
                            // Add to selected if not already there
                            if (!selectedAppointments.includes(id)) {
                                selectedAppointments.push(id);
                            }
                        } else {
                            // Remove from selected
                            selectedAppointments = selectedAppointments.filter(item => item !== id);
                            // Uncheck "select all" if any individual checkbox is unchecked
                            $('#selectAll').prop('checked', false);
                        }

                        // Enable/disable rejection button
                        updateRejectionButtonState();
                    });

                    // Update rejection button state
                    function updateRejectionButtonState() {
                        $('#sendRejectionBtn').prop('disabled', selectedAppointments.length === 0);
                    }

                    // Open rejection modal
                    $('#sendRejectionBtn').on('click', function() {
                        $('#rejectionModal').removeClass('hidden');
                    });

                    // Close rejection modal
                    $('#cancelRejection').on('click', function() {
                        $('#rejectionModal').addClass('hidden');
                        resetRejectionForm();
                    });

                    // Submit rejection notification
                    $('#sendRejectionNotification').on('click', function() {
                        // Get selected reason
                        const selectedReason = $('input[name="rejection_reason"]:checked').val();
                        const otherReason = $('#reason_other').val().trim();

                        // Validate a reason is selected
                        if (!selectedReason) {
                            Swal.fire({
                                icon: 'error',
                                title: '{{ __('error_occurred') }}',
                                text: '{{ __('please_select_reason') }}',
                                confirmButtonColor: '#3B82F6'
                            });
                            return;
                        }

                        // If "other" is selected, validate text is provided
                        if (selectedReason === 'other' && otherReason === '') {
                            Swal.fire({
                                icon: 'error',
                                title: '{{ __('error_occurred') }}',
                                text: '{{ __('please_provide_other_reason') }}',
                                confirmButtonColor: '#3B82F6'
                            });
                            return;
                        }

                        // Show loading state
                        const originalBtnText = $('#sendRejectionNotification').text();
                        $('#sendRejectionNotification').html(`
                    <svg class="animate-spin h-4 w-4 mr-2 text-white inline-block" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ __('sending') }}...
                `).prop('disabled', true);

                        // Prepare data based on selected reason
                        const requestData = {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            appointment_ids: selectedAppointments,
                            no_contact: selectedReason === 'no_contact',
                            no_insurance: selectedReason === 'no_insurance',
                            other_reason: selectedReason === 'other' ? otherReason : null
                        };

                        // Send the rejection notification
                        $.ajax({
                            url: window.appointmentManager.routes.sendRejection,
                            type: 'POST',
                            data: requestData,
                            success: function(response) {
                                $('#rejectionModal').addClass('hidden');
                                resetRejectionForm();

                                Swal.fire({
                                    icon: 'success',
                                    title: '{{ __('success_title') }}',
                                    text: '{{ __('rejection_notifications_sent') }}',
                                    confirmButtonColor: '#3B82F6'
                                });

                                // Clear selected appointments
                                selectedAppointments = [];
                                $('.appointment-checkbox, #selectAll').prop('checked', false);
                                updateRejectionButtonState();

                                // Refresh the table
                                window.appointmentManager.loadEntities();
                            },
                            error: function(xhr) {
                                let errorMessage =
                                    '{{ __('rejection_error') }}';

                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }

                                Swal.fire({
                                    icon: 'error',
                                    title: '{{ __('error_occurred') }}',
                                    text: errorMessage,
                                    confirmButtonColor: '#3B82F6'
                                });
                            },
                            complete: function() {
                                $('#sendRejectionNotification').text(originalBtnText).prop('disabled',
                                    false);
                            }
                        });
                    });

                    // Reset rejection form
                    function resetRejectionForm() {
                        $('input[name="rejection_reason"]').prop('checked', false);
                        $('#reason_other').val('');
                        $('#other_reason_container').addClass('hidden');
                    }

                    // Toggle other reason textarea visibility
                    $(document).on('change', 'input[name="rejection_reason"]', function() {
                        if ($(this).val() === 'other') {
                            $('#other_reason_container').removeClass('hidden');
                        } else {
                            $('#other_reason_container').addClass('hidden');
                        }
                    });

                    // Compartir ubicación desde el listado
                    $(document).on('click', '.share-location', function(e) {
                        e.preventDefault();

                        // Verificar si no hay coordenadas disponibles
                        if ($(this).data('no-coords')) {
                            Swal.fire({
                                icon: 'warning',
                                title: '{{ __('no_location_title') }}',
                                text: '{{ __('no_coordinates_edit_appointment') }}',
                                confirmButtonColor: '#3B82F6'
                            });
                            return;
                        }

                        const lat = $(this).data('lat');
                        const lng = $(this).data('lng');
                        const address = $(this).data('address');

                        if (!lat || !lng) {
                            Swal.fire({
                                icon: 'error',
                                title: '{{ __('error_title') }}',
                                text: '{{ __('no_coordinates_address') }}',
                                confirmButtonColor: '#3B82F6'
                            });
                            return;
                        }

                        const mapsUrl = `https://www.google.com/maps?q=${lat},${lng}`;

                        // Mostrar opciones de compartir
                        Swal.fire({
                            title: '{{ __('share_location_title') }}',
                            html: `
                        <div class="p-6">
                            <div class="mb-6 text-center">
                                <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 dark:bg-blue-900 rounded-full mb-4">
                                    <svg class="w-8 h-8 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <p class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">${address}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Choose how you want to share this location</p>
                            </div>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
                                <a href="https://wa.me/?text=${encodeURIComponent('{{ __('location_for_inspection') }} ' + address + ' - ' + mapsUrl)}" target="_blank" class="flex flex-col items-center justify-center p-4 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-all duration-200 transform hover:scale-105 shadow-md">
                                    <svg class="w-6 h-6 mb-2" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                                    </svg>
                                    <span class="text-sm font-medium">{{ __('whatsapp') }}</span>
                                </a>
                                
                                <a href="mailto:?subject=${encodeURIComponent('{{ __('location_for_inspection') }}')}&body=${encodeURIComponent('{{ __('location_for_inspection') }} ' + address + '\n\n{{ __('view_google_maps') }} ' + mapsUrl)}" class="flex flex-col items-center justify-center p-4 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-all duration-200 transform hover:scale-105 shadow-md">
                                    <svg class="w-6 h-6 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-sm font-medium">{{ __('email_share') }}</span>
                                </a>
                                
                                <a href="${mapsUrl}" target="_blank" class="flex flex-col items-center justify-center p-4 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-all duration-200 transform hover:scale-105 shadow-md">
                                    <svg class="w-6 h-6 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                    </svg>
                                    <span class="text-sm font-medium">{{ __('open_maps') }}</span>
                                </a>
                                
                                <button id="copy-map-link" class="flex flex-col items-center justify-center p-4 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-all duration-200 transform hover:scale-105 shadow-md">
                                    <svg class="w-6 h-6 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-sm font-medium">{{ __('copy_link') }}</span>
                            </button>
                            </div>
                            
                            <div class="text-center text-sm text-gray-500 dark:text-gray-400">
                                <p>Coordinates: ${lat}, ${lng}</p>
                            </div>
                        </div>
                    `,
                            showConfirmButton: false,
                            showCloseButton: true,
                            customClass: {
                                container: 'swal-fullscreen',
                                popup: 'swal-fullscreen-popup'
                            },
                            width: '95%',
                            heightAuto: false
                        });

                        // Copiar enlace
                        $(document).on('click', '#copy-map-link', function() {
                            navigator.clipboard.writeText(mapsUrl).then(() => {
                                $(this).text('{{ __('copied') }}');
                                setTimeout(() => {
                                    $(this).text('{{ __('copy_link') }}');
                                }, 2000);
                            });
                        });
                    });
                });
            </script>

            <style>
                /* Glassmorphic Badge Styles for Status */
                .glassmorphism-container .badge,
                .glassmorphism-container .status-badge,
                .glassmorphism-container .appointment-status {
                    display: inline-flex;
                    align-items: center;
                    padding: 0.375rem 0.875rem;
                    border-radius: 0.5rem;
                    font-size: 0.75rem;
                    font-weight: 600;
                    text-transform: uppercase;
                    letter-spacing: 0.05em;
                    backdrop-filter: blur(12px);
                    -webkit-backdrop-filter: blur(12px);
                    border: 1px solid;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1),
                        0 2px 4px rgba(0, 0, 0, 0.06),
                        inset 0 1px 0 rgba(255, 255, 255, 0.2);
                    transition: all 0.3s ease;
                    position: relative;
                    overflow: hidden;
                }

                /* Status-specific badge colors - Glassmorphic */
                .glassmorphism-container .badge.new,
                .glassmorphism-container .status-badge.new,
                .glassmorphism-container .appointment-status.new {
                    background: rgba(34, 197, 94, 0.2);
                    border-color: rgba(34, 197, 94, 0.3);
                    color: rgba(240, 253, 244, 0.95);
                    box-shadow: 0 4px 12px rgba(34, 197, 94, 0.25),
                        0 2px 6px rgba(34, 197, 94, 0.15),
                        inset 0 1px 0 rgba(255, 255, 255, 0.2);
                }

                .glassmorphism-container .badge.called,
                .glassmorphism-container .status-badge.called,
                .glassmorphism-container .appointment-status.called {
                    background: rgba(59, 130, 246, 0.2);
                    border-color: rgba(59, 130, 246, 0.3);
                    color: rgba(239, 246, 255, 0.95);
                    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25),
                        0 2px 6px rgba(59, 130, 246, 0.15),
                        inset 0 1px 0 rgba(255, 255, 255, 0.2);
                }

                .glassmorphism-container .badge.pending,
                .glassmorphism-container .status-badge.pending,
                .glassmorphism-container .appointment-status.pending {
                    background: rgba(245, 158, 11, 0.2);
                    border-color: rgba(245, 158, 11, 0.3);
                    color: rgba(255, 251, 235, 0.95);
                    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.25),
                        0 2px 6px rgba(245, 158, 11, 0.15),
                        inset 0 1px 0 rgba(255, 255, 255, 0.2);
                }

                .glassmorphism-container .badge.declined,
                .glassmorphism-container .status-badge.declined,
                .glassmorphism-container .appointment-status.declined {
                    background: rgba(239, 68, 68, 0.2);
                    border-color: rgba(239, 68, 68, 0.3);
                    color: rgba(254, 242, 242, 0.95);
                    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25),
                        0 2px 6px rgba(239, 68, 68, 0.15),
                        inset 0 1px 0 rgba(255, 255, 255, 0.2);
                }

                .glassmorphism-container .badge.yes,
                .glassmorphism-container .status-badge.yes {
                    background: rgba(16, 185, 129, 0.2);
                    border-color: rgba(16, 185, 129, 0.3);
                    color: rgba(236, 253, 245, 0.95);
                    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25),
                        0 2px 6px rgba(16, 185, 129, 0.15),
                        inset 0 1px 0 rgba(255, 255, 255, 0.2);
                }

                .glassmorphism-container .badge.no,
                .glassmorphism-container .status-badge.no {
                    background: rgba(156, 163, 175, 0.2);
                    border-color: rgba(156, 163, 175, 0.3);
                    color: rgba(249, 250, 251, 0.95);
                    box-shadow: 0 4px 12px rgba(156, 163, 175, 0.25),
                        0 2px 6px rgba(156, 163, 175, 0.15),
                        inset 0 1px 0 rgba(255, 255, 255, 0.2);
                }

                /* Badge hover effects */
                .glassmorphism-container .badge:hover,
                .glassmorphism-container .status-badge:hover,
                .glassmorphism-container .appointment-status:hover {
                    transform: translateY(-1px) scale(1.02);
                    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15),
                        0 4px 8px rgba(0, 0, 0, 0.1),
                        inset 0 1px 0 rgba(255, 255, 255, 0.3);
                }

                /* Enhanced Deleted/Soft Deleted Row Styles - Premium Red Glass Effect */
                .glassmorphism-body tr.deleted-row,
                .glassmorphism-container .appointment-deleted {
                    position: relative;
                    background: rgba(220, 38, 38, 0.15) !important;
                    backdrop-filter: blur(12px) saturate(1.1);
                    -webkit-backdrop-filter: blur(12px) saturate(1.1);
                    border: 1px solid rgba(220, 38, 38, 0.25);
                    border-radius: 8px;
                    box-shadow:
                        0 2px 16px rgba(220, 38, 38, 0.2),
                        0 4px 24px rgba(239, 68, 68, 0.15),
                        inset 0 1px 0 rgba(255, 255, 255, 0.1);
                    opacity: 0.75;
                    transform: scale(0.995);
                    overflow: hidden;
                }

                .glassmorphism-body tr.deleted-row::before,
                .glassmorphism-container .appointment-deleted::before {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: linear-gradient(135deg,
                            rgba(220, 38, 38, 0.1) 0%,
                            rgba(239, 68, 68, 0.08) 25%,
                            transparent 50%,
                            rgba(185, 28, 28, 0.12) 75%,
                            rgba(220, 38, 38, 0.15) 100%);
                    pointer-events: none;
                    border-radius: 8px;
                }

                .glassmorphism-body tr.deleted-row::after,
                .glassmorphism-container .appointment-deleted::after {
                    content: '';
                    position: absolute;
                    top: 50%;
                    left: 5%;
                    right: 5%;
                    height: 2px;
                    background: linear-gradient(90deg,
                            transparent 0%,
                            rgba(220, 38, 38, 0.3) 10%,
                            rgba(220, 38, 38, 0.8) 20%,
                            rgba(239, 68, 68, 1) 50%,
                            rgba(220, 38, 38, 0.8) 80%,
                            rgba(220, 38, 38, 0.3) 90%,
                            transparent 100%);
                    transform: translateY(-50%);
                    pointer-events: none;
                    border-radius: 1px;
                    box-shadow:
                        0 0 8px rgba(220, 38, 38, 0.6),
                        0 0 16px rgba(239, 68, 68, 0.4);
                    z-index: 1;
                    animation: deletedGlow 2s ease-in-out infinite alternate;
                }

                .glassmorphism-body tr.deleted-row:hover,
                .glassmorphism-container .appointment-deleted:hover {
                    background: rgba(220, 38, 38, 0.2) !important;
                    transform: scale(0.995) translateY(-1px);
                    box-shadow:
                        0 4px 24px rgba(220, 38, 38, 0.3),
                        0 8px 40px rgba(239, 68, 68, 0.2),
                        inset 0 1px 0 rgba(255, 255, 255, 0.15);
                    opacity: 0.85;
                }

                .glassmorphism-body tr.deleted-row td,
                .glassmorphism-container .appointment-deleted td {
                    color: rgba(255, 255, 255, 0.7) !important;
                    text-decoration: line-through;
                    text-decoration-color: rgba(220, 38, 38, 0.8);
                    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
                }

                /* Animation for deleted row glow effect */
                @keyframes deletedGlow {
                    0% {
                        box-shadow:
                            0 0 8px rgba(220, 38, 38, 0.6),
                            0 0 16px rgba(239, 68, 68, 0.4);
                    }

                    100% {
                        box-shadow:
                            0 0 12px rgba(220, 38, 38, 0.8),
                            0 0 24px rgba(239, 68, 68, 0.6);
                    }
                }

                /* Fix para Input Search - Separar el texto del icono */
                #searchInput {
                    padding-left: 2.75rem !important;
                    /* Aumentar espacio desde el icono */
                    height: 2.75rem !important;
                    /* Altura consistente */
                    font-size: 0.875rem !important;
                }

                /* Mejorar espaciado del botón limpiar */
                #clearDateFilters {
                    min-height: 2.75rem !important;
                    /* Altura consistente con otros elementos */
                    padding-top: 0.625rem !important;
                    padding-bottom: 0.625rem !important;
                }

                /* Mejorar renderizado del input search dentro del glassmorphism */
                .glassmorphism-container #searchInput {
                    background: rgba(40, 40, 40, 0.9) !important;
                    border: 1px solid rgba(139, 69, 190, 0.4) !important;
                    color: #ffffff !important;
                    border-radius: 0.5rem !important;
                    backdrop-filter: blur(8px) !important;
                    -webkit-backdrop-filter: blur(8px) !important;
                }

                .glassmorphism-container #searchInput::placeholder {
                    color: rgba(255, 255, 255, 0.6) !important;
                }

                .glassmorphism-container #searchInput:focus {
                    border-color: rgba(168, 85, 247, 0.6) !important;
                    box-shadow: 0 0 0 3px rgba(139, 69, 190, 0.2) !important;
                    background: rgba(50, 50, 50, 0.95) !important;
                }

                /* Mejorar el icono de búsqueda */
                .glassmorphism-container #searchInput+div svg {
                    color: rgba(255, 255, 255, 0.7) !important;
                }
            </style>
        @endpush

        <style>
            /* SweetAlert Glassmorphic Style - Dark Premium - ÚNICO */
            .swal2-popup {
                background: rgba(17, 24, 39, 0.95) !important;
                backdrop-filter: blur(20px) !important;
                -webkit-backdrop-filter: blur(20px) !important;
                border: 1px solid rgba(255, 255, 255, 0.1) !important;
                border-radius: 16px !important;
                box-shadow:
                    0 25px 50px rgba(0, 0, 0, 0.5),
                    0 10px 30px rgba(0, 0, 0, 0.3),
                    inset 0 1px 0 rgba(255, 255, 255, 0.1) !important;
                animation: none !important;
                transition: none !important;
            }

            .swal2-title {
                color: #f59e0b !important;
                font-weight: 700 !important;
                text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3) !important;
            }

            .swal2-html-container {
                color: #e5e7eb !important;
                font-size: 1rem !important;
                text-align: left !important;
            }

            /* Específicamente para emails en modales - FORZAR color y estilo */
            .swal2-html-container span[style*="color: #60a5fa"],
            .swal2-html-container span[style*="60a5fa"] {
                color: #60a5fa !important;
                font-weight: 600 !important;
                text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2) !important;
                display: inline !important;
            }

            /* Asegurar que todos los elementos de email mantengan su estilo */
            .swal2-popup [style*="60a5fa"] {
                color: #60a5fa !important;
                font-weight: 600 !important;
            }

            .swal2-confirm {
                background: linear-gradient(135deg, #ef4444, #dc2626) !important;
                border: none !important;
                box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4) !important;
            }

            .swal2-cancel {
                background: linear-gradient(135deg, #6b7280, #4b5563) !important;
                border: none !important;
                box-shadow: 0 4px 15px rgba(107, 114, 128, 0.3) !important;
            }

            /* Deshabilitar animaciones que causan parpadeo */
            .swal2-show {
                animation: none !important;
            }

            .swal2-backdrop-show {
                animation: none !important;
            }

            /* Estilos para modal fullscreen */
            .swal-fullscreen-popup {
                background: rgba(255, 255, 255, 0.98) !important;
            }
        </style>
    </div>
</x-app-layout>
