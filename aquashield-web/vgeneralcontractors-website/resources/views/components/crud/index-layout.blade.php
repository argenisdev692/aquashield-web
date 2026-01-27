@props([
    'title',
    'subtitle',
    'entityName',
    'entityNamePlural',
    'searchPlaceholder',
    'showDeletedLabel',
    'addNewLabel',
    'managerName',
    'tableColumns' => [],
    'tableId' => 'crud-table',
    'createButtonId' => 'createBtn',
    'searchId' => 'searchInput',
    'showDeletedId' => 'showDeleted',
    'perPageId' => 'perPage',
    'paginationId' => 'pagination',
    'alertId' => 'alertContainer',
])

{{-- Estilos del modal CRUD ahora están incluidos en app.css --}}

<x-app-layout>
    {{-- Dark background container with consistent styling --}}
    <div class="min-h-screen" style="background-color: #141414;">

        {{-- Enhanced Animated Header Section --}}
        <div class="p-4 sm:p-6">
            <div class="animated-header-card bg-white rounded-2xl shadow-2xl mb-8 overflow-hidden">
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
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between relative z-10">
                        <div class="mb-4 sm:mb-0 w-full">
                            <h1 class="text-2xl xs:text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-2 tracking-tight text-center sm:text-left"
                                style="text-shadow: 0 4px 8px rgba(0, 0, 0, 0.4), 0 2px 4px rgba(0, 0, 0, 0.3);">
                                {{ $title }}
                            </h1>

                            {{-- Animated subtitle with marquee effect --}}
                            <div
                                class="bg-white/10 backdrop-blur-md rounded-lg px-2 py-2 border border-white/20 max-w-full sm:max-w-md mx-auto sm:mx-0 text-center">
                                <div class="marquee-container overflow-hidden w-full">
                                    <div
                                        class="marquee-text animate-marquee whitespace-nowrap text-purple-100/90 text-xs xs:text-sm sm:text-sm font-medium text-center">
                                        ✨ {{ $subtitle }} • 🚀 {{ __('efficient_management') }} • 💼
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
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                </div>
                                {{-- Animated ring --}}
                                <div class="absolute inset-0 border-2 border-white/30 rounded-xl animate-ping-slow">
                                </div>
                            </div>

                            {{-- Secondary floating icons --}}
                            <div class="hidden sm:flex flex-col space-y-2">
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-purple-400/20 to-blue-400/20 rounded-lg flex items-center justify-center animate-float-medium">
                                    <svg class="w-4 h-4 text-white/70" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                    </svg>
                                </div>
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-blue-400/20 to-purple-400/20 rounded-lg flex items-center justify-center animate-float-slow">
                                    <svg class="w-4 h-4 text-white/70" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto py-2 sm:py-4 md:py-2 lg:py-2 px-4 sm:px-6 lg:px-8">
            <!-- Success and error messages -->
            <div id="{{ $alertId }}"></div>
            @if (session()->has('message'))
                <x-crud.alert type="success" :message="session('message')" />
            @endif
            @if (session()->has('error'))
                <x-crud.alert type="error" :message="session('error')" />
            @endif

            <!-- Filter and action bar (outside the main container) -->
            <div class="mb-6">
                <x-crud.filter-bar :search-id="$searchId" :search-placeholder="$searchPlaceholder" :show-deleted-id="$showDeletedId" :show-deleted-label="$showDeletedLabel"
                    :per-page-id="$perPageId" :create-button-id="$createButtonId" :add-new-label="$addNewLabel" :manager-name="$managerName" />
            </div>

            <!-- Table with enhanced border -->
            <div class="mb-6">
                <x-crud.generic-advanced-table :id="$tableId" :columns="$tableColumns" :manager-name="$managerName" />
            </div>

            <!-- Pagination -->
            <div id="{{ $paginationId }}" class="mt-4"></div>
        </div>

        {{ $slot }}
    </div>
</x-app-layout>

{{-- Styles for the animated header --}}
<style>
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

    .animate-float-slow {
        animation: float-slow 6s ease-in-out infinite;
    }

    .animate-float-medium {
        animation: float-medium 4s ease-in-out infinite;
    }

    .animate-float-fast {
        animation: float-fast 3s ease-in-out infinite;
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

    @keyframes bounce-subtle {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-3px);
        }
    }

    @keyframes ping-slow {
        0% {
            transform: scale(1);
            opacity: 1;
        }

        75%,
        100% {
            transform: scale(1.1);
            opacity: 0;
        }
    }

    .animate-pulse-soft {
        animation: pulse-soft 3s ease-in-out infinite;
    }

    .animate-bounce-subtle {
        animation: bounce-subtle 2s ease-in-out infinite;
    }

    .animate-ping-slow {
        animation: ping-slow 3s cubic-bezier(0, 0, 0.2, 1) infinite;
    }

    /* Glass morphism effects */
    .glass-text {
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }

    /* Responsive adjustments */
    @media (max-width: 640px) {
        .marquee-container {
            max-width: 280px;
        }

        .animated-gradient-header {
            padding: 1.5rem 1rem;
        }
    }
</style>
