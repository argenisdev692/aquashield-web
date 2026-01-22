<?php

namespace App\Helpers;

class PhoneHelper
{
    public static function format($phone)
    {
        // Remove everything except digits
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Format as (XXX) XXX-XXXX
        if (strlen($phone) == 10) {
            return sprintf("(%s) %s-%s",
                substr($phone, 0, 3),
                substr($phone, 3, 3),
                substr($phone, 6, 4)
            );
        } elseif (strlen($phone) == 11) {
            // Remove the first digit (1) and format the rest
            return sprintf("(%s) %s-%s",
                substr($phone, 1, 3),
                substr($phone, 4, 3),
                substr($phone, 7, 4)
            );
        }
        
        return $phone;
    }
} 