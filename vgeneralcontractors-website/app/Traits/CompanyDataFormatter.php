<?php

namespace App\Traits;

trait CompanyDataFormatter
{
    /**
     * Format company data before saving
     * 
     * @param array $data
     * @return array
     */
    protected function formatCompanyData($data)
    {
        $formatted = [];
        
        // Format company name
        if (isset($data['company_name'])) {
            $formatted['company_name'] = ucwords(strtolower($data['company_name']));
        }
        
        // Format name
        if (isset($data['name'])) {
            $formatted['name'] = ucwords(strtolower($data['name']));
        }
        
        // Format phone
        if (isset($data['phone'])) {
            $formatted['phone'] = '+1' . preg_replace('/[^0-9]/', '', $data['phone']);
        }
        
        // Format address
        if (isset($data['address'])) {
            $formatted['address'] = strtoupper($data['address']);
        }
        
        // Format website
        if (isset($data['website'])) {
            $website = $data['website'];
            if (!preg_match('/^https?:\/\//i', $website)) {
                $website = 'https://' . $website;
            }
            $formatted['website'] = $website;
        }
        
        // Copy remaining fields
        $otherFields = ['signature_path', 'email', 'latitude', 'longitude', 'user_id', 'uuid'];
        foreach ($otherFields as $field) {
            if (isset($data[$field])) {
                $formatted[$field] = $data[$field];
            }
        }
        
        return $formatted;
    }
    
    /**
     * Format phone number for display in the format (XXX) XXX - XXXX
     * 
     * @param string $phone
     * @return string
     */
    protected function formatPhoneForDisplay($phone)
    {
        $rawPhone = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($rawPhone) >= 10) {
            $rawPhone = substr($rawPhone, -10);
            return sprintf("(%s) %s-%s",
                substr($rawPhone, 0, 3),
                substr($rawPhone, 3, 3),
                substr($rawPhone, 6)
            );
        } 
        return $phone;
    }
}
