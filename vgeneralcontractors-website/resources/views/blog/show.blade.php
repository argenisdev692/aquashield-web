@extends('layouts.blog')

@section('content')
    <!-- Agregar Schema.org Article markup -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BlogPosting",
        "headline": "{{ $post->post_title }}",
        "description": "{{ strip_tags(Str::limit($post->post_content, 160)) }}",
        "image": "{{ $post->post_image ?: asset('assets/img/default-blog.jpg') }}",
        "datePublished": "{{ $post->created_at->toIso8601String() }}",
        "dateModified": "{{ $post->updated_at->toIso8601String() }}",
        "author": {
            "@type": "Person",
            "name": "{{ $post->user->name ?? 'V General Contractors' }}"
        },
        "publisher": {
            "@type": "Organization",
            "name": "V General Contractors",
            "logo": {
                "@type": "ImageObject",
                "url": "{{ asset('assets/logo/logo3.webp') }}"
            }
        },
        "mainEntityOfPage": {
            "@type": "WebPage",
            "@id": "{{ url()->current() }}"
        }
    }
    </script>

    <div class="pt-16 lg:pt-20">
        <!-- Featured image -->
        @if ($post->post_image)
            <div class="relative h-96 md:h-[500px] w-full blog-hero-section">
                <img src="{{ $post->post_image }}" alt="{{ $post->post_title }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-black opacity-40"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="container mx-auto px-4">
                        <div class="max-w-4xl mx-auto text-white text-center">
                            <p class="mb-4">
                                <span class="px-3 py-1 bg-yellow-500 rounded-full text-sm font-semibold">
                                    {{ $post->category ? $post->category->blog_category_name : 'Blog' }}
                                </span>
                            </p>
                            <h1 class="text-3xl md:text-5xl font-bold mb-4 drop-shadow-md">{{ $post->post_title }}</h1>
                            <div class="flex items-center justify-center text-sm">
                                <span class="font-bold ">{{ $post->formatted_date }}</span>
                                <span class="mx-2">•</span>
                                <span>{{ \Illuminate\Support\Str::readDuration($post->post_content) }}
                                    {{ __('blog_min_read') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- No featured image version -->
        @if (!$post->post_image)
            <div class="bg-gray-100 py-16">
                <div class="container mx-auto px-4">
                    <div class="max-w-4xl mx-auto text-center">
                        <p class="mb-4">
                            <span class="px-3 py-1 bg-yellow-500 text-white rounded-full text-sm font-semibold">
                                {{ $post->category ? $post->category->blog_category_name : 'Blog' }}
                            </span>
                        </p>
                        <h1 class="text-3xl md:text-5xl font-bold text-gray-900 mb-6">{{ $post->post_title }}</h1>
                        <div class="flex items-center justify-center text-sm text-gray-600">
                            <span class="font-bold text-yellow-600">{{ $post->formatted_date }}</span>
                            <span class="mx-2">•</span>
                            <span>{{ \Illuminate\Support\Str::readDuration($post->post_content) }}
                                {{ __('blog_min_read') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Article content -->
        <div class="py-12">
            <div class="container mx-auto px-4">
                <div class="max-w-3xl mx-auto">
                    <!-- Author info -->
                    <div class="flex items-center mb-8 pb-8 border-b border-gray-200">
                        <div class="h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center">
                            <span class="text-gray-600 font-bold">
                                {{ strtoupper(substr($post->user->name ?? 'A', 0, 1)) }}
                            </span>
                        </div>
                        <div class="ml-4">
                            <div class="font-medium text-gray-900">{{ $post->user->name ?? 'Admin' }}</div>
                            <div class="text-gray-500 text-sm">{{ __('blog_author') }}</div>
                        </div>

                        <!-- Share buttons -->
                        <div class="ml-auto">
                            <div class="flex space-x-3">
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                                    target="_blank" class="text-gray-400 hover:text-blue-600 transition">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M18.77,7.46H14.5v-1.9c0-.9.6-1.1,1-1.1h3V.5h-4.33c-3.28,0-5.37,1.54-5.37,4.47v2.49H5.5v4h2.8V24h6.2V11.46h3.27L18.77,7.46Z" />
                                    </svg>
                                </a>
                                <a href="https://twitter.com/intent/tweet?text={{ urlencode($post->post_title) }}&url={{ urlencode(url()->current()) }}"
                                    target="_blank" class="text-gray-400 hover:text-blue-400 transition">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M23.44,4.83c-.8.37-1.5.38-2.22.02.93-.56.98-.96,1.32-2.02-.88.52-1.86.9-2.9,1.1-.82-.88-2-1.43-3.3-1.43-2.5,0-4.55,2.04-4.55,4.54,0,.36.03.7.1,1.04-3.77-.2-7.12-2-9.36-4.75-.4.67-.6,1.45-.6,2.3,0,1.56.8,2.95,2,3.77-.74-.03-1.44-.23-2.05-.57v.06c0,2.2,1.56,4.03,3.64,4.44-.67.2-1.37.2-2.06.08.58,1.8,2.26,3.12,4.25,3.16C5.78,18.1,3.37,18.74,1,18.46c2,1.3,4.4,2.04,6.97,2.04,8.35,0,12.92-6.92,12.92-12.93,0-.2,0-.4-.02-.6.9-.63,1.96-1.22,2.56-2.14Z" />
                                    </svg>
                                </a>
                                <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(url()->current()) }}&title={{ urlencode($post->post_title) }}"
                                    target="_blank" class="text-gray-400 hover:text-blue-700 transition">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M20.447,20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853,0-2.136,1.445-2.136,2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9,1.637-1.85,3.37-1.85,3.601,0,4.267,2.37,4.267,5.455v6.286ZM5.337,7.433c-1.144,0-2.063-.926-2.063-2.065,0-1.138.92-2.063,2.063-2.063,1.14,0,2.064.925,2.064,2.063,0,1.139-.925,2.065-2.064,2.065Zm1.782,13.019H3.555V9h3.564v11.452ZM22.225,0H1.771C.792,0,0,.774,0,1.729v20.542C0,23.227.792,24,1.771,24h20.451C23.2,24,24,23.227,24,22.271V1.729C24,.774,23.2,0,22.222,0h.003Z" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Post content -->
                    <article
                        class="prose prose-lg max-w-none prose-headings:text-gray-900 prose-headings:font-bold prose-p:text-gray-700 prose-img:rounded-lg">
                        {!! $post->post_content !!}
                    </article>

                    <!-- Tags -->
                    @if ($post->meta_keywords)
                        <div class="mt-12 pt-6 border-t border-gray-200">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">{{ __('blog_tags') }}</h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach (explode(',', $post->meta_keywords) as $tag)
                                    <a href="{{ route('blog.search', ['q' => trim($tag)]) }}"
                                        class="px-4 py-2 bg-gray-100 rounded-full text-sm text-gray-700 hover:bg-yellow-100 transition">
                                        #{{ trim($tag) }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Related posts -->
                    @if (isset($relatedPosts) && $relatedPosts->count() > 0)
                        <div class="mt-12 pt-6 border-t border-gray-200">
                            <h4 class="text-2xl font-semibold text-gray-900 mb-6">{{ __('blog_you_might_also_like') }}</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                @foreach ($relatedPosts as $relatedPost)
                                    <a href="{{ route('blog.show', $relatedPost->post_title_slug) }}" class="group">
                                        <div
                                            class="overflow-hidden rounded-lg shadow-md h-full bg-white hover:shadow-xl transition-shadow">
                                            <div class="aspect-[16/9] overflow-hidden bg-gray-200">
                                                @if ($relatedPost->post_image)
                                                    <img src="{{ $relatedPost->post_image }}"
                                                        alt="{{ $relatedPost->post_title }}"
                                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                                @else
                                                    <div
                                                        class="w-full h-full bg-gradient-to-r from-yellow-400 to-yellow-600 flex items-center justify-center">
                                                        <svg class="w-10 h-10 text-white" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1M19 20a2 2 0 002-2V8a2 2 0 00-2-2h-1M19 20H9a2 2 0 01-2-2v-5">
                                                            </path>
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="p-4">
                                                <h5
                                                    class="text-lg font-medium text-gray-900 mb-2 group-hover:text-yellow-600 transition line-clamp-2">
                                                    {{ $relatedPost->post_title }}</h5>
                                                <p class="text-sm text-gray-500">
                                                    {{ Carbon\Carbon::parse($relatedPost->created_at)->format('M d, Y') }}
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Back to blog button -->
                    <div class="mt-12 text-center">
                        <a href="{{ route('blog.index') }}"
                            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-yellow-500 hover:bg-yellow-600 transition">
                            <svg class="mr-2 w-5 h-5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                            {{ __('blog_back_to_blog') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            /* Add this to fix the hero image position */
            .blog-hero-section {
                margin-top: -5rem;
                /* Adjust to compensate for navbar */
            }

            /* Existing styles */
            .prose img {
                @apply my-6 rounded-lg shadow-md;
            }

            .prose h2 {
                @apply text-2xl mt-8 mb-4;
            }

            .prose h3 {
                @apply text-xl mt-6 mb-3;
            }

            .prose a {
                @apply text-yellow-600 hover:text-yellow-700 transition-colors;
            }

            .prose ul,
            .prose ol {
                @apply mb-6;
            }
        </style>
    @endpush


@endsection
