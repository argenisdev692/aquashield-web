@extends('layouts.main')

@section('title', __('storm_damage_meta_title'))
@section('meta_description', __('storm_damage_meta_description'))
@section('meta_keywords', __('storm_damage_meta_keywords'))
@section('canonical_url', route('storm-damage'))
@section('og_title', __('storm_damage_meta_title'))
@section('og_description', __('storm_damage_meta_description'))
@section('og_image', asset('assets/img/storm-damage.webp'))
@section('twitter_title', __('storm_damage_meta_title'))
@section('twitter_description', __('storm_damage_meta_description'))
@section('twitter_image', asset('assets/img/storm-damage.webp'))

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
        <img src="{{ asset('assets/img/storm-damage.webp') }}" alt="{{ __('storm_damage_roof_repair_services_alt') }}"
            class="absolute inset-0 w-full h-full object-cover">

        <!-- Dark Overlay -->
        <div class="absolute inset-0 bg-black bg-opacity-50"></div>

        <!-- Content -->
        <div class="relative z-10 h-full flex items-center justify-center">
            <div class="text-center">
                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-4">
                    {{ __('emergency_storm_damage_repair') }}</h1>
                <p class="text-lg sm:text-xl md:text-2xl text-white max-w-2xl mx-auto px-4 mb-12">
                    {{ __('storm_damage_hero_subtitle') }}</p>

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
                            <li class="text-yellow-500 font-medium">{{ __('storm_damage_service') }}</li>
                        </ol>
                    </div>
                </nav>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <!-- Emergency Contact Banner -->
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-8">
            @php
                use App\Helpers\PhoneHelper;
                $phoneNumber = $companyData->phone ?? '7135876423';
                $formattedPhone = class_exists(PhoneHelper::class) ? PhoneHelper::format($phoneNumber) : $phoneNumber;
            @endphp
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center">
                    <svg class="h-6 w-6 text-yellow-400 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <p class="ml-3 text-base sm:text-lg font-medium text-yellow-700 text-center sm:text-left">
                        {{ __('emergency_storm_damage_call') }}
                        <a href="tel:{{ $phoneNumber }}"
                            class="font-bold hover:text-yellow-800 whitespace-nowrap">{{ $formattedPhone }}</a>
                    </p>
                </div>
                <a href="javascript:void(0)" @click.prevent="$dispatch('open-appointment-modal')"
                    class="bg-yellow-500 text-white px-6 py-2 rounded-md hover:bg-yellow-600 transition-colors whitespace-nowrap flex-shrink-0">
                    {{ __('get_free_inspection_service') }}
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start mb-8">
                <!-- Text Content Column -->
                <div class="space-y-6">
                    <div>
                        <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                            {{ __('professional_storm_damage_repair') }}</h2>
                        <p class="text-base sm:text-lg text-gray-600">
                            {!! __('storm_damage_description_service') !!}
                        </p>
                    </div>
                </div>

                <!-- Image Column -->
                <div class="relative rounded-lg overflow-hidden fade-in-section">
                    <img src="{{ asset('assets/img/storm-damage-content.webp') }}"
                        alt="{{ __('storm_damage_roof_repair_process_alt') }}"
                        class="w-full h-auto object-cover image-zoom">
                </div>
            </div>

            <div class="space-y-6">
                <div class="space-y-4">
                    <p class="text-base sm:text-lg text-gray-600">
                        {!! __('storm_damage_contact_description') !!}
                    </p>

                    <p class="text-base sm:text-lg text-gray-600">
                        {{ __('storm_damage_plan_description') }}
                    </p>
                </div>

                <!-- Call to Action Box -->
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                    <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-4">{{ __('why_choose_us_storm_damage') }}
                    </h3>
                    <ul class="space-y-3 text-base sm:text-lg">
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-yellow-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>{{ __('24_7_emergency_response') }}</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-yellow-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>{{ __('free_comprehensive_inspection') }}</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-yellow-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>{{ __('expert_insurance_claim_assistance') }}</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-yellow-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>{{ __('experienced_gaf_certified') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="mt-12 bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-6">{{ __('storm_damage_restoration_process') }}</h2>
            <div class="space-y-4">
                <div class="bg-gray-50 p-6 rounded-lg border-l-4 border-yellow-500">
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ __('thorough_damage_assessment') }}</h3>
                    <p class="text-base sm:text-lg text-gray-600">
                        {{ __('damage_assessment_description') }}
                    </p>
                </div>
                <div class="bg-gray-50 p-6 rounded-lg border-l-4 border-yellow-500">
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ __('clear_repair_replacement_plan') }}</h3>
                    <p class="text-base sm:text-lg text-gray-600">
                        {{ __('repair_plan_description') }}
                    </p>
                </div>
                <div class="bg-gray-50 p-6 rounded-lg border-l-4 border-yellow-500">
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ __('professional_execution') }}</h3>
                    <p class="text-base sm:text-lg text-gray-600">
                        {{ __('professional_execution_description') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Renamed ID from contact-form to schedule-inspection --}}
        <div id="schedule-inspection" class="mt-12">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-900">{{ __('request_free_storm_inspection') }}</h2>
                {{-- Adjusted title slightly --}}
                <p class="text-lg text-gray-600 mt-2">{{ __('dont_wait_schedule_inspection') }}</p> {{-- Adjusted text --}}
            </div>
            {{-- Removed the direct contact form --}}
            {{-- <x-contact-form /> --}}

            {{-- Added button to trigger the appointment modal --}}
            <div class="text-center">
                <x-primary-button @click="$dispatch('open-appointment-modal')"
                    class="w-full sm:w-auto text-center justify-center px-8 py-3 text-lg">
                    {{ __('book_free_inspection_now') }}
                </x-primary-button>
            </div>
        </div>
    </main>
@endsection
