<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class InvoiceDemoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'invoice_number' => $this->invoice_number,
            
            // Invoice header information
            'invoice_date' => $this->invoice_date,
            'invoice_date_formatted' => $this->formatDate($this->invoice_date),
            
            // Bill to information
            'bill_to_name' => $this->bill_to_name,
            'bill_to_address' => $this->bill_to_address,
            'bill_to_phone' => $this->formatPhone($this->bill_to_phone),
            'bill_to_phone_raw' => $this->bill_to_phone,
            
            // Financial information
            'subtotal' => $this->subtotal,
            'subtotal_formatted' => $this->formatCurrency($this->subtotal),
            'tax_amount' => $this->tax_amount,
            'tax_amount_formatted' => $this->formatCurrency($this->tax_amount),
            'balance_due' => $this->balance_due,
            'balance_due_formatted' => $this->formatCurrency($this->balance_due),
            
            // Insurance and claim information
            'claim_number' => $this->claim_number,
            'policy_number' => $this->policy_number,
            'insurance_company' => $this->insurance_company,
            'date_of_loss' => $this->date_of_loss,
            'date_of_loss_formatted' => $this->formatDate($this->date_of_loss),
            'date_received' => $this->date_received,
            'date_received_formatted' => $this->formatDateTime($this->date_received),
            'date_inspected' => $this->date_inspected,
            'date_inspected_formatted' => $this->formatDateTime($this->date_inspected),
            'date_entered' => $this->date_entered,
            'date_entered_formatted' => $this->formatDateTime($this->date_entered),
            
            // Additional fields
            'price_list_code' => $this->price_list_code,
            'type_of_loss' => $this->type_of_loss,
            'notes' => $this->notes,
            
            // Status
            'status' => $this->status,
            'status_label' => $this->getStatusLabel(),
            'status_color' => $this->getStatusColor(),
            
            // Items relationship
            'items' => $this->whenLoaded('items', function () {
                return $this->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'uuid' => $item->uuid,
                        'service_name' => $item->service_name,
                        'description' => $item->description,
                        'quantity' => $item->quantity,
                        'rate' => $item->rate,
                        'rate_formatted' => $this->formatCurrency($item->rate),
                        'amount' => $item->amount,
                        'amount_formatted' => $this->formatCurrency($item->amount),
                        'sort_order' => $item->sort_order
                    ];
                });
            }),
            'items_count' => $this->whenLoaded('items', $this->items->count()),
            
            // User information
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),
            'user_id' => $this->user_id,
            'user_name' => $this->whenLoaded('user', $this->user?->name),
            
            // Timestamps
            'created_at' => $this->created_at,
            'created_at_formatted' => $this->formatDateTime($this->created_at),
            'created_at_human' => $this->created_at?->diffForHumans(),
            'updated_at' => $this->updated_at,
            'updated_at_formatted' => $this->formatDateTime($this->updated_at),
            'updated_at_human' => $this->updated_at?->diffForHumans(),
            
            // Soft Delete Information
            'deleted_at' => $this->deleted_at,
            'deleted_at_formatted' => $this->formatDateTime($this->deleted_at),
            'deleted_at_human' => $this->deleted_at?->diffForHumans(),
            'is_deleted' => !is_null($this->deleted_at),
            
            // Computed Properties
            'days_since_loss' => $this->getDaysSinceLoss(),
            'is_overdue' => $this->isOverdue(),
            'age_in_days' => $this->getAgeInDays(),
            
            // Meta Information
            'meta' => [
                'can_edit' => $this->canEdit(),
                'can_delete' => $this->canDelete(),
                'can_restore' => $this->canRestore(),
                'requires_attention' => $this->requiresAttention(),
                'next_action' => $this->getNextAction(),
                'is_recent' => $this->isRecent()
            ],
        ];
    }

    /**
     * Format phone number for display.
     */
    protected function formatPhone(?string $phone): ?string
    {
        if (!$phone) {
            return null;
        }
        
        // Remove all non-numeric characters
        $cleaned = preg_replace('/[^\d]/', '', $phone);
        
        // Format US phone numbers
        if (strlen($cleaned) === 10) {
            return sprintf('(%s) %s-%s', 
                substr($cleaned, 0, 3),
                substr($cleaned, 3, 3),
                substr($cleaned, 6, 4)
            );
        }
        
        // Format international numbers
        if (strlen($cleaned) === 11 && substr($cleaned, 0, 1) === '1') {
            return sprintf('+1 (%s) %s-%s', 
                substr($cleaned, 1, 3),
                substr($cleaned, 4, 3),
                substr($cleaned, 7, 4)
            );
        }
        
        return $phone; // Return original if can't format
    }

    /**
     * Format currency amount.
     */
    protected function formatCurrency(?float $amount): ?string
    {
        if ($amount === null) {
            return null;
        }
        
        return '$' . number_format($amount, 2);
    }

    /**
     * Format date for display.
     */
    protected function formatDate(?string $date): ?string
    {
        if (!$date) {
            return null;
        }
        
        return Carbon::parse($date)->format('M j, Y');
    }

    /**
     * Format datetime for display.
     */
    protected function formatDateTime(?Carbon $datetime): ?string
    {
        if (!$datetime) {
            return null;
        }
        
        return $datetime->format('M j, Y g:i A');
    }

    /**
     * Get status label.
     */
    protected function getStatusLabel(): string
    {
        $labels = [
            'draft' => 'Draft',
            'sent' => 'Sent',
            'paid' => 'Paid',
            'cancelled' => 'Cancelled',
        ];
        
        return $labels[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Get status color for UI.
     */
    protected function getStatusColor(): string
    {
        $colors = [
            'draft' => 'gray',
            'sent' => 'blue',
            'paid' => 'green',
            'cancelled' => 'red',
        ];
        
        return $colors[$this->status] ?? 'gray';
    }

    /**
     * Get days since loss occurred.
     */
    protected function getDaysSinceLoss(): ?int
    {
        if (!$this->date_of_loss) {
            return null;
        }
        
        return Carbon::parse($this->date_of_loss)->diffInDays(Carbon::now());
    }

    /**
     * Check if invoice is overdue (example: more than 30 days since sent)
     */
    protected function isOverdue(): bool
    {
        if ($this->status !== 'sent') {
            return false;
        }
        
        return $this->updated_at && $this->updated_at->addDays(30)->isPast();
    }

    /**
     * Get age of invoice in days
     */
    protected function getAgeInDays(): int
    {
        return $this->created_at->diffInDays(Carbon::now());
    }

    /**
     * Check if user can edit this invoice.
     */
    protected function canEdit(): bool
    {
        // Basic permission check - can be expanded based on business rules
        return in_array($this->status, ['draft', 'sent']) && 
               is_null($this->deleted_at);
    }

    /**
     * Check if user can delete this invoice.
     */
    protected function canDelete(): bool
    {
        return $this->status !== 'paid' && is_null($this->deleted_at);
    }

    /**
     * Check if user can restore this invoice.
     */
    protected function canRestore(): bool
    {
        return !is_null($this->deleted_at);
    }

    /**
     * Check if invoice requires attention.
     */
    protected function requiresAttention(): bool
    {
        return $this->isOverdue() || 
               ($this->status === 'sent' && $this->getAgeInDays() > 30) ||
               ($this->date_of_loss && $this->getDaysSinceLoss() > 90);
    }

    /**
     * Get next recommended action.
     */
    protected function getNextAction(): ?string
    {
        if ($this->deleted_at) {
            return 'Restore or permanently delete';
        }
        
        switch ($this->status) {
            case 'draft':
                return 'Complete and send invoice';
            case 'sent':
                if ($this->isOverdue()) {
                    return 'Follow up on payment';
                }
                return 'Awaiting payment';
            case 'paid':
                return 'Invoice completed';
            case 'cancelled':
                return 'Invoice cancelled';
            default:
                return null;
        }
    }

    /**
     * Check if invoice is recent (created within last 7 days)
     */
    protected function isRecent(): bool
    {
        return $this->created_at && $this->created_at->isAfter(Carbon::now()->subDays(7));
    }

    /**
     * Get additional data when including relationships.
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'version' => '1.0',
                'generated_at' => now()->toISOString(),
                'timezone' => config('app.timezone'),
            ],
        ];
    }
}