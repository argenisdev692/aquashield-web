<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait UserDataFormatter
{
    /**
     * Format user data before saving to database
     * 
     * @param array $data Raw user data
     * @return array Formatted user data
     */
    protected function formatUserData($data)
    {
        $formatted = [];
        
        // Format name fields with proper capitalization
        if (isset($data['name'])) {
            $formatted['name'] = ucwords(strtolower($data['name']));
        }
        
        if (isset($data['last_name'])) {
            $formatted['last_name'] = ucwords(strtolower($data['last_name']));
        }
        
        // Format address fields
        if (isset($data['address'])) {
            $formatted['address'] = ucwords(strtolower($data['address']));
        }
        
        if (isset($data['city'])) {
            $formatted['city'] = ucwords(strtolower($data['city']));
        }
        
        if (isset($data['state'])) {
            $formatted['state'] = ucwords(strtolower($data['state']));
        }
        
        if (isset($data['country'])) {
            $formatted['country'] = ucwords(strtolower($data['country']));
        }
        
        // Format phone number
        if (isset($data['phone']) && !empty($data['phone'])) {
            $formatted['phone'] = $this->formatPhone($data['phone']);
        }
        
        // Pass through other fields unchanged
        $passthroughFields = [
            'uuid', 'username', 'email', 'date_of_birth', 'password',
            'zip_code', 'gender', 'terms_and_conditions', 'latitude', 'longitude'
        ];
        
        foreach ($passthroughFields as $field) {
            if (isset($data[$field])) {
                $formatted[$field] = $data[$field];
            }
        }
        
        return $formatted;
    }
    
    /**
     * Format phone number to standardized format
     * 
     * @param string $phone Raw phone number
     * @return string|null Formatted phone number or null if empty
     */
    protected function formatPhone($phone)
    {
        if (empty($phone)) {
            return null;
        }
        return '+1' . preg_replace('/[^0-9]/', '', $phone);
    }
    
    /**
     * Format phone number for display
     * 
     * @param string $phone Raw phone number from database
     * @return string Formatted phone number for display
     */
    protected function formatPhoneForDisplay($phone)
    {
        if (empty($phone)) {
            return '';
        }
        
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