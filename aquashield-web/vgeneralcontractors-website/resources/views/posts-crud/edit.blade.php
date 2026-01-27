<x-app-layout>
    {{-- TinyMCE CDN --}}
    <script src="https://cdn.tiny.cloud/1/o37wydoc26hw1jj4mpqtzxsgfu1an5c3r8fz59f84yqt7z5u/tinymce/7/tinymce.min.js"
        referrerpolicy="origin"></script>

    {{-- Dark background container with consistent styling --}}
    <div class="min-h-screen bg-gray-900" style="background-color: #141414;">
        {{-- Header section with title and subtitle --}}
        <div class="p-4 sm:p-6">
            <div class="mb-4 sm:mb-8 text-center sm:text-center md:text-left lg:text-left">
                <h2 class="text-base sm:text-base md:text-2xl lg:text-2xl font-bold text-white mb-2">
                    {{ __('edit_post') }}
                </h2>
                <p class="text-base sm:text-base md:text-base lg:text-base text-gray-400">
                    {{ __('edit_post_subtitle') }} "{{ Str::limit($post->post_title, 50) }}"
                </p>
            </div>
        </div>

        {{-- Main content area --}}
        <div class="max-w-7xl mx-auto py-2 sm:py-4 md:py-2 lg:py-2 px-4 sm:px-6 lg:px-8 pb-12">
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

            {{-- Form container --}}
            <div class="dark:bg-gray-800 overflow-hidden shadow-xl rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ secure_url(route('posts-crud.update', $post->uuid, [], false)) }}"
                        id="editPostForm" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            {{-- Main Content Column (Left) --}}
                            <div class="lg:col-span-2 space-y-6">
                                {{-- Post Title --}}
                                <div>
                                    <label for="post_title" class="block text-sm font-medium text-gray-300 mb-2">
                                        {{ __('post_title') }} <span class="text-red-400">*</span>
                                    </label>
                                    <input type="text" name="post_title" id="post_title"
                                        value="{{ old('post_title', $post->post_title) }}" required
                                        class="w-full text-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-yellow-500 border-0 @error('post_title') ring-2 ring-red-500 @enderror"
                                        style="background-color: #2C2E36;" placeholder="{{ __('enter_post_title') }}">
                                    @error('post_title')
                                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Post Content --}}
                                <div>
                                    <label for="post_content" class="block text-sm font-medium text-gray-300 mb-2">
                                        {{ __('post_content') }} <span class="text-red-400">*</span>
                                    </label>
                                    <textarea name="post_content" id="post_content" required
                                        class="w-full min-h-96 @error('post_content') ring-2 ring-red-500 @enderror">{{ old('post_content', $post->post_content) }}</textarea>
                                    @error('post_content')
                                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Sidebar (Right) --}}
                            <div class="space-y-6">
                                {{-- Post Status --}}
                                <div class="bg-gray-700 rounded-lg p-4">
                                    <h3 class="text-lg font-medium text-gray-200 mb-4">{{ __('publish') }}</h3>

                                    <div class="space-y-4">
                                        {{-- Post Status --}}
                                        <div>
                                            <label for="post_status"
                                                class="block text-sm font-medium text-gray-300 mb-2">
                                                {{ __('status') }}
                                            </label>
                                            <select name="post_status" id="post_status"
                                                class="w-full text-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-500 border-0"
                                                style="background-color: #2C2E36;" onchange="toggleScheduleField()">
                                                <option value="published"
                                                    {{ old('post_status', $post->post_status) === 'published' ? 'selected' : '' }}>
                                                    {{ __('published') }}
                                                </option>
                                                <option value="scheduled"
                                                    {{ old('post_status', $post->post_status) === 'scheduled' ? 'selected' : '' }}>
                                                    {{ __('scheduled') }}
                                                </option>
                                            </select>
                                        </div>

                                        {{-- Scheduled Date --}}
                                        <div id="scheduled_at_field"
                                            style="display: {{ old('post_status', $post->post_status) === 'scheduled' ? 'block' : 'none' }};">
                                            <label for="scheduled_at"
                                                class="block text-sm font-medium text-gray-300 mb-2">
                                                {{ __('schedule_date') }}
                                            </label>
                                            <input type="datetime-local" name="scheduled_at" id="scheduled_at"
                                                value="{{ old('scheduled_at', $post->scheduled_at ? $post->scheduled_at->format('Y-m-d\TH:i') : '') }}"
                                                class="w-full text-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-500 border-0"
                                                style="background-color: #2C2E36;">
                                        </div>
                                    </div>

                                    {{-- Submit Buttons --}}
                                    <div class="mt-6 flex space-x-3">
                                        <button type="submit"
                                            class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-gray-900 px-4 py-2 rounded-lg font-medium transition-colors">
                                            {{ __('update_post') }}
                                        </button>
                                        <a href="{{ route('posts-crud.index') }}"
                                            class="flex-1 bg-gray-600 hover:bg-gray-500 text-white px-4 py-2 rounded-lg font-medium transition-colors text-center">
                                            {{ __('cancel') }}
                                        </a>
                                    </div>
                                </div>

                                {{-- Category --}}
                                <div class="bg-gray-700 rounded-lg p-4">
                                    <h3 class="text-lg font-medium text-gray-200 mb-4">{{ __('category') }}</h3>

                                    <div>
                                        <label for="category_id" class="block text-sm font-medium text-gray-300 mb-2">
                                            {{ __('select_category') }} <span class="text-red-400">*</span>
                                        </label>
                                        <select name="category_id" id="category_id" required
                                            class="w-full text-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-500 border-0 @error('category_id') ring-2 ring-red-500 @enderror"
                                            style="background-color: #2C2E36;">
                                            <option value="">{{ __('choose_category') }}</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ old('category_id', $post->category_id) == $category->id ? 'selected' : '' }}>
                                                    {{ $category->blog_category_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('category_id')
                                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Featured Image with Upload & Preview --}}
                                <div class="bg-gray-700 rounded-lg p-4">
                                    <h3 class="text-lg font-medium text-gray-200 mb-4">{{ __('featured_image') }}</h3>

                                    {{-- Image Upload Options --}}
                                    <div class="space-y-4">
                                        {{-- Upload Method Selector --}}
                                        <div>
                                            <label class="block text-sm font-medium text-gray-300 mb-2">
                                                {{ __('upload_method') }}
                                            </label>
                                            <div class="flex space-x-2">
                                                <button type="button" id="upload-tab"
                                                    class="flex-1 px-3 py-2 text-sm rounded-lg transition-colors bg-yellow-500 text-gray-900"
                                                    onclick="switchUploadMethod('upload')">
                                                    {{ __('upload_file') }}
                                                </button>
                                                <button type="button" id="url-tab"
                                                    class="flex-1 px-3 py-2 text-sm rounded-lg transition-colors bg-gray-600 text-gray-300 hover:bg-gray-500"
                                                    onclick="switchUploadMethod('url')">
                                                    {{ __('image_url') }}
                                                </button>
                                            </div>
                                        </div>

                                        {{-- File Upload Section --}}
                                        <div id="upload-section">
                                            <label for="post_image_file"
                                                class="block text-sm font-medium text-gray-300 mb-2">
                                                {{ __('select_image_file') }}
                                            </label>
                                            <div class="relative">
                                                <input type="file" name="post_image_file" id="post_image_file"
                                                    accept="image/*" class="hidden" onchange="handleImageUpload(this)">
                                                <button type="button"
                                                    onclick="document.getElementById('post_image_file').click()"
                                                    class="w-full text-gray-300 rounded-lg px-3 py-2 border-2 border-dashed border-gray-500 hover:border-yellow-500 transition-colors text-center bg-gray-800">
                                                    <svg class="w-6 h-6 mx-auto mb-2 text-gray-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                    </svg>
                                                    {{ __('click_to_upload_new_image') }}
                                                </button>
                                            </div>
                                            @error('post_image_file')
                                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        {{-- URL Input Section --}}
                                        <div id="url-section" style="display: none;">
                                            <label for="post_image_url"
                                                class="block text-sm font-medium text-gray-300 mb-2">
                                                {{ __('Image URL') }}
                                            </label>
                                            <input type="url" name="post_image_url" id="post_image_url"
                                                value="{{ old('post_image_url', $post->post_image) }}"
                                                class="w-full text-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-500 border-0"
                                                style="background-color: #2C2E36;"
                                                placeholder="{{ __('placeholder_image_url') }}"
                                                onchange="handleImageUrl(this)">
                                            @error('post_image_url')
                                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        {{-- Current Image Preview --}}
                                        @if ($post->post_image)
                                            <div class="mt-4">
                                                <label class="block text-sm font-medium text-gray-300 mb-2">
                                                    {{ __('Current Image') }}
                                                </label>
                                                <div class="relative">
                                                    <img src="{{ Str::startsWith($post->post_image, 'http://') ? Str::replaceFirst('http://', 'https://', $post->post_image) : $post->post_image }}"
                                                        alt="Current featured image"
                                                        class="w-full h-48 object-cover rounded-lg border border-gray-600"
                                                        onerror="this.style.display='none'">
                                                </div>
                                            </div>
                                        @endif

                                        {{-- New Image Preview --}}
                                        <div id="image-preview" style="display: none;" class="mt-4">
                                            <label class="block text-sm font-medium text-gray-300 mb-2">
                                                {{ __('New Preview') }}
                                            </label>
                                            <div class="relative">
                                                <img id="preview-image" src="" alt="Preview"
                                                    class="w-full h-48 object-cover rounded-lg border border-gray-600">
                                                <button type="button" onclick="removeImagePreview()"
                                                    class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm transition-colors">
                                                    ×
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- SEO Section --}}
                                <div class="bg-gray-700 rounded-lg p-4">
                                    <h3 class="text-lg font-medium text-gray-200 mb-4">{{ __('SEO Settings') }}</h3>

                                    <div class="space-y-4">
                                        {{-- Meta Title --}}
                                        <div>
                                            <label for="meta_title"
                                                class="block text-sm font-medium text-gray-300 mb-2">
                                                {{ __('Meta Title') }}
                                            </label>
                                            <input type="text" name="meta_title" id="meta_title"
                                                value="{{ old('meta_title', $post->meta_title) }}" maxlength="100"
                                                class="w-full text-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-500 border-0"
                                                style="background-color: #2C2E36;"
                                                placeholder="{{ __('placeholder_meta_title') }}">
                                            @error('meta_title')
                                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        {{-- Meta Description --}}
                                        <div>
                                            <label for="meta_description"
                                                class="block text-sm font-medium text-gray-300 mb-2">
                                                {{ __('Meta Description') }}
                                            </label>
                                            <textarea name="meta_description" id="meta_description" rows="3" maxlength="255"
                                                class="w-full text-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-500 border-0"
                                                style="background-color: #2C2E36;" placeholder="{{ __('placeholder_meta_description') }}">{{ old('meta_description', $post->meta_description) }}</textarea>
                                            @error('meta_description')
                                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        {{-- Meta Keywords --}}
                                        <div>
                                            <label for="meta_keywords"
                                                class="block text-sm font-medium text-gray-300 mb-2">
                                                {{ __('Meta Keywords') }}
                                            </label>
                                            <input type="text" name="meta_keywords" id="meta_keywords"
                                                value="{{ old('meta_keywords', $post->meta_keywords) }}"
                                                class="w-full text-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-500 border-0"
                                                style="background-color: #2C2E36;"
                                                placeholder="{{ __('placeholder_meta_keywords') }}">
                                            @error('meta_keywords')
                                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                {{-- Post Info --}}
                                <div class="bg-gray-700 rounded-lg p-4">
                                    <h3 class="text-lg font-medium text-gray-200 mb-4">{{ __('Post Info') }}</h3>

                                    <div class="space-y-3 text-sm text-gray-400">
                                        <div class="flex justify-between">
                                            <span>{{ __('Created:') }}</span>
                                            <span>{{ $post->created_at->format('M d, Y H:i') }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>{{ __('Updated:') }}</span>
                                            <span>{{ $post->updated_at->format('M d, Y H:i') }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>{{ __('Author:') }}</span>
                                            <span>{{ $post->user->name ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>{{ __('Slug:') }}</span>
                                            <span class="break-all">{{ $post->post_title_slug }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- TinyMCE and Custom JavaScript --}}
    <script>
        // TinyMCE Configuration
        tinymce.init({
            selector: '#post_content',
            height: 500,
            skin: 'oxide-dark',
            content_css: 'dark',
            plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount code fullscreen',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat | code fullscreen',
            menubar: 'file edit view insert format tools table help',
            branding: false,
            setup: function(editor) {
                editor.on('change', function() {
                    editor.save();
                });
            },
            content_style: `
                body { 
                    font-family: -apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Oxygen,Ubuntu,Cantarell,Open Sans,Helvetica Neue,sans-serif; 
                    font-size: 14px; 
                    background-color: #1a1a1a; 
                    color: #e5e7eb; 
                }
                p { margin: 1rem 0; }
                h1, h2, h3, h4, h5, h6 { margin: 1.5rem 0 1rem 0; }
            `,
            style_formats: [{
                    title: 'Headers',
                    items: [{
                            title: 'Header 1',
                            format: 'h1'
                        },
                        {
                            title: 'Header 2',
                            format: 'h2'
                        },
                        {
                            title: 'Header 3',
                            format: 'h3'
                        },
                        {
                            title: 'Header 4',
                            format: 'h4'
                        },
                        {
                            title: 'Header 5',
                            format: 'h5'
                        },
                        {
                            title: 'Header 6',
                            format: 'h6'
                        }
                    ]
                },
                {
                    title: 'Inline',
                    items: [{
                            title: 'Bold',
                            icon: 'bold',
                            format: 'bold'
                        },
                        {
                            title: 'Italic',
                            icon: 'italic',
                            format: 'italic'
                        },
                        {
                            title: 'Underline',
                            icon: 'underline',
                            format: 'underline'
                        },
                        {
                            title: 'Strikethrough',
                            icon: 'strikethrough',
                            format: 'strikethrough'
                        },
                        {
                            title: 'Superscript',
                            icon: 'superscript',
                            format: 'superscript'
                        },
                        {
                            title: 'Subscript',
                            icon: 'subscript',
                            format: 'subscript'
                        },
                        {
                            title: 'Code',
                            icon: 'code',
                            format: 'code'
                        }
                    ]
                },
                {
                    title: 'Blocks',
                    items: [{
                            title: 'Paragraph',
                            format: 'p'
                        },
                        {
                            title: 'Blockquote',
                            format: 'blockquote'
                        },
                        {
                            title: 'Div',
                            format: 'div'
                        },
                        {
                            title: 'Pre',
                            format: 'pre'
                        }
                    ]
                }
            ]
        });

        // Schedule Field Toggle
        function toggleScheduleField() {
            const status = document.getElementById('post_status').value;
            const scheduledField = document.getElementById('scheduled_at_field');

            if (status === 'scheduled') {
                scheduledField.style.display = 'block';
                document.getElementById('scheduled_at').required = true;
            } else {
                scheduledField.style.display = 'none';
                document.getElementById('scheduled_at').required = false;
                document.getElementById('scheduled_at').value = '';
            }
        }

        // Auto-generate meta title from post title
        document.getElementById('post_title').addEventListener('input', function() {
            const metaTitleField = document.getElementById('meta_title');
            if (!metaTitleField.value || metaTitleField.value === '{{ $post->post_title }}') {
                metaTitleField.value = this.value;
            }
        });

        // Real-time title validation for edit mode
        let titleValidationTimeout;
        const originalTitle = '{{ $post->post_title }}';

        document.getElementById('post_title').addEventListener('input', function() {
            clearTimeout(titleValidationTimeout);
            const title = this.value.trim();

            if (title.length >= 3 && title !== originalTitle) {
                titleValidationTimeout = setTimeout(() => {
                    validateTitle(title);
                }, 500);
            } else {
                clearTitleValidation();
            }
        });

        // Title validation function for edit mode
        async function validateTitle(title) {
            try {
                const response = await fetch("{{ secure_url(route('posts-crud.check-title', [], false)) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        title: title,
                        exclude_uuid: '{{ $post->uuid }}'
                    })
                });

                const data = await response.json();

                if (data.success) {
                    if (data.exists) {
                        showTitleError('{{ __('title_already_exists_post') }}');
                    } else {
                        showTitleSuccess('{{ __('title_available') }}');
                    }
                } else {
                    clearTitleValidation();
                }
            } catch (error) {
                console.error('Error validating title:', error);
                clearTitleValidation();
            }
        }

        // Show title validation error
        function showTitleError(message) {
            const titleField = document.getElementById('post_title');
            const existingError = document.getElementById('title-validation-error');

            if (existingError) {
                existingError.remove();
            }

            titleField.classList.add('ring-2', 'ring-red-500');

            const errorDiv = document.createElement('div');
            errorDiv.id = 'title-validation-error';
            errorDiv.className = 'mt-1 text-sm text-red-400';
            errorDiv.textContent = message;

            titleField.parentNode.appendChild(errorDiv);
        }

        // Show title validation success
        function showTitleSuccess(message) {
            const titleField = document.getElementById('post_title');
            const existingError = document.getElementById('title-validation-error');
            const existingSuccess = document.getElementById('title-validation-success');

            if (existingError) {
                existingError.remove();
            }
            if (existingSuccess) {
                existingSuccess.remove();
            }

            titleField.classList.remove('ring-2', 'ring-red-500');
            titleField.classList.add('ring-2', 'ring-green-500');

            const successDiv = document.createElement('div');
            successDiv.id = 'title-validation-success';
            successDiv.className = 'mt-1 text-sm text-green-400';
            successDiv.textContent = message;

            titleField.parentNode.appendChild(successDiv);

            // Remove success message after 3 seconds
            setTimeout(() => {
                if (successDiv.parentNode) {
                    successDiv.remove();
                    titleField.classList.remove('ring-2', 'ring-green-500');
                }
            }, 3000);
        }

        // Clear title validation
        function clearTitleValidation() {
            const titleField = document.getElementById('post_title');
            const existingError = document.getElementById('title-validation-error');
            const existingSuccess = document.getElementById('title-validation-success');

            if (existingError) {
                existingError.remove();
            }
            if (existingSuccess) {
                existingSuccess.remove();
            }

            titleField.classList.remove('ring-2', 'ring-red-500', 'ring-green-500');
        }

        // Upload Method Switcher
        function switchUploadMethod(method) {
            const uploadTab = document.getElementById('upload-tab');
            const urlTab = document.getElementById('url-tab');
            const uploadSection = document.getElementById('upload-section');
            const urlSection = document.getElementById('url-section');

            if (method === 'upload') {
                uploadTab.className = 'flex-1 px-3 py-2 text-sm rounded-lg transition-colors bg-yellow-500 text-gray-900';
                urlTab.className =
                    'flex-1 px-3 py-2 text-sm rounded-lg transition-colors bg-gray-600 text-gray-300 hover:bg-gray-500';
                uploadSection.style.display = 'block';
                urlSection.style.display = 'none';

                // Clear URL input
                document.getElementById('post_image_url').value = '';
            } else {
                uploadTab.className =
                    'flex-1 px-3 py-2 text-sm rounded-lg transition-colors bg-gray-600 text-gray-300 hover:bg-gray-500';
                urlTab.className = 'flex-1 px-3 py-2 text-sm rounded-lg transition-colors bg-yellow-500 text-gray-900';
                uploadSection.style.display = 'none';
                urlSection.style.display = 'block';

                // Clear file input
                document.getElementById('post_image_file').value = '';
            }

            // Hide preview when switching
            document.getElementById('image-preview').style.display = 'none';
        }

        // Handle Image Upload
        function handleImageUpload(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];

                // Validate file type
                if (!file.type.startsWith('image/')) {
                    alert('Please select a valid image file.');
                    input.value = '';
                    return;
                }

                // Validate file size (max 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('Image size must be less than 5MB.');
                    input.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    showImagePreview(e.target.result);

                    // Update upload button text
                    const uploadButton = document.querySelector('#upload-section button');
                    uploadButton.innerHTML = `
                        <svg class="w-6 h-6 mx-auto mb-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        {{ __('New image selected') }}
                    `;
                    uploadButton.className =
                        'w-full text-green-300 rounded-lg px-3 py-2 border-2 border-dashed border-green-500 hover:border-green-400 transition-colors text-center bg-green-900/20';
                };
                reader.readAsDataURL(file);
            }
        }

        // Handle Image URL
        function handleImageUrl(input) {
            const url = input.value.trim();
            if (url) {
                // Basic URL validation
                try {
                    new URL(url);
                    showImagePreview(url);
                } catch (e) {
                    alert('Please enter a valid URL.');
                    input.value = '';
                }
            } else {
                document.getElementById('image-preview').style.display = 'none';
            }
        }

        // Show Image Preview
        function showImagePreview(src) {
            const preview = document.getElementById('image-preview');
            const previewImage = document.getElementById('preview-image');

            previewImage.src = src;
            preview.style.display = 'block';

            // Handle image load error
            previewImage.onerror = function() {
                alert('Failed to load image. Please check the URL or try a different image.');
                removeImagePreview();
            };
        }

        // Remove Image Preview
        function removeImagePreview() {
            document.getElementById('image-preview').style.display = 'none';
            document.getElementById('post_image_file').value = '';
            document.getElementById('post_image_url').value = '';

            // Reset upload button
            const uploadButton = document.querySelector('#upload-section button');
            uploadButton.innerHTML = `
                <svg class="w-6 h-6 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                {{ __('Click to upload new image') }}
            `;
            uploadButton.className =
                'w-full text-gray-300 rounded-lg px-3 py-2 border-2 border-dashed border-gray-500 hover:border-yellow-500 transition-colors text-center bg-gray-800';
        }

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
            toggleScheduleField();
            autoHideAlerts();

            // Initialize with URL method if there's an existing image
            @if ($post->post_image)
                switchUploadMethod('url');
            @endif
        });
    </script>
</x-app-layout>
