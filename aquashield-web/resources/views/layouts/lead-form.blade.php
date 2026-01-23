<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="transparent" />
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ secure_asset('assets/favicon_io/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32"
        href="{{ secure_asset('assets/favicon_io/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16"
        href="{{ secure_asset('assets/favicon_io/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ secure_asset('assets/favicon_io/site.webmanifest') }}">

    {{-- SEO Meta Tags for Lead Form --}}
    <title>Get Your Free Inspection - V General Contractors</title>
    <meta name="description"
        content="Fill out the form to schedule your free, no-obligation roofing inspection with V General Contractors. Serving Houston, Dallas, and Texas.">
    <meta name="keywords"
        content="free roof inspection, roofing estimate, V General Contractors, Houston roofing, Dallas roofing, Texas roofer">
    <link rel="canonical" href="{{ secure_url(route('facebook.lead.form', [], false)) }}">
    <meta name="robots" content="index,follow"> {{-- Or noindex if you prefer this page not be indexed directly --}}

    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ secure_url(route('facebook.lead.form', [], false)) }}">
    <meta property="og:title" content="Get Your Free Inspection - V General Contractors">
    <meta property="og:description"
        content="Fill out the form to schedule your free, no-obligation roofing inspection with V General Contractors.">
    <meta property="og:image" content="{{ secure_asset('assets/logo/logo3.webp') }}">
    <meta property="og:site_name" content="V General Contractors">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ secure_url(route('facebook.lead.form', [], false)) }}">
    <meta name="twitter:title" content="Get Your Free Inspection - V General Contractors">
    <meta name="twitter:description"
        content="Fill out the form to schedule your free, no-obligation roofing inspection with V General Contractors.">
    <meta name="twitter:image" content="{{ secure_asset('assets/logo/logo3.webp') }}">
    <style>
        ::-webkit-scrollbar {
            width: 12px;
        }

        ::-webkit-scrollbar-track {
            background-color: #e5e7eb;
            border-radius: 9px;
        }

        ::-webkit-scrollbar-thumb {
            background-color: #6b7280;
            border-radius: 7px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background-color: #e7c104;
            border-radius: 7px;
        }
    </style>
    {{-- Tracking Scripts (Include necessary ones) --}}
    <x-google-analytics />
    <x-facebook-pixel />

    {{-- Styles and Scripts via Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Additional Page-Specific Styles --}}
    @stack('styles')

    {{-- Google Maps API --}}
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places&callback=initAutocomplete&solution_channel=GMP_QB_addressselection_v4_cABC&v=beta"
        defer async></script>



</head>

<body class="bg-gray-50 font-sans antialiased">

    {{-- Content Section --}}
    @yield('content')

    {{-- Additional Page-Specific Scripts --}}
    @stack('scripts')

    {{-- Additional Body Scripts (If needed for specific integrations on this page) --}}
    @stack('body-scripts')

</body>

</html>
