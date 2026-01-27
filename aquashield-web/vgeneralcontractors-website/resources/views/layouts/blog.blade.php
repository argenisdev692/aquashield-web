@php
    use Artesaos\SEOTools\Facades\SEOTools;
    use Artesaos\SEOTools\Facades\SEOMeta;
    use Artesaos\SEOTools\Facades\OpenGraph;
    use Artesaos\SEOTools\Facades\TwitterCard;
    use Artesaos\SEOTools\Facades\JsonLd;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="transparent" />

    <!-- SEO Tools Integration -->
    {!! SEOTools::generate() !!}

    <!-- Fallback title if SEO Tools doesn't set one -->
    @if (empty(SEOTools::getTitle()))
        <title>{{ isset($title) ? $title . ' - ' . config('app.name') : config('app.name') . ' - Blog' }}</title>
    @endif

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ secure_asset('assets/favicon_io/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32"
        href="{{ secure_asset('assets/favicon_io/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16"
        href="{{ secure_asset('assets/favicon_io/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ secure_asset('assets/favicon_io/site.webmanifest') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles

    <!-- SEO Metadata -->
    <meta name="generator" content="V General Contractors Blog">
    <meta name="robots" content="index, follow">

    @if (isset($post))
        <meta property="og:title" content="{{ $post->post_title }}">
        <meta property="og:description" content="{{ strip_tags(Str::limit($post->post_content, 160)) }}">
        <meta property="og:type" content="article">
        <meta property="og:url" content="{{ secure_url(URL::current()) }}">
        @if ($post->post_image)
            <meta property="og:image" content="{{ $post->post_image }}">
        @endif
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $post->post_title }}">
        <meta name="twitter:description" content="{{ strip_tags(Str::limit($post->post_content, 160)) }}">
        @if ($post->post_image)
            <meta name="twitter:image" content="{{ $post->post_image }}">
        @endif
    @else
        <meta property="og:title" content="{{ __('blog_og_title') }}">
        <meta property="og:description" content="{{ __('blog_og_description') }}">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ secure_url(URL::current()) }}">
        <meta property="og:image" content="{{ secure_asset('assets/img/blog-share.jpg') }}">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ __('blog_twitter_title') }}">
        <meta name="twitter:description" content="{{ __('blog_twitter_description') }}">
        <meta name="twitter:image" content="{{ secure_asset('assets/img/blog-share.jpg') }}">
    @endif

    <link rel="alternate" type="application/rss+xml" title="{{ config('app.name') }} Blog"
        href="{{ secure_url(route('feeds.rss', [], false)) }}">

    <x-google-analytics />
    <x-facebook-pixel />

    @stack('styles')
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
</head>

<body class="font-sans antialiased bg-gray-100">
    <!-- Navbar -->
    <x-navbar />

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <x-footer />

    @livewireScripts
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('scripts')
</body>

</html>
