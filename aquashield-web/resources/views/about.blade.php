@extends('layouts.main')

@section('title', __('about_us_page_title'))

@push('styles')
    <style>
        /* Estilos específicos de la página About */
        .hero-section {
            margin-top: -5rem;
            /* Ajuste para compensar el navbar */
        }
    </style>
@endpush

@section('content')
    <!-- Hero Section with Image Overlay -->
    <div class="relative h-[500px] w-full hero-section">
        <!-- Background Image -->
        <img src="{{ asset('assets/img/about.webp') }}" alt="{{ __('about_v_general_contractors_alt') }}"
            class="absolute inset-0 w-full h-full object-cover">

        <!-- Dark Overlay -->
        <div class="absolute inset-0 bg-black bg-opacity-50"></div>

        <!-- Content -->
        <div class="relative z-10 h-full flex items-center justify-center">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">{{ __('about_us_page_title') }}</h1>
                <p class="text-xl text-white max-w-2xl mx-auto px-4 mb-8">{{ __('trusted_partner_commercial_residential') }}
                </p>

                <!-- Breadcrumb Navigation -->
                <nav class="px-4 md:px-8">
                    <div class="mx-auto">
                        <ol class="flex items-center justify-center space-x-2 text-white">
                            <li>
                                <a href="{{ route('home') }}"
                                    class="hover:text-yellow-500 transition-colors">{{ __('home') }}</a>
                            </li>
                            <li>
                                <span class="mx-2">/</span>
                            </li>
                            <li class="text-yellow-500 font-medium">{{ __('about_us_page_title') }}</li>
                        </ol>
                    </div>
                </nav>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                <!-- Text Content Column -->
                <div class="space-y-8">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900 mb-4">{{ __('about_us_page_title') }}</h2>
                        <p class="text-lg text-gray-600 italic">{{ __('passion_quality_10_years') }}</p>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <h3 class="text-2xl font-semibold text-gray-900 flex items-center gap-2">
                                <span class="text-yellow-500">01.</span> {{ __('vision') }}
                            </h3>
                            <p class="mt-2 text-gray-600">
                                {{ __('vision_description_about') }}
                            </p>
                        </div>

                        <div>
                            <h3 class="text-2xl font-semibold text-gray-900 flex items-center gap-2">
                                <span class="text-yellow-500">02.</span> {{ __('mission') }}
                            </h3>
                            <p class="mt-2 text-gray-600">
                                {{ __('mission_description') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Image Column -->
                <div class="relative h-[500px] rounded-lg overflow-hidden fade-in-section">
                    <img src="{{ asset('assets/img/about-content.webp') }}"
                        alt="{{ __('v_general_contractors_team_alt') }}"
                        class="absolute inset-0 w-full h-full object-cover image-zoom about-image">
                </div>
            </div>
        </div>
    </main>
@endsection
