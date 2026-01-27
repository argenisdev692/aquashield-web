<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

abstract class BaseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return array_merge($this->getBaseAttributes(), $this->getCustomAttributes($request));
    }

    /**
     * Get base attributes common to all resources
     */
    protected function getBaseAttributes(): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'created_at_human' => $this->created_at?->diffForHumans(),
            'updated_at_human' => $this->updated_at?->diffForHumans(),
        ];
    }

    /**
     * Get custom attributes specific to each resource
     */
    abstract protected function getCustomAttributes(Request $request): array;

    /**
     * Get formatted currency value
     */
    protected function formatCurrency(float $value, string $currency = 'USD'): string
    {
        return $currency === 'USD' ? '$' . number_format($value, 2) : number_format($value, 2) . ' ' . $currency;
    }

    /**
     * Get formatted date
     */
    protected function formatDate($date, string $format = 'M d, Y'): ?string
    {
        return $date ? $date->format($format) : null;
    }

    /**
     * Get formatted phone number
     */
    protected function formatPhone(string $phone): string
    {
        // Remove all non-digits
        $cleaned = preg_replace('/\D/', '', $phone);
        
        // Format as (XXX) XXX-XXXX
        if (strlen($cleaned) === 10) {
            return sprintf('(%s) %s-%s', 
                substr($cleaned, 0, 3),
                substr($cleaned, 3, 3),
                substr($cleaned, 6, 4)
            );
        }
        
        return $phone; // Return original if can't format
    }

    /**
     * Get badge class for status
     */
    protected function getStatusBadgeClass(string $status): string
    {
        return match(strtolower($status)) {
            'active', 'approved', 'completed', 'success' => 'badge-success',
            'pending', 'processing', 'warning' => 'badge-warning', 
            'inactive', 'rejected', 'cancelled', 'failed' => 'badge-danger',
            'draft', 'info' => 'badge-info',
            default => 'badge-secondary'
        };
    }

    /**
     * Get priority class for priority levels
     */
    protected function getPriorityClass(string $priority): string
    {
        return match(strtolower($priority)) {
            'urgent', 'critical' => 'text-danger',
            'high' => 'text-warning',
            'medium', 'normal' => 'text-info',
            'low' => 'text-success',
            default => 'text-muted'
        };
    }

    /**
     * Safely get nested attribute
     */
    protected function getSafeAttribute(string $path, $default = null)
    {
        $keys = explode('.', $path);
        $value = $this->resource;
        
        foreach ($keys as $key) {
            if (is_object($value) && isset($value->$key)) {
                $value = $value->$key;
            } elseif (is_array($value) && isset($value[$key])) {
                $value = $value[$key];
            } else {
                return $default;
            }
        }
        
        return $value;
    }
} 