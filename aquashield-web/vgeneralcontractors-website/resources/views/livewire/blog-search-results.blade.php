<div>
    <!-- Search status indicator -->
    @if ($isSearching)
        <div class="mb-8 text-center">
            <h2 class="text-2xl font-bold text-gray-700">
                {{ $posts->total() }} {{ Str::plural(__('result'), $posts->total()) }} {{ __('for') }}
                "{{ $query }}"
            </h2>
            <button wire:click="clearSearch" class="mt-2 text-yellow-600 hover:text-yellow-700 inline-flex items-center">
                <span>{{ __('clear_search') }}</span>
                <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>
    @endif

    <!-- Loading indicator -->
    <div wire:loading class="flex justify-center my-8">
        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-yellow-500"></div>
    </div>

    <!-- Posts Grid -->
    <div wire:loading.remove class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($posts as $post)
            <article
                class="bg-white rounded-lg shadow-md overflow-hidden transition-transform duration-300 hover:shadow-xl hover:-translate-y-1">
                <a href="{{ route('blog.show', $post->post_title_slug) }}" class="block">
                    <div class="aspect-[16/9] overflow-hidden bg-gray-200">
                        @if ($post->post_image)
                            <img src="{{ $post->post_image }}" alt="{{ $post->post_title }}"
                                class="w-full h-full object-cover object-center transition duration-500 hover:scale-105">
                        @else
                            <div
                                class="w-full h-full bg-gradient-to-r from-yellow-400 to-yellow-600 flex items-center justify-center">
                                <svg class="w-16 h-16 text-white opacity-75" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1M19 20a2 2 0 002-2V8a2 2 0 00-2-2h-1M19 20H9a2 2 0 01-2-2v-5">
                                    </path>
                                </svg>
                            </div>
                        @endif
                    </div>
                </a>

                <div class="p-6">
                    @if ($post->category)
                        <a href="{{ route('blog.category', $post->category->blog_category_name) }}"
                            class="inline-block px-3 py-1 rounded-full bg-yellow-100 text-yellow-800 text-xs font-medium mb-3 hover:bg-yellow-200 transition">
                            {{ $post->category->blog_category_name }}
                        </a>
                    @endif

                    <a href="{{ route('blog.show', $post->post_title_slug) }}" class="block">
                        <h2 class="text-xl font-bold text-gray-900 mb-3 hover:text-yellow-600 transition line-clamp-2">
                            {{ $post->post_title }}
                        </h2>
                    </a>

                    <div class="text-gray-600 mb-4 line-clamp-3">
                        {!! \Illuminate\Support\Str::limit(strip_tags($post->post_content), 150) !!}
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                                <span class="text-gray-600 font-medium text-sm">
                                    {{ strtoupper(substr($post->user->name ?? 'A', 0, 1)) }}
                                </span>
                            </div>
                            <span class="ml-2 text-sm text-gray-700">{{ $post->user->name ?? 'Admin' }}</span>
                        </div>
                        <span class="text-sm text-gray-500">{{ $post->created_at->format('M d, Y') }}</span>
                    </div>

                    <a href="{{ route('blog.show', $post->post_title_slug) }}"
                        class="inline-flex items-center mt-4 text-yellow-600 hover:text-yellow-700">
                        {{ __('read_more_blog') }}
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </a>
                </div>
            </article>
        @empty
            <div class="col-span-3 text-center py-16">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-xl font-medium text-gray-700 mb-2">
                    @if ($isSearching)
                        {{ __('no_results_found_for') }} "{{ $query }}"
                    @else
                        {{ __('no_posts_available') }}
                    @endif
                </h3>
                <p class="text-gray-500 mb-6">
                    @if ($isSearching)
                        {{ __('try_different_search_term') }}
                    @else
                        {{ __('working_on_content') }}
                    @endif
                </p>
                @if ($isSearching)
                    <button wire:click="clearSearch"
                        class="inline-flex items-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-md transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                        </svg>
                        {{ __('back_to_blog') }}
                    </button>
                @endif
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-12">
        {{ $posts->links() }}
    </div>
</div>
