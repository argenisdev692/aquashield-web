@extends('layouts.main')

@php use App\Helpers\PhoneHelper; @endphp

@section('title', __('contact_support_page_title'))

@section('meta')
    <meta name="description" content="{{ __('contact_support_meta_description') }}">
    <meta name="keywords" content="{{ __('contact_support_meta_keywords') }}">
    <meta property="og:title" content="{{ __('contact_support_og_title') }}">
    <meta property="og:description" content="{{ __('contact_support_og_description') }}">
    <meta property="og:type" content="website">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url('/contact-support') }}">
@endsection

@push('styles')
    <style>
        .hero-section {
            margin-top: -2rem;
        }
    </style>
@endpush

@section('content')
    <!-- Hero Section with Image Overlay -->
    <div class="relative h-[500px] w-full hero-section">
        <!-- Background Image -->
        <img src="{{ asset('assets/img/contact-support.webp') }}" alt="{{ __('contact_support_hero_alt') }}"
            class="absolute inset-0 w-full h-full object-cover object-[center_25%]">

        <!-- Dark Overlay -->
        <div class="absolute inset-0 bg-black bg-opacity-50"></div>

        <!-- Content -->
        <div class="relative z-10 h-full flex items-center justify-center">
            <div class="text-center">
                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-4">
                    {{ __('contact_support') }}</h1>
                <p class="text-lg sm:text-xl md:text-2xl text-white max-w-2xl mx-auto px-4 mb-12">
                    {{ __('here_to_help_roofing_needs') }}</p>

                <!-- Breadcrumb Navigation -->
                <nav class="px-4 md:px-8 mt-8">
                    <div class="mx-auto">
                        <ol class="flex items-center justify-center space-x-2 text-white">
                            <li>
                                <a href="{{ route('home') }}"
                                    class="hover:text-yellow-500 transition-colors">{{ __('home') }}</a>
                            </li>
                            <li>
                                <span class="mx-2">/</span>
                            </li>
                            <li class="text-yellow-500 font-medium">{{ __('contact_support_page') }}</li>
                        </ol>
                    </div>
                </nav>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <!-- Contact Support Form -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div id="success-message" class="hidden p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
            </div>
            <div id="general-error-message" class="hidden p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg"
                role="alert"></div>

            <form id="contact-support-form" action="{{ secure_url(route('contact-support.store', [], false)) }}"
                method="POST" class="space-y-6">
                @csrf
                <!-- Hidden Input for reCAPTCHA v3 Token -->
                <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- First Name -->
                    <div>
                        <label for="first_name"
                            class="block text-sm font-medium text-gray-700">{{ __('first_name') }}</label>
                        <input type="text" id="first_name" name="first_name" maxlength="50"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 input-field">
                        <p class="mt-1 text-sm text-red-600 error-message" data-field="first_name"></p>
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label for="last_name"
                            class="block text-sm font-medium text-gray-700">{{ __('last_name') }}</label>
                        <input type="text" id="last_name" name="last_name" maxlength="50"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 input-field">
                        <p class="mt-1 text-sm text-red-600 error-message" data-field="last_name"></p>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">{{ __('email') }}</label>
                        <input type="email" id="email" name="email"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 input-field">
                        <p class="mt-1 text-sm text-red-600 error-message" data-field="email"></p>
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">{{ __('phone') }}</label>
                        <input type="tel" id="phone" name="phone" placeholder="{{ __('phone_placeholder') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 input-field">
                        <p class="mt-1 text-sm text-red-600 error-message" data-field="phone"></p>
                    </div>
                </div>

                <!-- Message -->
                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700">{{ __('message') }}</label>
                    <textarea id="message" name="message" rows="4"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 input-field"></textarea>
                    <p class="mt-1 text-sm text-red-600 error-message" data-field="message"></p>
                </div>

                <!-- SMS Consent Checkbox -->
                <div class="mt-6">
                    <label class="inline-flex items-start cursor-pointer">
                        <input type="checkbox" name="sms_consent" id="sms_consent"
                            class="form-checkbox text-yellow-500 mt-1 h-5 w-5 border-gray-300 rounded focus:ring-yellow-500 input-field">
                        <span class="ml-2 text-sm text-gray-600">
                            {!! __('sms_consent_text_page', [
                                'phone' => App\Helpers\PhoneHelper::format($companyData->phone),
                                'privacy_url' => route('privacy-policy'),
                                'terms_url' => route('terms-and-conditions'),
                            ]) !!}
                        </span>
                    </label>
                    <p class="mt-1 text-sm text-red-600 error-message" data-field="sms_consent"></p>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-center py-10 mt-5">
                    <button type="submit" id="submit-button"
                        class="group relative overflow-hidden bg-yellow-500 hover:bg-yellow-600 focus:ring-4 focus:ring-yellow-300 inline-flex items-center px-7 py-2.5 rounded-lg text-white justify-center disabled:opacity-75 disabled:cursor-not-allowed w-full md:w-auto">
                        <span id="submit-spinner" class="hidden z-40 animate-spin -ml-1 mr-3 h-5 w-5 text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </span>
                        <span id="submit-button-text" class="z-40">{{ __('send_message') }}</span>
                        <div
                            class="absolute inset-0 h-[200%] w-[200%] rotate-45 translate-x-[-70%] transition-all group-hover:scale-100 bg-white/30 group-hover:translate-x-[50%] z-20 duration-1000">
                        </div>
                    </button>
                </div>
            </form>
        </div>
    </main>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://www.google.com/recaptcha/api.js?render={{ $recaptchaSiteKey }}&onload=onRecaptchaLoad" async
        defer></script>
    <script>
        // Global variables for reCAPTCHA
        window.recaptchaSiteKey = '{{ $recaptchaSiteKey ?? '' }}';
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
                    reject(new Error('reCAPTCHA API not loaded'));
                    return;
                }

                try {
                    grecaptcha.ready(function() {
                        console.log('[executeRecaptcha] grecaptcha.ready callback fired.');
                        console.log('[executeRecaptcha] Attempting to execute with key:', window
                            .recaptchaSiteKey);
                        grecaptcha.execute(window.recaptchaSiteKey, {
                                action: action
                            })
                            .then(token => {
                                console.log('[executeRecaptcha] Token received:', token ? '***' :
                                    'null/undefined');
                                document.getElementById('g-recaptcha-response').value = token;
                                resolve(token);
                            })
                            .catch(error => {
                                console.error('[executeRecaptcha] grecaptcha.execute() failed:', error);
                                reject(error);
                            });
                    });
                } catch (error) {
                    console.error('[executeRecaptcha] Error during ready/execute:', error);
                    reject(error);
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('contact-support-form');
            const submitButton = document.getElementById('submit-button');
            const submitSpinner = document.getElementById('submit-spinner');
            const submitButtonText = document.getElementById('submit-button-text');
            const successMessageDiv = document.getElementById('success-message');
            const generalErrorDiv = document.getElementById('general-error-message');
            const csrfToken = document.querySelector('input[name="_token"]')?.value;
            const allInputs = form.querySelectorAll('.input-field');
            const firstNameInput = document.getElementById('first_name');
            const lastNameInput = document.getElementById('last_name');
            const phoneInput = document.getElementById('phone');
            let successTimeoutId = null;
            let errorTimeoutId = null;

            // --- Helper Functions ---
            function clearFieldError(fieldElement) {
                const fieldName = fieldElement.name;
                const errorSpan = form.querySelector(`.error-message[data-field="${fieldName}"]`);
                if (errorSpan) errorSpan.textContent = '';
                fieldElement.classList.remove('border-red-500');
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
                clearAllErrors();
                clearTimeout(successTimeoutId);

                successMessageDiv.textContent = message;
                successMessageDiv.classList.remove('hidden');
                form.reset();
                successMessageDiv.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                submitButton.disabled = true;

                successTimeoutId = setTimeout(() => {
                    successMessageDiv.classList.add('hidden');
                    successMessageDiv.textContent = '';
                }, 5000);
            }

            function showGeneralError(message) {
                clearTimeout(successTimeoutId);
                successMessageDiv.classList.add('hidden');
                successMessageDiv.textContent = '';
                clearTimeout(errorTimeoutId);

                generalErrorDiv.textContent = message || '{{ __('unexpected_error') }}';
                generalErrorDiv.classList.remove('hidden');
                generalErrorDiv.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                submitButton.disabled = true;

                errorTimeoutId = setTimeout(() => {
                    generalErrorDiv.classList.add('hidden');
                    generalErrorDiv.textContent = '';
                }, 5000);
            }

            function setLoadingState(isLoading) {
                submitButton.disabled = isLoading;
                if (isLoading) {
                    submitSpinner.classList.remove('hidden');
                    submitButtonText.textContent = '{{ __('sending_page') }}';
                    submitButton.setAttribute('aria-busy', 'true');
                    submitButton.setAttribute('aria-label', '{{ __('sending_page') }}');
                } else {
                    submitSpinner.classList.add('hidden');
                    submitButtonText.textContent = '{{ __('send_message') }}';
                    submitButton.removeAttribute('aria-busy');
                    submitButton.removeAttribute('aria-label');
                    checkFormValidity();
                }
            }

            function formatName(inputElement) {
                let value = inputElement.value;
                if (typeof value === 'string' && value.length > 0) {
                    // Limit to 50 characters
                    if (value.length > 50) {
                        value = value.substring(0, 50);
                    }
                    
                    // Store cursor position
                    const cursorPosition = inputElement.selectionStart;
                    
                    // Allow multiple names separated by spaces
                    // Only clean up multiple spaces, but preserve trailing space if user is typing
                    const endsWithSpace = value.endsWith(' ');
                    value = value.replace(/\s{2,}/g, ' '); // Replace multiple spaces with single space
                    
                    let parts = value.split(' ');
                    let formattedParts = parts.map((part, index) => {
                        if (part.length > 0) {
                            return part.charAt(0).toUpperCase() + part.slice(1).toLowerCase();
                        }
                        // Keep empty parts if they're not at the end (to preserve spaces between words)
                        return part;
                    });
                    
                    let formattedValue = formattedParts.join(' ');
                    
                    // Preserve trailing space if user was typing a space
                    if (endsWithSpace && !formattedValue.endsWith(' ') && formattedValue.length < 50) {
                        formattedValue += ' ';
                    }
                    
                    inputElement.value = formattedValue;
                    
                    // Restore cursor position
                    inputElement.setSelectionRange(cursorPosition, cursorPosition);
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
                    formattedValue = `(${value.substring(0, 3)}) ${value.substring(3, 6)}-${value.substring(6)}`;
                }
                inputElement.value = formattedValue;
                validateField(inputElement);
            }

            // --- Check Form Validity Function ---
            function checkFormValidity() {
                const requiredFields = ['first_name', 'last_name', 'email', 'phone', 'message'];
                let allRequiredFilled = true;

                requiredFields.forEach(field => {
                    const input = form.querySelector(`[name="${field}"]`);
                    if (input && !input.value.trim()) {
                        allRequiredFilled = false;
                    }
                });

                const hasVisibleErrors = Array.from(form.querySelectorAll('.error-message'))
                    .some(span => span.textContent.trim() !== '');

                submitButton.disabled = !allRequiredFilled || hasVisibleErrors;
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

            // --- Field Validation Function ---
            function validateField(fieldElement) {
                const fieldName = fieldElement.name;
                let fieldValue = fieldElement.type === 'checkbox' ? (fieldElement.checked ? 1 : 0) : fieldElement
                    .value;

                fetch('{{ secure_url(route('contact-support.validate', [], false)) }}', {
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

            // Add listeners to all other inputs
            allInputs.forEach(input => {
                if (input.name === 'first_name' || input.name === 'last_name' || input.name === 'phone') {
                    return; // Skip these as they already have listeners
                }

                if (input.type === 'checkbox') {
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

                setLoadingState(true);
                clearAllErrors();

                executeRecaptcha('submit_contact_support')
                    .then(function(token) {
                        submitFormData();
                    })
                    .catch(function(error) {
                        console.error('reCAPTCHA execution failed:', error);
                        showGeneralError('{{ __('swal_could_not_verify') }}');
                        setLoadingState(false);
                    });
            });

            function submitFormData() {
                const formData = new FormData(form);

                // Ensure sms_consent is always sent as a boolean value
                const smsConsentCheckbox = document.getElementById('sms_consent');
                if (smsConsentCheckbox) {
                    // Remove the original value and set a proper boolean
                    formData.delete('sms_consent');
                    formData.append('sms_consent', smsConsentCheckbox.checked ? '1' : '0');
                }

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
                                throw new Error(`{{ __('swal_security_check_failed') }}`);
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
                            showGeneralError('{{ __('swal_recaptcha_failed') }}');
                        } else {
                            displayErrors(body.errors);
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
                            title: '{{ __('swal_success') }}',
                            text: body.message || '{{ __('swal_message_sent_successfully') }}',
                            icon: 'success',
                            confirmButtonText: '{{ __('swal_ok') }}',
                            confirmButtonColor: '#f59e0b',
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: () => {
                                form.reset();
                                window.scrollTo({
                                    top: 0,
                                    behavior: 'smooth'
                                });
                            }
                        });
                        } else {
                        Swal.fire({
                            title: '{{ __('swal_oops') }}',
                            text: body.message ||
                                '{{ __('swal_server_error') }}',
                            icon: 'error',
                            confirmButtonText: '{{ __('swal_ok') }}',
                            confirmButtonColor: '#f59e0b',
                        });
                        }
                    })
                    .catch(error => {
                        console.error('Submission failed:', error);
                        Swal.fire({
                            title: '{{ __('swal_submission_error') }}',
                            text: error.message.includes('{{ __('swal_security_check_failed') }}') ?
                                '{{ __('swal_security_check_failed') }}' :
                                '{{ __('swal_network_error') }}',
                            icon: 'error',
                            confirmButtonText: '{{ __('swal_ok') }}',
                            confirmButtonColor: '#f59e0b',
                        });
                    })
                    .finally(() => {
                        setLoadingState(false);
                    });
            }

            // Initial form validation check
            checkFormValidity();
        });
    </script>
@endpush
