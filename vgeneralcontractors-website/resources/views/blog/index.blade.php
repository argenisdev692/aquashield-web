@extends('layouts.blog')

@section('content')
    <div class="pt-16 lg:pt-20">
        <!-- Header Section with Search -->
        <div class="bg-gradient-to-r from-gray-800 to-gray-900 text-white py-28 blog-hero-section">
            <div class="container mx-auto px-4">
                <div class="max-w-4xl mx-auto text-center">
                    <h1 class="text-4xl md:text-5xl font-bold mb-6">{{ __('blog_our_blog') }}</h1>
                    <p class="text-lg md:text-xl text-gray-300 mb-8">
                        {{ __('blog_stay_informed_subtitle') }}
                    </p>

                    <!-- Livewire Search Component - Solo el formulario -->
                    @livewire('blog-search')
                </div>
            </div>
        </div>

        <!-- Blog Posts Grid with Search Results -->
        <div class="bg-gray-50 py-16">
            <div class="container mx-auto px-4">
                <!-- Search status indicator (cuando hay búsqueda activa) -->
                @livewire('blog-search-results')
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            /* Add this to fix the hero section position */
            .blog-hero-section {
                margin-top: -5rem;
                /* Adjust to compensate for navbar */
            }
        </style>
    @endpush
@endsection

@livewireStyles
<!-- Al final del body: -->
@livewireScripts
