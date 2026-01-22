<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class FacebookLeadFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Allow anyone to submit this form
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            // 'first_name' => 'required|min:2|alpha',
            'first_name' => ['required', 'min:2', 'regex:/^[A-Za-z\'-]+$/', 'not_regex:/test|asdf|qwerty|fake/i'], // Block common test names
            // 'last_name' => 'required|min:2|alpha',
            'last_name' => ['required', 'min:2', 'regex:/^[A-Za-z\'-]+$/', 'not_regex:/test|asdf|qwerty|fake/i'], // Block common test names
            // Note: The phone regex might need adjustment if the input mask is handled purely client-side now.
            // If the JS sends raw digits, the regex needs to change.
            // If the JS sends the masked format, this regex is correct.
            'phone' => ['required', 'regex:/^\(\d{3}\)\s\d{3}-\d{4}$/', 'not_regex:/\(123\)|^(555)|0000$/'], // Block obvious test phones
            'email' => ['required', 'email', 'not_regex:/test@|example@|fake@|sample@|temp@/i'], // Block test emails
            'address_map_input' => ['required', 'min:5', 'not_regex:/test|example|fake|asdf/i'], // Block test addresses
            'address' => ['required', 'min:5', 'not_regex:/test|example|fake|asdf/i'], // Block test addresses
            'address_2' => 'nullable',
            'city' => ['required', 'not_regex:/test|example|fake/i'], // Block test cities
            'state' => 'required',
           
            'zipcode' => 'required|digits:5', // Enforce exactly 5 digits
            'country' => 'required', // Consider validating against a list or keeping it simple
            'insurance_property' => 'required|in:yes,no',
            'message' => ['nullable', 'min:5', 'not_regex:/test|do not|don\'t reply|ignore|asdf|dummy/i'], // Block obvious test messages
            'sms_consent' => 'nullable|boolean', // Use nullable and boolean
            'latitude' => 'nullable|numeric|between:-90,90', // Add latitude validation
            'longitude' => 'nullable|numeric|between:-180,180', // Add longitude validation
            'lead_source' => 'nullable|string', // Add lead_source validation as nullable string
            'status_lead' => 'nullable|string|in:New,Called,Pending,Declined', // Add status_lead field validation
            'inspection_date' => 'nullable|date|required_with:inspection_time',
            'inspection_time' => 'nullable|date_format:H:i|required_with:inspection_date',
            // Make reCAPTCHA field nullable instead of required - we'll validate it in the controller
            'g-recaptcha-response' => 'nullable',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'first_name.required' => __('first_name_required'),
            'first_name.min' => __('validation.min.string', ['attribute' => __('first_name'), 'min' => 2]),
            'first_name.regex' => __('validation.regex', ['attribute' => __('first_name')]),
            'first_name.not_regex' => __('validation.not_regex', ['attribute' => __('first_name')]),
            'last_name.required' => __('last_name_required'),
            'last_name.min' => __('validation.min.string', ['attribute' => __('last_name'), 'min' => 2]),
            'last_name.regex' => __('validation.regex', ['attribute' => __('last_name')]),
            'last_name.not_regex' => __('validation.not_regex', ['attribute' => __('last_name')]),
            'phone.required' => __('phone_required'),
            'phone.regex' => __('validation.regex', ['attribute' => __('phone')]),
            'phone.not_regex' => __('validation.not_regex', ['attribute' => __('phone')]),
            'email.required' => __('email_required'),
            'email.email' => __('validation.email', ['attribute' => __('email')]),
            'email.not_regex' => __('validation.not_regex', ['attribute' => __('email')]),
            'zipcode.required' => __('zipcode_required'),
            'zipcode.digits' => __('validation.digits', ['attribute' => __('zipcode'), 'digits' => 5]),
            'address_map_input.required' => __('address_required'),
            'address_map_input.min' => __('validation.min.string', ['attribute' => __('address'), 'min' => 5]),
            'address_map_input.not_regex' => __('validation.not_regex', ['attribute' => __('address')]),
            'address.required' => __('address_required'),
            'address.min' => __('validation.min.string', ['attribute' => __('address'), 'min' => 5]),
            'address.not_regex' => __('validation.not_regex', ['attribute' => __('address')]),
            'city.required' => __('city_required'),
            'city.not_regex' => __('validation.not_regex', ['attribute' => __('city')]),
            'state.required' => __('state_required'),
            'country.required' => __('country_required'),
            'insurance_property.required' => __('insurance_property_required'),
            'insurance_property.in' => __('validation.in', ['attribute' => __('insurance_property')]),
            'message.min' => __('validation.min.string', ['attribute' => __('message'), 'min' => 5]),
            'message.not_regex' => __('validation.not_regex', ['attribute' => __('message')]),
            'latitude.numeric' => __('validation.numeric', ['attribute' => __('latitude')]),
            'latitude.between' => __('validation.between.numeric', ['attribute' => __('latitude'), 'min' => -90, 'max' => 90]),
            'longitude.numeric' => __('validation.numeric', ['attribute' => __('longitude')]),
            'longitude.between' => __('validation.between.numeric', ['attribute' => __('longitude'), 'min' => -180, 'max' => 180]),
            'g-recaptcha-response.required' => __('captcha_required'),
            'inspection_date.date' => __('validation.date', ['attribute' => __('inspection_date')]),
            'inspection_date.required_with' => __('validation.required_with', ['attribute' => __('inspection_date'), 'values' => __('inspection_time')]),
            'inspection_time.date_format' => __('validation.date_format', ['attribute' => __('inspection_time'), 'format' => 'H:i']),
            'inspection_time.required_with' => __('validation.required_with', ['attribute' => __('inspection_time'), 'values' => __('inspection_date')]),
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        // Since this is specifically for an AJAX request, always return JSON
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422) // Use 422 Unprocessable Entity status code
        );
    }
}
