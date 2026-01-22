<?php

namespace App\Traits;

trait PhoneDataFormatter
{
    /**
     * Format phone number for storage (e.g., +11234567890).
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
     * Format phone number for display (e.g., (123) 456-7890).
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