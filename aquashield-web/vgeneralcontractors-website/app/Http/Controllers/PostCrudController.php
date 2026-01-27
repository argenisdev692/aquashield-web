<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Throwable;
use App\Services\TransactionService;
use Illuminate\Support\Facades\Cache;
use App\Traits\CacheTraitCrud;
use App\Services\PostImageService;

class PostCrudController extends BaseCrudController
{
    use CacheTraitCrud;
    
    public function __construct(TransactionService $transactionService)
    {
        parent::__construct($transactionService);
        $this->modelClass = Post::class;
        $this->entityName = 'POST';
        $this->viewPrefix = 'posts-crud';
        $this->routePrefix = 'posts-crud';
        
        // Initialize cache properties with defaults
        $this->initializeCacheProperties();
    }
    
    /**
     * Cache time override for posts (5 minutes - posts change moderately)
     */
    protected $cacheTime = 300;

    /**
     * Get validation rules for post
     */
    protected function getValidationRules($id = null)
    {
        $titleRule = 'required|string|min:3|max:255|unique:posts,post_title';
        
        // If we have an ID (UUID in this case), exclude it from the unique check
        if ($id) {
            $titleRule .= ',' . $id . ',uuid';
        }
        
        return [
            'post_title' => $titleRule,
            'post_content' => 'required|string',
            'post_image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
            'post_image_url' => 'nullable|url|max:500',
            'meta_description' => 'nullable|string|max:255',
            'meta_title' => 'nullable|string|max:100',
            'meta_keywords' => 'nullable|string|max:255',
            'category_id' => 'required|exists:blog_categories,id',
            'post_status' => 'nullable|in:published,scheduled',
            'scheduled_at' => 'nullable|date|after_or_equal:now',
        ];
    }

    /**
     * Get validation messages for post
     */
    protected function getValidationMessages()
    {
        return [
            'post_title.required' => 'The post title is required.',
            'post_title.unique' => 'A post with this title already exists.',
            'post_title.min' => 'The post title must be at least 3 characters.',
            'post_title.max' => 'The post title may not be greater than 255 characters.',
            'post_content.required' => 'The post content is required.',
            'post_image_file.image' => 'The uploaded file must be an image.',
            'post_image_file.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif, webp.',
            'post_image_file.max' => 'The image size must not exceed 5MB.',
            'post_image_url.url' => 'The image URL must be a valid URL.',
            'post_image_url.max' => 'The post image URL may not be greater than 500 characters.',
            'meta_description.max' => 'The meta description may not be greater than 255 characters.',
            'meta_title.max' => 'The meta title may not be greater than 100 characters.',
            'meta_keywords.max' => 'The meta keywords may not be greater than 255 characters.',
            'category_id.required' => 'The category is required.',
            'category_id.exists' => 'The selected category does not exist.',
            'post_status.in' => 'The post status must be either published or scheduled.',
            'scheduled_at.date' => 'The scheduled date must be a valid date.',
            'scheduled_at.after_or_equal' => 'The scheduled date must be today or a future date.',
        ];
    }

    /**
     * Prepare data for storing a post
     */
    protected function prepareStoreData(Request $request)
    {
        // Generate slug from title
        $slug = Str::slug($request->post_title);
        
        // Check if slug exists, if so, append a random string
        if (Post::where('post_title_slug', $slug)->exists()) {
            $slug = $slug . '-' . Str::random(5);
        }

        // Determine post status based on scheduled_at
        $post_status = 'published';
        if ($request->scheduled_at) {
            $post_status = 'scheduled';
        }

        // Handle image upload
        $imageUrl = null;
        if ($request->hasFile('post_image_file')) {
            $imageUrl = $this->handleImageUpload($request->file('post_image_file'));
        } elseif ($request->filled('post_image_url')) {
            $imageUrl = $request->post_image_url;
        }

        return [
            'uuid' => (string) Str::uuid(),
            'post_title' => $request->post_title,
            'post_content' => $this->formatPostContentForSaving($request->post_content),
            'post_image' => $imageUrl,
            'meta_description' => $request->meta_description,
            'meta_title' => $request->meta_title ?? $request->post_title,
            'meta_keywords' => $request->meta_keywords,
            'post_title_slug' => $slug,
            'category_id' => $request->category_id,
            'user_id' => auth()->id(),
            'post_status' => $post_status,
            'scheduled_at' => $request->scheduled_at,
        ];
    }

    /**
     * Prepare data for updating a post
     */
    protected function prepareUpdateData(Request $request)
    {
        $data = [];
        
        if ($request->filled('post_title')) {
            $data['post_title'] = $request->post_title;
            
            // Generate new slug if title changed
            $slug = Str::slug($request->post_title);
            if (Post::where('post_title_slug', $slug)->where('uuid', '!=', $request->route('uuid'))->exists()) {
                $slug = $slug . '-' . Str::random(5);
            }
            $data['post_title_slug'] = $slug;
        }
        
        if ($request->filled('post_content')) {
            $data['post_content'] = $this->formatPostContentForSaving($request->post_content);
        }
        
        // Handle image upload for updates
        if ($request->hasFile('post_image_file')) {
            // Get the current post to delete old image if exists
            $currentPost = Post::where('uuid', $request->route('uuid'))->first();
            
            // Delete old image if exists
            if ($currentPost && $currentPost->post_image) {
                $postImageService = app(PostImageService::class);
                $postImageService->deletePostImage($currentPost->post_image);
            }
            
            // Upload new image
            $data['post_image'] = $this->handleImageUpload($request->file('post_image_file'));
        } elseif ($request->filled('post_image_url')) {
            $data['post_image'] = $request->post_image_url;
        } elseif ($request->has('post_image_url') && $request->post_image_url === '') {
            // If URL field is explicitly cleared
            $currentPost = Post::where('uuid', $request->route('uuid'))->first();
            
            // Delete old image if exists
            if ($currentPost && $currentPost->post_image) {
                $postImageService = app(PostImageService::class);
                $postImageService->deletePostImage($currentPost->post_image);
            }
            
            $data['post_image'] = null;
        }
        
        if ($request->has('meta_description')) {
            $data['meta_description'] = $request->meta_description;
        }
        
        if ($request->has('meta_title')) {
            $data['meta_title'] = $request->meta_title ?? $request->post_title;
        }
        
        if ($request->has('meta_keywords')) {
            $data['meta_keywords'] = $request->meta_keywords;
        }
        
        if ($request->filled('category_id')) {
            $data['category_id'] = $request->category_id;
        }
        
        // Determine post status based on scheduled_at
        if ($request->has('scheduled_at')) {
            $data['scheduled_at'] = $request->scheduled_at;
            $data['post_status'] = $request->scheduled_at ? 'scheduled' : 'published';
        }
        
        return $data;
    }

    /**
     * Display a listing of the posts
     */
    public function index(Request $request)
    {
        // Debug: Log request details
        Log::info('PostCrudController::index - Request details:', [
            'method' => $request->method(),
            'is_ajax' => $request->ajax(),
            'accepts_json' => $request->acceptsJson(),
            'wants_json' => $request->wantsJson(),
            'headers' => $request->headers->all(),
            'all_params' => $request->all()
        ]);

        // Check permission first - this is critical for security
        if (!$this->checkPermissionWithMessage("READ_{$this->entityName}", "You don't have permission to view {$this->entityName}")) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to view posts',
                ], 403);
            }
            
            return redirect()->route('dashboard')->with('error', 'You do not have permission to view posts');
        }

        try {
            // Set up cache and search parameters
            $this->search = $request->input('search', '');
            $this->sortField = $request->input('sort_field', 'created_at');
            $this->sortDirection = $request->input('sort_direction', 'desc');
            $this->perPage = $request->input('per_page', 10);
            $this->showDeleted = $request->input('show_deleted', 'false') === 'true';
            
            Log::info('PostCrudController::index - Request parameters:', [
                'all_params' => $request->all(),
                'search_param' => $this->search,
                'has_search' => $request->has('search'),
                'is_empty' => empty($this->search)
            ]);
            
            $page = $request->input('page', 1);
            
            // Build and execute query
            $query = $this->buildPostsQuery($request);
            $posts = $query->paginate($this->perPage, ['*'], 'page', $page);

            // Debug: Log query results
            Log::info('PostCrudController::index - Query results:', [
                'total_posts' => $posts->total(),
                'current_page' => $posts->currentPage(),
                'per_page' => $posts->perPage(),
                'has_pages' => $posts->hasPages()
            ]);

            if ($request->ajax()) {
                Log::info('PostCrudController::index - Returning AJAX response', [
                    'posts_type' => get_class($posts),
                    'posts_count' => $posts->count(),
                    'posts_total' => $posts->total(),
                    'posts_data_count' => count($posts->items()),
                    'sample_response_structure' => [
                        'current_page' => $posts->currentPage(),
                        'data' => 'array of ' . count($posts->items()) . ' items',
                        'total' => $posts->total()
                    ]
                ]);
                // Return consistent structure like ServiceCategoryController  
                return response()->json($posts);
            }

            Log::info('PostCrudController::index - Returning view response');
            return view("{$this->viewPrefix}.index", [
                'posts' => $posts,
                'entityName' => $this->entityName,
            ]);
        } catch (Throwable $e) {
            Log::error("Error listing {$this->entityName}s: {$e->getMessage()}", [
                'exception' => $e,
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => "Error listing {$this->entityName}s",
                ], 500);
            }

            return back()->with('error', "Error listing {$this->entityName}s");
        }
    }
    
    /**
     * Build posts query based on request filters
     */
    private function buildPostsQuery(Request $request)
    {
        $query = $this->modelClass::with(['category', 'user']);
        
        // Handle search
        if (!empty($this->search)) {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('post_title', 'like', $searchTerm)
                  ->orWhere('post_content', 'like', $searchTerm)
                  ->orWhere('meta_description', 'like', $searchTerm)
                  ->orWhere('meta_keywords', 'like', $searchTerm);
            });
        }

        // Handle soft deletes
        if ($this->showDeleted) {
            $query->withTrashed();
        }

        // Sorting
        $query->orderBy($this->sortField, $this->sortDirection);
        
        return $query;
    }

    /**
     * Show the form for creating a new post
     */
    public function create()
    {
        try {
            if (!$this->checkPermissionWithMessage("CREATE_{$this->entityName}", "You don't have permission to create {$this->entityName}")) {
                return redirect()->route($this->routePrefix . '.index')->with('error', "Permission denied");
            }
            
            $categories = BlogCategory::orderBy('blog_category_name')->get();
            
            return view("{$this->viewPrefix}.create", [
                'entityName' => $this->entityName,
                'categories' => $categories,
            ]);
        } catch (Throwable $e) {
            Log::error("Error showing create form for {$this->entityName}: {$e->getMessage()}", [
                'exception' => $e,
            ]);
            
            return redirect()->route($this->routePrefix . '.index')->with('error', "Error loading create form");
        }
    }

    /**
     * Store a newly created post
     */
    public function store(Request $request)
    {
        try {
            $data = $this->validateRequest($request);

            // Store operation simplified for debugging
            $post = $this->transactionService->run(function () use ($data) {
                $preparedData = $this->prepareStoreData($data);
                return $this->modelClass::create($preparedData);
            }, function ($post) {
                Log::info("{$this->entityName} created successfully", ['id' => $post->id]);
                $this->afterStore($post);
            });

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "{$this->entityName} created successfully",
                    'post' => $post,
                    'redirectUrl' => route("{$this->routePrefix}.index"),
                ]);
            }

            return redirect()->route("{$this->routePrefix}.index")
                ->with('message', "{$this->entityName} created successfully");
        } catch (Throwable $e) {
            Log::error("Error creating {$this->entityName}: {$e->getMessage()}", [
                'exception' => $e,
                'request' => $request->all(),
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => "Error creating {$this->entityName}",
                    'errors' => $e instanceof \Illuminate\Validation\ValidationException
                        ? $e->errors()
                        : [$e->getMessage()],
                ], $e instanceof \Illuminate\Validation\ValidationException ? 422 : 500);
            }

            return back()->withErrors($e instanceof \Illuminate\Validation\ValidationException
                ? $e->errors()
                : [$e->getMessage()])->withInput();
        }
    }

    /**
     * Show the form for editing the specified post
     */
    public function edit($uuid)
    {
        try {
            if (!$uuid || $uuid === 'undefined') {
                throw new \InvalidArgumentException('Invalid UUID');
            }

            $post = $this->modelClass::withTrashed()->where('uuid', $uuid)->first();

            if (!$post) {
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException("{$this->entityName} not found");
            }

            // Ensure scheduled_at is properly formatted for datetime-local input
            if ($post->scheduled_at && is_string($post->scheduled_at)) {
                try {
                    $post->scheduled_at = \Carbon\Carbon::parse($post->scheduled_at);
                } catch (\Exception $e) {
                    Log::warning("Could not parse scheduled_at for post {$post->uuid}: {$post->scheduled_at}");
                    $post->scheduled_at = null;
                }
            }

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'post' => $post,
                ]);
            }

            $categories = BlogCategory::orderBy('blog_category_name')->get();

            return view("{$this->viewPrefix}.edit", [
                'post' => $post,
                'entityName' => $this->entityName,
                'categories' => $categories,
            ]);
        } catch (Throwable $e) {
            Log::error("Error retrieving {$this->entityName}: {$e->getMessage()}", [
                'uuid' => $uuid,
                'exception' => $e,
            ]);

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => "Error retrieving {$this->entityName}",
                ], $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ? 404 : 500);
            }

            return back()->with('error', "Error retrieving {$this->entityName}");
        }
    }

    /**
     * Update the specified post
     */
    public function update(Request $request, $uuid)
    {
        try {
            if (!$uuid || $uuid === 'undefined') {
                throw new \InvalidArgumentException('Invalid UUID');
            }

            $data = $this->validateRequest($request, $uuid);

            // Update operation simplified for debugging
            $post = $this->transactionService->run(function () use ($uuid, $data) {
                $post = $this->modelClass::withTrashed()->where('uuid', $uuid)->firstOrFail();
                $preparedData = $this->prepareUpdateData($data);
                $post->update($preparedData);
                return $post->fresh();
            }, function ($post) {
                Log::info("{$this->entityName} updated successfully", ['id' => $post->id]);
                $this->afterUpdate($post);
            });

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "{$this->entityName} updated successfully",
                    'post' => $post,
                    'redirectUrl' => route("{$this->routePrefix}.index"),
                ]);
            }

            return redirect()->route("{$this->routePrefix}.index")
                ->with('message', "{$this->entityName} updated successfully");
        } catch (Throwable $e) {
            Log::error("Error updating {$this->entityName}: {$e->getMessage()}", [
                'uuid' => $uuid,
                'exception' => $e,
                'request' => $request->all(),
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => "Error updating {$this->entityName}",
                    'errors' => $e instanceof \Illuminate\Validation\ValidationException
                        ? $e->errors()
                        : [$e->getMessage()],
                ], $e instanceof \Illuminate\Validation\ValidationException ? 422 : ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ? 404 : 500));
            }

            return back()->withErrors($e instanceof \Illuminate\Validation\ValidationException
                ? $e->errors()
                : [$e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified post (soft delete)
     */
    public function destroy($uuid)
    {
        try {
            if (!$uuid || $uuid === 'undefined') {
                throw new \InvalidArgumentException('Invalid UUID');
            }

            // Use CacheTraitCrud's executeCrudOperation for better cache management
            $this->executeCrudOperation('posts', function () use ($uuid) {
                return $this->transactionService->run(function () use ($uuid) {
                    $post = $this->modelClass::where('uuid', $uuid)->firstOrFail();
                    
                    // Delete post image from S3 if exists
                    if ($post->post_image) {
                        $postImageService = app(PostImageService::class);
                        $postImageService->deletePostImage($post->post_image);
                    }
                    
                    $post->delete();
                    return $post;
                }, function ($post) {
                    Log::info("{$this->entityName} deleted successfully", ['id' => $post->id]);
                    $this->afterDestroy($post);
                });
            });

            return response()->json([
                'success' => true,
                'message' => "{$this->entityName} deleted successfully",
            ]);
        } catch (Throwable $e) {
            Log::error("Error deleting {$this->entityName}: {$e->getMessage()}", [
                'uuid' => $uuid,
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => "Error deleting {$this->entityName}",
            ], $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ? 404 : 500);
        }
    }

    /**
     * Restore a soft-deleted post
     */
    public function restore($uuid)
    {
        try {
            if (!$uuid || $uuid === 'undefined') {
                throw new \InvalidArgumentException('Invalid UUID');
            }

            // Use CacheTraitCrud's executeCrudOperation for better cache management
            $this->executeCrudOperation('posts', function () use ($uuid) {
                return $this->transactionService->run(function () use ($uuid) {
                    $post = $this->modelClass::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
                    $post->restore();
                    return $post;
                }, function ($post) {
                    Log::info("{$this->entityName} restored successfully", ['id' => $post->id]);
                    $this->afterRestore($post);
                });
            });

            return response()->json([
                'success' => true,
                'message' => "{$this->entityName} restored successfully",
            ]);
        } catch (Throwable $e) {
            Log::error("Error restoring {$this->entityName}: {$e->getMessage()}", [
                'uuid' => $uuid,
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => "Error restoring {$this->entityName}",
            ], $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ? 404 : 500);
        }
    }

    /**
     * Check if a post title already exists for real-time validation
     */
    public function checkTitleExists(Request $request)
    {
        if (!$this->checkPermissionWithMessage("READ_{$this->entityName}", "You don't have permission to check post titles")) {
            return response()->json([
                'success' => false,
                'message' => 'Permission denied',
            ], 403);
        }

        try {
            // Handle both form data and JSON data
            $data = $request->isJson() ? $request->json()->all() : $request->all();
            
            $title = $data['title'] ?? null;
            $excludeUuid = $data['exclude_uuid'] ?? null;

            if (!$title) {
                return response()->json([
                    'success' => false,
                    'message' => 'Title is required',
                ], 400);
            }

            $query = Post::where('post_title', $title);

            if ($excludeUuid) {
                $query->where('uuid', '!=', $excludeUuid);
            }

            $exists = $query->withTrashed()->exists();

            return response()->json([
                'success' => true,
                'exists' => $exists,
            ]);
        } catch (Throwable $e) {
            Log::error("Error checking title existence: {$e->getMessage()}", [
                'exception' => $e,
                'request' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error checking title existence',
            ], 500);
        }
    }

    /**
     * Validate the request data
     */
    protected function validateRequest(Request $request, $id = null)
    {
        $rules = $this->getValidationRules($id);
        $messages = $this->getValidationMessages();

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        return $request;
    }
    
    /**
     * Get search field for filtering
     */
    protected function getSearchField()
    {
        return 'post_title';
    }

    /**
     * Get name field for display
     */
    protected function getNameField()
    {
        return 'post_title';
    }

    /**
     * Get entity display name
     */
    protected function getEntityDisplayName($entity)
    {
        return $entity->post_title;
    }

    /**
     * Format post content for saving
     */
    protected function formatPostContentForSaving($content)
    {
        // Permitir etiquetas HTML seguras
        $allowedTags = '<p><br><strong><b><em><i><u><ul><ol><li><a><h1><h2><h3><h4><h5><h6><blockquote><table><thead><tbody><tr><td><th><img><div><span>';
        
        // Limpiar el contenido pero conservar las etiquetas permitidas
        $content = strip_tags($content, $allowedTags);
        
        // Asegurarse de que no esté vacío después de limpiar
        if (empty(trim(strip_tags($content)))) {
            throw new \InvalidArgumentException('Post content cannot be empty');
        }
        
        // Convert newlines to <br> tags for proper rendering
        return nl2br($content, false);
    }

    /**
     * Hook after storing a post
     */
    protected function afterStore($post)
    {
        // Add custom logic here, e.g., clear related caches
    }

    /**
     * Hook after updating a post
     */
    protected function afterUpdate($post)
    {
        // Add custom logic here, e.g., update related records
    }

    /**
     * Hook after deleting a post
     */
    protected function afterDestroy($post)
    {
        // Add custom logic here, e.g., log activity
    }

    /**
     * Hook after restoring a post
     */
    protected function afterRestore($post)
    {
        // Add custom logic here, e.g., notify user
    }

    /**
     * Handle image file upload using PostImageService (AWS S3)
     */
    protected function handleImageUpload($file)
    {
        try {
            $postImageService = app(PostImageService::class);
            $imageUrl = $postImageService->storePostImage($file);
            
            if (!$imageUrl) {
                throw new \Exception('Failed to upload image to S3');
            }
            
            return $imageUrl;
        } catch (\Exception $e) {
            Log::error('Error uploading image to S3', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception('Failed to upload image: ' . $e->getMessage());
        }
    }
} 