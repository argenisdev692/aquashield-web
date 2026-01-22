<?php

namespace App\Traits;

use Illuminate\Validation\Rule;

trait UserValidation
{
    /**
     * Get user validation rules
     * 
     * @param string|null $uuid UUID to ignore for unique validation
     * @param string $modalAction Action being performed (store/update)
     * @return array
     */
    protected function getUserValidationRules($uuid = null, $modalAction = 'store')
    {
        return [
            'name' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u',
            'last_name' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u',
            'username' => ['required', 'string', 'min:7', 'max:255', 'regex:/^.*[0-9].*[0-9].*$/',
                Rule::unique('users', 'username')->ignore($uuid, 'uuid')],
            'date_of_birth' => 'nullable|date',
            'email' => ['required', 'string', 'email', 'max:255', 
                Rule::unique('users', 'email')->ignore($uuid, 'uuid')],
            'password' => $modalAction === 'store' 
                ? 'required|string|min:8|confirmed' 
                : 'nullable|string|min:8|confirmed',
            'phone' => ['nullable', 'string', 'max:20',
                Rule::unique('users', 'phone')->ignore($uuid, 'uuid')],
            'address' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'gender' => 'nullable|string|in:male,female,other',
            'terms_and_conditions' => 'boolean',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'state' => 'nullable|string|max:100',
        ];
    }
    
    /**
     * Get create-specific validation rules
     */
    protected function getCreateValidationRules($uuid = null)
    {
        return [
            'name' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u',
            'last_name' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u',
            'email' => ['required', 'string', 'email', 'max:255', 
                Rule::unique('users', 'email')->ignore($uuid, 'uuid')],
            'phone' => ['nullable', 'string', 'max:20',
                Rule::unique('users', 'phone')->ignore($uuid, 'uuid')],
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'gender' => 'nullable|string|in:male,female,other',
            'terms_and_conditions' => 'boolean',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ];
    }
    
    /**
     * Get update-specific validation rules
     */
    protected function getUpdateValidationRules($uuid = null)
    {
        return [
            'name' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u',
            'last_name' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u',
            'email' => ['required', 'string', 'email', 'max:255', 
                Rule::unique('users', 'email')->ignore($uuid, 'uuid')],
            'username' => ['required', 'string', 'min:7', 'max:255', 'regex:/^.*[0-9].*[0-9].*$/',
                Rule::unique('users', 'username')->ignore($uuid, 'uuid')],
            'date_of_birth' => 'nullable|date',
            'phone' => ['nullable', 'string', 'max:20',
                Rule::unique('users', 'phone')->ignore($uuid, 'uuid')],
            'address' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'gender' => 'nullable|string|in:male,female,other',
            'terms_and_conditions' => 'boolean',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'send_password_reset' => 'sometimes|boolean',
        ];
    }
}