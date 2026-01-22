<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Exception;

class LoggerService
{
    /**
     * Log CRUD operations with consistent format
     */
    public function logCrudOperation(string $operation, Model $entity, array $additionalData = []): void
    {
        $baseData = [
            'operation' => $operation,
            'entity_type' => get_class($entity),
            'entity_id' => $entity->id,
            'entity_uuid' => $entity->uuid ?? null,
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString(),
        ];

        Log::info("CRUD Operation: {$operation}", array_merge($baseData, $additionalData));
    }

    /**
     * Log errors with comprehensive context
     */
    public function logError(Exception $exception, string $context, array $additionalData = []): void
    {
        $errorData = [
            'context' => $context,
            'error_message' => $exception->getMessage(),
            'error_code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'stack_trace' => $exception->getTraceAsString(),
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'request_data' => request()->all(),
            'timestamp' => now()->toISOString(),
        ];

        Log::error("Error in {$context}: {$exception->getMessage()}", array_merge($errorData, $additionalData));
    }

    /**
     * Log performance metrics
     */
    public function logPerformance(string $operation, float $executionTime, array $additionalData = []): void
    {
        $performanceData = [
            'operation' => $operation,
            'execution_time_ms' => round($executionTime * 1000, 2),
            'user_id' => auth()->id(),
            'timestamp' => now()->toISOString(),
        ];

        Log::info("Performance: {$operation}", array_merge($performanceData, $additionalData));
    }

    /**
     * Log user actions for audit trail
     */
    public function logUserAction(string $action, array $data = []): void
    {
        $actionData = [
            'action' => $action,
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email ?? null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString(),
        ];

        Log::info("User Action: {$action}", array_merge($actionData, $data));
    }

    /**
     * Log cache operations
     */
    public function logCacheOperation(string $operation, string $key, array $additionalData = []): void
    {
        $cacheData = [
            'cache_operation' => $operation,
            'cache_key' => $key,
            'timestamp' => now()->toISOString(),
        ];

        Log::debug("Cache Operation: {$operation}", array_merge($cacheData, $additionalData));
    }

    /**
     * Log validation failures
     */
    public function logValidationFailure(string $context, array $errors, array $inputData = []): void
    {
        $validationData = [
            'context' => $context,
            'validation_errors' => $errors,
            'input_data' => $this->sanitizeInputData($inputData),
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'timestamp' => now()->toISOString(),
        ];

        Log::warning("Validation Failed: {$context}", $validationData);
    }

    /**
     * Sanitize input data for logging (remove sensitive information)
     */
    private function sanitizeInputData(array $data): array
    {
        $sensitiveFields = ['password', 'password_confirmation', 'token', 'api_key', 'secret'];
        
        foreach ($sensitiveFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = '[REDACTED]';
            }
        }

        return $data;
    }
} 