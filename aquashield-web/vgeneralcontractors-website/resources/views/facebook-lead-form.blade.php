@extends('layouts.lead-form') {{-- Use the new dedicated layout --}}
@php use App\Helpers\PhoneHelper; @endphp

@section('title', 'Get Your Free Inspection') {{-- Title is set in layout, but can override if needed --}}

@section('content')
    <div class="container mx-auto px-4 py-12 min-h-screen">
        <div class="max-w-3xl mx-auto bg-white p-6 md:p-8 shadow-lg rounded-lg">

            {{-- Centered Logo --}}
            <div class="flex justify-center mb-6">
                <a href="{{ route('home') }}" target="_blank" title="Visit Main Website">
                    <img src="{{ asset('assets/logo/logo3.webp') }}" alt="V General Contractors Logo" class="h-12 md:h-16">
                </a>
            </div>

            {{-- Header --}}
            <div class="px-4 py-3 mb-4 bg-yellow-500 sm:px-6 rounded-lg">
                <h3 class="text-xl font-bold leading-6 text-center text-white">Get Your Free Inspection</h3>
            </div>

            <p class="mt-6 mb-4 text-sm text-gray-600 text-center">
                Fill out the form below and our team will contact you shortly to schedule your free inspection.
            </p>

            {{-- Success Message Placeholder --}}
            <div id="success-message" class="hidden p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
            </div>
            {{-- General Error Message Placeholder --}}
            <div id="general-error-message" class="hidden p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg"
                role="alert">
            </div>

            {{-- Form --}}
            <form id="facebook-lead-form" action="{{ secure_url(route('facebook.lead.store', [], false)) }}" method="POST"
                class="space-y-4" novalidate>
                @csrf

                {{-- Set form start time in session --}}
                @php
                    session(['form_start_time' => time()]);
                @endphp

                {{-- Hidden Inputs for Coordinates --}}
                <input type="hidden" name="latitude" id="latitude">
                <input type="hidden" name="longitude" id="longitude">
                {{-- Hidden Input for reCAPTCHA v3 Token --}}
                <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">

                <div class="grid grid-cols-1 gap-x-4 gap-y-2 sm:grid-cols-2">
                    {{-- First Name --}}
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700">First Name <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="first_name" name="first_name"
                            class="input-field block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm"
                            autocomplete="given-name" maxlength="50" required>
                        <span class="error-message text-xs text-red-500 mt-1 block h-4" data-field="first_name"></span>
                    </div>

                    {{-- Last Name --}}
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="last_name" name="last_name"
                            class="input-field block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm"
                            autocomplete="family-name" maxlength="50" required>
                        <span class="error-message text-xs text-red-500 mt-1 block h-4" data-field="last_name"></span>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-x-4 gap-y-2 sm:grid-cols-2">
                    {{-- Phone --}}
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone <span
                                class="text-red-500">*</span></label>
                        <input type="tel" id="phone" name="phone" placeholder="(XXX) XXX-XXXX"
                            class="input-field block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm"
                            autocomplete="tel" required maxlength="14">
                        <span class="error-message text-xs text-red-500 mt-1 block h-4" data-field="phone"></span>
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email <span
                                class="text-red-500">*</span></label>
                        <input type="email" id="email" name="email"
                            class="input-field block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm"
                            autocomplete="email" required>
                        <span class="error-message text-xs text-red-500 mt-1 block h-4" data-field="email"></span>
                    </div>
                </div>

                {{-- Address Map Input Field (visible) --}}
                <div>
                    <label for="address_map_input" class="block text-sm font-medium text-gray-700">Address <span
                            class="text-red-500">*</span></label>
                    <input type="text" id="address_map_input" name="address_map_input"
                        placeholder="Enter your complete address"
                        class="input-field block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm"
                        autocomplete="off" required>
                    <span class="error-message text-xs text-red-500 mt-1 block h-4" data-field="address_map_input"></span>
                </div>

                {{-- Hidden Address Fields --}}
                <input type="hidden" id="address" name="address">
                <input type="hidden" id="city" name="city">
                <input type="hidden" id="state" name="state">
                <input type="hidden" id="zipcode" name="zipcode">
                <input type="hidden" id="country" name="country" value="USA">

                {{-- Address 2 --}}
                <div>
                    <label for="address_2" class="block text-sm font-medium text-gray-700">Address 2 <span
                            class="text-xs text-gray-500">(Optional, e.g., Apt, Suite, Unit)</span></label>
                    <input type="text" id="address_2" name="address_2" placeholder="Apt #, Suite #, etc."
                        class="input-field block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm"
                        autocomplete="address-line2">
                    <span class="error-message text-xs text-red-500 mt-1 block h-4" data-field="address_2"></span>
                </div>

                {{-- Map Display --}}
                <div class="mt-4 mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Location Map</label>
                    <div id="location-map" class="w-full h-48 bg-gray-200 rounded-lg border border-gray-300">
                        <!-- Map will be initialized here -->
                    </div>
                </div>

                {{-- Comment/Message --}}
                <div>
                    <label for="message" class="block mt-8 text-sm font-medium text-gray-700">Comment or Message <span
                            class="text-xs text-gray-500">(Optional)</span></label>
                    <textarea id="message" name="message" rows="4"
                        class="input-field block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm"></textarea>
                    <span class="error-message text-xs text-red-500 mt-1 block h-4" data-field="message"></span>
                </div>

                {{-- Property Insurance --}}
                <div class="mt-6 text-center md:text-center sm:text-center">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Do you have property insurance? <span
                            class="text-red-500">*</span></label>
                    <fieldset class="mt-2">
                        <legend class="sr-only">Property Insurance</legend>
                        <div class="flex items-center justify-center space-x-4">
                            <div class="radio-option flex items-center">
                                <input id="insurance_yes" name="insurance_property" type="radio" value="yes"
                                    class="radio-field sr-only" required>
                                <label for="insurance_yes"
                                    class="insurance-label flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md cursor-pointer text-sm w-20">
                                    Yes
                                </label>
                            </div>
                            <div class="radio-option flex items-center">
                                <input id="insurance_no" name="insurance_property" type="radio" value="no"
                                    class="radio-field sr-only" required>
                                <label for="insurance_no"
                                    class="insurance-label flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md cursor-pointer text-sm w-20">
                                    No
                                </label>
                            </div>
                        </div>
                    </fieldset>
                    <span class="error-message text-xs text-red-500 mt-1 block h-4"
                        data-field="insurance_property"></span>
                </div>

                {{-- SMS Consent --}}
                <div class="mt-6">
                    <label class="inline-flex items-start cursor-pointer">
                        <input id="sms_consent" name="sms_consent" type="checkbox" value="1"
                            class="checkbox-field form-checkbox text-yellow-500 mt-1 h-5 w-5 border-gray-300 rounded focus:ring-yellow-500">
                        <span class="ml-2 text-sm text-gray-600">
                            {!! __('sms_consent_text') !!}
                            <strong>{{ \App\Helpers\PhoneHelper::format($companyData->phone) }}</strong>
                            <a href="{{ route('privacy-policy') }}" target="_blank"
                                class="text-yellow-500 font-bold hover:text-yellow-600 no-underline">{{ __('privacy_policy') }}</a>
                            {{ __('and') }} <a href="{{ route('terms-and-conditions') }}" target="_blank"
                                class="text-yellow-500 font-bold hover:text-yellow-600 no-underline">{{ __('terms_of_service') }}</a>.
                        </span>
                    </label>
                    {{-- Error message outside the label --}}
                    <span class="error-message text-xs text-red-500 mt-1 block h-4" data-field="sms_consent"></span>
                </div>

                {{-- Submit Button --}}
                <div class="pt-4 text-center">
                    <button type="submit" id="submit-button" {{-- Classes mostly from x-primary-button --}}
                        class="group relative overflow-hidden bg-yellow-500 hover:bg-yellow-600 focus:ring-4 focus:ring-yellow-300 inline-flex items-center px-7 py-2.5 rounded-lg text-white justify-center disabled:opacity-75 disabled:cursor-not-allowed w-full sm:w-auto"
                        disabled>

                        {{-- Spinner (hidden initially, shown during loading) --}}
                        <svg id="submit-spinner" class="hidden z-40 animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>

                        {{-- Button Text --}}
                        <span id="submit-button-text" class="z-40">Send Request</span>

                        {{-- Background Animation Div from x-primary-button --}}
                        <div
                            class="absolute inset-0 h-[200%] w-[200%] rotate-45 translate-x-[-70%] transition-all group-hover:scale-100 bg-white/30 group-hover:translate-x-[50%] z-20 duration-1000">
                        </div>
                    </button>
                </div>
            </form>

            {{-- Visit Website Link --}}
            <div class="text-center mt-8">
                <a href="{{ route('home') }}" target="_blank"
                    class="text-sm text-gray-600 hover:text-yellow-600 underline transition duration-150 ease-in-out">
                    Visit Full Website
                </a>
            </div>

        </div>
    </div>

@endsection

@push('scripts')
    {{-- Add reCAPTCHA v3 script --}}
    <script>
        // Global variables for reCAPTCHA
        window.recaptchaSiteKey = '{{ $recaptchaSiteKey ?? '' }}'; // Use ?? '' as fallback
        window.recaptchaLoaded = false;

        // Function to execute when reCAPTCHA API is loaded
        function onRecaptchaLoad() {
            console.log('reCAPTCHA API loaded');
            window.recaptchaLoaded = true;
        }

        // Function to get reCAPTCHA token safely
        function executeRecaptcha(action) {
            console.log('[executeRecaptcha] Called with action:', action);
            return new Promise((resolve, reject) => {
                if (!window.recaptchaLoaded || typeof grecaptcha === 'undefined') {
                    console.error('[executeRecaptcha] Error: reCAPTCHA API not loaded');

                    // Intentar cargar reCAPTCHA y reintentar
                    const script = document.createElement('script');
                    script.src = 'https://www.google.com/recaptcha/api.js?render={{ $recaptchaSiteKey }}';
                    script.async = true;
                    script.defer = true;
                    script.onload = function() {
                        console.log('[executeRecaptcha] reCAPTCHA script loaded, retrying...');
                        window.recaptchaLoaded = true;
                        setTimeout(() => {
                            executeRecaptcha(action).then(resolve).catch(reject);
                        }, 1000);
                    };
                    document.head.appendChild(script);
                    return;
                }

                try {
                    grecaptcha.ready(function() {
                        console.log('[executeRecaptcha] grecaptcha.ready callback fired.');
                        console.log('[executeRecaptcha] Attempting to execute with key:', window
                            .recaptchaSiteKey);

                        // Asegurarse de que haya un pequeño retraso antes de ejecutar
                        setTimeout(() => {
                            grecaptcha.execute('{{ $recaptchaSiteKey }}', {
                                    action: action
                                })
                                .then(token => {
                                    console.log('[executeRecaptcha] Token received:', token ?
                                        'success (length: ' + token.length + ')' :
                                        'null/undefined');

                                    if (!token) {
                                        console.error(
                                            '[executeRecaptcha] Received empty token');
                                        reject(new Error('Empty reCAPTCHA token'));
                                        return;
                                    }

                                    document.getElementById('g-recaptcha-response').value =
                                        token;
                                    resolve(token);
                                })
                                .catch(error => {
                                    console.error(
                                        '[executeRecaptcha] grecaptcha.execute() failed:',
                                        error);
                                    reject(error);
                                });
                        }, 500);
                    });
                } catch (error) {
                    console.error('[executeRecaptcha] Error during ready/execute:', error);
                    reject(error);
                }
            });
        }
    </script>
    <script src="https://www.google.com/recaptcha/api.js?render={{ $recaptchaSiteKey }}&onload=onRecaptchaLoad" async
        defer></script>
    <!-- SweetAlert2 for alerts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('facebook-lead-form');
            const submitButton = document.getElementById('submit-button');
            const submitSpinner = document.getElementById('submit-spinner');
            const submitButtonText = document.getElementById('submit-button-text');
            const successMessageDiv = document.getElementById('success-message');
            const generalErrorDiv = document.getElementById('general-error-message');
            const csrfToken = document.querySelector('input[name="_token"]')?.value;
            const allInputs = form.querySelectorAll('.input-field, .radio-field, .checkbox-field');
            const firstNameInput = document.getElementById('first_name');
            const lastNameInput = document.getElementById('last_name');
            const phoneInput = document.getElementById('phone');
            const requiredInputs = form.querySelectorAll('[required]'); // Seleccionar campos requeridos
            let successTimeoutId = null; // Variable para el timeout de éxito
            let errorTimeoutId = null; // Variable para el timeout de error
            let map; // Google map instance
            let marker; // Map marker

            // Setup radio button styling for insurance options
            const insuranceOptions = document.querySelectorAll('input[name="insurance_property"]');
            insuranceOptions.forEach(option => {
                option.addEventListener('change', function() {
                    // Remove selected class from all labels
                    document.querySelectorAll('.insurance-label').forEach(label => {
                        label.classList.remove('selected');
                    });

                    // Add selected class to the clicked option's label
                    if (this.checked) {
                        const label = document.querySelector(`label[for="${this.id}"]`);
                        if (label) {
                            label.classList.add('selected');
                        }
                    }

                    // Validate the field
                    validateField(this);
                });
            });

            // --- Google Maps Initialization ---
            function initializeMap() {
                // Default location (United States center)
                const defaultLocation = {
                    lat: 37.0902,
                    lng: -95.7129
                };

                // Create map
                map = new google.maps.Map(document.getElementById('location-map'), {
                    center: defaultLocation,
                    zoom: 4,
                    mapTypeControl: false,
                    streetViewControl: false,
                    fullscreenControl: false,
                    zoomControl: true
                });

                // Create marker (initially hidden)
                marker = new google.maps.Marker({
                    map: map,
                    visible: false
                });
            }

            // --- Google Maps Address Autocomplete ---
            function initAutocomplete() {
                const addressMapInput = document.getElementById('address_map_input');
                if (!addressMapInput) return;

                // Initialize map
                if (typeof google !== 'undefined' && google.maps) {
                    initializeMap();
                }

                const autocomplete = new google.maps.places.Autocomplete(addressMapInput, {
                    types: ['address'],
                    componentRestrictions: {
                        country: 'us'
                    }, // Restrict to US addresses
                    fields: ['address_components', 'geometry', 'formatted_address']
                });

                // When a place is selected
                autocomplete.addListener('place_changed', function() {
                    const place = autocomplete.getPlace();

                    if (!place.geometry) {
                        console.log("No details available for this place");
                        return;
                    }

                    // Get coordinates
                    const lat = place.geometry.location.lat();
                    const lng = place.geometry.location.lng();

                    // Set coordinates in hidden fields
                    document.getElementById('latitude').value = lat;
                    document.getElementById('longitude').value = lng;

                    // Update map with selected location
                    if (map && marker) {
                        map.setCenter({
                            lat,
                            lng
                        });
                        map.setZoom(16);
                        marker.setPosition({
                            lat,
                            lng
                        });
                        marker.setVisible(true);
                    }

                    // Fill address components
                    let addressLine1 = '';
                    let city = '';
                    let state = '';
                    let zipcode = '';

                    for (const component of place.address_components) {
                        const componentType = component.types[0];

                        switch (componentType) {
                            case 'street_number':
                                addressLine1 = component.long_name;
                                break;
                            case 'route':
                                addressLine1 = addressLine1 ?
                                    addressLine1 + ' ' + component.long_name :
                                    component.long_name;
                                break;
                            case 'locality':
                                city = component.long_name;
                                break;
                            case 'administrative_area_level_1':
                                state = component.short_name;
                                break;
                            case 'postal_code':
                                zipcode = component.long_name;
                                break;
                        }
                    }

                    // Use formatted_address if available
                    if (place.formatted_address) {
                        addressMapInput.value = place.formatted_address;
                    }

                    // Fill in hidden form fields
                    if (addressLine1) document.getElementById('address').value = addressLine1;
                    if (city) document.getElementById('city').value = city;
                    if (state) document.getElementById('state').value = state;
                    if (zipcode) document.getElementById('zipcode').value = zipcode;
                    document.getElementById('country').value = 'USA';

                    // Validate the address field after populating
                    validateField(addressMapInput);

                    // Copy value to hidden fields for validation
                    ['address', 'city', 'state', 'zipcode'].forEach(fieldId => {
                        const hiddenField = document.getElementById(fieldId);
                        if (hiddenField) {
                            // Create a custom event to trigger change detection
                            const event = new Event('change', {
                                bubbles: true
                            });
                            hiddenField.dispatchEvent(event);
                        }
                    });
                });
            }

            // Initialize autocomplete when Google Maps API is loaded
            if (typeof google !== 'undefined' && google.maps && google.maps.places) {
                initAutocomplete();
            } else {
                // If Google Maps API is not available yet, wait for it
                window.initAutocomplete = initAutocomplete;
            }

            // --- Debounce Function ---
            function debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            };

            // --- Helper Functions ---
            function clearFieldError(fieldElement) {
                const fieldName = fieldElement.name;
                const errorSpan = form.querySelector(`.error-message[data-field="${fieldName}"]`);
                if (errorSpan) errorSpan.textContent = '';
                fieldElement.classList.remove('border-red-500');
                if (fieldElement.type === 'radio') {
                    document.querySelectorAll(`input[name="${fieldName}"]`).forEach(radio => radio.classList.remove(
                        'border-red-500'));
                }
                checkFormValidity();
            }

            function clearAllErrors() {
                document.querySelectorAll('.error-message').forEach(span => span.textContent = '');
                allInputs.forEach(input => input.classList.remove('border-red-500'));

                // Clear timeouts and hide messages if errors are cleared explicitly
                clearTimeout(successTimeoutId);
                successMessageDiv.classList.add('hidden');
                successMessageDiv.textContent = '';

                clearTimeout(errorTimeoutId);
                generalErrorDiv.classList.add('hidden');
                generalErrorDiv.textContent = '';

                checkFormValidity();
            }

            function displayErrors(errors) {
                clearAllErrors();
                let firstErrorField = null;
                for (const field in errors) {
                    const errorSpan = form.querySelector(`.error-message[data-field="${field}"]`);
                    const inputElement = form.querySelector(`[name="${field}"]`);

                    if (errorSpan && errors[field]?.[0]) {
                        errorSpan.textContent = errors[field][0];
                    }
                    if (inputElement) {
                        inputElement.classList.add('border-red-500');
                        if (!firstErrorField) firstErrorField = inputElement;
                    }
                }
                if (firstErrorField) {
                    firstErrorField.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
                submitButton.disabled = true;
            }

            function showSuccessMessage(message) {
                // Clear previous errors/timeouts first
                clearAllErrors();
                clearTimeout(successTimeoutId); // Clear previous success timeout

                successMessageDiv.textContent = message;
                successMessageDiv.classList.remove('hidden');
                form.reset();
                successMessageDiv.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                submitButton.disabled = true;

                // Set timeout to hide success message
                successTimeoutId = setTimeout(() => {
                    successMessageDiv.classList.add('hidden');
                    successMessageDiv.textContent = '';
                }, 5000); // 5000ms = 5 seconds
            }

            function showGeneralError(message) {
                // Clear previous success/error timeouts but not field errors
                clearTimeout(successTimeoutId);
                successMessageDiv.classList.add('hidden');
                successMessageDiv.textContent = '';
                clearTimeout(errorTimeoutId); // Clear previous error timeout

                generalErrorDiv.textContent = message || 'An unexpected error occurred.';
                generalErrorDiv.classList.remove('hidden');
                generalErrorDiv.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                submitButton.disabled = true;

                // Set timeout to hide general error message
                errorTimeoutId = setTimeout(() => {
                    generalErrorDiv.classList.add('hidden');
                    generalErrorDiv.textContent = '';
                }, 5000); // 5000ms = 5 seconds
            }

            function setLoadingState(isLoading) {
                submitButton.disabled = isLoading;
                if (isLoading) {
                    submitSpinner.classList.remove('hidden');
                    submitButtonText.textContent = 'Sending...';
                    submitButton.setAttribute('aria-busy', 'true');
                    submitButton.setAttribute('aria-label', 'Sending');
                } else {
                    submitSpinner.classList.add('hidden');
                    submitButtonText.textContent = 'Send Request';
                    submitButton.removeAttribute('aria-busy');
                    submitButton.removeAttribute('aria-label');
                    checkFormValidity();
                }
            }

            function formatName(inputElement) {
                // Store cursor position
                const cursorPosition = inputElement.selectionStart;
                let value = inputElement.value;
                
                if (typeof value === 'string' && value.length > 0) {
                    // Limit to 50 characters
                    if (value.length > 50) {
                        value = value.substring(0, 50);
                    }
                    
                    // Check if value ends with space to preserve it
                    const endsWithSpace = value.endsWith(' ');
                    
                    // Replace multiple spaces with single space
                    value = value.replace(/\s+/g, ' ');
                    
                    // Split by spaces and filter out empty parts
                    let parts = value.trim().split(' ').filter(part => part.length > 0);
                    
                    // Capitalize each word
                    parts = parts.map(part => {
                        return part.charAt(0).toUpperCase() + part.slice(1).toLowerCase();
                    });
                    
                    // Join parts with single space
                    let formattedValue = parts.join(' ');
                    
                    // Preserve trailing space if original had it and we're under 50 chars
                    if (endsWithSpace && formattedValue.length < 50) {
                        formattedValue += ' ';
                    }
                    
                    inputElement.value = formattedValue;
                    
                    // Restore cursor position
                    const newCursorPosition = Math.min(cursorPosition, formattedValue.length);
                    inputElement.setSelectionRange(newCursorPosition, newCursorPosition);
                }
            }

            function formatPhoneInput(inputElement, event) {
                const isBackspace = event?.inputType === 'deleteContentBackward';
                let value = inputElement.value.replace(/\D/g, '');

                if (isBackspace) {
                    // value = value.substring(0, value.length);
                } else {
                    value = value.substring(0, 10);
                }

                let formattedValue = '';
                if (value.length === 0) {
                    formattedValue = '';
                } else if (value.length <= 3) {
                    formattedValue = `(${value}`;
                } else if (value.length <= 6) {
                    formattedValue = `(${value.substring(0, 3)}) ${value.substring(3)}`;
                } else {
                    formattedValue = `(${value.substring(0, 3)}) ${value.substring(3, 6)}-${value.substring(6)}`;
                }
                inputElement.value = formattedValue;
                validateField(inputElement); // Validate after format
            }

            // --- Check Form Validity Function ---
            function checkFormValidity() {
                let allRequiredFilled = true;
                requiredInputs.forEach(input => {
                    let value = input.value.trim();
                    if (input.type === 'radio') {
                        // Check if any radio button in the group is checked
                        const groupName = input.name;
                        if (!form.querySelector(`input[name="${groupName}"]:checked`)) {
                            allRequiredFilled = false;
                        }
                    } else if (!value) {
                        allRequiredFilled = false;
                    }
                });

                // Check if any error messages are currently displayed
                const hasVisibleErrors = Array.from(form.querySelectorAll('.error-message'))
                    .some(span => span.textContent.trim() !== '');

                // Enable button only if all required fields are filled AND there are no errors
                submitButton.disabled = !allRequiredFilled || hasVisibleErrors;
            }

            // --- Real-time Field Validation Function ---
            function validateField(fieldElement) {
                const fieldName = fieldElement.name;
                let fieldValue = fieldElement.type === 'checkbox' ? (fieldElement.checked ? 1 : 0) :
                    fieldElement.value;

                if (fieldElement.type === 'radio') {
                    const checkedRadio = form.querySelector(`input[name="${fieldName}"]:checked`);
                    if (!checkedRadio) {
                        // If required, error will show on submit. Clear blur error if any.
                        clearFieldError(fieldElement);
                        return;
                    }
                    fieldValue = checkedRadio.value;
                }

                // Special handling for address_map_input
                if (fieldName === 'address_map_input') {
                    // If address_map_input is filled but hidden fields are not,
                    // copy the value to the address field
                    const addressValue = document.getElementById('address').value;
                    if (fieldValue && !addressValue) {
                        document.getElementById('address').value = fieldValue;
                    }
                }

                fetch('{{ secure_url(route('facebook.lead.validate', [], false)) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            fieldName: fieldName,
                            fieldValue: fieldValue
                        })
                    })
                    .then(response => {
                        if (!response.ok && response.status !== 422) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        const errorSpan = form.querySelector(`.error-message[data-field="${fieldName}"]`);
                        if (errorSpan) {
                            if (!data.valid && data.errors?.[0]) {
                                errorSpan.textContent = data.errors[0];
                                fieldElement.classList.add('border-red-500');

                                // Si es un error de email duplicado, mostrar información adicional
                                if (fieldName === 'email' && data.duplicate_email) {
                                    // Opcionalmente mostrar un tooltip o mensaje flotante
                                    if (typeof Swal !== 'undefined') {
                                        Swal.fire({
                                            title: '{{ __('swal_email_already_registered') }}',
                        html: '{{ __('swal_email_in_system') }}' +
                                                'Please contact our support team or call us at <strong>(713) 587-6423</strong> to schedule your appointment.',
                                            icon: 'info',
                                            confirmButtonText: 'OK',
                                            confirmButtonColor: '#f59e0b'
                                        });
                                    }
                                }
                            } else {
                                errorSpan.textContent = '';
                                fieldElement.classList.remove('border-red-500');
                            }
                        }
                        checkFormValidity();
                    })
                    .catch(error => {
                        console.error('Validation request failed:', error);
                        checkFormValidity();
                    });
            }

            // Debounced version of the validation function
            const debouncedValidateField = debounce(validateField, 500);

            // --- Event Listeners ---
            if (firstNameInput) {
                firstNameInput.addEventListener('input', (event) => {
                    formatName(event.target);
                    debouncedValidateField(event.target);
                });
                firstNameInput.addEventListener('blur', (event) => {
                    formatName(event.target);
                    validateField(event.target);
                });
            }
            if (lastNameInput) {
                lastNameInput.addEventListener('input', (event) => {
                    formatName(event.target);
                    debouncedValidateField(event.target);
                });
                lastNameInput.addEventListener('blur', (event) => {
                    formatName(event.target);
                    validateField(event.target);
                });
            }
            if (phoneInput) {
                phoneInput.addEventListener('input', (event) => {
                    formatPhoneInput(phoneInput, event);
                });
                phoneInput.addEventListener('blur', (event) => {
                    validateField(phoneInput);
                });
            }

            // Modify the generic listeners slightly to also check validity on input/change
            allInputs.forEach(input => {
                if (input.name === 'first_name' || input.name === 'last_name' || input.name === 'phone')
                    return;

                if (input.type === 'checkbox' || input.type === 'radio') {
                    input.addEventListener('change', (event) => {
                        validateField(event.target);
                    });
                } else {
                    input.addEventListener('input', (event) => {
                        debouncedValidateField(event.target);
                    });
                    input.addEventListener('blur', (event) => {
                        validateField(event.target);
                    });
                }
            });

            // --- Form Submission ---
            form.addEventListener('submit', function(event) {
                event.preventDefault();

                // Disable button immediately to prevent multiple clicks
                setLoadingState(true);
                clearAllErrors();

                // Execute reCAPTCHA safely using our helper function
                executeRecaptcha('submit_facebook_lead')
                    .then(function(token) {
                        // Token is now already set in the form by the executeRecaptcha function
                        // Now submit the form data via fetch
                        submitFormData();
                    })
                    .catch(function(error) {
                        console.error('reCAPTCHA execution failed:', error);
                        showGeneralError('Could not verify request. Please try again.');
                        setLoadingState(false); // Re-enable button on reCAPTCHA error
                    });
            });

            // Function to handle the actual form data submission
            function submitFormData() {
                // Before submitting, make sure all hidden address fields are populated
                const addressMapInput = document.getElementById('address_map_input');
                if (addressMapInput && addressMapInput.value) {
                    // If any required address field is empty, populate it with the map input value
                    const requiredAddressFields = ['address', 'city', 'state', 'zipcode'];
                    let missingFields = false;

                    requiredAddressFields.forEach(field => {
                        const fieldElement = document.getElementById(field);
                        if (fieldElement && !fieldElement.value) {
                            missingFields = true;
                        }
                    });

                    // If fields are missing and we haven't completed address extraction,
                    // show an error message
                    if (missingFields) {
                        Swal.fire({
                            title: 'Address Incomplete',
                            text: 'Please select a complete address from the dropdown suggestions.',
                            icon: 'warning',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#f59e0b'
                        });
                        setLoadingState(false); // Re-enable button
                        return; // Stop submission
                    }
                }

                // Data is already in loading state from the event listener
                const formData = new FormData(form);
                fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                            // Content-Type is automatically set by FormData
                        },
                        body: formData
                    })
                    .then(response => {
                        const contentType = response.headers.get("content-type");
                        if (!response.ok && !(contentType && contentType.indexOf("application/json") !==
                                -1 &&
                                response.status === 422)) {
                            // Check for reCAPTCHA failure specifically (might need adjustment based on backend response)
                            if (response.status ===
                                403) { // Assuming 403 for potential reCAPTCHA failure indication
                                throw new Error(`Security check failed.`);
                            }
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json().then(data => ({
                            status: response.status,
                            body: data
                        }));
                    })
                    .then(({
                        status,
                        body
                    }) => {
                        if (status === 422 && body.errors) {
                            // Check if there's a specific reCAPTCHA error
                            if (body.errors['g-recaptcha-response']) {
                                showGeneralError('reCAPTCHA validation failed. Please try again.');
                            }
                            // Check for duplicate email error
                            else if (body.duplicate_email) {
                                Swal.fire({
                                    title: '{{ __('swal_email_already_registered') }}',
                        html: '{{ __('swal_email_in_system') }}' +
                                        'Please contact our support team or call us at <strong>(713) 587-6423</strong> to schedule your appointment.',
                                    icon: 'info',
                                    confirmButtonText: 'OK',
                                    confirmButtonColor: '#f59e0b'
                                });
                            } else {
                                displayErrors(body.errors);
                                // Show validation errors with SweetAlert toast
                                const Toast = Swal.mixin({
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 5000,
                                    timerProgressBar: true,
                                    didOpen: (toast) => {
                                        toast.addEventListener('mouseenter', Swal.stopTimer)
                                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                                    }
                                });

                                Toast.fire({
                                    icon: 'warning',
                                    title: 'Please correct the errors marked below'
                                });
                            }
                        } else if (body.success) {
                            if (body.redirectUrl) {
                                // Set flag to indicate we're redirecting
                                form.dataset.isRedirecting = 'true';

                                // Show success message with SweetAlert2 before redirecting
                                Swal.fire({
                                    title: 'Success!',
                                    text: body.message ||
                                        'Your request has been submitted successfully!',
                                    icon: 'success',
                                    confirmButtonText: 'OK',
                                    confirmButtonColor: '#f59e0b', // Yellow-500 to match your theme
                                    timer: 3000, // Auto close after 3 seconds
                                    timerProgressBar: true,
                                    didOpen: () => {
                                        // Reset form when alert appears
                                        form.reset();

                                        // Reset map marker if it exists
                                        if (marker) {
                                            marker.setVisible(false);
                                        }

                                        // Reset insurance radio button styling
                                        document.querySelectorAll('.insurance-label')
                                            .forEach(label => {
                                                label.classList.remove('selected');
                                            });

                                        // Scroll to top for better UX
                                        window.scrollTo({
                                            top: 0,
                                            behavior: 'smooth'
                                        });
                                    },
                                    willClose: () => {
                                        // Redirect after alert closes
                                        window.location.href = body.redirectUrl;
                                    }
                                });
                                return;
                            } else {
                                // Show success message with SweetAlert2 without redirecting
                                Swal.fire({
                                    title: 'Thank You!',
                                    text: body.message ||
                                        'Your request has been submitted successfully!',
                                    icon: 'success',
                                    confirmButtonText: 'OK',
                                    confirmButtonColor: '#f59e0b', // Yellow-500 to match your theme
                                    timer: 4000, // Auto close after 4 seconds
                                    timerProgressBar: true,
                                    didOpen: () => {
                                        // Reset form
                                        form.reset();

                                        // Reset map marker if it exists
                                        if (marker) {
                                            marker.setVisible(false);
                                        }

                                        // Reset insurance radio button styling
                                        document.querySelectorAll('.insurance-label')
                                            .forEach(label => {
                                                label.classList.remove('selected');
                                            });
                                    }
                                });
                            }
                        } else {
                            // Use SweetAlert for error messages too
                            Swal.fire({
                                title: 'Oops!',
                                text: body.message ||
                                    'An error occurred on the server. Please try again.',
                                icon: 'error',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#f59e0b', // Yellow-500 to match your theme
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Submission failed:', error);
                        // Show error message with SweetAlert
                        Swal.fire({
                            title: 'Submission Error',
                            text: error.message.includes('Security check failed') ?
                                'Security check failed. Please refresh and try again.' :
                                'Could not submit the form due to a network or server issue. Please try again later.',
                            icon: 'error',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#f59e0b', // Yellow-500 to match your theme
                        });
                        // Don't disable submit button permanently on error, allow retry
                        // submitButton.disabled = true;
                    })
                    .finally(() => {
                        // Re-enable button unless redirecting
                        const isRedirecting = form.dataset.isRedirecting === 'true';
                        if (!isRedirecting) {
                            setLoadingState(false);
                        }
                    });
            } // End of submitFormData function

            // --- Initial Check ---
            checkFormValidity();

            // ... (beforeunload listener) ...
            window.addEventListener('beforeunload', () => {
                if (document.activeElement === submitButton) {
                    form.dataset.isRedirecting = 'true';
                }
            });

        });
    </script>
@endpush

@push('styles')
    <style>
        /* Basic styling for error indication */
        .border-red-500 {
            border-color: #f56565 !important;
            /* Tailwind red-500, use !important if needed */
        }

        .error-message {
            min-height: 1rem;
            /* Ensure space is reserved for error messages */
        }

        /* Radio buttons styling */
        .insurance-label {
            transition: all 0.2s ease;
            background-color: white;
        }

        .insurance-label:hover {
            background-color: #facc15 !important;
            /* yellow-400 - más brillante */
            color: white !important;
            border-color: #eab308 !important;
            /* yellow-500 */
        }

        .insurance-label.selected {
            background-color: #f59e0b !important;
            /* yellow-500 */
            color: white !important;
            border-color: #d97706 !important;
            /* yellow-600 */
        }

        /* Ensure reCAPTCHA badge is visible */
        /* Removing custom styles as requested */
        /*
                                                                                                .grecaptcha-badge {
                                                                                                    right: 14px !important;
                                                                                                    visibility: visible !important;
                                                                                                    opacity: 1 !important;
                                                                                                    z-index: 9999 !important;
                                                                                                }
                                                                                                */
    </style>
@endpush
