<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * CacheTraitCrud - Generic caching system for CRUD operations
 * 
 * CACHE TIME CONFIGURATION (Senior Best Practice):
 * ===============================================
 * 
 * Priority Order:
 * 1. Controller Override (highest) - Define $cacheTime in your controller when specific timing needed
 *    protected $cacheTime = 60; // 1 minute for frequently changing data
 * 
 * 2. Trait Default (medium) - Uses $defaultCacheTime from this trait (300 seconds)
 *    Most CRUD operations work well with 5 minutes
 * 
 * 3. Fallback (lowest) - Hardcoded 300 seconds if nothing else is defined
 * 
 * USAGE EXAMPLES:
 * ==============
 * 
 * // Most controllers - just use the trait, no cache time definition needed
 * class ProductController extends BaseCrudController {
 *     use CacheTraitCrud; // Will use 300 seconds (5 minutes)
 * }
 * 
 * // High-frequency controllers - override when needed
 * class EmailDataController extends BaseCrudController {
 *     use CacheTraitCrud;
 *     protected $cacheTime = 60; // Override to 1 minute
 * }
 * 
 * // Very stable data - override for longer cache
 * class CountryController extends BaseCrudController {
 *     use CacheTraitCrud;
 *     protected $cacheTime = 3600; // Override to 1 hour
 * }
 */
trait CacheTraitCrud
{
    /**
     * Cache configuration
     */
    protected $cacheEnabled = true;
    protected $defaultCacheTime = 300; // 5 minutes - sensible default for most CRUD operations
    protected $significantDataChange = false;
    
    // Cache key properties - will be initialized if not set
    protected $search = '';
    protected $sortField = 'created_at';
    protected $sortDirection = 'desc';
    protected $perPage = 10;
    protected $showDeleted = false;

    /**
     * Initialize cache properties with default values
     */
    protected function initializeCacheProperties()
    {
        $this->search = $this->search ?? '';
        $this->sortField = $this->sortField ?? 'created_at';
        $this->sortDirection = $this->sortDirection ?? 'desc';
        $this->perPage = $this->perPage ?? 10;
        $this->showDeleted = $this->showDeleted ?? false;
        $this->significantDataChange = $this->significantDataChange ?? false;
    }

    /**
     * Generate a cache key for CRUD operations
     *
     * @param string $modelName Name of the model
     * @param int|null $page Current page number
     * @param bool|null $includeTrashed Whether to include soft-deleted records
     * @return string
     */
    protected function generateCrudCacheKey($modelName, $page = null, $includeTrashed = null)
    {
        $this->initializeCacheProperties();
        
        $page = $page ?? request()->query('page', 1);
        $includeTrashed = $includeTrashed ?? $this->showDeleted;

        return strtolower($modelName) . '_crud_' .
               md5($this->search) . '_' .
               $this->sortField . '_' .
               $this->sortDirection . '_' .
               $this->perPage . '_' .
               $page . '_' .
               ($includeTrashed ? 'with_deleted' : 'active');
    }

    /**
     * Clear CRUD cache for the specified model
     *
     * @param string $modelName Name of the model
     */
    protected function clearCrudCache($modelName)
    {
        if (!$this->cacheEnabled) {
            return;
        }

        $this->initializeCacheProperties();
        
        Log::info('CacheTraitCrud::clearCrudCache - Starting cache clear', [
            'modelName' => $modelName,
            'search' => $this->search,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
            'perPage' => $this->perPage,
            'showDeleted' => $this->showDeleted,
            'significantDataChange' => $this->significantDataChange
        ]);
        
        $currentPage = request()->query('page', 1);
        $clearedKeys = [];

        // Clear the current cache key
        $currentCacheKey = $this->generateCrudCacheKey($modelName, $currentPage);
        Cache::forget($currentCacheKey);
        $clearedKeys[] = $currentCacheKey;
        
        // Clear search count cache
        $countKey = strtolower($modelName) . '_crud_count_' . md5($this->search);
        Cache::forget($countKey);
        $clearedKeys[] = $countKey;
        
        // Clear adjacent pages
        if ($currentPage > 1) {
            $prevPageKey = $this->generateCrudCacheKey($modelName, $currentPage - 1);
            Cache::forget($prevPageKey);
            $clearedKeys[] = $prevPageKey;
        }
        
        $nextPageKey = $this->generateCrudCacheKey($modelName, $currentPage + 1);
        Cache::forget($nextPageKey);
        $clearedKeys[] = $nextPageKey;

        // If significant data change, clear more cache entries
        if ($this->significantDataChange) {
            Log::info('CacheTraitCrud::clearCrudCache - Clearing extensive cache due to significant data change');
            
            // Clear first 15 pages for both active and deleted states
            for ($i = 1; $i <= 15; $i++) {
                $activeKey = $this->generateCrudCacheKey($modelName, $i, false);
                $deletedKey = $this->generateCrudCacheKey($modelName, $i, true);
                Cache::forget($activeKey);
                Cache::forget($deletedKey);
                $clearedKeys[] = $activeKey;
                $clearedKeys[] = $deletedKey;
            }
            
            // Clear common search variations
            $commonSearches = ['', 'deleted', 'active', 'test', 'admin'];
            $originalSearch = $this->search;
            
            foreach ($commonSearches as $searchTerm) {
                $this->search = $searchTerm;
                for ($i = 1; $i <= 10; $i++) {
                    $activeKey = $this->generateCrudCacheKey($modelName, $i, false);
                    $deletedKey = $this->generateCrudCacheKey($modelName, $i, true);
                    Cache::forget($activeKey);
                    Cache::forget($deletedKey);
                    $clearedKeys[] = $activeKey;
                    $clearedKeys[] = $deletedKey;
                }
            }
            
            // Restore original search
            $this->search = $originalSearch;
            
            // Clear with different sort fields
            $commonSortFields = ['created_at', 'updated_at', 'name', 'email', 'description'];
            $originalSortField = $this->sortField;
            
            foreach ($commonSortFields as $sortField) {
                $this->sortField = $sortField;
                for ($i = 1; $i <= 5; $i++) {
                    $key = $this->generateCrudCacheKey($modelName, $i);
                    Cache::forget($key);
                    $clearedKeys[] = $key;
                }
            }
            
            // Restore original sort field
            $this->sortField = $originalSortField;
            
            Log::info('CacheTraitCrud::clearCrudCache - Extensive cache clear completed', [
                'total_keys_cleared' => count(array_unique($clearedKeys)),
                'sample_keys' => array_slice(array_unique($clearedKeys), 0, 10)
            ]);
        } else {
            Log::info('CacheTraitCrud::clearCrudCache - Basic cache clear completed', [
                'keys_cleared' => $clearedKeys
            ]);
        }
    }

    /**
     * Remember cache for CRUD operations
     *
     * @param string $modelName
     * @param callable $callback
     * @param int|null $page
     * @return mixed
     */
    protected function rememberCrudCache($modelName, callable $callback, $page = null)
    {
        if (!$this->cacheEnabled) {
            return $callback();
        }

        $this->initializeCacheProperties();
        $cacheKey = $this->generateCrudCacheKey($modelName, $page);
        
        // Priority order: controller override -> trait default -> fallback
        $cacheTime = $this->getCacheTime();
        
        Log::debug('CacheTraitCrud::rememberCrudCache - Using cache', [
            'modelName' => $modelName,
            'cacheKey' => $cacheKey,
            'cacheTime' => $cacheTime,
            'source' => $this->getCacheTimeSource()
        ]);

        return Cache::remember($cacheKey, $cacheTime, $callback);
    }

    /**
     * Get cache time with priority: controller override -> trait default -> fallback
     *
     * @return int
     */
    protected function getCacheTime()
    {
        // 1. Controller override (highest priority)
        if (property_exists($this, 'cacheTime') && isset($this->cacheTime)) {
            return $this->cacheTime;
        }
        
        // 2. Trait default (medium priority)
        if (property_exists($this, 'defaultCacheTime') && isset($this->defaultCacheTime)) {
            return $this->defaultCacheTime;
        }
        
        // 3. Fallback (lowest priority)
        return 300; // 5 minutes
    }

    /**
     * Get the source of cache time for debugging purposes
     *
     * @return string
     */
    protected function getCacheTimeSource()
    {
        if (property_exists($this, 'cacheTime') && isset($this->cacheTime)) {
            return 'controller_override';
        }
        
        if (property_exists($this, 'defaultCacheTime') && isset($this->defaultCacheTime)) {
            return 'trait_default';
        }
        
        return 'fallback';
    }

    /**
     * Mark that a significant data change occurred
     */
    protected function markSignificantDataChange()
    {
        $this->significantDataChange = true;
        Log::info('CacheTraitCrud::markSignificantDataChange - Marked significant data change');
    }

    /**
     * Execute a CRUD operation with automatic cache clearing
     *
     * @param string $modelName
     * @param callable $operation
     * @param callable|null $onSuccess
     * @param bool $markSignificant
     * @return mixed
     */
    protected function executeCrudOperation($modelName, callable $operation, callable $onSuccess = null, $markSignificant = true)
    {
        try {
            $result = $operation();
            
            if ($markSignificant) {
                $this->markSignificantDataChange();
            }
            
            $this->clearCrudCache($modelName);
            
            if ($onSuccess) {
                $onSuccess($result);
            }
            
            Log::info('CacheTraitCrud::executeCrudOperation - Operation completed successfully', [
                'modelName' => $modelName,
                'markSignificant' => $markSignificant
            ]);
            
            return $result;
        } catch (\Throwable $e) {
            Log::error('CacheTraitCrud::executeCrudOperation - Operation failed', [
                'modelName' => $modelName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
} 