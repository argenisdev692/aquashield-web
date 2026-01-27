@extends('layouts.main')

@section('title', __('financing_meta_title'))
@section('meta_description', __('financing_meta_description'))
@section('meta_keywords', __('financing_meta_keywords'))
@section('canonical_url', route('financing'))
@section('og_title', __('financing_og_title'))
@section('og_description', __('financing_og_description'))
@section('og_image', asset('assets/img/financing-hero.webp'))
@section('twitter_title', __('financing_twitter_title'))
@section('twitter_description', __('financing_twitter_description'))
@section('twitter_image', asset('assets/img/financing-hero.webp'))

@push('styles')
    <style>
        .hero-section {
            margin-top: -2rem;
        }
    </style>
@endpush

@section('content')
    <div class="relative h-[500px] w-full hero-section">
        <img src="{{ asset('assets/img/financing-hero.webp') }}"
            alt="{{ __('roofing_financing_options_houston_dallas_alt') }}"
            class="absolute inset-0 w-full h-full object-cover">

        <div class="absolute inset-0 bg-black bg-opacity-50"></div>

        <div class="relative z-10 h-full flex items-center justify-center">
            <div class="text-center">
                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-4">
                    {{ __('financing_options') }}</h1>
                <p class="text-lg sm:text-xl md:text-2xl text-white max-w-2xl mx-auto px-4 mb-12">
                    {{ __('flexible_payment_solutions') }}</p>

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
                            <li class="text-yellow-500 font-medium">{{ __('financing') }}</li>
                        </ol>
                    </div>
                </nav>
            </div>
        </div>
    </div>

    <main class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-3xl font-semibold text-gray-900 mb-6 text-center md:text-left">
                {{ __('financing_opportunities') }}</h2>

            <div class="prose prose-lg max-w-none">
                <p class="mb-6">{!! __('financing_intro_description') !!}</p>

                <ul class="space-y-4 mb-8">
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>{{ __('flexible_payment_plans_competitive') }}</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>{{ __('special_financing_offers_0') }}</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>{{ __('quick_easy_approval_process') }}</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>{{ __('transparent_terms_no_hidden') }}</span>
                    </li>
                </ul>

                <p class="mb-6">{{ __('financing_designed_description') }}</p>

                <div class="bg-gray-50 p-6 rounded-lg mb-8">
                    <h3 class="text-xl font-semibold mb-4">{{ __('why_choose_our_financing') }}</h3>
                    <ul class="space-y-2">
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span>{{ __('simple_application_process') }}</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span>{{ __('competitive_rates') }}</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span>{{ __('flexible_terms_fit_budget') }}</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span>{{ __('quick_approval_decisions') }}</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span>{{ __('professional_guidance_throughout') }}</span>
                        </li>
                    </ul>
                </div>

                <div class="text-center">
                    <p class="text-lg font-medium mb-4">{{ __('ready_discuss_financing_options') }}</p>
                    <a href="{{ route('contact-support') }}" target="_blank" rel="noopener noreferrer"
                        class="inline-flex items-center justify-center space-x-2 bg-yellow-500 text-white no-underline font-semibold px-8 py-3 text-lg rounded hover:bg-yellow-600 transition-colors duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <span>{{ __('contact_us_today') }}</span>
                    </a>
                </div>
            </div>
        </div>
    </main>
@endsection
