@extends('layouts.main')

@section('title', 'Appointment Confirmation - V General Contractors')
@section('meta_description',
    'Your free inspection request has been received. V General Contractors will contact you
    soon.')
    {{-- Add other meta tags if needed --}}

@section('content')
    {{-- Include Navbar --}}
    <x-navbar />

    {{-- Hero Section (Styled like portfolio) --}}
    <section class="relative py-24 bg-gray-900 text-white">
        <div class="absolute inset-0">
            {{-- Optional: Add background image like portfolio if desired --}}
            {{-- <img src="{{ asset('assets/img/confirmation-hero.webp') }}" alt="Confirmation Background" class="w-full h-full object-cover"> --}}
            <div class="absolute inset-0 bg-black opacity-60"></div>
        </div>
        <div class="relative container mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">V General Contractors</h1>
            <p class="text-xl text-gray-300">Thank you. You're all set.</p>
        </div>
    </section>

    {{-- Breadcrumb (Optional but good practice) --}}
    <nav class="bg-gray-100 py-3">
        <div class="container mx-auto px-4">
            <div class="flex items-center space-x-2 text-gray-500">
                <a href="{{ route('home') }}" class="hover:text-yellow-500">Home</a>
                <span>/</span>
                <span class="text-yellow-500">Appointment Confirmation</span>
            </div>
        </div>
    </nav>

    {{-- Confirmation Content --}}
    <section class="py-12 bg-white">
        <div class="container mx-auto px-4 max-w-3xl">
            @if (session('appointment_success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md" role="alert">
                    <p class="font-bold">Success!</p>
                    <p>{{ session('appointment_success') }}</p>
                </div>
            @endif

            {{-- Green Check Icon --}}
            <div class="flex justify-center mb-8">
                <div class="rounded-full bg-green-500 p-4 shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-white" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>

            <div class="prose prose-lg max-w-none text-gray-700">
                <p class="lead">By completing this form, you authorize V General Contractors to contact you to schedule
                    your free inspection.</p>
                <p>An agent or virtual assistant will call you from <strong
                        class="text-gray-900">{{ App\Helpers\PhoneHelper::format($companyData->phone) }}</strong> to
                    coordinate your appointment.</p>
                <p>Your information is confidential and will be used only for this purpose.</p>
            </div>

            <div class="mt-8 text-center">
                <a href="{{ route('home') }}"
                    class="inline-block bg-yellow-500 text-white font-bold py-3 px-6 rounded-lg hover:bg-yellow-600 transition duration-300 ease-in-out shadow-md">
                    Return to Homepage
                </a>
            </div>
        </div>
    </section>
@endsection
