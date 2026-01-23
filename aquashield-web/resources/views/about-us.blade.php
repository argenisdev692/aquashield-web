@extends('layouts.main')

@section('title', __('about_page_title'))
@section('meta_description', __('about_meta_description'))

@section('content')
    <!-- Hero Section -->
    <section class="relative py-32 bg-gray-900 text-white">
        <div class="absolute inset-0">
            <img src="{{ asset('assets/img/about-hero.webp') }}" alt="V General Contractors team"
                class="w-full h-full object-cover opacity-25" />
        </div>
        <div class="relative container mx-auto px-4">
            <div class="max-w-3xl">
                <h1 class="text-4xl md:text-5xl font-bold mb-6">{{ __('trusted_roofing_partner') }}</h1>
                <p class="text-xl text-gray-300">{!! __('about_hero_description') !!}</p>
            </div>
        </div>
    </section>

    <!-- Breadcrumb -->
    <nav class="bg-gray-100 py-3">
        <div class="container mx-auto px-4">
            <div class="flex items-center space-x-2 text-gray-500">
                <a href="{{ route('home') }}" class="hover:text-yellow-500">{{ __('home') }}</a>
                <span>/</span>
                <span class="text-yellow-500">{{ __('about_us') }}</span>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <section class="py-16">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <!-- Left Column: Image -->
                <div class="relative">
                    <img src="{{ asset('assets/img/about-team.webp') }}" alt="{{ __('our_expert_roofing_team') }}"
                        class="rounded-lg shadow-xl" />
                    <div class="absolute -bottom-6 -right-6 bg-yellow-500 text-white p-6 rounded-lg shadow-lg">
                        <p class="text-2xl font-bold">{{ __('years_of_excellence') }}</p>
                        <p>{{ __('of_excellence') }}</p>
                    </div>
                </div>

                <!-- Right Column: Content -->
                <div>
                    <h2 class="text-3xl font-bold mb-6">{{ __('our_commitment_to_excellence') }}</h2>
                    <p class="text-gray-600 mb-6">{!! __('about_commitment_p1') !!}</p>
                    <p class="text-gray-600 mb-6">{!! __('about_commitment_p2') !!}</p>
                    <div class="grid grid-cols-2 gap-6 mb-8">
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <p class="text-3xl font-bold text-yellow-500">2,500+</p>
                            <p class="text-gray-600">{{ __('projects_completed') }}</p>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <p class="text-3xl font-bold text-yellow-500">98%</p>
                            <p class="text-gray-600">{{ __('client_satisfaction') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Vision Section -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto text-center">
                <h2 class="text-3xl font-bold mb-6">{{ __('our_vision_2030') }}</h2>
                <p class="text-gray-600 mb-8">{!! __('vision_description') !!}</p>
            </div>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="py-16">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12">{{ __('why_choose_v_general') }}</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="text-center p-6 rounded-lg shadow-lg bg-white">
                    <div class="w-16 h-16 mx-auto mb-4 text-yellow-500">
                        <svg class="w-full h-full" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-4">{!! __('free_professional_inspections') !!}</h3>
                    <p class="text-gray-600">{{ __('free_inspections_description') }}</p>
                </div>

                <!-- Feature 2 -->
                <div class="text-center p-6 rounded-lg shadow-lg bg-white">
                    <div class="w-16 h-16 mx-auto mb-4 text-yellow-500">
                        <svg class="w-full h-full" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-4">{!! __('expert_insurance_claim_support') !!}</h3>
                    <p class="text-gray-600">{{ __('insurance_support_description') }}</p>
                </div>

                <!-- Feature 3 -->
                <div class="text-center p-6 rounded-lg shadow-lg bg-white">
                    <div class="w-16 h-16 mx-auto mb-4 text-yellow-500">
                        <svg class="w-full h-full" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4zm3 1h6v4H7V5zm6 6H7v2h6v-2z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-4">{!! __('comprehensive_documentation') !!}</h3>
                    <p class="text-gray-600">{{ __('documentation_description') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-gray-900 text-white">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-4xl font-bold mb-8">{!! __('ready_for_free_inspection') !!}</h2>
            <p class="text-xl mb-8">{{ __('assessment_help_description') }}</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <x-primary-button @click="$dispatch('open-modal', 'schedule-inspection')" class="text-lg px-8 py-4">
                    {{ __('schedule_free_inspection') }}
                </x-primary-button>
                <x-secondary-button href="{{ route('contact-support') }}" class="text-lg px-8 py-4">
                    {{ __('contact_support') }}
                </x-secondary-button>
            </div>
        </div>
    </section>

    <!-- Schedule Inspection Modal -->
    <x-modal name="schedule-inspection" :show="false">
        <div class="p-6">
            <h2 class="text-2xl font-bold mb-4">{{ __('schedule_your_free_inspection') }}</h2>
            <livewire:contact-form />
        </div>
    </x-modal>
@endsection
