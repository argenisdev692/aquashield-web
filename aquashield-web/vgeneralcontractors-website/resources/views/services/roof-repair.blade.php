@extends('layouts.main')

{{-- SEO Meta Tags --}}
@section('title', __('roof_repair_meta_title'))
@section('meta_description', __('roof_repair_meta_description'))
@section('meta_keywords', __('roof_repair_meta_keywords'))
@section('canonical_url', route('roof-repair')) {{-- Assuming 'roof-repair' is the route name --}}
@section('og_title', __('roof_repair_meta_title'))
@section('og_description', __('roof_repair_meta_description'))
@section('og_image', asset('assets/img/roof-repair.webp')) {{-- Specific image for this page --}}
@section('twitter_title', __('roof_repair_meta_title'))
@section('twitter_description', __('roof_repair_meta_description'))
@section('twitter_image', asset('assets/img/roof-repair.webp')) {{-- Specific image for this page --}}
{{-- Note: og:type, og:locale, og:site_name, twitter:card, robots are usually inherited from the layout --}}

@push('styles')
    <style>
        .hero-section {
            margin-top: -2rem;
        }
    </style>
@endpush

@section('content')
    <!-- Hero Section with Image Overlay -->
    <div class="relative h-[500px] w-full hero-section">
        <!-- Background Image -->
        <img src="{{ asset('assets/img/roof-repair.webp') }}" alt="{{ __('professional_roof_repair_services_alt') }}"
            class="absolute inset-0 w-full h-full object-cover">

        <!-- Dark Overlay -->
        <div class="absolute inset-0 bg-black bg-opacity-50"></div>

        <!-- Content -->
        <div class="relative z-10 h-full flex items-center justify-center">
            <div class="text-center">
                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-4">
                    {{ __('roof_repair_hero_title') }}</h1>
                <p class="text-lg sm:text-xl md:text-2xl text-white max-w-2xl mx-auto px-4 mb-12">
                    {{ __('roof_repair_hero_subtitle') }}</p>

                <!-- Breadcrumb Navigation -->
                <nav class="px-4 md:px-8 mt-8">
                    <div class="mx-auto">
                        <ol class="flex items-center justify-center space-x-2 text-white">
                            <li>
                                <a href="{{ route('home') }}"
                                    class="hover:text-yellow-500 transition-colors">{{ __('home') }}</a>
                            </li>
                            <li>
                                <span class="mx-2">/</span>
                            </li>
                            <li class="text-yellow-500 font-medium">{{ __('roof_repair') }}</li>
                        </ol>
                    </div>
                </nav>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-lg p-8">
            {{-- Initial Grid: Title/Intro Text beside Image --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start mb-8"> {{-- Added mb-8 for spacing --}}
                <!-- Text Content Column (Shorter) -->
                <div class="space-y-6">
                    <div>
                        <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                            {{ __('expert_roof_repair_services') }}</h2>
                        <p class="text-base sm:text-lg md:text-xl text-gray-600">
                            {!! __('roof_repair_description') !!}
                        </p>
                    </div>
                    {{-- Moved the "Common Reasons" section and final P tag out of this column --}}
                </div>

                <!-- Image Column -->
                <div class="relative rounded-lg overflow-hidden fade-in-section">
                    <img src="{{ asset('assets/img/roof-repair-content.webp') }}"
                        alt="{{ __('professional_roof_repair_process_alt') }}"
                        class="w-full h-auto object-cover image-zoom">
                </div>
            </div>

            {{-- Moved Content: Common Reasons and Final P tag now span full width below the grid --}}
            <div class="space-y-6"> {{-- Wrapped moved content in a new div --}}
                <div>
                    <h3 class="text-xl sm:text-2xl font-semibold text-gray-900 mb-3">{{ __('common_reasons_roof_repair') }}
                    </h3>
                    <p class="text-base sm:text-lg text-gray-600 mb-4">
                        {{ __('roof_repair_evaluation_description') }}
                    </p>
                    <ul class="space-y-2 text-base sm:text-lg text-gray-600">
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-yellow-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span>{{ __('damage_caused_storms') }}</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-yellow-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span>{{ __('damage_caused_hail') }}</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-yellow-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span>{{ __('wear_tear_roof_age') }}</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-yellow-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span>{{ __('issues_warranty_defective') }}</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-yellow-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span>{{ __('leaks_missing_shingles') }}</span>
                        </li>
                    </ul>
                </div>

                <p class="text-base sm:text-lg md:text-xl text-gray-600">
                    {!! __('trust_v_general_contractors') !!}
                </p>
            </div>
        </div>

        <!-- Services Tabs Section -->
        <div class="mt-12 bg-white rounded-lg shadow-lg p-8" x-data="{ activeTab: 'repair' }">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
                <!-- Vertical Tabs -->
                <div class="md:col-span-4 flex flex-col border-r">
                    <button @click="activeTab = 'repair'"
                        :class="{ 'bg-yellow-50 border-yellow-500 text-yellow-600': activeTab === 'repair' }"
                        class="px-4 py-3 text-left text-lg font-semibold border-l-4 hover:bg-yellow-50 transition-colors">
                        {{ __('roof_repair_process') }}
                    </button>
                    <button @click="activeTab = 'replacement'"
                        :class="{ 'bg-yellow-50 border-yellow-500 text-yellow-600': activeTab === 'replacement' }"
                        class="px-4 py-3 text-left text-lg font-semibold border-l-4 hover:bg-yellow-50 transition-colors">
                        {{ __('roof_replacement_tab') }}
                    </button>
                </div>

                <!-- Content Area with Image -->
                <div class="md:col-span-8">
                    <!-- Repair Tab Content -->
                    <div x-show="activeTab === 'repair'" class="space-y-4">
                        <div class="prose max-w-none">
                            <p class="text-lg text-gray-600">
                                {{ __('comprehensive_evaluation_description') }}
                            </p>
                        </div>
                    </div>

                    <!-- Replacement Tab Content -->
                    <div x-show="activeTab === 'replacement'" class="space-y-4">
                        <div class="prose max-w-none">
                            <p class="text-lg text-gray-600">
                                {{ __('new_roof_30_years') }}
                            </p>

                            <h3 class="text-xl font-semibold text-gray-900 mt-6">{{ __('how_roof_replacement_works') }}
                            </h3>
                            <ul class="space-y-4 text-gray-600">
                                <li class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                    <strong class="text-lg font-semibold text-gray-900">1.
                                        {{ __('quote_inspection_evaluation') }}</strong>
                                    <p class="mt-2">{{ __('quote_inspection_description') }}</p>
                                </li>
                                <li class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                    <strong class="text-lg font-semibold text-gray-900">2.
                                        {{ __('processing_insurance_claims') }}</strong>
                                    <p class="mt-2">{{ __('processing_insurance_description') }}</p>
                                </li>
                                <li class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                    <strong class="text-lg font-semibold text-gray-900">3.
                                        {{ __('materials_delivery_installation') }}</strong>
                                    <p class="mt-2">{{ __('materials_delivery_description') }}</p>
                                </li>
                                <li class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                    <strong class="text-lg font-semibold text-gray-900">4.
                                        {{ __('quality_assurance_cleanup') }}</strong>
                                    <p class="mt-2">{{ __('quality_assurance_description') }}</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Form Section -->
        <div id="schedule-inspection" class="mt-12">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-900">{{ __('get_your_free_roof_repair_estimate') }}</h2>
                <p class="text-lg text-gray-600 mt-2">{{ __('schedule_free_no_obligation_estimate') }}</p>
            </div>

            <div class="text-center">
                <x-primary-button @click="$dispatch('open-appointment-modal')"
                    class="w-full sm:w-auto text-center justify-center px-8 py-3 text-lg">
                    {{ __('book_free_estimate_now') }}
                </x-primary-button>
            </div>
        </div>
    </main>
@endsection
