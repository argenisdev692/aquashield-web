@props([
    'count' => 3,
    'title' => 'Latest From Our Blog',
    'subtitle' => 'Stay updated with the latest roofing trends, maintenance tips, and industry insights.',
])

<div class="bg-gray-50 dark:bg-gray-900 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center" data-aos="fade-up">
            <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                {{ $title }}
            </h2>
            <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-500 dark:text-gray-300">
                {{ $subtitle }}
            </p>
        </div>

        @php
            $posts = App\Models\Post::with('category')->latest()->take($count)->get();
        @endphp

        <div class="mt-16 grid gap-8 md:grid-cols-2 lg:grid-cols-{{ min($count, 3) }}">
            @foreach ($posts as $index => $post)
                <div class="flex flex-col rounded-lg shadow-lg overflow-hidden bg-white dark:bg-gray-800"
                    data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                    <div class="flex-shrink-0 relative overflow-hidden">
                        <a href="{{ route('blog.show', $post->post_title_slug) }}" class="block">
                            @if ($post->post_image)
                                <img class="h-64 w-full object-cover transform hover:scale-105 transition duration-500"
                                    src="{{ $post->post_image }}" alt="{{ $post->post_title }}">
                            @else
                                <div
                                    class="h-64 w-full bg-gradient-to-r from-yellow-400 to-yellow-600 flex items-center justify-center">
                                    <svg class="w-20 h-20 text-white opacity-75" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1M19 20a2 2 0 002-2V8a2 2 0 00-2-2h-1M19 20H9a2 2 0 01-2-2v-5">
                                        </path>
                                    </svg>
                                </div>
                            @endif

                            <!-- Date badge -->
                            <div class="absolute top-4 left-4">
                                <div
                                    class="bg-white dark:bg-gray-900 text-gray-800 dark:text-white px-3 py-1 rounded-full text-sm font-medium shadow-md">
                                    {{ $post->created_at->format('M d, Y') }}
                                </div>
                            </div>

                            <!-- Category badge -->
                            @if ($post->category)
                                <div class="absolute top-4 right-4">
                                    <div
                                        class="bg-yellow-500 text-white px-3 py-1 rounded-full text-sm font-medium shadow-md">
                                        {{ $post->category->blog_category_name }}
                                    </div>
                                </div>
                            @endif
                        </a>
                    </div>

                    <div class="flex-1 p-6 flex flex-col justify-between">
                        <div class="flex-1">
                            <a href="{{ route('blog.show', $post->post_title_slug) }}" class="block">
                                <h3
                                    class="text-xl font-semibold text-gray-900 dark:text-white hover:text-yellow-600 dark:hover:text-yellow-400 transition">
                                    {{ $post->post_title }}
                                </h3>
                                <p class="mt-3 text-base text-gray-500 dark:text-gray-300 line-clamp-3">
                                    {!! \Illuminate\Support\Str::limit(strip_tags($post->post_content), 120) !!}
                                </p>
                            </a>
                        </div>

                        <div class="mt-6">
                            <a href="{{ route('blog.show', $post->post_title_slug) }}"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition">
                                Read Article
                                <svg class="ml-2 -mr-1 w-4 h-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-12 text-center" data-aos="fade-up" data-aos-delay="300">
            <a href="{{ route('blog.index') }}"
                class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition">
                View All Articles
                <svg class="ml-2 -mr-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3">
                    </path>
                </svg>
            </a>
        </div>
    </div>
</div>
