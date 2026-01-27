<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/favicon_io/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/favicon_io/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/favicon_io/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('assets/favicon_io/site.webmanifest') }}">

    <meta name="theme-color" content="transparent" />

    <!-- Primary Meta Tags -->
    <title>{{ $companyData->company_name }} | {{ __('professional_roofing_texas') }}</title>
    <meta name="title" content="{{ __('meta_title') }}">
    <meta name="description" content="{{ __('meta_description') }}">
    <meta name="keywords" content="{{ __('meta_keywords') }}">
    <meta name="robots" content="index,follow">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ __('og_title') }}">
    <meta property="og:description" content="{{ __('og_description') }}">
    <meta property="og:image" content="{{ asset('assets/logo/logo3.webp') }}">
    <meta property="og:site_name" content="{{ $companyData->name }}">
    <meta property="fb:app_id" content="123456789">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="{{ __('twitter_title') }}">
    <meta property="twitter:description" content="{{ __('twitter_description') }}">
    <meta property="twitter:image" content="{{ asset('assets/logo/logo3.webp') }}">
    <meta name="twitter:site" content="@VGeneralContractors">
    <meta name="twitter:creator" content="@VGeneralContractors">

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Schema.org markup -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "RoofingContractor",
        "name": "{{ $companyData->name }}",
        "description": "Leading commercial and residential roofing contractor in Texas. GAF certified experts providing professional installation, repair, and maintenance services for businesses and homes in Houston & Dallas.",
        "image": "{{ asset('assets/logo/logo3.webp') }}",
        "url": "{{ url()->current() }}",
        "telephone": "{{ $companyData->phone }}",
        "email": "info@vgeneralcontractors.com",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "1302 Waugh Dr # 810",
            "addressLocality": "Houston",
            "addressRegion": "TX",
            "postalCode": "77019",
            "addressCountry": "US"
        },
        "geo": {
            "@type": "GeoCoordinates",
            "latitude": "29.7520",
            "longitude": "-95.3810"
        },
        "areaServed": {
            "@type": "GeoCircle",
            "geoMidpoint": {
                "@type": "GeoCoordinates",
                "latitude": "29.7520",
                "longitude": "-95.3810"
            },
            "geoRadius": "50000"
        },
        "sameAs": [
            "https://facebook.com/vgeneralcontractors",
            "https://twitter.com/vgeneralcontractors",
            "https://instagram.com/vgeneralcontractors",
            "https://linkedin.com/company/vgeneralcontractors"
        ],
        "openingHours": "Mo,Tu,We,Th,Fr 09:00-17:00",
        "priceRange": "$$",
        "makesOffer": [
            {
                "@type": "Offer",
                "name": "Commercial Roofing Services",
                "description": "Comprehensive commercial roofing solutions including installation, repair, and maintenance for businesses across Texas"
            },
            {
                "@type": "Offer",
                "name": "Residential Roofing Solutions",
                "description": "Expert residential roofing services including new installations, repairs, and maintenance in Houston and Dallas"
            },
            {
                "@type": "Offer",
                "name": "Emergency Roof Repair",
                "description": "24/7 emergency roof repair services for commercial and residential properties in Texas"
            },
            {
                "@type": "Offer",
                "name": "Storm Damage Restoration",
                "description": "Professional storm damage assessment and restoration services with insurance claim assistance"
            },
            {
                "@type": "Offer",
                "name": "Commercial Roof Maintenance",
                "description": "Preventive maintenance programs and regular inspections for commercial roofing systems"
            },
            {
                "@type": "Offer",
                "name": "Industrial Roofing Solutions",
                "description": "Specialized roofing solutions for industrial facilities and warehouses across Texas"
            }
        ],
        "hasCredential": [
            {
                "@type": "EducationalOccupationalCredential",
                "credentialCategory": "certification",
                "name": "GAF Certified Commercial Roofing Contractor",
                "validIn": {
                    "@type": "State",
                    "name": "Texas"
                }
            }
        ]
    }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <x-google-analytics />
    <x-facebook-pixel />
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
    <!-- Local Roboto Font -->
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }

        /* Fade In Animation */
        .fade-in-section {
            opacity: 0;
            transform: translateY(20px);
            visibility: hidden;
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
            will-change: opacity, visibility;
        }

        .fade-in-section.is-visible {
            opacity: 1;
            transform: translateY(0);
            visibility: visible;
        }
    </style>
    <!-- Add Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <!-- Intersection Observer for Fade In -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const observerOptions = {
                root: null,
                rootMargin: '0px',
                threshold: 0.1
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.fade-in-section').forEach((section) => {
                observer.observe(section);
            });
        });
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places"
        defer></script>
    @include('cookie-consent::index')
</head>

<body class="bg-gray-100 font-sans" x-data>
    <x-navbar />
    <x-facebook-lead-modal />

    <x-hero />
    <x-city-locations />
    <x-about-us />
    <x-services />
    <x-service-cards />
    <x-inline-facebook-lead-form />
    <x-why-us />
    <x-video-section />
    <x-certifications />
    <x-financing />
    <x-blog-posts />

    <!-- Add CompanyCam Portfolio Showcase -->

    <x-company-cam />


    <x-footer />

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
