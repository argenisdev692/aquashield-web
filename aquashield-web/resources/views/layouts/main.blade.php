<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="transparent" />

    {{-- Primary SEO Meta Tags --}}
    <title>@yield('title', 'V General Contractors') - V General Contractors</title>
    <meta name="description" content="@yield('meta_description', 'Expert commercial and residential roofing services in Texas. GAF certified contractors in Houston & Dallas specializing in installation, repairs, and storm damage restoration.')">
    <meta name="keywords" content="@yield('meta_keywords', 'roofing contractor, commercial roofing, residential roofing, roof repair, storm damage, GAF certified, Houston, Dallas, Texas')">
    <link rel="canonical" href="@yield('canonical_url', secure_url(URL::current()))">
    <meta name="robots" content="index,follow">
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ secure_asset('assets/favicon_io/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32"
        href="{{ secure_asset('assets/favicon_io/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16"
        href="{{ secure_asset('assets/favicon_io/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ secure_asset('assets/favicon_io/site.webmanifest') }}">

    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="@yield('og_url', secure_url(URL::current()))">
    <meta property="og:title" content="@yield('og_title', View::yieldContent('title') . ' - V General Contractors')">
    <meta property="og:description" content="@yield('og_description', View::yieldContent('meta_description'))">
    <meta property="og:image" content="@yield('og_image', secure_asset('assets/logo/logo3.webp'))">
    <meta property="og:site_name" content="V General Contractors">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="@yield('twitter_card', 'summary_large_image')">
    <meta name="twitter:url" content="@yield('twitter_url', secure_url(URL::current()))">
    <meta name="twitter:title" content="@yield('twitter_title', View::yieldContent('title') . ' - V General Contractors')">
    <meta name="twitter:description" content="@yield('twitter_description', View::yieldContent('meta_description'))">
    <meta name="twitter:image" content="@yield('twitter_image', secure_asset('assets/logo/logo3.webp'))">
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
    <x-facebook-pixel />
    <!-- Styles and Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Additional Styles -->
    @stack('styles')

    <!-- Additional Scripts -->
    @stack('scripts')
</head>

<body class="bg-gray-100">
    <x-navbar />

    @yield('content')

    <x-footer />

    {{-- <x-appointment-modal /> --}}
    <x-facebook-lead-modal />

    @include('cookie-consent::index')

    <!-- Additional Body Scripts -->
    @stack('body-scripts')

    <!-- CompanyCam Showcase Initialization -->
    <script>
        (function() {
            var ccShowcaseRoot = document.getElementById("companycam-showcase-root");
            if (!ccShowcaseRoot || !ccShowcaseRoot.attachShadow || !window.fetch) {
                return;
            }
            var ccShowcaseRootParent = document.getElementsByTagName("body")[0];
            var ccShowcaseScript = document.createElement("script");
            ccShowcaseScript.src = 'https://showcase.companycam.com/bundle.js';
            ccShowcaseScript.type = "text/javascript";
            ccShowcaseRootParent.appendChild(ccShowcaseScript);
        })();
    </script>
</body>

</html>
