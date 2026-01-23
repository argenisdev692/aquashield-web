<x-app-layout>
    {{-- Dark background container with consistent styling --}}
    <div class="min-h-screen bg-gray-900" style="background-color: #141414;">
        {{-- Header section with title and subtitle --}}
        <div class="p-4 sm:p-6">
            <div class="mb-4 sm:mb-8 text-center sm:text-center md:text-left lg:text-left">
                <h2 class="text-base sm:text-base md:text-2xl lg:text-2xl font-bold text-white mb-2">
                    {{ __('posts_management') }}
                </h2>
                <p class="text-base sm:text-base md:text-base lg:text-base text-gray-400">
                    {{ __('manage_blog_posts_crud') }}
                </p>
            </div>
        </div>

        {{-- Main content area --}}
        <div class="max-w-7xl mx-auto py-2 sm:py-4 md:py-2 lg:py-2 px-4 sm:px-6 lg:px-8 pb-12">
            {{-- Alert container para AJAX --}}
            <div id="alertMessage"></div>

            {{-- Success and error messages --}}
            @if (session()->has('message'))
                <div class="mb-6 bg-green-500/20 border border-green-500/30 text-green-400 px-4 py-3 rounded-lg">
                    {{ session('message') }}
                </div>
            @endif
            @if (session()->has('error'))
                <div class="mb-6 bg-red-500/20 border border-red-500/30 text-red-400 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Main container --}}
            <div class="dark:bg-gray-800 overflow-hidden shadow-xl rounded-lg">
                <div class="p-6">
                    {{-- Header with search and controls --}}
                    <div
                        class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 space-y-4 md:space-y-0">
                        {{-- Search bar --}}
                        <div class="relative flex-1 max-w-md">
                            <input type="text" id="searchInput" value="{{ request('search') }}"
                                placeholder="{{ __('search_posts') }}"
                                class="w-full text-gray-300 placeholder-gray-500 rounded-lg px-4 py-2 pl-10 focus:outline-none focus:ring-2 focus:ring-yellow-500 border-0"
                                style="background-color: #2C2E36;">
                            <svg class="w-5 h-5 text-gray-500 absolute left-3 top-2.5" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>

                        {{-- Controls --}}
                        <div
                            class="flex flex-col sm:flex-row items-center w-full md:w-auto space-y-3 sm:space-y-0 sm:space-x-4">
                            {{-- Show deleted toggle --}}
                            <label class="flex items-center text-gray-300 cursor-pointer">
                                <div class="relative">
                                    <input type="checkbox" id="showDeletedToggle"
                                        {{ request('show_deleted') === 'true' ? 'checked' : '' }} class="sr-only">
                                    <div
                                        class="block bg-gray-600 w-14 h-8 rounded-full transition-colors duration-200 ease-in-out {{ request('show_deleted') === 'true' ? 'bg-yellow-500' : '' }}">
                                    </div>
                                    <div
                                        class="dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition-transform duration-200 ease-in-out {{ request('show_deleted') === 'true' ? 'transform translate-x-6' : '' }}">
                                    </div>
                                </div>
                                <span class="ml-3 text-sm">{{ __('show_deleted') }}</span>
                            </label>

                            {{-- Per page dropdown --}}
                            <select id="perPage"
                                class="text-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-500 border-0"
                                style="background-color: #2C2E36;">
                                <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10
                                    {{ __('per_page') }}</option>
                                <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25
                                    {{ __('per_page') }}</option>
                                <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50
                                    {{ __('per_page') }}</option>
                                <option value="100" {{ request('per_page', 10) == 100 ? 'selected' : '' }}>100
                                    {{ __('per_page') }}</option>
                            </select>

                            {{-- Create button --}}
                            <a href="{{ route('posts-crud.create') }}"
                                class="bg-yellow-500 hover:bg-yellow-600 text-gray-900 px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                {{ __('create_post') }}
                            </a>
                        </div>
                    </div>

                    {{-- Posts table --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-700">
                            <thead class="bg-gray-700">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                        {{ __('image') }}
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                        <a href="{{ request()->fullUrlWithQuery(['sort_field' => 'post_title', 'sort_direction' => request('sort_field') === 'post_title' && request('sort_direction') === 'asc' ? 'desc' : 'asc']) }}"
                                            class="hover:text-yellow-400">
                                            {{ __('title') }}
                                            @if (request('sort_field') === 'post_title')
                                                @if (request('sort_direction') === 'asc')
                                                    ↑
                                                @else
                                                    ↓
                                                @endif
                                            @endif
                                        </a>
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                        {{ __('category') }}
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                        {{ __('status') }}
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                        <a href="{{ request()->fullUrlWithQuery(['sort_field' => 'created_at', 'sort_direction' => request('sort_field') === 'created_at' && request('sort_direction') === 'asc' ? 'desc' : 'asc']) }}"
                                            class="hover:text-yellow-400">
                                            {{ __('created') }}
                                            @if (request('sort_field') === 'created_at')
                                                @if (request('sort_direction') === 'asc')
                                                    ↑
                                                @else
                                                    ↓
                                                @endif
                                            @endif
                                        </a>
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                        {{ __('author') }}
                                    </th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-300 uppercase tracking-wider">
                                        {{ __('actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="postsTable" class="bg-gray-800 divide-y divide-gray-700">
                                @forelse($posts as $post)
                                    <tr class="hover:bg-gray-750 {{ $post->deleted_at ? 'opacity-60' : '' }}">
                                        <td class="px-4 py-3 text-sm text-gray-400">
                                            @if ($post->post_image)
                                                <img class="h-12 w-12 object-cover rounded-md"
                                                    src="{{ Str::startsWith($post->post_image, 'http://') ? Str::replaceFirst('http://', 'https://', $post->post_image) : $post->post_image }}"
                                                    alt="Post image" onerror="this.style.display='none'">
                                            @else
                                                <div
                                                    class="h-12 w-12 flex items-center justify-center bg-gray-700 rounded-md">
                                                    <svg class="w-6 h-6 text-gray-500" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14">
                                                        </path>
                                                    </svg>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-200">
                                                {{ Str::limit($post->post_title, 50) }}
                                            </div>
                                            <div class="text-sm text-gray-400">
                                                {{ Str::limit(strip_tags($post->post_content), 80) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-500/20 text-blue-400">
                                                {{ $post->category->blog_category_name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($post->post_status === 'published')
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-500/20 text-green-400">
                                                    {{ __('published') }}
                                                </span>
                                            @elseif($post->post_status === 'scheduled')
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-500/20 text-orange-400">
                                                    {{ __('scheduled') }}
                                                </span>
                                            @else
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-500/20 text-gray-400">
                                                    {{ $post->post_status }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                            {{ $post->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                            {{ $post->user->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            <div class="flex justify-center space-x-2">
                                                @if (!$post->deleted_at)
                                                    {{-- Show button (only for published posts) - FIRST --}}
                                                    @if ($post->post_status === 'published')
                                                        <a href="{{ route('blog.show', $post->post_title_slug) }}"
                                                            target="_blank"
                                                            class="inline-flex items-center justify-center w-9 h-9 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105"
                                                            title="{{ __('View post') }}">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24" stroke-width="2">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                            </svg>
                                                        </a>
                                                    @endif

                                                    {{-- Edit button - SECOND --}}
                                                    <a href="{{ route('posts-crud.edit', $post->uuid) }}"
                                                        class="inline-flex items-center justify-center w-9 h-9 bg-gradient-to-r from-yellow-500 to-yellow-600 text-white rounded-lg hover:from-yellow-600 hover:to-yellow-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105"
                                                        title="{{ __('Edit post') }}">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </a>

                                                    {{-- Delete button - THIRD --}}
                                                    <button data-uuid="{{ $post->uuid }}"
                                                        data-title="{{ addslashes($post->post_title) }}"
                                                        class="delete-btn inline-flex items-center justify-center w-9 h-9 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg hover:from-red-600 hover:to-red-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105"
                                                        title="{{ __('Delete post') }}">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                @else
                                                    {{-- Restore button --}}
                                                    <button data-uuid="{{ $post->uuid }}"
                                                        data-title="{{ addslashes($post->post_title) }}"
                                                        class="restore-btn inline-flex items-center justify-center w-9 h-9 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-lg hover:from-emerald-600 hover:to-emerald-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105"
                                                        title="{{ __('Restore post') }}">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                        </svg>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-gray-400">
                                            {{ __('no_posts_found') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Alert container --}}
                    <div id="alertMessage"></div>

                    {{-- Pagination --}}
                    <div id="pagination" class="mt-4 flex justify-between items-center">
                        @if ($posts->hasPages())
                            <div class="mt-6">
                                {{ $posts->appends(request()->query())->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Include SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Include PostsCrud JavaScript --}}
    <script src="{{ asset('js/postsCrud.js') }}"></script>

    {{-- Initialize PostsCrudManager --}}
    <script>
        $(document).ready(function() {
            // Set initial state from URL params
            const urlParams = new URLSearchParams(window.location.search);
            const showDeleted = urlParams.get('show_deleted') === 'true';
            const searchTerm = urlParams.get('search') || '';

            // Update UI to match URL state
            $('#showDeletedToggle').prop('checked', showDeleted);
            $('#searchInput').val(searchTerm);

            // Update toggle appearance
            const toggle = document.getElementById('showDeletedToggle');
            if (toggle && showDeleted) {
                const background = toggle.nextElementSibling;
                const dot = background.nextElementSibling;
                background.classList.remove('bg-gray-600');
                background.classList.add('bg-yellow-500');
                dot.classList.add('transform', 'translate-x-6');
            }

            // Initialize PostsCrudManager
            window.postsCrudManager = new PostsCrudManager({
                routes: {
                    index: "{{ secure_url(route('posts-crud.index', [], false)) }}"
                }
            });

            // Set initial state
            window.postsCrudManager.searchTerm = searchTerm;
            window.postsCrudManager.showDeleted = showDeleted;
            window.postsCrudManager.perPage = $('#perPage').val() || 10;

            // Cargar posts automáticamente al inicializar
            window.postsCrudManager.loadPosts();
        });

        // Auto-hide alerts after 5 seconds
        function autoHideAlerts() {
            const alerts = document.querySelectorAll('.mb-6[class*="bg-green-500"], .mb-6[class*="bg-red-500"]');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.5s ease-out';
                    alert.style.opacity = '0';
                    setTimeout(() => {
                        alert.style.display = 'none';
                    }, 500);
                }, 5000);
            });
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            autoHideAlerts();
        });
    </script>

    <style>
        /* Custom toggle styles */
        .dot {
            transition: transform 0.2s ease-in-out;
        }

        /* Smooth transitions for toggle */
        .toggle-bg {
            transition: background-color 0.2s ease-in-out;
        }
    </style>
</x-app-layout>
