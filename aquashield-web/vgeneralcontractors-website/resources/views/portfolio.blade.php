@extends('layouts.main')

@section('title', __('portfolio_page_title'))
@section('meta_description', __('portfolio_meta_description'))
@section('meta_keywords', __('portfolio_meta_keywords'))
@section('og_title', __('portfolio_og_title'))
@section('og_description', __('portfolio_og_description'))
@section('og_image', asset('assets/img/portfolio-featured.webp'))
@section('twitter_title', __('portfolio_twitter_title'))
@section('twitter_description', __('portfolio_twitter_description'))
@section('twitter_image', asset('assets/img/portfolio-featured.webp'))

@section('content')
    <!-- Hero Section -->
    <section class="relative py-24 bg-gray-900 text-white">
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-black opacity-60"></div>
        </div>
        <div class="relative container mx-auto px-4">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">{{ __('our_portfolio') }}</h1>
            <p class="text-xl text-gray-300">{{ __('showcasing_roofing_excellence') }}</p>
        </div>
    </section>

    <!-- Breadcrumb -->
    <nav class="bg-gray-100 py-3">
        <div class="container mx-auto px-4">
            <div class="flex items-center space-x-2 text-gray-500">
                <a href="{{ route('home') }}" class="hover:text-yellow-500">{{ __('home') }}</a>
                <span>/</span>
                <span class="text-yellow-500">{{ __('portfolio') }}</span>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <section class="py-12 bg-white">
        <div class="container mx-auto px-4">
            <x-company-cam />
        </div>
    </section>
@endsection
