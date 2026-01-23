@extends('layouts.main')

@section('title', __('warranties_meta_title'))

@section('meta')
    <meta name="description" content="{{ __('warranties_meta_description') }}">
    <meta name="keywords" content="{{ __('warranties_meta_keywords') }}">
@endsection

@push('styles')
    <style>
        .hero-section {
            margin-top: -2rem;
        }

        .warranty-image {
            transition: all 0.5s ease-in-out;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .warranty-image:hover {
            transform: scale(1.05);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .warranty-container {
            overflow: hidden;
        }
    </style>
@endpush

@section('content')
    <!-- Hero Section with Image Overlay -->
    <div class="relative h-[500px] w-full hero-section">
        <!-- Background Image -->
        <img src="{{ asset('assets/img/warranty-hero.webp') }}" alt="{{ __('roofing_warranties_houston_dallas_alt') }}"
            class="absolute inset-0 w-full h-full object-cover">

        <!-- Dark Overlay -->
        <div class="absolute inset-0 bg-black bg-opacity-50"></div>

        <!-- Content -->
        <div class="relative z-10 h-full flex items-center justify-center">
            <div class="text-center">
                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-4">
                    {{ __('roofing_warranties') }}</h1>
                <p class="text-lg sm:text-xl md:text-2xl text-white max-w-2xl mx-auto px-4 mb-12">
                    {{ __('industry_leading_coverage_areas') }}</p>

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
                            <li class="text-yellow-500 font-medium">{{ __('warranties') }}</li>
                        </ol>
                    </div>
                </nav>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="relative py-24">
        <div class="container mx-auto px-4">
            <div class="flex flex-wrap items-center justify-center warranty-container">
                <!-- Warranty Image 1 -->
                <div class="w-full md:w-1/2 px-4 mb-12">
                    <div class="relative overflow-hidden rounded-lg shadow-lg warranty-image h-full">
                        <img src="{{ asset('assets/img/warranty1.webp') }}" alt="{{ __('roofing_warranty_coverage_alt') }}"
                            class="w-full h-full object-contain">
                    </div>
                </div>

                <!-- Warranty Image 2 -->
                <div class="w-full md:w-1/2 px-4 mb-12">
                    <div class="relative overflow-hidden rounded-lg shadow-lg warranty-image h-full">
                        <img src="{{ asset('assets/img/warranty2.webp') }}" alt="{{ __('warranty_certificate_alt') }}"
                            class="w-full h-full object-contain">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
