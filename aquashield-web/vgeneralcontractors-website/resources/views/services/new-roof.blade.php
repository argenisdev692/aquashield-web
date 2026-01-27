@extends('layouts.main')

{{-- SEO Meta Tags --}}
@section('title', __('new_roof_meta_title'))
@section('meta_description', __('new_roof_meta_description'))
@section('meta_keywords', __('new_roof_meta_keywords'))
@section('canonical_url', route('new-roof')) {{-- Assuming 'new-roof' is the route name --}}
@section('og_title', __('new_roof_meta_title'))
@section('og_description', __('new_roof_meta_description'))
@section('og_image', asset('assets/img/new-roof.webp')) {{-- Specific image for this page --}}
@section('twitter_title', __('new_roof_meta_title'))
{{-- Reusing OG title --}}
@section('twitter_description', __('new_roof_meta_description')) {{-- Reusing OG description --}}
@section('twitter_image', asset('assets/img/new-roof.webp')) {{-- Specific image for this page --}}
{{-- Note: og:type, robots are usually inherited from the layout --}}

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
        <img src="{{ asset('assets/img/new-roof.webp') }}" alt="{{ __('professional_new_roof_installation_alt') }}"
            class="absolute inset-0 w-full h-full object-cover">

        <!-- Dark Overlay -->
        <div class="absolute inset-0 bg-black bg-opacity-50"></div>

        <!-- Content -->
        <div class="relative z-10 h-full flex items-center justify-center">
            <div class="text-center">
                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-4">
                    {{ __('new_roof_hero_title') }}
                </h1>
                <p class="text-lg sm:text-xl md:text-2xl text-white max-w-2xl mx-auto px-4 mb-12">
                    {{ __('new_roof_hero_subtitle') }}</p>

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
                            <li class="text-yellow-500 font-medium">{{ __('new_roof') }}</li>
                        </ol>
                    </div>
                </nav>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
                <!-- Text Content Column -->
                <div class="space-y-6">
                    <div>
                        <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                            {{ __('expert_new_roof_installation') }}</h2>
                        <p class="text-base sm:text-lg md:text-xl text-gray-600">
                            {!! __('new_roof_description') !!}
                        </p>
                    </div>

                    <div class="space-y-4">
                        <h3 class="text-xl sm:text-2xl md:text-3xl font-semibold text-gray-900">
                            {{ __('why_choose_us_colon') }}</h3>
                        <ul class="space-y-2 text-base sm:text-lg md:text-xl text-gray-600">
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-yellow-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>{{ __('expert_installation_team') }}</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-yellow-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>{{ __('premium_materials_workmanship') }}</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-yellow-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>{{ __('comprehensive_warranty_protection') }}</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-yellow-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>{{ __('free_no_obligation_estimates') }}</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-yellow-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>{{ __('insurance_claim_assistance_certified') }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Image Column -->
                <div class="relative h-[400px] rounded-lg overflow-hidden fade-in-section">
                    <img src="{{ asset('assets/img/new-roof-content.webp') }}"
                        alt="{{ __('professional_roof_installation_process_alt') }}"
                        class="absolute inset-0 w-full h-full object-cover image-zoom">
                </div>
            </div>
        </div>

        <!-- Contact Form Section -->
        <div id="schedule-estimate" class="mt-12">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-900">{{ __('get_your_free_new_roof_estimate') }}</h2>
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
