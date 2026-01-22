<!-- Latest Blog Posts Section -->
<div class="bg-white py-6 sm:py-8 lg:py-12 fade-in-section">
    <div class="mx-auto max-w-screen-2xl px-4 md:px-8">
        <!-- text - start -->
        <div class="mb-10 md:mb-16">
            <h2 class="mb-4 text-center text-2xl font-bold text-gray-800 md:mb-6 lg:text-3xl">
                {{ __('latest_from_our_blog') }}
            </h2>
            <p class="mx-auto max-w-screen-md text-center text-gray-600 md:text-lg">{{ __('blog_section_description') }}
            </p>
        </div>
        <!-- text - end -->

        <div class="grid gap-4 sm:grid-cols-2 md:gap-6 lg:grid-cols-4">
            @php
                $latestPosts = App\Models\Post::where('post_status', 'published')->latest()->take(4)->get();
            @endphp

            @forelse($latestPosts as $post)
                <!-- article - start -->
                <a href="{{ route('blog.show', $post->post_title_slug) }}"
                    class="group relative flex h-48 flex-col overflow-hidden rounded-lg bg-gray-100 shadow-lg md:h-64">
                    @if ($post->post_image)
                        <img src="{{ $post->post_image }}" alt="{{ $post->post_title }}"
                            class="absolute inset-0 h-full w-full object-cover object-center transition duration-200 group-hover:scale-110"
                            width="600" height="400" loading="lazy">
                    @else
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-yellow-400 to-yellow-600 transition duration-200 group-hover:scale-110">
                        </div>
                    @endif
                    <div
                        class="pointer-events-none absolute inset-0 bg-gradient-to-t from-gray-800 to-transparent md:via-transparent">
                    </div>
                    <div class="relative mt-auto p-4">
                        <span class="block text-sm text-gray-200">{{ $post->created_at->format('F d, Y') }}</span>
                        <h2 class="mb-2 text-xl font-semibold text-white transition duration-100">
                            {{ \Illuminate\Support\Str::limit($post->post_title, 40) }}</h2>
                        <span class="font-semibold text-yellow-400">{{ __('read_more') }}</span>
                    </div>
                </a>
                <!-- article - end -->
            @empty
                <!-- Fallback content if no posts -->
                <div class="col-span-full text-center py-8">
                    <p class="text-lg text-gray-600">{{ __('check_back_soon_blog') }}</p>
                </div>
            @endforelse
        </div>

        <div class="mt-10 text-center">
            <a href="{{ route('blog.index') }}"
                class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-yellow-500 hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition">
                {{ __('view_all_articles') }}
                <svg class="ml-2 -mr-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3">
                    </path>
                </svg>
            </a>
        </div>
    </div>
</div>

<!-- Floating Call Button -->
<a href="tel:{{ $companyData->phone }}"
    class="fixed bottom-6 right-6 bg-yellow-500 text-white p-4 rounded-full shadow-lg hover:bg-yellow-600 transition-all duration-300 z-50">
    <svg class="w-6 h-6 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
    </svg>
</a>
