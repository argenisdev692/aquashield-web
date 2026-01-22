<?php 
namespace App\Traits;

use Illuminate\Validation\Rule;

trait EmailDataValidation
{
    /**
     * Validation rules for creating a new email data.
     *
     * @return array
     */
    protected function getCreateValidationRules()
    {
        return [
            'description' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('email_data', 'email')],
            'phone' => ['nullable', 'string', 'max:20', Rule::unique('email_data', 'phone')],
            'type' => ['required', 'string', 'max:50'],
            'user_id' => ['required', 'exists:users,id'],
        ];
    }

    /**
     * Validation rules for updating an existing email data.
     *
     * @return array
     */
    protected function getUpdateValidationRules()
    {
        return [
            'description' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('email_data', 'email')->ignore($this->uuid, 'uuid')],
            'phone' => ['nullable', 'string', 'max:20', Rule::unique('email_data', 'phone')->ignore($this->uuid, 'uuid')],
            'type' => ['required', 'string', 'max:50'],
            'user_id' => ['required', 'exists:users,id'],
        ];
    }
}