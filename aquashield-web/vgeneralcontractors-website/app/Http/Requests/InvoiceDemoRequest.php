<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
class InvoiceDemoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization is handled by the controller
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        Log::error('InvoiceDemoRequest validation failed', [
            'errors' => $validator->errors()->toArray(),
            'input' => $this->except(['password', 'password_confirmation']),
            'url' => $this->url(),
            'method' => $this->method(),
            'user_agent' => $this->userAgent(),
            'ip' => $this->ip()
        ]);

        if ($this->wantsJson() || $this->ajax()) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => 'Validation errors occurred.',
                    'errors' => $validator->errors()
                ], 422)
            );
        }

        parent::failedValidation($validator);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // Invoice header information
            'invoice_number' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Z0-9\-]+$/',
                'unique:invoice_demos,invoice_number' // Use the simpler string format for unique rule
            ],
            'invoice_date' => [
                'required',
                'date',
                'before_or_equal:today'
            ],
            
            // Bill to information
            'bill_to_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z\s\-\'\.]+$/'
            ],
            'bill_to_address' => [
                'required',
                'string',
                'max:1000'
            ],
            'bill_to_phone' => [
                'required',
                'string',
                'max:20',
                'regex:/^\(\d{3}\)\s\d{3}-\d{4}$|^[\+]?[1-9]?[0-9]{7,15}$/'
            ],
            
            // Financial information
            'subtotal' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99'
            ],
            'tax_amount' => [
                'nullable',
                'numeric',
                'min:0',
                'max:99999.99'
            ],
            'balance_due' => [
                'required',
                'numeric',
                'min:0',
                'max:999999.99'
            ],
            
            // Insurance and claim information - All mandatory except specified optional fields
            'claim_number' => [
                'required',
                'string',
                'max:255'
            ],
            'policy_number' => [
                'required',
                'string',
                'max:255'
            ],
            'insurance_company' => [
                'required',
                'string',
                'max:255'
            ],
            'date_of_loss' => [
                'required',
                'date',
                'before_or_equal:today'
            ],
            'type_of_loss' => [
                'required',
                'string',
                'max:255'
            ],
            // Optional fields in Insurance & Claim Information
            'date_received' => [
                'nullable',
                'date',
                'after_or_equal:date_of_loss'
            ],
            'date_inspected' => [
                'nullable',
                'date',
                'after_or_equal:date_of_loss'
            ],
            'date_entered' => [
                'nullable',
                'date',
                'after_or_equal:date_of_loss'
            ],
            'price_list_code' => [
                'nullable',
                'string',
                'max:50'
            ],
            'notes' => [
                'nullable',
                'string',
                'max:2000'
            ],
            
            // Status
            'status' => [
                'required',
                'string',
                Rule::in(['draft', 'sent', 'paid', 'cancelled', 'print_pdf'])
            ],
            
            // Invoice items (mandatory section)
            'items' => [
                'required',
                'array',
                'min:1'
            ],
            'items.*.service_name' => [
                'required_with:items',
                'string',
                'max:255'
            ],
            'items.*.description' => [
                'required_with:items',
                'string',
                'max:1000'
            ],
            'items.*.quantity' => [
                'required_with:items',
                'integer',
                'min:1'
            ],
            'items.*.rate' => [
                'required_with:items',
                'numeric',
                'min:0'
            ],
            'items.*.sort_order' => [
                'nullable',
                'integer',
                'min:0'
            ]
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            // Invoice header messages
            'invoice_number.required' => 'The invoice number is required.',
            'invoice_number.unique' => 'This invoice number already exists.',
            'invoice_number.regex' => 'The invoice number format is invalid. Use only letters, numbers, and hyphens.',
            'invoice_number.max' => 'The invoice number cannot exceed 50 characters.',
            
            'invoice_date.required' => 'The invoice date is required.',
            'invoice_date.date' => 'Please enter a valid date.',
            'invoice_date.before_or_equal' => 'The invoice date cannot be in the future.',
            
            // Bill to information messages
            'bill_to_name.required' => 'The bill to name is required.',
            'bill_to_name.max' => 'The bill to name cannot exceed 255 characters.',
            'bill_to_name.regex' => 'The bill to name contains invalid characters.',
            
            'bill_to_address.required' => 'The bill to address is required.',
            'bill_to_address.max' => 'The bill to address cannot exceed 1000 characters.',
            
            'bill_to_phone.required' => 'The bill to phone number is required.',
            'bill_to_phone.regex' => 'Please enter a valid phone number.',
            'bill_to_phone.max' => 'The phone number cannot exceed 20 characters.',
            
            // Financial information messages
            'subtotal.numeric' => 'The subtotal must be a valid number.',
            'subtotal.min' => 'The subtotal cannot be negative.',
            'subtotal.max' => 'The subtotal cannot exceed $999,999.99.',
            
            'tax_amount.numeric' => 'The tax amount must be a valid number.',
            'tax_amount.min' => 'The tax amount cannot be negative.',
            'tax_amount.max' => 'The tax amount cannot exceed $99,999.99.',
            
            'balance_due.required' => 'The balance due is required.',
            'balance_due.numeric' => 'The balance due must be a valid number.',
            'balance_due.min' => 'The balance due cannot be negative.',
            'balance_due.max' => 'The balance due cannot exceed $999,999.99.',
            
            // Insurance and claim messages
            'claim_number.required' => 'The claim number is required.',
            'claim_number.max' => 'The claim number cannot exceed 255 characters.',
            'policy_number.required' => 'The policy number is required.',
            'policy_number.max' => 'The policy number cannot exceed 255 characters.',
            'insurance_company.required' => 'The insurance company is required.',
            'insurance_company.max' => 'The insurance company name cannot exceed 255 characters.',
            
            'date_of_loss.required' => 'The date of loss is required.',
            'type_of_loss.required' => 'The type of loss is required.',
            'type_of_loss.max' => 'The type of loss cannot exceed 255 characters.',
            
            'date_of_loss.date' => 'Please enter a valid date of loss.',
            'date_of_loss.before_or_equal' => 'The date of loss cannot be in the future.',
            
            'date_received.date' => 'Please enter a valid date received.',
            'date_received.after_or_equal' => 'The date received must be on or after the date of loss.',
            
            'date_inspected.date' => 'Please enter a valid date inspected.',
            'date_inspected.after_or_equal' => 'The date inspected must be on or after the date of loss.',
            
            'date_entered.date' => 'Please enter a valid date entered.',
            'date_entered.after_or_equal' => 'The date entered must be on or after the date of loss.',
            
            // Additional fields messages
            'price_list_code.max' => 'The price list code cannot exceed 50 characters.',
            'notes.max' => 'The notes cannot exceed 2000 characters.',
            
            // Status messages
            'status.required' => 'Please select a status.',
            'status.in' => 'The selected status is invalid.',
            
            // Items validation messages
            'items.required' => 'At least one invoice item is required.',
            'items.array' => 'Items must be an array.',
            'items.min' => 'At least one invoice item is required.',
            'items.*.service_name.required_with' => 'Service name is required for each item.',
            'items.*.service_name.max' => 'Service name cannot exceed 255 characters.',
            'items.*.description.required_with' => 'Description is required for each item.',
            'items.*.description.max' => 'Description cannot exceed 1000 characters.',
            'items.*.quantity.required_with' => 'Quantity is required for each item.',
            'items.*.quantity.integer' => 'Quantity must be a whole number.',
            'items.*.quantity.min' => 'Quantity must be at least 1.',
            'items.*.rate.required_with' => 'Rate is required for each item.',
            'items.*.rate.numeric' => 'Rate must be a valid number.',
            'items.*.rate.min' => 'Rate cannot be negative.',
            'items.*.sort_order.integer' => 'Sort order must be a whole number.',
            'items.*.sort_order.min' => 'Sort order cannot be negative.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'invoice_number' => 'invoice number',
            'invoice_date' => 'invoice date',
            'bill_to_name' => 'bill to name',
            'bill_to_address' => 'bill to address',
            'bill_to_phone' => 'bill to phone',
            'subtotal' => 'subtotal',
            'tax_amount' => 'tax amount',
            'balance_due' => 'balance due',
            'claim_number' => 'claim number',
            'policy_number' => 'policy number',
            'insurance_company' => 'insurance company',
            'date_of_loss' => 'date of loss',
            'date_received' => 'date received',
            'date_inspected' => 'date inspected',
            'date_entered' => 'date entered',
            'price_list_code' => 'price list code',
            'type_of_loss' => 'type of loss',
            'notes' => 'notes',
            'status' => 'status'
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Custom validation logic
            $this->validateDateLogic($validator);
            $this->validateFinancialData($validator);
            $this->validateBusinessRules($validator);
        });
    }

    /**
     * Validate date logic.
     */
    protected function validateDateLogic($validator): void
    {
        $dateOfLoss = $this->date('date_of_loss');
        $dateReceived = $this->date('date_received');
        $dateInspected = $this->date('date_inspected');
        $dateEntered = $this->date('date_entered');
        
        // Validate date relationships
        if ($dateOfLoss) {
            if ($dateReceived && $dateReceived->lt($dateOfLoss)) {
                $validator->errors()->add('date_received', 'Date received must be on or after the date of loss.');
            }
            
            if ($dateInspected && $dateInspected->lt($dateOfLoss)) {
                $validator->errors()->add('date_inspected', 'Date inspected must be on or after the date of loss.');
            }
            
            if ($dateEntered && $dateEntered->lt($dateOfLoss)) {
                $validator->errors()->add('date_entered', 'Date entered must be on or after the date of loss.');
            }
        }
    }

    /**
     * Validate financial data consistency.
     */
    protected function validateFinancialData($validator): void
    {
        $subtotal = (float) $this->input('subtotal', 0);
        $taxAmount = (float) $this->input('tax_amount', 0);
        $balanceDue = (float) $this->input('balance_due', 0);
        
        // Only validate balance calculation if we have items (subtotal > 0)
        if ($subtotal > 0) {
            $expectedBalance = $subtotal + $taxAmount;
            if (abs($balanceDue - $expectedBalance) > 0.01) { // Allow small rounding differences
                $validator->errors()->add(
                    'balance_due',
                    'Balance due must equal subtotal plus tax amount.'
                );
            }
        }
    }

    /**
     * Validate business rules.
     */
    protected function validateBusinessRules($validator): void
    {
        // Validate status transitions for updates
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $this->validateStatusTransition($validator);
        }
        
        // Validate invoice number format
        $invoiceNumber = $this->input('invoice_number');
        if ($invoiceNumber && !preg_match('/^VG-\d{4}$/', $invoiceNumber)) {
            $validator->errors()->add(
                'invoice_number',
                'Invoice number must follow VG-XXXX format (e.g., VG-0001).'
            );
        }
    }

    /**
     * Validate status transitions for updates.
     */
    protected function validateStatusTransition($validator): void
    {
        $currentStatus = $this->route('invoice_demo')?->status ?? null;
        $newStatus = $this->input('status');
        
        if ($currentStatus && $newStatus) {
            $invalidTransitions = [
                'paid' => ['draft', 'sent'], // Can't go back from paid
                'cancelled' => ['paid'] // Can't pay after cancellation
            ];
            
            if (isset($invalidTransitions[$currentStatus]) && 
                in_array($newStatus, $invalidTransitions[$currentStatus])) {
                $validator->errors()->add(
                    'status',
                    "Cannot change status from {$currentStatus} to {$newStatus}."
                );
            }
        }
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean and format phone number
        if ($this->has('bill_to_phone')) {
            // Check if the phone is already in the format (xxx) xxx-xxxx
            if (preg_match('/^\(\d{3}\)\s\d{3}-\d{4}$/', $this->bill_to_phone)) {
                // Keep the formatted phone number as is
            } else {
                // For other formats, clean to just digits
                $phone = preg_replace('/[^\d+]/', '', $this->bill_to_phone);
                $this->merge(['bill_to_phone' => $phone ?: null]);
            }
        }
        
        // Clean and format invoice number
        if ($this->has('invoice_number')) {
            $invoiceNumber = strtoupper(trim($this->invoice_number));
            $this->merge(['invoice_number' => $invoiceNumber]);
        }
        
        // Convert empty strings to null for optional fields
        $optionalFields = ['date_received', 'date_inspected', 'date_entered', 'price_list_code', 'notes'];
        foreach ($optionalFields as $field) {
            if ($this->has($field) && trim($this->input($field)) === '') {
                $this->merge([$field => null]);
            }
        }
        
        // Ensure financial amounts are properly formatted
        $financialFields = ['subtotal', 'tax_amount', 'balance_due'];
        foreach ($financialFields as $field) {
            if ($this->has($field) && $this->input($field) !== null) {
                $amount = (float) $this->input($field);
                $this->merge([$field => number_format($amount, 2, '.', '')]);
            }
        }
        
        // Set default status if not provided
        if (!$this->has('status') || trim($this->input('status')) === '') {
            $this->merge(['status' => 'draft']);
        }
    }
}