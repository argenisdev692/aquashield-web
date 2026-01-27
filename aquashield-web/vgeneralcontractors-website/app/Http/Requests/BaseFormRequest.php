<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;

abstract class BaseFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Override in child classes if needed
    }

    /**
     * Handle a failed validation attempt with structured logging
     */
    protected function failedValidation(Validator $validator): never
    {
        Log::warning('Validation failed', [
            'request_class' => static::class,
            'method' => $this->method(),
            'url' => $this->url(),
            'ip' => $this->ip(),
            'user_agent' => $this->userAgent(),
            'errors' => $validator->errors()->toArray(),
            'input' => $this->safe()->except(['password', 'password_confirmation']),
            'user_id' => auth()->id(),
        ]);

        if ($this->wantsJson() || $this->ajax()) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => __('Validation errors occurred.'),
                    'errors' => $validator->errors(),
                    'meta' => [
                        'timestamp' => now()->toISOString(),
                        'request_id' => request()->header('X-Request-ID', str()->uuid()),
                    ]
                ], 422)
            );
        }

        parent::failedValidation($validator);
    }

    /**
     * Get common validation rules with PHP 8.4 enhancements
     */
    protected function getCommonRules(): array
    {
        return [
            'uuid' => ['sometimes', 'uuid'],
            'created_at' => ['sometimes', 'date'],
            'updated_at' => ['sometimes', 'date'],
        ];
    }

    /**
     * Enhanced anti-spam validation rules - disabled in local/testing environments
     */
    protected function getAntiSpamRules(): array
    {
        // Disable anti-spam rules in local/testing environments
        if (app()->environment(['local', 'testing'])) {
            return [];
        }

        return [
            'not_regex:/(?i)test|example|fake|asdf|qwerty|dummy|lorem|ipsum|placeholder/',
            'not_in:test,example,fake,dummy,placeholder',
        ];
    }

    /**
     * Modern phone validation rules with international support
     */
    protected function getPhoneRules(bool $required = true, bool $international = true): array
    {
        $rules = [];
        
        if ($required) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }

        if ($international) {
            $rules[] = 'regex:/^(\(\d{3}\)\s\d{3}-\d{4}|\+?[1-9]\d{1,14})$/';
        } else {
            $rules[] = 'regex:/^\(\d{3}\)\s\d{3}-\d{4}$/';
        }

        $rules[] = 'not_regex:/\(000\)|\(111\)|\(123\)|555-0000|000-0000/';
        
        return $rules;
    }

    /**
     * Enhanced email validation rules
     */
    protected function getEmailRules(bool $required = true, int $maxLength = 255): array
    {
        $rules = [];
        
        if ($required) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }

        $rules = [
            ...$rules,
            'email:rfc,dns',
            "max:{$maxLength}",
            'not_regex:/(?i)test@|example@|fake@|sample@|temp@|noreply@|admin@localhost/',
        ];
        
        return $rules;
    }

    /**
     * Enhanced name validation rules with Unicode support
     */
    protected function getNameRules(int $minLength = 2, int $maxLength = 100, bool $required = true): array
    {
        $rules = [];
        
        if ($required) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }

        return [
            ...$rules,
            'string',
            "min:{$minLength}",
            "max:{$maxLength}",
            'regex:/^[\p{L}\s\'-\.]+$/u', // Unicode letters, spaces, hyphens, apostrophes, dots
            'not_regex:/(?i)test|example|fake|asdf|lorem|ipsum/',
        ];
    }

    /**
     * Enhanced address validation rules
     */
    protected function getAddressRules(int $minLength = 5, int $maxLength = 500, bool $required = true): array
    {
        $rules = [];
        
        if ($required) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }

        return [
            ...$rules,
            'string',
            "min:{$minLength}",
            "max:{$maxLength}",
            'regex:/^[\p{L}\p{N}\s\.,#\/-]+$/u', // Unicode letters, numbers, spaces, common address chars
            'not_regex:/(?i)test|example|fake|123 main|456 elm|lorem|ipsum/',
        ];
    }

    /**
     * Modern password validation rules
     */
    protected function getPasswordRules(bool $required = true): array
    {
        $rules = [];
        
        if ($required) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }

        return [
            ...$rules,
            'string',
            Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised(),
        ];
    }

    /**
     * Website/URL validation rules
     */
    protected function getWebsiteRules(bool $required = false): array
    {
        $rules = [];
        
        if ($required) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }

        return [
            ...$rules,
            'string',
            'max:255',
            'regex:/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/',
            'not_regex:/(?i)example\.com|test\.com|localhost|127\.0\.0\.1/',
        ];
    }

    /**
     * Enhanced common validation messages with localization
     */
    protected function getCommonMessages(): array
    {
        return [
            'not_regex' => __('validation.custom.not_test_data'),
            'not_in' => __('validation.custom.not_test_data'),
            'regex' => __('validation.regex'),
            'required' => __('validation.required'),
            'email' => __('validation.email'),
            'uuid' => __('validation.uuid'),
            'min' => __('validation.min.string'),
            'max' => __('validation.max.string'),
            'string' => __('validation.string'),
            'integer' => __('validation.integer'),
            'boolean' => __('validation.boolean'),
            'date' => __('validation.date'),
            'exists' => __('validation.exists'),
            'unique' => __('validation.unique'),
        ];
    }

    /**
     * Get sanitized and validated data
     */
    public function validatedSafe(): array
    {
        return $this->safe()->all();
    }

    /**
     * Apply common transformations to input data
     */
    protected function prepareForValidation(): void
    {
        $input = $this->all();

        // Trim all string inputs
        $trimmed = array_map(function ($value) {
            return is_string($value) ? trim($value) : $value;
        }, $input);

        // Remove empty strings and convert to null for nullable fields
        $cleaned = array_map(function ($value) {
            return $value === '' ? null : $value;
        }, $trimmed);

        $this->merge($cleaned);
    }

    /**
     * Convert to DTO after validation
     */
    abstract public function toDTO(): object;
}