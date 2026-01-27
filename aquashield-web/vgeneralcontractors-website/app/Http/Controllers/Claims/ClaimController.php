<?php

namespace App\Http\Controllers\Claims;

use App\Http\Controllers\BaseCrudController;
use App\Http\Requests\Claims\StoreClaimRequest;
use App\Http\Requests\Claims\UpdateClaimRequest;
use App\Http\Resources\Claims\ClaimResource;
use App\Services\Claims\ClaimService;
use App\Services\TransactionService;
use App\Models\Claim;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ClaimController extends BaseCrudController
{
    protected ClaimService $claimService;

    public function __construct(
        TransactionService $transactionService,
        ClaimService $claimService
    ) {
        parent::__construct($transactionService);
        $this->claimService = $claimService;
        
        // Set base properties
        $this->modelClass = Claim::class;
        $this->entityName = 'CLAIM';
        $this->viewPrefix = 'claims';
        $this->routePrefix = 'claims';
    }

    /**
     * Display a listing of claims
     */
    public function index(Request $request)
    {
        if (!$this->checkPermissionWithMessage("READ_{$this->entityName}", "You don't have permission to view claims")) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Permission denied'], 403);
            }
            return redirect()->route('dashboard')->with('error', 'Permission denied');
        }

        try {
            $filters = [
                'search' => $request->input('search', ''),
                'status' => $request->input('status'),
                'priority' => $request->input('priority'),
                'damage_type' => $request->input('damage_type'),
                'insurance_company' => $request->input('insurance_company'),
                'show_deleted' => $request->input('show_deleted', 'false'),
                'sort_field' => $request->input('sort_field', 'created_at'),
                'sort_direction' => $request->input('sort_direction', 'desc'),
            ];

            $perPage = $request->input('per_page', 15);
            $claims = $this->claimService->paginate($filters, $perPage);

            if ($request->ajax()) {
                return ClaimResource::collection($claims);
            }

            return view("{$this->viewPrefix}.index", [
                'claims' => $claims,
                'filters' => $filters
            ]);

        } catch (\Exception $e) {
            Log::error("Error in ClaimController::index: {$e->getMessage()}", [
                'exception' => $e,
                'request' => $request->all(),
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error loading claims'
                ], 500);
            }

            return back()->with('error', 'Error loading claims');
        }
    }

    /**
     * Store a newly created claim
     */
    public function store(StoreClaimRequest $request): JsonResponse
    {
        if (!$this->checkPermissionWithMessage("CREATE_{$this->entityName}")) {
            return response()->json(['error' => 'Permission denied'], 403);
        }

        try {
            $claim = $this->claimService->create($request->getClaimData());

            return response()->json([
                'success' => true,
                'message' => 'Claim created successfully!',
                'data' => new ClaimResource($claim),
                'redirectUrl' => route('claims.show', $claim->uuid)
            ], 201);

        } catch (\Exception $e) {
            Log::error("Error creating claim: {$e->getMessage()}", [
                'exception' => $e,
                'request_data' => $request->getClaimData(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error creating claim: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified claim
     */
    public function show(string $uuid, Request $request)
    {
        if (!$this->checkPermissionWithMessage("READ_{$this->entityName}")) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Permission denied'], 403);
            }
            return redirect()->route('dashboard')->with('error', 'Permission denied');
        }

        try {
            $claim = $this->claimService->findByUuid($uuid);

            if (!$claim) {
                if ($request->ajax()) {
                    return response()->json(['error' => 'Claim not found'], 404);
                }
                return redirect()->route('claims.index')->with('error', 'Claim not found');
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => new ClaimResource($claim)
                ]);
            }

            return view("{$this->viewPrefix}.show", [
                'claim' => $claim
            ]);

        } catch (\Exception $e) {
            Log::error("Error showing claim: {$e->getMessage()}", [
                'uuid' => $uuid,
                'exception' => $e,
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error loading claim'
                ], 500);
            }

            return back()->with('error', 'Error loading claim');
        }
    }

    /**
     * Show the form for editing the specified claim
     */
    public function edit(string $uuid, Request $request)
    {
        if (!$this->checkPermissionWithMessage("UPDATE_{$this->entityName}")) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Permission denied'], 403);
            }
            return redirect()->route('claims.index')->with('error', 'Permission denied');
        }

        try {
            $claim = $this->claimService->findByUuid($uuid);

            if (!$claim) {
                if ($request->ajax()) {
                    return response()->json(['error' => 'Claim not found'], 404);
                }
                return redirect()->route('claims.index')->with('error', 'Claim not found');
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => new ClaimResource($claim)
                ]);
            }

            return view("{$this->viewPrefix}.edit", [
                'claim' => $claim
            ]);

        } catch (\Exception $e) {
            Log::error("Error loading claim for edit: {$e->getMessage()}", [
                'uuid' => $uuid,
                'exception' => $e,
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error loading claim'
                ], 500);
            }

            return back()->with('error', 'Error loading claim');
        }
    }

    /**
     * Update the specified claim
     */
    public function update(UpdateClaimRequest $request, string $uuid): JsonResponse
    {
        if (!$this->checkPermissionWithMessage("UPDATE_{$this->entityName}")) {
            return response()->json(['error' => 'Permission denied'], 403);
        }

        try {
            $claim = $this->claimService->findByUuid($uuid);

            if (!$claim) {
                return response()->json(['error' => 'Claim not found'], 404);
            }

            $updatedClaim = $this->claimService->update($claim, $request->getClaimData());

            return response()->json([
                'success' => true,
                'message' => 'Claim updated successfully!',
                'data' => new ClaimResource($updatedClaim)
            ]);

        } catch (\Exception $e) {
            Log::error("Error updating claim: {$e->getMessage()}", [
                'uuid' => $uuid,
                'exception' => $e,
                'request_data' => $request->getClaimData(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error updating claim: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified claim from storage
     */
    public function destroy(string $uuid): JsonResponse
    {
        if (!$this->checkPermissionWithMessage("DELETE_{$this->entityName}")) {
            return response()->json(['error' => 'Permission denied'], 403);
        }

        try {
            $claim = $this->claimService->findByUuid($uuid);

            if (!$claim) {
                return response()->json(['error' => 'Claim not found'], 404);
            }

            $this->claimService->delete($claim);

            return response()->json([
                'success' => true,
                'message' => 'Claim deleted successfully!'
            ]);

        } catch (\Exception $e) {
            Log::error("Error deleting claim: {$e->getMessage()}", [
                'uuid' => $uuid,
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error deleting claim: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search claims
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:2'
        ]);

        try {
            $claims = $this->claimService->searchClaims($request->q);
            
            return response()->json([
                'success' => true,
                'data' => ClaimResource::collection($claims)
            ]);

        } catch (\Exception $e) {
            Log::error("Error searching claims: {$e->getMessage()}", [
                'query' => $request->q,
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error searching claims'
            ], 500);
        }
    }

    /**
     * Get dashboard data for claims
     */
    public function dashboard(): JsonResponse
    {
        try {
            $data = [
                'pending_claims' => $this->claimService->getPendingClaims()->count(),
                'urgent_claims' => $this->claimService->getUrgentClaims()->count(),
                'inspections_scheduled' => $this->claimService->getClaimsForInspection()->count(),
                'follow_up_needed' => $this->claimService->getClaimsNeedingFollowUp()->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            Log::error("Error loading dashboard data: {$e->getMessage()}", [
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading dashboard data'
            ], 500);
        }
    }
}