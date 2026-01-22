<?php

namespace App\Traits;

use Illuminate\Validation\Rule;

trait CompanyValidation
{
    /**
     * Get validation rules for company data
     * 
     * @return array
     */
    protected function getCompanyValidationRules()
    {
        return [
            'company_name' => 'required',
            'name' => 'required|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u',
            'email' => 'required|email',
            'phone' => 'required',
            'address' => 'required',
            'website' => 'required|url',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric'
        ];
    }
    
    /**
     * Get create-specific validation rules
     */
    protected function getCreateValidationRules()
    {
        return [
            'company_name' => 'required|string|max:255',
            'name' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u',
            'email' => ['required', 'string', 'email', 'max:255',
                Rule::unique('company_data', 'email')->ignore($this->uuid ?? null, 'uuid')],
            'phone' => ['required', 'string', 'max:20',
                Rule::unique('company_data', 'phone')->ignore($this->uuid ?? null, 'uuid')],
            'address' => 'required|string|max:255',
            'website' => 'required|url|max:255',
        ];
    }
    
    /**
     * Get update-specific validation rules
     */
    protected function getUpdateValidationRules()
    {
        return [
            'company_name' => 'required|string|max:255',
            'name' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u',
            'email' => ['required', 'string', 'email', 'max:255',
                Rule::unique('company_data', 'email')->ignore($this->uuid ?? null, 'uuid')],
            'phone' => ['required', 'string', 'max:20',
                Rule::unique('company_data', 'phone')->ignore($this->uuid ?? null, 'uuid')],
            'address' => 'required|string|max:255',
            'website' => 'required|url|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ];
    }
    
    /**
     * Check if an email already exists in the database
     * 
     * @param string $email
     * @return bool
     */
    public function checkEmailExists($email)
    {
        if (empty($email)) {
            return false;
        }

        // If we're in update mode and the email hasn't changed, it's valid
        if ($this->companyId) {
            $company = \App\Models\CompanyData::find($this->companyId);
            if ($company && $company->email === $email) {
                return false;
            }
        }

        // Check if email exists for any other company
        return \App\Models\CompanyData::where('email', $email)
            ->when($this->companyId, function ($query) {
                return $query->where('id', '!=', $this->companyId);
            })
            ->exists();
    }
    
    /**
     * Check if a phone already exists in the database
     * 
     * @param string $phone
     * @return bool
     */
    public function checkPhoneExists($phone)
    {
        if (empty($phone)) {
            return false;
        }

        // Format phone for comparison
        $formattedPhone = '+1' . preg_replace('/[^0-9]/', '', $phone);

        // If we're in update mode and the phone hasn't changed, it's valid
        if ($this->companyId) {
            $company = \App\Models\CompanyData::find($this->companyId);
            if ($company && $company->phone === $formattedPhone) {
                return false;
            }
        }

        // Check if phone exists for any other company
        return \App\Models\CompanyData::where('phone', $formattedPhone)
            ->when($this->companyId, function ($query) {
                return $query->where('id', '!=', $this->companyId);
            })
            ->exists();
    }
}
