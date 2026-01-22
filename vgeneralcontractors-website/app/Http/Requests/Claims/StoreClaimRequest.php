<?php

namespace App\Http\Requests\Claims;

use App\Http\Requests\BaseFormRequest;

class StoreClaimRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return array_merge($this->getCommonRules(), [
            'property_address' => array_merge($this->getAddressRules(), [
                'max:255'
            ]),
            'damage_type' => [
                'required',
                'string',
                'in:hail,wind,water,fire,storm,flood,other'
            ],
            'estimated_cost' => [
                'required',
                'numeric',
                'min:0',
                'max:9999999.99'
            ],
            'insurance_company' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'not_regex:/test|example|fake/i'
            ],
            'policy_number' => [
                'required',
                'string',
                'min:3',
                'max:50',
                'regex:/^[A-Z0-9\-]+$/i'
            ],
            'description' => [
                'nullable',
                'string',
                'max:2000',
                'not_regex:/test|example|fake|lorem ipsum/i'
            ],
            'priority' => [
                'required',
                'in:low,medium,high,urgent'
            ],
            'contact_name' => $this->getNameRules(2, 100),
            'contact_phone' => $this->getPhoneRules(),
            'contact_email' => $this->getEmailRules(),
            'scheduled_inspection_date' => [
                'nullable',
                'date',
                'after:today'
            ],
            'inspection_notes' => [
                'nullable',
                'string',
                'max:1000'
            ]
        ]);
    }

    public function messages(): array
    {
        return array_merge($this->getCommonMessages(), [
            'property_address.not_regex' => 'Please provide a real property address.',
            'damage_type.in' => 'Please select a valid damage type.',
            'estimated_cost.numeric' => 'Estimated cost must be a valid number.',
            'estimated_cost.max' => 'Estimated cost cannot exceed $9,999,999.99.',
            'policy_number.regex' => 'Policy number can only contain letters, numbers, and hyphens.',
            'priority.in' => 'Priority must be low, medium, high, or urgent.',
            'scheduled_inspection_date.after' => 'Inspection date must be in the future.',
            'contact_phone.regex' => 'Phone number must be in format (XXX) XXX-XXXX.',
        ]);
    }

    /**
     * Get validated data for claim creation
     */
    public function getClaimData(): array
    {
        return $this->only([
            'property_address',
            'damage_type',
            'estimated_cost',
            'insurance_company',
            'policy_number',
            'description',
            'priority',
            'contact_name',
            'contact_phone',
            'contact_email',
            'scheduled_inspection_date',
            'inspection_notes'
        ]);
    }
} 