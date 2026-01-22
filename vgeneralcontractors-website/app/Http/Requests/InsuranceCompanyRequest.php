<?php

namespace App\Http\Requests;

use App\Http\DTOs\InsuranceCompanyDTO;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Validation\Validator;

class InsuranceCompanyRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $uuid = $this->route('insurance_company');
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        return [
            // Company name - required field
            'insurance_company_name' => [
                $isUpdate ? 'sometimes' : 'required',
                'string',
                'min:3',
                'max:255',
                'regex:/^[\p{L}\p{N}\s\.\-\&\']+$/u',
                ...$this->getAntiSpamRules(),
                Rule::unique('insurance_companies', 'insurance_company_name')
                    ->ignore($uuid, 'uuid')
                    ->whereNull('deleted_at'),
            ],

            // Contact information
            'phone' => $this->getPhoneRules(required: false, international: true),
            'email' => [
                ...$this->getEmailRules(required: false, maxLength: 255),
                Rule::unique('insurance_companies', 'email')
                    ->ignore($uuid, 'uuid')
                    ->whereNull('deleted_at'),
            ],
            'website' => $this->getWebsiteRules(required: false),

            // Address information
            'address' => $this->getAddressRules(minLength: 10, maxLength: 500, required: false),

            // User assignment (automatically set)
            'user_id' => ['sometimes', 'integer', 'exists:users,id'],

            // Status
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            ...parent::getCommonMessages(),
            
            // Company name messages
            'insurance_company_name.required' => __('insurance_companies.validation.name_required'),
            'insurance_company_name.unique' => __('insurance_companies.validation.name_unique'),
            'insurance_company_name.min' => __('insurance_companies.validation.name_min'),
            'insurance_company_name.max' => __('insurance_companies.validation.name_max'),
            'insurance_company_name.regex' => __('insurance_companies.validation.name_format'),
            
            // Contact messages
            'phone.regex' => __('insurance_companies.validation.phone_format'),
            'email.unique' => __('insurance_companies.validation.email_unique'),
            'website.regex' => __('insurance_companies.validation.website_format'),
            
            // Address messages
            'address.min' => __('insurance_companies.validation.address_min'),
            
            // User messages
            'user_id.exists' => __('insurance_companies.validation.user_not_found'),
        ];
    }

    /**
     * Get custom attribute names.
     */
    public function attributes(): array
    {
        return [
            'insurance_company_name' => __('insurance_companies.fields.name'),
            'phone' => __('insurance_companies.fields.phone'),
            'email' => __('insurance_companies.fields.email'),
            'website' => __('insurance_companies.fields.website'),
            'address' => __('insurance_companies.fields.address'),
            'user_id' => __('insurance_companies.fields.assigned_user'),
            'is_active' => __('insurance_companies.fields.status'),
        ];
    }

    /**
     * Prepare data for validation - auto-set user_id and format inputs.
     */
    protected function prepareForValidation(): void
    {
        $this->logValidationStart();
        
        parent::prepareForValidation();

        $input = $this->all();

        // Auto-set user_id from authenticated user
        $input['user_id'] = auth()->id();

        // Format company name
        if (isset($input['insurance_company_name'])) {
            $input['insurance_company_name'] = trim($input['insurance_company_name']);
        }

        $this->merge($input);
        
        $this->logValidationPrepared($input);
    }

    /**
     * Handle validation failure with detailed logging
     */
    protected function failedValidation(Validator $validator): never
    {
        $this->logValidationFailure($validator);
        
        parent::failedValidation($validator);
    }

    /**
     * Log validation start with request context
     */
    private function logValidationStart(): void
    {
        Log::info('InsuranceCompanyRequest validation started', [
            'request_id' => request()->header('X-Request-ID', str()->uuid()),
            'method' => $this->method(),
            'url' => $this->url(),
            'route_name' => request()->route()?->getName(),
            'route_params' => request()->route()?->parameters(),
            'user_id' => auth()->id(),
            'user_email' => auth()->user()?->email,
            'ip_address' => $this->ip(),
            'user_agent' => $this->userAgent(),
            'timestamp' => now()->toISOString(),
            'raw_input_keys' => array_keys($this->all()),
            'input_count' => count($this->all()),
        ]);
    }

    /**
     * Log prepared validation data
     */
    private function logValidationPrepared(array $input): void
    {
        // Mask sensitive data for logging
        $logData = $this->maskSensitiveData($input);
        
        Log::debug('InsuranceCompanyRequest data prepared for validation', [
            'prepared_data' => $logData,
            'data_keys' => array_keys($input),
            'has_phone' => isset($input['phone']),
            'has_email' => isset($input['email']),
            'has_website' => isset($input['website']),
            'phone_format' => isset($input['phone']) ? $this->analyzePhoneFormat($input['phone']) : null,
            'email_format' => isset($input['email']) ? $this->analyzeEmailFormat($input['email']) : null,
        ]);
    }

    /**
     * Log detailed validation failure information
     */
    private function logValidationFailure(Validator $validator): void
    {
        $errors = $validator->errors()->toArray();
        $failedRules = $validator->failed();
        
        Log::error('InsuranceCompanyRequest validation FAILED', [
            'request_id' => request()->header('X-Request-ID', str()->uuid()),
            'validation_errors' => $errors,
            'failed_rules' => $failedRules,
            'error_count' => $validator->errors()->count(),
            'failed_fields' => array_keys($errors),
            'input_data' => $this->maskSensitiveData($this->all()),
            'route_params' => request()->route()?->parameters(),
            'is_update' => $this->isMethod('PUT') || $this->isMethod('PATCH'),
            'uuid_from_route' => $this->route('insurance_company'),
            'user_context' => [
                'user_id' => auth()->id(),
                'user_email' => auth()->user()?->email,
                'user_roles' => auth()->user()?->getRoleNames() ?? [],
            ],
            'request_context' => [
                'method' => $this->method(),
                'url' => $this->url(),
                'ip' => $this->ip(),
                'user_agent' => $this->userAgent(),
                'referer' => $this->header('referer'),
            ],
            'phone_analysis' => $this->input('phone') ? $this->analyzePhoneFormat($this->input('phone')) : null,
            'email_analysis' => $this->input('email') ? $this->analyzeEmailFormat($this->input('email')) : null,
            'timestamp' => now()->toISOString(),
        ]);

        // Log specific field failures for troubleshooting
        foreach ($errors as $field => $fieldErrors) {
            Log::warning("Field '{$field}' validation failed", [
                'field' => $field,
                'errors' => $fieldErrors,
                'value' => $this->maskFieldValue($field, $this->input($field)),
                'failed_rules' => $failedRules[$field] ?? [],
                'field_type' => $this->getFieldType($field),
            ]);
        }
    }

    /**
     * Mask sensitive data for logging
     */
    private function maskSensitiveData(array $data): array
    {
        $masked = $data;
        
        // Mask email partially
        if (isset($masked['email']) && $masked['email']) {
            $email = $masked['email'];
            $atPos = strpos($email, '@');
            if ($atPos !== false) {
                $masked['email'] = substr($email, 0, 2) . '***' . substr($email, $atPos);
            }
        }
        
        // Mask phone partially
        if (isset($masked['phone']) && $masked['phone']) {
            $phone = $masked['phone'];
            if (strlen($phone) > 4) {
                $masked['phone'] = substr($phone, 0, 3) . '***' . substr($phone, -4);
            }
        }
        
        return $masked;
    }

    /**
     * Mask individual field values
     */
    private function maskFieldValue(string $field, $value): string
    {
        if (is_null($value)) return 'null';
        
        return match($field) {
            'email' => $this->maskEmail((string)$value),
            'phone' => $this->maskPhone((string)$value),
            default => (string)$value
        };
    }

    /**
     * Analyze phone format for debugging
     */
    private function analyzePhoneFormat(?string $phone): ?array
    {
        if (!$phone) return null;
        
        return [
            'original' => $phone,
            'length' => strlen($phone),
            'digits_only' => preg_replace('/\D/', '', $phone),
            'digits_count' => strlen(preg_replace('/\D/', '', $phone)),
            'has_parentheses' => str_contains($phone, '(') && str_contains($phone, ')'),
            'has_dashes' => str_contains($phone, '-'),
            'has_spaces' => str_contains($phone, ' '),
            'matches_us_format' => preg_match('/^\(\d{3}\)\s\d{3}-\d{4}$/', $phone),
            'matches_international' => preg_match('/^\+?[1-9]\d{1,14}$/', preg_replace('/\D/', '', $phone)),
        ];
    }

    /**
     * Analyze email format for debugging
     */
    private function analyzeEmailFormat(?string $email): ?array
    {
        if (!$email) return null;
        
        return [
            'original' => $this->maskEmail($email),
            'length' => strlen($email),
            'has_at' => str_contains($email, '@'),
            'has_dot' => str_contains($email, '.'),
            'domain' => substr(strstr($email, '@'), 1) ?: null,
            'local_part_length' => strpos($email, '@') ?: 0,
            'is_valid_format' => filter_var($email, FILTER_VALIDATE_EMAIL) !== false,
        ];
    }

    /**
     * Get field type for logging
     */
    private function getFieldType(string $field): string
    {
        return match($field) {
            'insurance_company_name' => 'company_name',
            'phone' => 'phone_number',
            'email' => 'email_address',
            'website' => 'url',
            'address' => 'text_area',
            'user_id' => 'integer',
            'is_active' => 'boolean',
            default => 'string'
        };
    }

    /**
     * Mask email for logging
     */
    private function maskEmail(string $email): string
    {
        $atPos = strpos($email, '@');
        if ($atPos === false || $atPos < 2) return '***';
        
        return substr($email, 0, 2) . '***' . substr($email, $atPos);
    }

    /**
     * Mask phone for logging
     */
    private function maskPhone(string $phone): string
    {
        if (strlen($phone) < 4) return '***';
        
        return substr($phone, 0, 3) . '***' . substr($phone, -4);
    }

    /**
     * Convert validated request to DTO - CLEAN DATA TRANSFER ONLY.
     */
    public function toDTO(): InsuranceCompanyDTO
    {
        $this->logValidationSuccess();
        
        $validatedData = $this->validatedSafe();
        
        // Add UUID for updates
        if ($uuid = $this->route('insurance_company')) {
            $validatedData['uuid'] = $uuid;
        }

        $dto = InsuranceCompanyDTO::from($validatedData);
        
        $this->logDTOCreation($dto);
        
        return $dto;
    }

    /**
     * Log successful validation
     */
    private function logValidationSuccess(): void
    {
        Log::info('InsuranceCompanyRequest validation SUCCESS', [
            'request_id' => request()->header('X-Request-ID', str()->uuid()),
            'validated_fields' => array_keys($this->validatedSafe()),
            'is_update' => $this->isMethod('PUT') || $this->isMethod('PATCH'),
            'uuid_from_route' => $this->route('insurance_company'),
            'user_id' => auth()->id(),
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Log DTO creation
     */
    private function logDTOCreation(InsuranceCompanyDTO $dto): void
    {
        Log::debug('InsuranceCompanyDTO created successfully', [
            'dto_uuid' => $dto->getIdentifier(),
            'dto_name' => $dto->insurance_company_name,
            'is_new_record' => $dto->isNew(),
            'has_phone' => !empty($dto->phone),
            'has_email' => !empty($dto->email),
            'has_website' => !empty($dto->website),
            'is_active' => $dto->is_active,
        ]);
    }
}