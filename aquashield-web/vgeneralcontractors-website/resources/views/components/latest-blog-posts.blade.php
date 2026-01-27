<div class="bg-white py-12 dark:bg-gray-900">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl">Latest From Our Blog
            </h2>
            <p class="mx-auto mt-3 max-w-2xl text-xl text-gray-500 dark:text-gray-300 sm:mt-4">
                Stay updated with the latest roofing trends, maintenance tips, and industry insights.
            </p>
        </div>

        <div class="mt-12 grid gap-8 md:grid-cols-2 lg:grid-cols-3">
            @foreach (App\Models\Post::latest()->take(3)->get() as $post)
                <div
                    class="flex flex-col overflow-hidden rounded-lg shadow-lg transition-transform duration-300 hover:scale-105 bg-white dark:bg-gray-800">
                    <a href="{{ route('blog.show', $post->post_title_slug) }}">
                        <div class="flex-shrink-0 relative">
                            @if ($post->post_image)
                                <img class="h-60 w-full object-cover" src="{{ $post->post_image }}"
                                    alt="{{ $post->post_title }}">
                            @else
                                <div class="h-60 w-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                </div>
                            @endif

                            <!-- Category badge -->
                            @if ($post->category)
                                <div class="absolute top-4 right-4">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-500 text-white">
                                        {{ $post->category->blog_category_name }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </a>

                    <div class="flex flex-1 flex-col justify-between p-6">
                        <div class="flex-1">
                            <a href="{{ route('blog.show', $post->post_title_slug) }}" class="block">
                                <h3
                                    class="text-xl font-semibold text-gray-900 dark:text-white hover:text-yellow-600 dark:hover:text-yellow-400 transition">
                                    {{ $post->post_title }}
                                </h3>
                                <p class="mt-3 text-base text-gray-500 dark:text-gray-300 line-clamp-3">
                                    {!! \Illuminate\Support\Str::limit(strip_tags($post->post_content), 150) !!}
                                </p>
                            </a>
                        </div>

                        <div class="mt-6 flex items-center">
                            <div class="flex-shrink-0">
                                <div
                                    class="h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                    <span class="text-gray-600 dark:text-gray-300 font-medium">
                                        {{ strtoupper(substr($post->user->name ?? 'A', 0, 1)) }}
                                    </span>
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $post->user->name ?? 'Admin' }}
                                </p>
                                <div class="flex space-x-1 text-sm text-gray-500 dark:text-gray-400">
                                    <time datetime="{{ $post->created_at->format('Y-m-d') }}">
                                        {{ $post->created_at->format('M d, Y') }}
                                    </time>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-10 text-center">
            <a href="{{ route('blog.index') }}"
                class="inline-block rounded-md border border-transparent bg-yellow-500 px-6 py-3 text-base font-medium text-white shadow-sm hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition">
                View All Posts
            </a>
        </div>
    </div>
</div>
