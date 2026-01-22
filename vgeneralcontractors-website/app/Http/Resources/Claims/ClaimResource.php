<?php

namespace App\Http\Resources\Claims;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class ClaimResource extends BaseResource
{
    protected function getCustomAttributes(Request $request): array
    {
        return [
            'claim_number' => $this->claim_number,
            'property_address' => $this->property_address,
            'damage_type' => $this->damage_type,
            'damage_type_display' => ucfirst(str_replace('_', ' ', $this->damage_type)),
            'estimated_cost' => $this->estimated_cost,
            'estimated_cost_formatted' => $this->formatCurrency($this->estimated_cost),
            'insurance_company' => $this->insurance_company,
            'policy_number' => $this->policy_number,
            'status' => $this->status,
            'status_display' => $this->status_display,
            'status_badge_class' => $this->getStatusBadgeClass($this->status),
            'priority' => $this->priority,
            'priority_display' => $this->priority_display,
            'priority_class' => $this->getPriorityClass($this->priority),
            'contact_name' => $this->contact_name,
            'contact_phone' => $this->contact_phone,
            'contact_phone_formatted' => $this->formatPhone($this->contact_phone),
            'contact_email' => $this->contact_email,
            'description' => $this->when($request->include_description, $this->description),
            'scheduled_inspection_date' => $this->formatDate($this->scheduled_inspection_date),
            'scheduled_inspection_date_formatted' => $this->formatDate($this->scheduled_inspection_date, 'M d, Y g:i A'),
            'inspection_notes' => $this->when($request->include_notes, $this->inspection_notes),
            
            // Relationships
            'created_by_name' => $this->getSafeAttribute('createdBy.name', 'Unknown'),
            'updated_by_name' => $this->getSafeAttribute('updatedBy.name', 'Unknown'),
            
            // Actions
            'can_edit' => $this->canEdit(),
            'can_delete' => $this->canDelete(),
            'can_schedule_inspection' => $this->canScheduleInspection(),
            
            // URLs
            'edit_url' => route('claims.edit', $this->uuid),
            'show_url' => route('claims.show', $this->uuid),
        ];
    }

    /**
     * Check if user can edit this claim
     */
    private function canEdit(): bool
    {
        $user = auth()->user();
        
        if (!$user) {
            return false;
        }
        
        // Admin can edit all
        if ($user->hasRole('admin')) {
            return true;
        }
        
        // Creator can edit if status allows
        if ($this->created_by === $user->id) {
            return in_array($this->status, ['pending', 'in_progress']);
        }
        
        return false;
    }

    /**
     * Check if user can delete this claim
     */
    private function canDelete(): bool
    {
        $user = auth()->user();
        
        if (!$user) {
            return false;
        }
        
        // Only admin can delete
        if ($user->hasRole('admin')) {
            return true;
        }
        
        // Creator can delete only if pending and created recently
        if ($this->created_by === $user->id && $this->status === 'pending') {
            return $this->created_at->diffInHours() < 24;
        }
        
        return false;
    }

    /**
     * Check if inspection can be scheduled
     */
    private function canScheduleInspection(): bool
    {
        return in_array($this->status, ['pending', 'in_progress']) && !$this->scheduled_inspection_date;
    }
} 