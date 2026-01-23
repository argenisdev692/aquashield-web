@extends('layouts.main')

{{-- SEO Meta Tags --}}
@section('title', __('insurance_claims_meta_title')) {{-- Optimized Title --}}
@section('meta_description', __('insurance_claims_meta_description')) {{-- Updated Description --}}
@section('meta_keywords', __('insurance_claims_meta_keywords')) {{-- Added Brand/Locations --}}
@section('canonical_url', route('insurance-claims')) {{-- Added Canonical --}}
@section('og_title', __('insurance_claims_og_title')) {{-- Specific OG Title --}}
@section('og_description', __('insurance_claims_og_description')) {{-- Specific OG Desc --}}
@section('og_image', asset('assets/img/insurance-claims-hero.webp')) {{-- Added OG Image (Ensure it exists) --}}
@section('twitter_title', __('insurance_claims_twitter_title')) {{-- Specific Twitter Title --}}
@section('twitter_description', __('insurance_claims_twitter_description')) {{-- Specific Twitter Desc --}}
@section('twitter_image', asset('assets/img/insurance-claims-hero.webp')) {{-- Added Twitter Image (Ensure it exists) --}}

{{-- Added Styles Push for Hero --}}
@push('styles')
    <style>
        .hero-section {
            margin-top: -2rem;
        }
    </style>
@endpush

@section('content')
    {{-- Added Hero Section --}}
    <div class="relative h-[500px] w-full hero-section">
        {{-- Add a relevant background image at public/assets/img/insurance-claims-hero.webp --}}
        <img src="{{ asset('assets/img/insurance-claims-hero.webp') }}"
            alt="{{ __('roof_insurance_claim_assistance_houston_dallas_alt') }}"
            class="absolute inset-0 w-full h-full object-cover">
        <div class="absolute inset-0 bg-black bg-opacity-50"></div>
        <div class="relative z-10 h-full flex items-center justify-center">
            <div class="text-center">
                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-4">
                    {{ __('roof_insurance_claims_assistance') }}</h1>
                <p class="text-lg sm:text-xl md:text-2xl text-white max-w-3xl mx-auto px-4 mb-12">
                    {{ __('expert_guidance_insurance_claim_process') }}</p>
                {{-- Breadcrumb --}}
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
                            {{-- Assuming 'insurance-claims' is the route name --}}
                            <li class="text-yellow-500 font-medium">{{ __('insurance_claims_page') }}</li>
                        </ol>
                    </div>
                </nav>
            </div>
        </div>
    </div>

    {{-- Wrapped existing content in main tag --}}
    <main class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="grid md:grid-cols-2 gap-12 items-start"> {{-- Added items-start --}}
                {{-- Column 1: Process and Covered Damage --}}
                <div>
                    <h2 class="text-3xl font-semibold text-gray-900 mb-6">{{ __('our_insurance_claim_support') }}</h2>
                    <div class="prose prose-lg max-w-none">
                        <p class="mb-6">{!! __('navigating_insurance_claims_complex') !!}
                        </p>

                        <h3 class="text-2xl font-semibold mb-4">{{ __('our_insurance_claim_process') }}</h3>
                        {{-- Updated list with check icons --}}
                        <ul class="space-y-4 mb-8">
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>{{ __('free_initial_roof_inspection') }}</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>{{ __('comprehensive_documentation_damage') }}</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>{{ __('direct_collaboration_certified_adjusters') }}</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>{{ __('guidance_assistance_claim_filing') }}</span>
                            </li>
                        </ul>

                        {{-- Updated "Types of Covered Damage" list with check icons --}}
                        <div class="bg-gray-50 p-6 rounded-lg mb-8">
                            <h3 class="text-xl font-semibold mb-4">{{ __('types_damage_we_handle') }}</h3>
                            <ul class="space-y-2">
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 text-gray-500 mr-2 mt-1 flex-shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span>{{ __('storm_damage_wind_rain_debris') }}</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 text-gray-500 mr-2 mt-1 flex-shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span>{{ __('hail_damage_impacts_bruising') }}</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 text-gray-500 mr-2 mt-1 flex-shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span>{{ __('damage_fallen_trees_branches') }}</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 text-gray-500 mr-2 mt-1 flex-shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span>{{ __('water_damage_resulting_roof_leaks') }}</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 text-gray-500 mr-2 mt-1 flex-shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span>{{ __('other_perils_covered_insurance') }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                {{-- Column 2: Public Adjusters Info and Contact Button --}}
                <div>
                    <div class="bg-gray-50 rounded-lg p-8">
                        <h3 class="text-2xl font-semibold mb-6">{{ __('why_work_certified_public_adjusters') }}</h3>
                        <p class="text-gray-600 mb-6">{{ __('public_adjusters_licensed_professionals') }}</p>

                        {{-- Updated list with check icons --}}
                        <ul class="space-y-4 mb-8">
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-blue-500 mr-2 flex-shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>{{ __('accurately_assess_value_damages') }}</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-blue-500 mr-2 flex-shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>{{ __('navigate_complex_insurance_policy') }}</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-blue-500 mr-2 flex-shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>{{ __('ensure_covered_damage_documented') }}</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-blue-500 mr-2 flex-shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>{{ __('negotiate_insurance_company_maximize') }}</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-blue-500 mr-2 flex-shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>{{ __('reduce_stress_time_managing') }}</span>
                            </li>
                        </ul>

                        <div class="text-center mt-8">
                            {{-- Assuming $companyData and PhoneHelper are available --}}
                            @php
                                use App\Helpers\PhoneHelper;
                                $phoneNumber = $companyData->phone ?? '7135876423'; // Use your fallback
                                $formattedPhone = class_exists(PhoneHelper::class)
                                    ? PhoneHelper::format($phoneNumber)
                                    : $phoneNumber;
                            @endphp
                            <p class="text-lg font-medium mb-4">{{ __('need_help_insurance_claim') }}</p>
                            {{-- Changed button to a styled phone link --}}
                            <a href="tel:{{ $phoneNumber }}"
                                class="inline-flex items-center justify-center space-x-2 bg-yellow-500 text-white no-underline font-bold px-8 py-3 rounded-lg hover:bg-yellow-600 transition duration-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <span>{{ __('call_for_expert_help') }}</span> {{-- Changed text slightly --}}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
