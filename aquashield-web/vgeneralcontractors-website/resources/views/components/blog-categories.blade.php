<div class="bg-white rounded-lg shadow-sm p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Categories</h3>
    <ul class="space-y-2">
        @forelse($blogCategories as $category)
            <li>
                <a href="{{ route('blog.category', $category->blog_category_name) }}"
                    class="flex items-center text-gray-700 hover:text-yellow-600 transition-colors">
                    <span class="mr-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                    </span>
                    {{ $category->blog_category_name }}
                    @if (isset($category->posts_count))
                        <span
                            class="ml-auto text-xs bg-gray-100 px-2 py-1 rounded-full">{{ $category->posts_count }}</span>
                    @endif
                </a>
            </li>
        @empty
            <li class="text-gray-500">No categories found</li>
        @endforelse
    </ul>
</div>
