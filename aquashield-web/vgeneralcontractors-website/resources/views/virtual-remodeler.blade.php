@extends('layouts.main')

@section('title', __('virtual_remodeler_meta_title'))

@section('meta_description', __('virtual_remodeler_meta_description'))

@section('meta_keywords', __('virtual_remodeler_meta_keywords'))

@section('canonical_url', route('virtual-remodeler'))

@section('og_title', __('virtual_remodeler_og_title'))

@section('og_description', __('virtual_remodeler_og_description'))

@section('og_image', asset('assets/img/virtual-remodeler-hero.webp'))

@section('twitter_title', __('virtual_remodeler_twitter_title'))

@section('twitter_description', __('virtual_remodeler_twitter_description'))

@section('twitter_image', asset('assets/img/virtual-remodeler-hero.webp'))

@push('styles')
    <style>
        .hero-section {
            margin-top: -2rem;
        }

        /* Optional: Add specific styles for this page if needed */
    </style>
@endpush

@section('content')
    <div class="relative h-[500px] w-full hero-section">
        <img src="{{ asset('assets/img/virtual-remodeler-hero.webp') }}"
            alt="{{ __('virtual_roof_remodeler_tool_visualization_alt') }}"
            class="absolute inset-0 w-full h-full object-cover">
        <div class="absolute inset-0 bg-black bg-opacity-50"></div>
        <div class="relative z-10 h-full flex items-center justify-center">
            <div class="text-center">
                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-4">
                    {{ __('virtual_roof_remodeler') }}
                </h1>
                <p class="text-lg sm:text-xl md:text-2xl text-white max-w-3xl mx-auto px-4 mb-12">
                    {{ __('visualize_dream_roof_before_decision') }}</p>
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
                            <li class="text-yellow-500 font-medium">{{ __('virtual_remodeler') }}</li>
                        </ol>
                    </div>
                </nav>
            </div>
        </div>
    </div>

    <main class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl font-semibold text-gray-900 mb-6">{{ __('design_your_perfect_roof') }}</h2>
                    <div class="prose prose-lg max-w-none">
                        <p class="mb-6">{{ __('see_how_different_roofing_options') }}</p>
                        <ul class="space-y-4 mb-8">
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>{{ __('upload_photo_actual_home') }}</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>{{ __('experiment_various_gaf_shingles') }}</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>{{ __('compare_different_combinations') }}</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>{{ __('save_favorite_designs_share') }}</span>
                            </li>
                        </ul>
                        <p>{{ __('tool_helps_confident_decisions') }}</p>
                    </div>
                </div>
                <div class="relative text-center bg-gray-50 p-8 rounded-lg">
                    <img src="{{ asset('assets/img/gaf-logo.webp') }}" alt="{{ __('gaf_logo_alt') }}"
                        class="h-12 mx-auto mb-6">
                    <h3 class="text-2xl font-semibold text-gray-900 mb-4">{{ __('launch_gaf_virtual_remodeler') }}</h3>
                    <p class="text-gray-600 mb-6">{{ __('click_button_interactive_tool') }}</p>
                    <a href="https://www.gaf.com/en-us/plan-design/design-your-roof" target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center justify-center space-x-2 bg-yellow-500 text-white no-underline font-semibold px-8 py-3 text-lg rounded hover:bg-yellow-600 transition-colors duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                        <span>{{ __('launch_tool') }}</span>
                    </a>
                </div>
            </div>
        </div>
    </main>
@endsection
