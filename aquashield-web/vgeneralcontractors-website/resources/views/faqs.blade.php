@extends('layouts.main')

@section('title', __('faqs_page_title'))

@section('meta')
    <meta name="description" content="{{ __('faqs_meta_description') }}">
    <meta name="keywords" content="{{ __('faqs_meta_keywords') }}">
@endsection

@push('styles')
    <style>
        .hero-section {
            margin-top: -2rem;
        }

        .tab-content {
            transition: all 0.3s ease-in-out;
        }

        .faq-item {
            transition: all 0.3s ease-in-out;
        }

        .faq-item:hover {
            transform: translateX(5px);
        }
    </style>
@endpush

@section('content')
    <!-- Hero Section with Image Overlay -->
    <div class="relative h-[500px] w-full hero-section">
        <!-- Background Image -->
        <img src="{{ asset('assets/img/faqs.webp') }}" alt="{{ __('faqs_hero_alt') }}"
            class="absolute inset-0 w-full h-full object-cover object-[center_25%]">

        <!-- Dark Overlay -->
        <div class="absolute inset-0 bg-black bg-opacity-50"></div>

        <!-- Content -->
        <div class="relative z-10 h-full flex items-center justify-center">
            <div class="text-center">
                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-4">
                    {{ __('questions_and_answers') }}
                </h1>
                <p class="text-lg sm:text-xl md:text-2xl text-white max-w-2xl mx-auto px-4 mb-12">
                    {{ __('find_answers_common_roofing_questions') }}</p>

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
                            <li class="text-yellow-500 font-medium">{{ __('faqs') }}</li>
                        </ol>
                    </div>
                </nav>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- FAQ Content -->
            <div class="lg:col-span-2">
                <div x-data="{ activeTab: 'general' }" class="bg-white rounded-lg shadow-lg p-6">
                    <!-- Tab Navigation -->
                    <div class="flex flex-wrap gap-4 mb-6">
                        <button @click="activeTab = 'general'"
                            :class="{ 'bg-yellow-500 text-white': activeTab === 'general', 'bg-gray-100 text-gray-700 hover:bg-gray-200': activeTab !== 'general' }"
                            class="px-4 py-2 rounded-lg font-semibold transition-colors">
                            {{ __('general_questions') }}
                        </button>
                        <button @click="activeTab = 'services'"
                            :class="{ 'bg-yellow-500 text-white': activeTab === 'services', 'bg-gray-100 text-gray-700 hover:bg-gray-200': activeTab !== 'services' }"
                            class="px-4 py-2 rounded-lg font-semibold transition-colors">
                            {{ __('our_services') }}
                        </button>
                        <button @click="activeTab = 'process'"
                            :class="{ 'bg-yellow-500 text-white': activeTab === 'process', 'bg-gray-100 text-gray-700 hover:bg-gray-200': activeTab !== 'process' }"
                            class="px-4 py-2 rounded-lg font-semibold transition-colors">
                            {{ __('process_timeline') }}
                        </button>
                    </div>

                    <!-- Tab Content -->
                    <div class="space-y-4">
                        <!-- General Questions -->
                        <div x-show="activeTab === 'general'" class="space-y-4">
                            <div class="faq-item bg-gray-50 p-6 rounded-lg">
                                <h3 class="text-xl font-bold text-gray-900 mb-3">
                                    {{ __('faq_trusted_roofing_company_question') }}</h3>
                                <p class="text-gray-700">{!! __('faq_trusted_roofing_company_answer') !!}</p>
                            </div>
                            <div class="faq-item bg-gray-50 p-6 rounded-lg">
                                <h3 class="text-xl font-bold text-gray-900 mb-3">
                                    {{ __('faq_provide_references_question') }}</h3>
                                <p class="text-gray-700">{!! __('faq_provide_references_answer') !!}</p>
                            </div>
                            <div class="faq-item bg-gray-50 p-6 rounded-lg">
                                <h3 class="text-xl font-bold text-gray-900 mb-3">{{ __('faq_roof_lifespan_question') }}
                                </h3>
                                <p class="text-gray-700">{!! __('faq_roof_lifespan_answer') !!}</p>
                            </div>
                        </div>

                        <!-- Services -->
                        <div x-show="activeTab === 'services'" class="space-y-4">
                            <div class="faq-item bg-gray-50 p-6 rounded-lg">
                                <h3 class="text-xl font-bold text-gray-900 mb-3">{{ __('faq_roof_types_question') }}</h3>
                                <p class="text-gray-700">{!! __('faq_roof_types_answer') !!}</p>
                            </div>
                            <div class="faq-item bg-gray-50 p-6 rounded-lg">
                                <h3 class="text-xl font-bold text-gray-900 mb-3">{{ __('faq_insurance_claims_question') }}
                                </h3>
                                <p class="text-gray-700">{!! __('faq_insurance_claims_answer') !!}</p>
                            </div>
                            <div class="faq-item bg-gray-50 p-6 rounded-lg">
                                <h3 class="text-xl font-bold text-gray-900 mb-3">{{ __('faq_need_new_roof_question') }}
                                </h3>
                                <p class="text-gray-700">{!! __('faq_need_new_roof_answer') !!}</p>
                            </div>
                        </div>

                        <!-- Process -->
                        <div x-show="activeTab === 'process'" class="space-y-4">
                            <div class="faq-item bg-gray-50 p-6 rounded-lg">
                                <h3 class="text-xl font-bold text-gray-900 mb-3">{{ __('faq_roof_inspection_question') }}
                                </h3>
                                <p class="text-gray-700">{!! __('faq_roof_inspection_answer') !!}</p>
                            </div>
                            <div class="faq-item bg-gray-50 p-6 rounded-lg">
                                <h3 class="text-xl font-bold text-gray-900 mb-3">{{ __('faq_replacement_time_question') }}
                                </h3>
                                <p class="text-gray-700">{!! __('faq_replacement_time_answer') !!}</p>
                            </div>
                            <div class="faq-item bg-gray-50 p-6 rounded-lg">
                                <h3 class="text-xl font-bold text-gray-900 mb-3">{{ __('faq_stay_in_house_question') }}
                                </h3>
                                <p class="text-gray-700">{!! __('faq_stay_in_house_answer') !!}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="sticky top-32 space-y-6">
                    <!-- Key Benefits Box -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">{{ __('why_choose_us_faqs') }}</h3>
                        <ul class="space-y-3">
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-yellow-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-gray-700">{!! __('free_roof_inspections_benefit') !!}</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-yellow-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-gray-700">{!! __('certified_public_adjusters_benefit') !!}</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-yellow-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-gray-700">{!! __('expert_insurance_claim_assistance_benefit') !!}</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-yellow-500 mr-2 flex-shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-gray-700">{!! __('serving_houston_dallas_benefit') !!}</span>
                            </li>
                        </ul>
                    </div>

                    <!-- CTA Box -->
                    <div class="bg-yellow-500 rounded-lg shadow-lg p-6 text-center">
                        <h3 class="text-xl font-bold text-white mb-4">{{ __('schedule_free_inspection_cta') }}</h3>
                        <p class="text-white mb-6">{{ __('experts_answer_questions_guide_process') }}</p>
                        <button @click="$dispatch('open-appointment-modal')"
                            class="w-full bg-white text-yellow-500 px-6 py-3 rounded-lg font-bold hover:bg-gray-100 transition-colors">
                            {{ __('book_now') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
