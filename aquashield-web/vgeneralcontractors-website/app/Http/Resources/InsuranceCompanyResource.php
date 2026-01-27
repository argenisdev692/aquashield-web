<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class InsuranceCompanyResource extends BaseResource
{
    /**
     * Get custom attributes specific to InsuranceCompany resource
     */
    protected function getCustomAttributes(Request $request): array
    {
        return [
            'insurance_company_name' => $this->insurance_company_name,
            'address' => $this->address,
            'phone' => $this->formatPhone($this->phone ?? ''),
            'phone_raw' => $this->phone,
            'email' => $this->email,
            'website' => $this->website,
            'website_display' => $this->getWebsiteDisplay(),
            'user_id' => $this->user_id,
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),
            'user_name' => $this->user?->name ?? 'No user assigned',
            'user_email' => $this->user?->email ?? '',
            'deleted_at' => $this->deleted_at?->format('Y-m-d H:i:s'),
            'deleted_at_human' => $this->deleted_at?->diffForHumans(),
            'is_deleted' => !is_null($this->deleted_at),
            'status' => $this->getStatusInfo(),
            'actions' => $this->getActionButtons(),
        ];
    }

    /**
     * Get website display format
     */
    private function getWebsiteDisplay(): ?string
    {
        if (!$this->website) {
            return null;
        }

        // Remove protocol for display
        $display = preg_replace('/^https?:\/\//', '', $this->website);
        return $display;
    }

    /**
     * Get status information
     */
    private function getStatusInfo(): array
    {
        $isDeleted = !is_null($this->deleted_at);
        
        return [
            'is_active' => !$isDeleted,
            'is_deleted' => $isDeleted,
            'status_text' => $isDeleted ? 'Inactive' : 'Active',
            'status_class' => $isDeleted ? 'badge-danger' : 'badge-success',
            'status_badge' => $isDeleted 
                ? '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">Inactive</span>'
                : '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Active</span>'
        ];
    }

    /**
     * Get action buttons HTML
     */
    private function getActionButtons(): string
    {
        $isDeleted = !is_null($this->deleted_at);
        $uuid = $this->uuid;
        
        $buttons = '';
        
        // Edit button (always available)
        $buttons .= '<button data-id="' . $uuid . '" class="edit-btn inline-flex items-center justify-center w-9 h-9 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-md hover:shadow-lg mr-2" title="Edit Insurance Company">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </button>';
        
        if ($isDeleted) {
            // Restore button
            $buttons .= '<button data-id="' . $uuid . '" class="restore-btn inline-flex items-center justify-center w-9 h-9 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-lg hover:from-emerald-600 hover:to-emerald-700 transition-all duration-200 shadow-md hover:shadow-lg" title="Restore Insurance Company">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </button>';
        } else {
            // Delete button
            $buttons .= '<button data-id="' . $uuid . '" class="delete-btn inline-flex items-center justify-center w-9 h-9 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg hover:from-red-600 hover:to-red-700 transition-all duration-200 shadow-md hover:shadow-lg" title="Delete Insurance Company">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>';
        }
        
        return $buttons;
    }
} 