@php use App\Helpers\PhoneHelper; @endphp
<!-- Inline Facebook Lead Form -->
<div class="max-w-3xl mx-auto my-8 px-4 fade-in-section">
    <!-- Centered Logo -->
    <div class="flex justify-center mb-6">
        <a href="{{ route('home') }}" title="V General Contractors">
            <img src="{{ asset('assets/logo/logo3.webp') }}" alt="V General Contractors Logo" class="h-12 md:h-16">
        </a>
    </div>

    <!-- Header -->
    <div class="px-4 py-3 mb-4 bg-yellow-500 sm:px-6 rounded-lg">
        <h3 class="font-bold leading-6 text-center text-white text-lg sm:text-xl md:text-2xl">
            {{ __('get_your_free_inspection') }}
        </h3>
    </div>

    <p class="mt-6 mb-4 text-gray-600 text-center text-sm sm:text-base">
        {{ __('fill_form_below') }}
    </p>

    <!-- Success/Error Messages -->
    <div id="inline-success-message" class="hidden p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg"
        role="alert"></div>
    <div id="inline-general-error-message" class="hidden p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg"
        role="alert"></div>

    <!-- Form -->
    <form id="inline-facebook-lead-form" action="{{ secure_url(route('facebook.lead.store', [], false)) }}"
        method="POST" class="space-y-4" novalidate>
        @csrf

        {{-- Set form start time in session --}}
        @php
            session(['form_start_time' => time()]);
        @endphp

        <!-- Hidden Inputs for Coordinates -->
        <input type="hidden" name="latitude" id="inline-latitude">
        <input type="hidden" name="longitude" id="inline-longitude">
        <!-- Hidden Input for reCAPTCHA v3 Token -->
        <input type="hidden" name="g-recaptcha-response" id="inline-g-recaptcha-response">

        <div class="grid grid-cols-1 gap-x-4 gap-y-2 sm:grid-cols-2">
            <!-- First Name -->
            <div>
                <label for="inline_first_name"
                    class="block font-medium text-gray-700 text-sm sm:text-base">{{ __('first_name') }}
                    <span class="text-red-500">*</span></label>
                <input type="text" id="inline_first_name" name="first_name"
                    class="input-field block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm"
                    autocomplete="given-name" maxlength="50" required>
                <span class="error-message text-xs text-red-500 mt-1 block h-4" data-field="first_name"></span>
            </div>
            <!-- Last Name -->
            <div>
                <label for="inline_last_name"
                    class="block font-medium text-gray-700 text-sm sm:text-base">{{ __('last_name') }}
                    <span class="text-red-500">*</span></label>
                <input type="text" id="inline_last_name" name="last_name"
                    class="input-field block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm"
                    autocomplete="family-name" maxlength="50" required>
                <span class="error-message text-xs text-red-500 mt-1 block h-4" data-field="last_name"></span>
            </div>
        </div>
        <div class="grid grid-cols-1 gap-x-4 gap-y-2 sm:grid-cols-2">
            <!-- Phone -->
            <div>
                <label for="inline_phone"
                    class="block font-medium text-gray-700 text-sm sm:text-base">{{ __('phone') }} <span
                        class="text-red-500">*</span></label>
                <input type="tel" id="inline_phone" name="phone" placeholder="(XXX) XXX-XXXX"
                    class="input-field block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm"
                    autocomplete="tel" required maxlength="14">
                <span class="error-message text-xs text-red-500 mt-1 block h-4" data-field="phone"></span>
            </div>
            <!-- Email -->
            <div>
                <label for="inline_email"
                    class="block font-medium text-gray-700 text-sm sm:text-base">{{ __('email') }} <span
                        class="text-red-500">*</span></label>
                <input type="email" id="inline_email" name="email"
                    class="input-field block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm"
                    autocomplete="email" required>
                <span class="error-message text-xs text-red-500 mt-1 block h-4" data-field="email"></span>
            </div>
        </div>
        <!-- Address Map Input Field -->
        <div>
            <label for="inline_address_map_input"
                class="block font-medium text-gray-700 text-sm sm:text-base">{{ __('address') }}
                <span class="text-red-500">*</span></label>
            <input type="text" id="inline_address_map_input" name="address_map_input"
                placeholder="{{ __('enter_complete_address') }}"
                class="input-field block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm"
                autocomplete="off" required>
            <span class="error-message text-xs text-red-500 mt-1 block h-4" data-field="address_map_input"></span>
        </div>
        <!-- Hidden Address Fields -->
        <input type="hidden" id="inline_address" name="address">
        <input type="hidden" id="inline_city" name="city">
        <input type="hidden" id="inline_state" name="state">
        <input type="hidden" id="inline_zipcode" name="zipcode">
        <input type="hidden" id="inline_country" name="country" value="USA">
        <!-- Address 2 -->
        <div>
            <label for="inline_address_2"
                class="block font-medium text-gray-700 text-sm sm:text-base">{{ __('address_2') }} <span
                    class="text-xs text-gray-500">{{ __('optional_apt_suite') }}</span></label>
            <input type="text" id="inline_address_2" name="address_2"
                placeholder="{{ __('apt_suite_placeholder') }}"
                class="input-field block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm"
                autocomplete="address-line2">
            <span class="error-message text-xs text-red-500 mt-1 block h-4" data-field="address_2"></span>
        </div>
        <!-- Map Display -->
        <div class="mt-4 mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('location_map') }}</label>
            <div id="inline-location-map" class="w-full h-48 bg-gray-200 rounded-lg border border-gray-300"></div>
        </div>
        <!-- Comment/Message -->
        <div>
            <label for="inline_message"
                class="block mt-8 font-medium text-gray-700 text-sm sm:text-base">{{ __('comment_or_message') }} <span
                    class="text-xs text-gray-500">{{ __('optional_label') }}</span></label>
            <textarea id="inline_message" name="message" rows="4"
                class="input-field block w-full  border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm"></textarea>
            <span class="error-message text-xs text-red-500 mt-1 block h-4" data-field="message"></span>
        </div>
        <!-- Property Insurance -->
        <div class="mt-6 text-center md:text-center sm:text-center">
            <label
                class="block font-medium text-gray-700 mb-2 text-sm sm:text-base">{{ __('property_insurance_question') }}
                <span class="text-red-500">*</span></label>
            <fieldset class="mt-2">
                <legend class="sr-only">Property Insurance</legend>
                <div class="flex items-center justify-center space-x-4">
                    <div class="radio-option flex items-center">
                        <input id="inline_insurance_yes" name="insurance_property" type="radio" value="1"
                            class="radio-field sr-only" required>
                        <label for="inline_insurance_yes"
                            class="insurance-label flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md cursor-pointer text-sm w-20">{{ __('yes') }}</label>
                    </div>
                    <div class="radio-option flex items-center">
                        <input id="inline_insurance_no" name="insurance_property" type="radio" value="0"
                            class="radio-field sr-only" required>
                        <label for="inline_insurance_no"
                            class="insurance-label flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md cursor-pointer text-sm w-20">{{ __('no') }}</label>
                    </div>
                </div>
            </fieldset>
            <span class="error-message text-xs text-red-500 mt-1 block h-4" data-field="insurance_property"></span>
        </div>
        <!-- SMS Consent -->
        <div class="mt-6">
            <label class="inline-flex items-start cursor-pointer text-sm sm:text-base">
                <input id="inline_sms_consent" name="sms_consent" type="checkbox" value="1"
                    class="checkbox-field form-checkbox text-yellow-500 mt-1 h-5 w-5 border-gray-300 rounded focus:ring-yellow-500">
                <span class="ml-2 text-sm text-gray-600">
                    {!! __('sms_consent_text') !!}
                    <strong>{{ \App\Helpers\PhoneHelper::format($companyData->phone) }}</strong>
                    <a href="{{ route('privacy-policy') }}" target="_blank"
                        class="text-yellow-500 hover:text-yellow-600 underline">{{ __('privacy_policy') }}</a>
                    {{ __('and') }} <a href="{{ route('terms-and-conditions') }}" target="_blank"
                        class="text-yellow-500 hover:text-yellow-600 underline">{{ __('terms_of_service') }}</a>.
                </span>
            </label>
            <span class="error-message text-xs text-red-500 mt-1 block h-4" data-field="sms_consent"></span>
        </div>
        <!-- Submit Button -->
        <div class="pt-6 mt-4 text-center">
            <button type="submit" id="inline-submit-button"
                class="group relative overflow-hidden bg-yellow-500 hover:bg-yellow-600 focus:ring-4 focus:ring-yellow-300 inline-flex items-center px-7 py-2.5 rounded-lg text-white justify-center disabled:opacity-75 disabled:cursor-not-allowed w-full sm:w-auto"
                disabled>
                <!-- Spinner -->
                <svg id="inline-submit-spinner" class="hidden z-40 animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <span id="inline-submit-button-text" class="z-40">{{ __('send_request') }}</span>
                <div
                    class="absolute inset-0 h-[200%] w-[200%] rotate-45 translate-x-[-70%] transition-all group-hover:scale-100 bg-white/30 group-hover:translate-x-[50%] z-20 duration-1000">
                </div>
            </button>
        </div>
        <div class="pb-6"></div>
    </form>
</div>

@once
    <style>
        .border-red-500 {
            border-color: #f56565 !important;
        }

        .error-message {
            min-height: 1rem;
        }

        .insurance-label {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background-color: white;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            font-weight: 500;
            position: relative;
            overflow: hidden;
        }

        .insurance-label::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .insurance-label:hover {
            background: linear-gradient(135deg, #facc15, #f59e0b) !important;
            color: white !important;
            border-color: #eab308 !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 12px 0 rgba(245, 158, 11, 0.25), 0 2px 4px 0 rgba(0, 0, 0, 0.1) !important;
        }

        .insurance-label:hover::before {
            left: 100%;
        }

        .insurance-label.selected {
            background: linear-gradient(135deg, #f59e0b, #d97706) !important;
            color: white !important;
            border-color: #d97706 !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 12px 0 rgba(217, 119, 6, 0.3), 0 2px 4px 0 rgba(0, 0, 0, 0.1) !important;
        }

        .insurance-label:active {
            transform: translateY(0) !important;
            box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1) !important;
        }

        /* Animación de pulso para el estado seleccionado */
        .insurance-label.selected {
            animation: pulse-selected 2s infinite;
        }

        @keyframes pulse-selected {
            0%, 100% {
                box-shadow: 0 4px 12px 0 rgba(217, 119, 6, 0.3), 0 2px 4px 0 rgba(0, 0, 0, 0.1);
            }
            50% {
                box-shadow: 0 4px 12px 0 rgba(217, 119, 6, 0.5), 0 2px 4px 0 rgba(0, 0, 0, 0.1);
            }
        }
    </style>
@endonce

@once
    <script>
        // Similar JS que el modal, pero adaptado para IDs inline y sin Alpine.js
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('inline-facebook-lead-form');
            if (!form) return;
            const submitButton = document.getElementById('inline-submit-button');
            const submitSpinner = document.getElementById('inline-submit-spinner');
            const submitButtonText = document.getElementById('inline-submit-button-text');
            const successMessageDiv = document.getElementById('inline-success-message');
            const generalErrorDiv = document.getElementById('inline-general-error-message');
            const csrfToken = document.querySelector('input[name="_token"]')?.value;
            const allInputs = form.querySelectorAll('.input-field, .radio-field, .checkbox-field');
            const firstNameInput = document.getElementById('inline_first_name');
            const lastNameInput = document.getElementById('inline_last_name');
            const phoneInput = document.getElementById('inline_phone');
            const requiredInputs = form.querySelectorAll('[required]');
            let successTimeoutId = null;
            let errorTimeoutId = null;

            // Radio button styling and initialization
            const insuranceOptions = document.querySelectorAll('input[name="insurance_property"]');
            
            // Initialize radio button styles on load
            insuranceOptions.forEach(option => {
                if (option.checked) {
                    const label = document.querySelector(`label[for="${option.id}"]`);
                    if (label) {
                        label.classList.add('selected');
                    }
                }
                
                // Custom validation message
                option.setCustomValidity('');
                option.addEventListener('invalid', function() {
                    this.setCustomValidity('{{ __('insurance_property_required') }}');
                });
                
                option.addEventListener('change', function() {
                    // Clear custom validation
                    this.setCustomValidity('');
                    
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

            // Google Maps Autocomplete
            function initializeInlineAutocomplete() {
                const addressMapInput = document.getElementById('inline_address_map_input');
                const mapContainer = document.getElementById('inline-location-map');
                if (!addressMapInput || !mapContainer) return;
                const map = new google.maps.Map(mapContainer, {
                    center: {
                        lat: 37.0902,
                        lng: -95.7129
                    },
                    zoom: 4,
                    mapTypeControl: false,
                    streetViewControl: false,
                    fullscreenControl: false,
                    zoomControl: true
                });
                const marker = new google.maps.Marker({
                    map: map,
                    visible: false
                });
                const autocomplete = new google.maps.places.Autocomplete(addressMapInput, {
                    types: ['address'],
                    componentRestrictions: {
                        country: 'us'
                    },
                    fields: ['address_components', 'geometry', 'formatted_address']
                });
                autocomplete.addListener('place_changed', function() {
                    const place = autocomplete.getPlace();
                    if (!place.geometry) return;
                    const lat = place.geometry.location.lat();
                    const lng = place.geometry.location.lng();
                    document.getElementById('inline-latitude').value = lat;
                    document.getElementById('inline-longitude').value = lng;
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
                    let addressLine1 = '',
                        city = '',
                        state = '',
                        zipcode = '';
                    for (const component of place.address_components) {
                        const componentType = component.types[0];
                        switch (componentType) {
                            case 'street_number':
                                addressLine1 = component.long_name;
                                break;
                            case 'route':
                                addressLine1 = addressLine1 ? addressLine1 + ' ' + component.long_name :
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
                    if (place.formatted_address) addressMapInput.value = place.formatted_address;
                    if (addressLine1) document.getElementById('inline_address').value = addressLine1;
                    if (city) document.getElementById('inline_city').value = city;
                    if (state) document.getElementById('inline_state').value = state;
                    if (zipcode) document.getElementById('inline_zipcode').value = zipcode;
                    document.getElementById('inline_country').value = 'USA';
                    const validateEvent = new Event('change', {
                        bubbles: true
                    });
                    addressMapInput.dispatchEvent(validateEvent);
                    ['inline_address', 'inline_city', 'inline_state', 'inline_zipcode'].forEach(fieldId => {
                        const hiddenField = document.getElementById(fieldId);
                        if (hiddenField) {
                            hiddenField.dispatchEvent(new Event('change', {
                                bubbles: true
                            }));
                        }
                    });
                    setupFormValidation();
                });
                setupFormValidation();
            }

            // Esperar a que Google Maps esté disponible
            function waitForGoogleMaps() {
                if (window.google && window.google.maps && window.google.maps.places) {
                    initializeInlineAutocomplete();
                } else {
                    setTimeout(waitForGoogleMaps, 500);
                }
            }
            waitForGoogleMaps();

            function setupFormValidation() {
                if (form.dataset.validationInitialized === 'true') return;

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

                function clearFieldError(fieldElement) {
                    const fieldName = fieldElement.name;
                    const errorSpan = form.querySelector(`.error-message[data-field="${fieldName}"]`);
                    if (errorSpan) errorSpan.textContent = '';
                    fieldElement.classList.remove('border-red-500');
                    if (fieldElement.type === 'radio') {
                        document.querySelectorAll(`input[name="${fieldName}"]`).forEach(radio => radio.classList
                            .remove('border-red-500'));
                    }
                    checkFormValidity();
                }

                function clearAllErrors() {
                    document.querySelectorAll('.error-message').forEach(span => span.textContent = '');
                    allInputs.forEach(input => input.classList.remove('border-red-500'));
                    clearTimeout(successTimeoutId);
                    successMessageDiv.classList.add('hidden');
                    successMessageDiv.textContent = '';
                    clearTimeout(errorTimeoutId);
                    generalErrorDiv.classList.add('hidden');
                    generalErrorDiv.textContent = '';
                    checkFormValidity();
                }

                function checkFormValidity() {
                    let allRequiredFilled = true;
                    requiredInputs.forEach(input => {
                        let value = input.value.trim();
                        if (input.type === 'radio') {
                            const groupName = input.name;
                            if (!form.querySelector(`input[name="${groupName}"]:checked`)) {
                                allRequiredFilled = false;
                            }
                        } else if (!value) {
                            allRequiredFilled = false;
                        }
                    });
                    const hasVisibleErrors = Array.from(form.querySelectorAll('.error-message')).some(span => span
                        .textContent.trim() !== '');
                    submitButton.disabled = !allRequiredFilled || hasVisibleErrors;
                }

                function validateField(fieldElement) {
                    const fieldName = fieldElement.name;
                    let fieldValue = fieldElement.type === 'checkbox' ? (fieldElement.checked ? 1 : 0) :
                        fieldElement.value;
                    if (fieldElement.type === 'radio') {
                        const checkedRadio = form.querySelector(`input[name="${fieldName}"]:checked`);
                        if (!checkedRadio) {
                            clearFieldError(fieldElement);
                            return;
                        }
                        fieldValue = checkedRadio.value;
                    }
                    if (fieldName === 'address_map_input') {
                        const addressValue = document.getElementById('inline_address').value;
                        if (fieldValue && !addressValue) {
                            document.getElementById('inline_address').value = fieldValue;
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

                                    // Si es un error de email duplicado, mostrar un mensaje descriptivo
                                    if (fieldName === 'email' && data.duplicate_email) {
                                        errorSpan.innerHTML =
                                            '{{ __('swal_email_in_system') }}';
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
                const debouncedValidateField = debounce(validateField, 500);

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
                    if (!isBackspace) {
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
                        formattedValue =
                            `(${value.substring(0, 3)}) ${value.substring(3, 6)}-${value.substring(6, 10)}`;
                    }
                    inputElement.value = formattedValue;
                    if (form && typeof validateField === 'function') {
                        validateField(inputElement);
                    }
                }
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
                form.addEventListener('submit', function(event) {
                    event.preventDefault();
                    setLoadingState(true);
                    clearAllErrors();
                    if (generalErrorDiv) {
                        generalErrorDiv.classList.add('hidden');
                        generalErrorDiv.textContent = '';
                    }
                    executeInlineRecaptcha('submit_inline_lead')
                        .then(function(token) {
                            submitInlineFormData(form, csrfToken);
                        })
                        .catch(function(error) {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    title: '{{ __('swal_verification_error') }}',
                                    text: '{{ __('swal_could_not_verify') }}',
                                    icon: 'error',
                                    confirmButtonText: '{{ __('swal_ok') }}',
                                    confirmButtonColor: '#f59e0b'
                                });
                            } else {
                                alert('{{ __('swal_could_not_verify') }}');
                            }
                            setLoadingState(false);
                        });
                });

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

                function setLoadingState(isLoading) {
                    submitButton.disabled = isLoading;
                    if (isLoading) {
                        submitSpinner.classList.remove('hidden');
                        submitButtonText.textContent = '{{ __('sending') }}';
                        submitButton.setAttribute('aria-busy', 'true');
                        submitButton.setAttribute('aria-label', 'Sending');
                    } else {
                        submitSpinner.classList.add('hidden');
                        submitButtonText.textContent = '{{ __('send_request') }}';
                        submitButton.removeAttribute('aria-busy');
                        submitButton.removeAttribute('aria-label');
                        checkFormValidity();
                    }
                }
                checkFormValidity();
                form.dataset.validationInitialized = 'true';
            }
            // reCAPTCHA v3 para inline
            function executeInlineRecaptcha(action) {
                console.log('[executeInlineRecaptcha] Called with action:', action);
                return new Promise((resolve, reject) => {
                    if (typeof grecaptcha === 'undefined') {
                        console.error('[executeInlineRecaptcha] Error: reCAPTCHA API not loaded');

                        // Intentar cargar reCAPTCHA y reintentar
                        const script = document.createElement('script');
                        script.src =
                            'https://www.google.com/recaptcha/api.js?render={{ config('captcha.sitekey') }}';
                        script.async = true;
                        script.defer = true;
                        script.onload = function() {
                            console.log(
                                '[executeInlineRecaptcha] reCAPTCHA script loaded, retrying...');
                            window.recaptchaLoaded = true;
                            setTimeout(() => {
                                executeInlineRecaptcha(action).then(resolve).catch(reject);
                            }, 1000);
                        };
                        document.head.appendChild(script);
                        return;
                    }

                    try {
                        grecaptcha.ready(function() {
                            console.log(
                                '[executeInlineRecaptcha] grecaptcha.ready callback fired.');
                            console.log('[executeInlineRecaptcha] Attempting to execute with key:',
                                '{{ config('captcha.sitekey') }}');

                            // Asegurarse de que haya un pequeño retraso antes de ejecutar
                            setTimeout(() => {
                                grecaptcha.execute('{{ config('captcha.sitekey') }}', {
                                        action: action
                                    })
                                    .then(token => {
                                        console.log(
                                            '[executeInlineRecaptcha] Token received:',
                                            token ? 'success (length: ' + token
                                            .length + ')' : 'null/undefined');

                                        if (!token) {
                                            console.error(
                                                '[executeInlineRecaptcha] Received empty token'
                                            );
                                            reject(new Error('Empty reCAPTCHA token'));
                                            return;
                                        }

                                        document.getElementById(
                                                'inline-g-recaptcha-response').value =
                                            token;
                                        resolve(token);
                                    })
                                    .catch(error => {
                                        console.error(
                                            '[executeInlineRecaptcha] grecaptcha.execute() failed:',
                                            error);
                                        reject(error);
                                    });
                            }, 500);
                        });
                    } catch (error) {
                        console.error('[executeInlineRecaptcha] Error during ready/execute:', error);
                        reject(error);
                    }
                });
            }
            // Envío del formulario inline
            function submitInlineFormData(form, csrfToken) {
                const addressMapInput = document.getElementById('inline_address_map_input');
                if (addressMapInput && addressMapInput.value) {
                    const requiredAddressFields = ['inline_address', 'inline_city', 'inline_state',
                        'inline_zipcode'
                    ];
                    let missingFields = false;
                    requiredAddressFields.forEach(field => {
                        const fieldElement = document.getElementById(field);
                        if (fieldElement && !fieldElement.value) {
                            missingFields = true;
                        }
                    });
                    if (missingFields) {
                        Swal.fire({
                            title: '{{ __('swal_address_incomplete') }}',
                            text: '{{ __('swal_select_complete_address') }}',
                            icon: 'warning',
                            confirmButtonText: '{{ __('swal_ok') }}',
                            confirmButtonColor: '#f59e0b'
                        });
                        document.getElementById('inline-submit-button').disabled = false;
                        document.getElementById('inline-submit-spinner').classList.add('hidden');
                        return;
                    }
                }
                const formData = new FormData(form);
                fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(response => {
                        const contentType = response.headers.get("content-type");
                        if (!response.ok && !(contentType && contentType.indexOf("application/json") !== -1 &&
                                response.status === 422)) {
                            if (response.status === 403) {
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
                            if (body.errors['g-recaptcha-response']) {
                                Swal.fire({
                                    title: '{{ __('swal_error') }}',
                                    text: '{{ __('swal_recaptcha_failed') }}',
                                    icon: 'error'
                                });
                            }
                            // Check for duplicate email error
                            else if (body.duplicate_email) {
                                Swal.fire({
                                    title: '{{ __('swal_email_already_registered') }}',
                                    html: '{!! __('swal_email_in_system') !!}',
                                    icon: 'info',
                                    confirmButtonText: '{{ __('swal_ok') }}',
                                    confirmButtonColor: '#f59e0b'
                                });
                            } else {
                                // Usar displayErrors en lugar de alert
                                displayErrors(body.errors);

                                // Mostrar notificación con SweetAlert
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
                                    title: '{{ __('swal_correct_errors') }}'
                                });
                            }
                        } else if (body.success) {
                            Swal.fire({
                                title: '{{ __('swal_thank_you') }}',
                                text: body.message || '{{ __('swal_request_submitted') }}',
                                icon: 'success',
                                confirmButtonText: '{{ __('swal_ok') }}',
                                confirmButtonColor: '#f59e0b',
                                timer: 4000,
                                timerProgressBar: true,
                                didOpen: () => {
                                    form.reset();
                                }
                            });
                        } else {
                            Swal.fire({
                                title: '{{ __('swal_oops') }}',
                                text: body.message || '{{ __('swal_server_error') }}',
                                icon: 'error',
                                confirmButtonText: '{{ __('swal_ok') }}',
                                confirmButtonColor: '#f59e0b'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: '{{ __('swal_submission_error') }}',
                            text: error.message.includes('Security check failed') ?
                                '{{ __('swal_security_check_failed') }}' :
                                '{{ __('swal_network_error') }}',
                            icon: 'error',
                            confirmButtonText: '{{ __('swal_ok') }}',
                            confirmButtonColor: '#f59e0b'
                        });
                    })
                    .finally(() => {
                        document.getElementById('inline-submit-button').disabled = false;
                        document.getElementById('inline-submit-spinner').classList.add('hidden');
                        document.getElementById('inline-submit-button-text').textContent =
                            '{{ __('send_request') }}';
                    });
            }
        });
    </script>
@endonce
