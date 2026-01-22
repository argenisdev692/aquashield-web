<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Services\TransactionService;
use App\Traits\CacheTraitCrud;

use App\Traits\UserDataFormatter;
use App\Jobs\SendUserCredentialsEmail;
use Throwable;

class UserController extends BaseCrudController
{
    use CacheTraitCrud;
    use UserDataFormatter;
    
    protected $modelClass = User::class;
    protected $entityName = 'USER';
    protected $routePrefix = 'users';
    protected $viewPrefix = 'users';
    
    // Cache time for users - 5 minutes
    protected $cacheTime = 300;

    public function __construct(TransactionService $transactionService)
    {
        parent::__construct($transactionService);
        
        // Initialize cache properties with defaults
        $this->initializeCacheProperties();
    }

    /**
     * Get validation rules for users
     */
    protected function getValidationRules($id = null)
    {
        // Determine if we're in store or update mode
        $modalAction = request()->isMethod('post') ? 'store' : 'update';
        
        if ($modalAction === 'store') {
            $rules = [
                'name' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u',
                'last_name' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u',
                'email' => ['required', 'string', 'email', 'max:255', 
                    Rule::unique('users', 'email')->ignore($id, 'uuid')],
                'phone' => ['nullable', 'string', 'max:20',
                    Rule::unique('users', 'phone')->ignore($id, 'uuid')],
                'date_of_birth' => 'nullable|date',
                'address' => 'nullable|string|max:255',
                'zip_code' => 'nullable|string|max:20',
                'city' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
                'country' => 'nullable|string|max:100',
                'gender' => 'nullable|string|in:male,female,other',
                'terms_and_conditions' => 'nullable|boolean',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'role' => 'required|string|exists:roles,name',
            ];
        } else {
            $rules = [
                'name' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u',
                'last_name' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u',
                'email' => ['required', 'string', 'email', 'max:255', 
                    Rule::unique('users', 'email')->ignore($id, 'uuid')],
                'username' => ['required', 'string', 'min:7', 'max:255', 'regex:/^.*[0-9].*[0-9].*$/',
                    Rule::unique('users', 'username')->ignore($id, 'uuid')],
                'date_of_birth' => 'nullable|date',
                'phone' => ['nullable', 'string', 'max:20',
                    Rule::unique('users', 'phone')->ignore($id, 'uuid')],
                'address' => 'nullable|string|max:255',
                'zip_code' => 'nullable|string|max:20',
                'city' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
                'country' => 'nullable|string|max:100',
                'gender' => 'nullable|string|in:male,female,other',
                'terms_and_conditions' => 'nullable|boolean',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'role' => 'required|string|exists:roles,name',
                // Campo especial para reset de password - permitir cualquier formato que pueda llegar
                'send_password_reset' => 'nullable',
            ];
        }
        
        return $rules;
    }

    /**
     * Get validation messages for users
     */
    protected function getValidationMessages()
    {
        return [
            'name.required' => 'The first name is required.',
            'name.string' => 'The first name must be a string.',
            'name.max' => 'The first name may not be greater than 255 characters.',
            'last_name.required' => 'The last name is required.',
            'last_name.string' => 'The last name must be a string.',
            'last_name.max' => 'The last name may not be greater than 255 characters.',
            'email.required' => 'The email is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'This email is already taken.',
            'email.max' => 'The email may not be greater than 255 characters.',
            'username.required' => 'The username is required.',
            'username.unique' => 'This username is already taken.',
            'username.min' => 'The username must be at least 7 characters.',
            'username.regex' => 'The username must contain at least 2 numbers.',
            'phone.required' => 'The phone number is required.',
            'phone.unique' => 'This phone number is already taken.',
            'phone.max' => 'The phone number may not be greater than 20 characters.',
            'role.required' => 'The role is required.',
            'role.exists' => 'The selected role does not exist.',
            'address.max' => 'The address may not be greater than 255 characters.',
            'city.max' => 'The city may not be greater than 100 characters.',
            'state.max' => 'The state may not be greater than 100 characters.',
            'zip_code.max' => 'The zip code may not be greater than 20 characters.',
            'country.max' => 'The country may not be greater than 100 characters.',
            'gender.in' => 'The gender must be male, female, or other.',
        ];
    }

    /**
     * Prepare data for storing a user
     */
    protected function prepareStoreData(Request $request)
    {
        Log::info('UserController::prepareStoreData - Preparing data', [
            'name' => $request->name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role
        ]);

        // Generate username from name and last_name
        $baseUsername = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $request->name)) . 
                       strtolower(substr(preg_replace('/[^a-zA-Z0-9]/', '', $request->last_name), 0, 1));
        $username = $this->generateUniqueUsername($baseUsername);
        
        // Generate random password
        $randomPassword = Str::random(12);

        $data = [
            'uuid' => (string) Str::uuid(),
            'name' => $request->name,
            'last_name' => $request->last_name,
            'username' => $username,
            'date_of_birth' => $request->date_of_birth,
            'email' => strtolower($request->email),
            'password' => $randomPassword, // Will be hashed in formatUserData
            'phone' => $request->phone,
            'address' => $request->address,
            'zip_code' => $request->zip_code,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'gender' => $request->gender,
            'terms_and_conditions' => true,
            'latitude' => null,
            'longitude' => null,
            'role' => $request->role,
            'generated_password' => $randomPassword, // Store for email sending
        ];

        // Preserve generated_password before formatting (since trait doesn't include it)
        $generatedPassword = $data['generated_password'];
        
        // Format data using trait
        $formattedData = $this->formatUserData($data);
        
        // Restore generated_password after formatting
        $formattedData['generated_password'] = $generatedPassword;
        
        // Hash password
        if (isset($formattedData['password'])) {
            $formattedData['password'] = Hash::make($formattedData['password']);
        }

        Log::info('UserController::prepareStoreData - Returning formatted data', [
            'has_generated_password' => isset($formattedData['generated_password']),
            'generated_password_length' => isset($formattedData['generated_password']) ? strlen($formattedData['generated_password']) : 0,
            'has_hashed_password' => isset($formattedData['password'])
        ]);

        return $formattedData;
    }

    /**
     * Prepare data for updating a user
     */
    protected function prepareUpdateData(Request $request)
    {
        Log::info('UserController::prepareUpdateData - Preparing data', [
            'name' => $request->name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role
        ]);

        $data = [
            'name' => $request->name,
            'last_name' => $request->last_name,
            'username' => $request->username,
            'email' => strtolower($request->email),
            'date_of_birth' => $request->date_of_birth,
            'phone' => $request->phone,
            'address' => $request->address,
            'zip_code' => $request->zip_code,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'gender' => $request->gender,
            'role' => $request->role,
        ];

        // Handle password reset if requested
        // Manejo robusto del campo checkbox que puede venir como boolean, string, número o null
        $sendPasswordResetValue = $request->input('send_password_reset');
        $sendPasswordReset = false;
        
        Log::info('UserController::prepareUpdateData - Processing send_password_reset', [
            'raw_value' => $sendPasswordResetValue,
            'type' => gettype($sendPasswordResetValue),
            'is_null' => is_null($sendPasswordResetValue)
        ]);
        
        if ($sendPasswordResetValue !== null && $sendPasswordResetValue !== '') {
            // Convertir diferentes tipos de valores a boolean
            if (is_bool($sendPasswordResetValue)) {
                $sendPasswordReset = $sendPasswordResetValue;
            } elseif (is_string($sendPasswordResetValue)) {
                $sendPasswordReset = in_array(strtolower($sendPasswordResetValue), ['true', '1', 'on', 'yes']);
            } elseif (is_numeric($sendPasswordResetValue)) {
                $sendPasswordReset = (int)$sendPasswordResetValue === 1;
            }
        }
        
        Log::info('UserController::prepareUpdateData - Final send_password_reset value', [
            'send_password_reset' => $sendPasswordReset
        ]);
        
        if ($sendPasswordReset) {
            $newPassword = Str::random(12);
            $data['password'] = $newPassword;
            $data['generated_password'] = $newPassword; // Store for email sending
            
            Log::info('UserController::prepareUpdateData - Password reset requested, generated new password', [
                'password_length' => strlen($newPassword)
            ]);
        }

        // Preserve generated_password before formatting (since trait doesn't include it)
        $generatedPassword = $data['generated_password'] ?? null;
        
        // Format data using trait
        $formattedData = $this->formatUserData($data);
        
        // Restore generated_password after formatting
        if ($generatedPassword) {
            $formattedData['generated_password'] = $generatedPassword;
        }
        
        // Hash password if provided
        if (isset($formattedData['password'])) {
            $formattedData['password'] = Hash::make($formattedData['password']);
        }

        Log::info('UserController::prepareUpdateData - Returning formatted data', [
            'has_generated_password' => isset($formattedData['generated_password']),
            'generated_password_length' => isset($formattedData['generated_password']) ? strlen($formattedData['generated_password']) : 0,
            'has_hashed_password' => isset($formattedData['password'])
        ]);

        return $formattedData;
    }

    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        // Check permission first
        if (!$this->checkPermission('READ_USER', false)) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to view users',
                ], 403);
            }
            
            return redirect()->route('dashboard')->with('error', 'You do not have permission to view users');
        }

        try {
            // Set up cache and search parameters
            $this->search = $request->input('search', '');
            $this->sortField = $request->input('sort_field', 'created_at');
            $this->sortDirection = $request->input('sort_direction', 'desc');
            $this->perPage = $request->input('per_page', 10);
            $this->showDeleted = $request->input('show_deleted', 'false') === 'true';
            
            $page = $request->input('page', 1);
            
            // Use cache for normal views
            $users = $this->rememberCrudCache('users', function() use ($request, $page) {
                $query = $this->buildUsersQuery($request);
                
                // Pagination
                return $query->paginate($this->perPage, ['*'], 'page', $page);
            }, $page);

            if ($request->ajax()) {
                return response()->json($users);
            }

            // Get roles for the create/edit modals
            $roles = \Spatie\Permission\Models\Role::pluck('name', 'name')->toArray();

            return view("{$this->viewPrefix}.index", [
                'users' => $users,
                'entityName' => $this->entityName,
                'roles' => $roles,
            ]);
        } catch (Throwable $e) {
            Log::error("Error in UserController::index: {$e->getMessage()}", [
                'exception' => $e,
                'request' => $request->all(),
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error loading users',
                ], 500);
            }

            return back()->with('error', 'Error loading users');
        }
    }

    /**
     * Build the users query
     */
    private function buildUsersQuery(Request $request)
    {
        $query = $this->modelClass::query();

        // Load roles relationship for table display
        $query->with('roles');

        // Handle search
        if (!empty($this->search)) {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('last_name', 'like', $searchTerm)
                  ->orWhere('email', 'like', $searchTerm)
                  ->orWhere('username', 'like', $searchTerm)
                  ->orWhere('address', 'like', $searchTerm)
                  ->orWhereHas('roles', function ($roleQuery) use ($searchTerm) {
                      $roleQuery->where('name', 'like', $searchTerm);
                  });
            });
        }

        // Handle soft deletes
        if ($this->showDeleted) {
            $query->withTrashed();
        }

        // Sorting
        if ($this->sortField === 'role') {
            // Special handling for role sorting
            $query->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                  ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
                  ->orderBy('roles.name', $this->sortDirection)
                  ->select('users.*'); // Ensure we only select users columns
        } else {
            $query->orderBy($this->sortField, $this->sortDirection);
        }
        
        return $query;
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        try {
            if (!$this->checkPermissionWithMessage("CREATE_{$this->entityName}", "You don't have permission to create users")) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permission denied',
                ], 403);
            }

            Log::info('UserController::store - Starting store process', [
                'request_data' => $request->except(['password'])
            ]);

            $data = $request->validate($this->getValidationRules());
            Log::info('UserController::store - Validation passed');

            $user = $this->transactionService->run(function () use ($request) {
                $preparedData = $this->prepareStoreData($request);
                $generatedPassword = $preparedData['generated_password'] ?? null;
                unset($preparedData['generated_password']); // Remove from data to be saved
                
                Log::info('UserController::store - Creating user', ['prepared_data' => collect($preparedData)->except(['password'])->toArray()]);
                
                $user = $this->modelClass::create($preparedData);
                
                // Assign role
                if ($request->role) {
                    $user->assignRole($request->role);
                }
                
                // Store password for email sending - assign it to the user object, not save to DB
                if ($generatedPassword) {
                    $user->generated_password = $generatedPassword;
                }
                
                return $user;
            }, function ($user) {
                Log::info("{$this->entityName} created successfully", ['id' => $user->id]);
                
                // Use new CRUD cache clearing
                $this->markSignificantDataChange();
                $this->clearCrudCache('users');
                
                $this->afterStore($user);
            });

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "{$this->entityName} created successfully. Credentials will be sent by email.",
                    'user' => $user,
                ]);
            }

            return redirect()->route("{$this->routePrefix}.index")
                ->with('message', "{$this->entityName} created successfully. Credentials will be sent by email.");
        } catch (Throwable $e) {
            Log::error("Error creating {$this->entityName}: {$e->getMessage()}", [
                'exception' => $e,
                'request' => $request->except(['password']),
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
     * Show the form for editing the specified user
     */
    public function edit($uuid)
    {
        try {
            if (!$uuid || $uuid === 'undefined') {
                throw new \InvalidArgumentException('Invalid UUID');
            }

            $user = $this->modelClass::withTrashed()->where('uuid', $uuid)->first();

            if (!$user) {
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException("{$this->entityName} not found");
            }

            if (request()->ajax()) {
                // Get user's role
                $userRole = $user->roles->first()->name ?? '';
                
                // Add role to user data
                $userData = $user->toArray();
                $userData['role'] = $userRole;
                
                Log::info('UserController::edit - Returning data:', [
                    'user' => collect($userData)->except(['password'])->toArray()
                ]);
                
                return response()->json([
                    'success' => true,
                    'data' => $userData,
                ]);
            }

            $roles = \Spatie\Permission\Models\Role::pluck('name', 'name')->toArray();

            return view("{$this->viewPrefix}.edit", [
                'user' => $user,
                'entityName' => $this->entityName,
                'roles' => $roles,
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
     * Update the specified user
     */
    public function update(Request $request, $uuid)
    {
        try {
            if (!$this->checkPermissionWithMessage("UPDATE_{$this->entityName}", "You don't have permission to update users")) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permission denied',
                ], 403);
            }

            Log::info('UserController::update - Starting update process', [
                'uuid' => $uuid,
                'all_request_data' => $request->all(),
                'send_password_reset_raw' => $request->input('send_password_reset'),
                'send_password_reset_type' => gettype($request->input('send_password_reset'))
            ]);

            if (!$uuid || $uuid === 'undefined') {
                throw new \InvalidArgumentException('Invalid UUID');
            }

            $data = $request->validate($this->getValidationRules($uuid));
            Log::info('UserController::update - Validation passed', [
                'validated_data' => collect($data)->except(['password'])->toArray()
            ]);

            $generatedPassword = null; // Declare outside transaction scope
            
            $user = $this->transactionService->run(function () use ($uuid, $request, &$generatedPassword) {
                $user = $this->modelClass::withTrashed()->where('uuid', $uuid)->firstOrFail();
                Log::info('UserController::update - Found user', ['current_data' => collect($user->toArray())->except(['password'])->toArray()]);
                
                $preparedData = $this->prepareUpdateData($request);
                $generatedPassword = $preparedData['generated_password'] ?? null; // Store in outer scope
                unset($preparedData['generated_password']); // Remove from data to be saved
                
                Log::info('UserController::update - Prepared data', ['prepared_data' => collect($preparedData)->except(['password'])->toArray()]);
                Log::info('UserController::update - Generated password captured', [
                    'has_generated_password' => !is_null($generatedPassword),
                    'password_length' => $generatedPassword ? strlen($generatedPassword) : 0
                ]);
                
                $user->update($preparedData);
                
                // Update user role
                if ($request->role) {
                    $user->syncRoles([$request->role]);
                }
                
                return $user->fresh();
            }, function ($user) use ($request, &$generatedPassword) {
                Log::info("{$this->entityName} updated successfully", ['id' => $user->id]);
                
                // Use new CRUD cache clearing
                $this->markSignificantDataChange();
                $this->clearCrudCache('users');
                
                // Store the password reset request info for afterUpdate hook
                // Manejo robusto del campo checkbox que puede venir como boolean, string, número o null
                $sendPasswordResetValue = $request->input('send_password_reset');
                $sendPasswordReset = false;
                
                Log::info('UserController transaction callback - Processing send_password_reset', [
                    'raw_value' => $sendPasswordResetValue,
                    'type' => gettype($sendPasswordResetValue),
                    'is_null' => is_null($sendPasswordResetValue)
                ]);
                
                if ($sendPasswordResetValue !== null && $sendPasswordResetValue !== '' && $sendPasswordResetValue !== false) {
                    // Convertir diferentes tipos de valores a boolean
                    if (is_bool($sendPasswordResetValue)) {
                        $sendPasswordReset = $sendPasswordResetValue;
                    } elseif (is_string($sendPasswordResetValue)) {
                        $sendPasswordReset = in_array(strtolower($sendPasswordResetValue), ['true', '1', 'on', 'yes']);
                    } elseif (is_numeric($sendPasswordResetValue)) {
                        $sendPasswordReset = (int)$sendPasswordResetValue === 1;
                    }
                }
                
                Log::info('UserController transaction callback - Final send_password_reset value', [
                    'send_password_reset' => $sendPasswordReset
                ]);
                
                // Store password reset flag and generated password for afterUpdate
                $user->should_send_password_reset = $sendPasswordReset;
                $user->generated_password = $generatedPassword;
                
                Log::info('UserController transaction callback - Passing to afterUpdate', [
                    'should_send_password_reset' => $sendPasswordReset,
                    'has_generated_password' => !is_null($generatedPassword),
                    'password_length' => $generatedPassword ? strlen($generatedPassword) : 0
                ]);
                
                $this->afterUpdate($user);
            });

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "{$this->entityName} updated successfully.",
                    'user' => $user,
                ]);
            }

            return redirect()->route("{$this->routePrefix}.index")
                ->with('message', "{$this->entityName} updated successfully.");
        } catch (Throwable $e) {
            Log::error("Error updating {$this->entityName}: {$e->getMessage()}", [
                'uuid' => $uuid,
                'exception' => $e,
                'request' => $request->except(['password']),
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => "Error updating {$this->entityName}",
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
     * Remove the specified user from storage
     */
    public function destroy($uuid)
    {
        try {
            if (!$this->checkPermissionWithMessage("DELETE_{$this->entityName}", "You don't have permission to delete users")) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permission denied',
                ], 403);
            }

            if (!$uuid || $uuid === 'undefined') {
                throw new \InvalidArgumentException('Invalid UUID');
            }

            $this->transactionService->run(function () use ($uuid) {
                $user = $this->modelClass::where('uuid', $uuid)->firstOrFail();
                $user->delete();
                return $user;
            }, function ($user) {
                Log::info("{$this->entityName} deleted successfully", ['id' => $user->id]);
                
                // Use new CRUD cache clearing
                $this->markSignificantDataChange();
                $this->clearCrudCache('users');
                
                $this->afterDestroy($user);
            });

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "{$this->entityName} deleted successfully",
                ]);
            }

            return redirect()->route("{$this->routePrefix}.index")
                ->with('message', "{$this->entityName} deleted successfully");
        } catch (Throwable $e) {
            Log::error("Error deleting {$this->entityName}: {$e->getMessage()}", [
                'uuid' => $uuid,
                'exception' => $e,
            ]);

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => "Error deleting {$this->entityName}",
                ], 500);
            }

            return back()->with('error', "Error deleting {$this->entityName}");
        }
    }

    /**
     * Restore the specified user
     */
    public function restore($uuid)
    {
        try {
            if (!$this->checkPermissionWithMessage("RESTORE_{$this->entityName}", "You don't have permission to restore users")) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permission denied',
                ], 403);
            }

            if (!$uuid || $uuid === 'undefined') {
                throw new \InvalidArgumentException('Invalid UUID');
            }

            $this->transactionService->run(function () use ($uuid) {
                $user = $this->modelClass::withTrashed()->where('uuid', $uuid)->firstOrFail();
                $user->restore();
                return $user;
            }, function ($user) {
                Log::info("{$this->entityName} restored successfully", ['id' => $user->id]);
                
                // Use new CRUD cache clearing
                $this->markSignificantDataChange();
                $this->clearCrudCache('users');
                
                $this->afterRestore($user);
            });

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "{$this->entityName} restored successfully",
                ]);
            }

            return redirect()->route("{$this->routePrefix}.index")
                ->with('message', "{$this->entityName} restored successfully");
        } catch (Throwable $e) {
            Log::error("Error restoring {$this->entityName}: {$e->getMessage()}", [
                'uuid' => $uuid,
                'exception' => $e,
            ]);

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => "Error restoring {$this->entityName}",
                ], 500);
            }

            return back()->with('error', "Error restoring {$this->entityName}");
        }
    }

    /**
     * Check if email exists
     */
    public function checkEmailExists(Request $request)
    {
        try {
            $email = $request->input('email');
            $uuid = $request->input('uuid');

            if (empty($email)) {
                return response()->json(['exists' => false]);
            }

            $query = User::where('email', strtolower($email));
            
            if ($uuid) {
                $query->where('uuid', '!=', $uuid);
            }

            $exists = $query->exists();

            return response()->json(['exists' => $exists]);
        } catch (Throwable $e) {
            Log::error('Error checking email existence: ' . $e->getMessage());
            return response()->json(['exists' => false], 500);
        }
    }

    /**
     * Check if phone exists
     */
    public function checkPhoneExists(Request $request)
    {
        try {
            $phone = $request->input('phone');
            $uuid = $request->input('uuid');

            if (empty($phone)) {
                return response()->json(['exists' => false]);
            }

            $formattedPhone = $this->formatPhoneForStorage($phone);
            
            $query = User::where('phone', $formattedPhone);
            
            if ($uuid) {
                $query->where('uuid', '!=', $uuid);
            }

            $exists = $query->exists();

            return response()->json(['exists' => $exists]);
        } catch (Throwable $e) {
            Log::error('Error checking phone existence: ' . $e->getMessage());
            return response()->json(['exists' => false], 500);
        }
    }

    /**
     * Check if username exists
     */
    public function checkUsernameExists(Request $request)
    {
        try {
            $username = $request->input('username');
            $uuid = $request->input('uuid');

            if (empty($username)) {
                return response()->json(['exists' => false]);
            }
            
            $query = User::where('username', $username);
            
            if ($uuid) {
                $query->where('uuid', '!=', $uuid);
            }

            $exists = $query->exists();

            return response()->json(['exists' => $exists]);
        } catch (Throwable $e) {
            Log::error('Error checking username existence: ' . $e->getMessage());
            return response()->json(['exists' => false], 500);
        }
    }

    /**
     * Generate a unique username
     */
    private function generateUniqueUsername($baseUsername)
    {
        $username = $baseUsername;
        $isUnique = false;
        $attempts = 0;
        
        while (!$isUnique && $attempts < 10) {
            // Add 3 random numbers to ensure we have at least 2 numbers
            $randomNumbers = rand(100, 999);
            $username = $baseUsername . $randomNumbers;
            
            // Ensure username is at least 7 characters
            if (strlen($username) < 7) {
                // Add more characters if needed
                $extraChars = 7 - strlen($username);
                $username .= Str::random($extraChars);
            }
            
            // Check if username exists
            $exists = User::where('username', $username)->exists();
            
            if (!$exists) {
                $isUnique = true;
            }
            
            $attempts++;
        }
        
        // If we couldn't generate a unique username after 10 attempts,
        // add more random characters
        if (!$isUnique) {
            $username = $baseUsername . rand(100, 999) . Str::random(3);
            
            // Ensure username is at least 7 characters
            if (strlen($username) < 7) {
                $username = $baseUsername . rand(100, 999) . Str::random(3);
                $extraChars = 7 - strlen($username);
                $username .= Str::random($extraChars);
            }
        }
        
        return $username;
    }

    /**
     * Format phone number for storage
     */
    private function formatPhoneForStorage($phone)
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // If it's a US number (10 digits), add +1 prefix
        if (strlen($phone) === 10) {
            return '+1' . $phone;
        }
        
        // If it's 11 digits and starts with 1, add + prefix
        if (strlen($phone) === 11 && substr($phone, 0, 1) === '1') {
            return '+' . $phone;
        }
        
        // Return as-is for international numbers
        return $phone;
    }

    /**
     * Get search field for the entity
     */
    protected function getSearchField()
    {
        return 'name';
    }

    /**
     * Get name field for the entity
     */
    protected function getNameField()
    {
        return 'name';
    }

    /**
     * Get entity display name
     */
    protected function getEntityDisplayName($entity)
    {
        return $entity->name . ' ' . $entity->last_name . ' (' . $entity->email . ')';
    }

    /**
     * After store hook
     */
    protected function afterStore($user)
    {
        Log::info("User created: {$user->name} {$user->last_name} ({$user->email})");
        
        // Send credentials email if password was generated
        if (isset($user->generated_password) && $user->generated_password) {
            dispatch(new SendUserCredentialsEmail($user, $user->generated_password, false));
            Log::info('User credentials email dispatched via creation', [
                'user_id' => $user->id,
                'email' => $user->email,
                'password_length' => strlen($user->generated_password)
            ]);
        }
    }

    /**
     * After update hook
     */
    protected function afterUpdate($user)
    {
        Log::info("User updated: {$user->name} {$user->last_name} ({$user->email})");
        
        // Send password reset email if requested
        if (isset($user->should_send_password_reset) && $user->should_send_password_reset) {
            if (isset($user->generated_password) && $user->generated_password) {
                dispatch(new SendUserCredentialsEmail($user, $user->generated_password, true));
                Log::info('Password reset email dispatched via update', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'password_length' => strlen($user->generated_password)
                ]);
            } else {
                Log::warning('Password reset requested but no generated password found', [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
            }
        }
    }

    /**
     * After destroy hook
     */
    protected function afterDestroy($user)
    {
        Log::info("User deleted: {$user->name} {$user->last_name} ({$user->email})");
    }

    /**
     * After restore hook
     */
    protected function afterRestore($user)
    {
        Log::info("User restored: {$user->name} {$user->last_name} ({$user->email})");
    }
} 