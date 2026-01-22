<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use App\Models\PortfolioImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Traits\CacheTraitCrud;
use Illuminate\Support\Facades\Validator;
use Throwable;
use App\Services\TransactionService;
use App\Services\PortfolioImageService;

class PortfolioCrudController extends BaseCrudController
{
    use CacheTraitCrud;

    // Constructor: inicializar propiedades necesarias
    public function __construct(TransactionService $transactionService)
    {
        parent::__construct($transactionService);
        $this->modelClass = Portfolio::class;
        $this->entityName = 'PORTFOLIO';
        $this->viewPrefix = 'portfolios-crud';
        $this->routePrefix = 'portfolios-crud';
    }

    /**
     * Display a listing of the portfolios
     */
    public function index(Request $request)
    {
        try {
            // Verificar permisos - temporalmente comentado para debug
            // if (!$this->checkPermission("READ_{$this->entityName}", false)) {
            //     if ($request->ajax()) {
            //         return response()->json([
            //             'success' => false,
            //             'message' => 'You do not have permission to view portfolios',
            //         ], 403);
            //     }
            //     return redirect()->route('dashboard')->with('error', 'You do not have permission to view portfolios');
            // }

            // Parámetros de búsqueda y paginación
            $search = $request->input('search', '');
            $sortField = $request->input('sort_field', 'created_at');
            $sortDirection = $request->input('sort_direction', 'desc');
            $perPage = $request->input('per_page', 10);
            $showDeleted = $request->input('show_deleted', 'false') === 'true';
            $page = $request->input('page', 1);

            // Query base
            $query = Portfolio::query();

            // Búsqueda
            if (!empty($search)) {
                $searchTerm = '%' . $search . '%';
                $query->where(function ($q) use ($searchTerm) {
                    $q->whereHas('projectType', function ($subQ) use ($searchTerm) {
                        $subQ->where('title', 'like', $searchTerm)
                             ->orWhere('description', 'like', $searchTerm);
                    });
                });
            }

            // Mostrar eliminados
            if ($showDeleted) {
                $query->withTrashed();
            }

            // Orden
            $query->orderBy($sortField, $sortDirection);

            // Relaciones necesarias para la tabla
            $query->with(['projectType.serviceCategory', 'images']);

            // Paginación
            $portfolios = $query->paginate($perPage, ['*'], 'page', $page);

            if ($request->ajax()) {
                // Debug: Log para verificar que llega aquí
                Log::info('Portfolio AJAX request received', [
                    'portfolios_count' => $portfolios->count(),
                    'total' => $portfolios->total(),
                    'current_page' => $portfolios->currentPage(),
                    'request_params' => $request->all(),
                ]);

                return response()->json([
                    'success' => true,
                    'data' => $portfolios->items(),
                    'current_page' => $portfolios->currentPage(),
                    'last_page' => $portfolios->lastPage(),
                    'from' => $portfolios->firstItem(),
                    'to' => $portfolios->lastItem(),
                    'total' => $portfolios->total(),
                ]);
            }

            // Vista normal (por si se accede directo)
            return view('portfolios-crud.index', [
                'entityName' => $this->entityName,
            ]);
        } catch (Throwable $e) {
            Log::error('Error loading portfolios: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all(),
            ]);
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error loading portfolios: ' . $e->getMessage(),
                ], 500);
            }
            return back()->with('error', 'Error loading portfolios');
        }
    }

    /**
     * Show the form for creating a new portfolio
     */
    public function create()
    {
        try {
            if (!$this->checkPermissionWithMessage("CREATE_{$this->entityName}", "You don't have permission to create {$this->entityName}")) {
                return redirect()->route($this->routePrefix . '.index')->with('error', "Permission denied");
            }
            
            // Para AJAX, solo necesitamos las categorías
            if (request()->ajax()) {
                $serviceCategories = \App\Models\ServiceCategory::orderBy('service_category_name')->get();
                return response()->json([
                    'success' => true,
                    'serviceCategories' => $serviceCategories,
                ]);
            }
            
            return view("{$this->viewPrefix}.create", [
                'entityName' => $this->entityName,
            ]);
        } catch (Throwable $e) {
            Log::error("Error showing create form for {$this->entityName}: {$e->getMessage()}", [
                'exception' => $e,
            ]);
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error loading create form',
                ], 500);
            }
            
            return redirect()->route($this->routePrefix . '.index')->with('error', "Error loading create form");
        }
    }

    /**
     * Store a newly created portfolio
     */
    public function store(Request $request)
    {
        try {
            $data = $this->validateRequest($request);

            $portfolio = $this->transactionService->run(function () use ($data) {
                $preparedData = $this->prepareStoreData($data);
                return $this->modelClass::create($preparedData);
            }, function ($portfolio) {
                Log::info("{$this->entityName} created successfully", ['id' => $portfolio->id]);
                $this->afterStore($portfolio);
            });

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "{$this->entityName} created successfully",
                    'portfolio' => $portfolio,
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
     * Display the specified portfolio
     */
    public function show($uuid)
    {
        try {
            $portfolio = $this->modelClass::withTrashed()->where('uuid', $uuid)->firstOrFail();
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'portfolio' => $portfolio->load(['projectType.serviceCategory', 'images']),
                ]);
            }
            
            return view("{$this->viewPrefix}.show", [
                'portfolio' => $portfolio,
                'entityName' => $this->entityName,
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
                ], 404);
            }

            return back()->with('error', "Error retrieving {$this->entityName}");
        }
    }

    /**
     * Show the form for editing the specified portfolio
     */
    public function edit($uuid)
    {
        try {
            if (!$uuid || $uuid === 'undefined') {
                throw new \InvalidArgumentException('Invalid UUID');
            }

            $portfolio = $this->modelClass::withTrashed()->where('uuid', $uuid)->first();

            if (!$portfolio) {
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException("{$this->entityName} not found");
            }

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'portfolio' => $portfolio->load(['projectType.serviceCategory', 'images']),
                ]);
            }

            return view("{$this->viewPrefix}.edit", [
                'portfolio' => $portfolio,
                'entityName' => $this->entityName,
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
     * Update the specified portfolio
     */
    public function update(Request $request, $uuid)
    {
        try {
            if (!$uuid || $uuid === 'undefined') {
                throw new \InvalidArgumentException('Invalid UUID');
            }

            $data = $this->validateRequest($request, $uuid);

            $portfolio = $this->transactionService->run(function () use ($uuid, $data) {
                $portfolio = $this->modelClass::withTrashed()->where('uuid', $uuid)->firstOrFail();
                
                // Actualizar el ProjectType relacionado
                if ($portfolio->projectType) {
                    $portfolio->projectType->update([
                        'title' => $data->title,
                        'description' => $data->description,
                        'service_category_id' => $data->service_category_id,
                    ]);
                }
                
                return $portfolio->fresh();
            }, function ($portfolio) {
                Log::info("{$this->entityName} updated successfully", ['id' => $portfolio->id]);
                $this->afterUpdate($portfolio);
            });

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "{$this->entityName} updated successfully",
                    'portfolio' => $portfolio,
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
     * Remove the specified portfolio (soft delete)
     */
    public function destroy($uuid)
    {
        try {
            if (!$uuid || $uuid === 'undefined') {
                throw new \InvalidArgumentException('Invalid UUID');
            }

            $this->transactionService->run(function () use ($uuid) {
                $portfolio = $this->modelClass::where('uuid', $uuid)->firstOrFail();
                $portfolio->delete();
                return $portfolio;
            }, function ($portfolio) {
                Log::info("{$this->entityName} deleted successfully", ['id' => $portfolio->id]);
                $this->afterDestroy($portfolio);
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
     * Restore a soft-deleted portfolio
     */
    public function restore($uuid)
    {
        try {
            if (!$uuid || $uuid === 'undefined') {
                throw new \InvalidArgumentException('Invalid UUID');
            }

            $this->transactionService->run(function () use ($uuid) {
                $portfolio = $this->modelClass::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
                $portfolio->restore();
                return $portfolio;
            }, function ($portfolio) {
                Log::info("{$this->entityName} restored successfully", ['id' => $portfolio->id]);
                $this->afterRestore($portfolio);
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
     * Check if a title already exists for real-time validation
     */
    public function checkTitleExists(Request $request)
    {
        try {
            $title = $request->input('title');
            $excludeUuid = $request->input('exclude_uuid');

            $query = \App\Models\ProjectType::where('title', $title);

            if ($excludeUuid) {
                // Buscar el portfolio por UUID y obtener su project_type_id
                $portfolio = Portfolio::where('uuid', $excludeUuid)->first();
                if ($portfolio && $portfolio->project_type_id) {
                    $query->where('id', '!=', $portfolio->project_type_id);
                }
            }

            $exists = $query->exists();

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
     * Prepare data for storing a portfolio
     */
    protected function prepareStoreData(Request $request)
    {
        // Crear ProjectType primero
        $projectType = \App\Models\ProjectType::create([
            'title' => $request->title,
            'description' => $request->description,
            'service_category_id' => $request->service_category_id,
        ]);

        return [
            'uuid' => (string) Str::uuid(),
            'project_type_id' => $projectType->id,
            'user_id' => auth()->id(),
        ];
    }

    /**
     * Prepare data for updating a portfolio
     */
    protected function prepareUpdateData(Request $request)
    {
        // No retornamos datos del portfolio directamente, 
        // actualizamos el ProjectType relacionado
        return [];
    }

    /**
     * Hook after storing a portfolio
     */
    protected function afterStore($portfolio)
    {
        // Procesar imágenes si fueron enviadas
        if (request()->hasFile('images')) {
            $this->processImages($portfolio, request()->file('images'));
        }
    }

    /**
     * Hook after updating a portfolio
     */
    protected function afterUpdate($portfolio)
    {
        $portfolioImageService = app(PortfolioImageService::class);
        
        // Procesar eliminación de imágenes
        if (request()->has('images_to_delete')) {
            $imagesToDelete = request()->input('images_to_delete', []);
            foreach ($imagesToDelete as $imageId) {
                $image = PortfolioImage::find($imageId);
                if ($image && $image->portfolio_id === $portfolio->id) {
                    try {
                        // Eliminar archivo de AWS S3
                        $portfolioImageService->deleteImage($image->path);
                        
                        // Eliminar de base de datos
                        $image->delete();
                        
                        Log::info('Portfolio image deleted successfully', [
                            'portfolio_id' => $portfolio->id,
                            'image_id' => $imageId,
                            'image_path' => $image->path
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Error deleting portfolio image', [
                            'portfolio_id' => $portfolio->id,
                            'image_id' => $imageId,
                            'image_path' => $image->path,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
        }
        
        // Procesar reordenamiento de imágenes existentes
        if (request()->has('existing_images_order')) {
            $imageOrder = request()->input('existing_images_order', []);
            foreach ($imageOrder as $index => $imageId) {
                PortfolioImage::where('id', $imageId)
                    ->where('portfolio_id', $portfolio->id)
                    ->update(['order' => $index + 1]);
            }
        }
        
        // Procesar nuevas imágenes
        if (request()->hasFile('images')) {
            $this->processImages($portfolio, request()->file('images'));
        }
    }

    /**
     * Hook after deleting a portfolio
     */
    protected function afterDestroy($portfolio)
    {
        // Eliminar todas las imágenes del portfolio de AWS S3
        $portfolioImageService = app(PortfolioImageService::class);
        
        $images = PortfolioImage::where('portfolio_id', $portfolio->id)->get();
        foreach ($images as $image) {
            try {
                // Eliminar archivo de AWS S3
                $portfolioImageService->deleteImage($image->path);
                
                Log::info('Portfolio image deleted on portfolio destruction', [
                    'portfolio_id' => $portfolio->id,
                    'image_id' => $image->id,
                    'image_path' => $image->path
                ]);
            } catch (\Exception $e) {
                Log::error('Error deleting portfolio image on portfolio destruction', [
                    'portfolio_id' => $portfolio->id,
                    'image_id' => $image->id,
                    'image_path' => $image->path,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        // Eliminar registros de imágenes de la base de datos
        PortfolioImage::where('portfolio_id', $portfolio->id)->delete();
    }

    /**
     * Hook after restoring a portfolio
     */
    protected function afterRestore($portfolio)
    {
        // Add custom logic here, e.g., notify user
    }

    // Métodos requeridos por BaseCrudController
    protected function getValidationRules($id = null)
    {
        $titleRule = 'required|string|min:3|max:255|unique:project_types,title';
        
        // If we have an ID (UUID in this case), exclude it from the unique check
        if ($id) {
            // Necesitamos obtener el project_type_id del portfolio para excluir correctamente
            $portfolio = Portfolio::where('uuid', $id)->first();
            if ($portfolio && $portfolio->project_type_id) {
                $titleRule .= ',' . $portfolio->project_type_id;
            }
        }
        
        return [
            'title' => $titleRule,
            'description' => 'required|string',
            'service_category_id' => 'required|exists:service_categories,id',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    protected function getValidationMessages()
    {
        return [
            'title.required' => 'The title is required.',
            'title.unique' => 'A portfolio with this title already exists.',
            'title.min' => 'The title must be at least 3 characters.',
            'title.max' => 'The title may not be greater than 255 characters.',
            'description.required' => 'The description is required.',
            'service_category_id.required' => 'The service category is required.',
            'service_category_id.exists' => 'The selected service category does not exist.',
        ];
    }

    /**
     * Process uploaded images for portfolio
     */
    private function processImages($portfolio, $images)
    {
        $portfolioImageService = app(PortfolioImageService::class);
        
        foreach ($images as $index => $image) {
            if ($image->isValid()) {
                try {
                    // Usar el servicio para subir a AWS S3
                    $imageUrl = $portfolioImageService->storeImage($image);
                    
                    if ($imageUrl) {
                        // Guardar en base de datos
                        PortfolioImage::create([
                            'portfolio_id' => $portfolio->id,
                            'path' => $imageUrl,
                            'filename' => $image->getClientOriginalName(),
                            'order' => $index + 1, // Mantener el orden
                        ]);
                        
                        Log::info('Portfolio image uploaded successfully', [
                            'portfolio_id' => $portfolio->id,
                            'image_url' => $imageUrl,
                            'original_name' => $image->getClientOriginalName()
                        ]);
                    } else {
                        Log::error('Failed to upload portfolio image', [
                            'portfolio_id' => $portfolio->id,
                            'original_name' => $image->getClientOriginalName()
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Error processing portfolio image', [
                        'portfolio_id' => $portfolio->id,
                        'original_name' => $image->getClientOriginalName(),
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }
    }
} 