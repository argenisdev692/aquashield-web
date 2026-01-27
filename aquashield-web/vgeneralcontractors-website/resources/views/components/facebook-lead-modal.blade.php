<div x-data="{ showFacebookLeadModal: false }"
    @open-appointment-modal.window="
        showFacebookLeadModal = true; 
        $nextTick(() => { 
            initGoogleMapsForModal();
        });
    ">
    @php use App\Helpers\PhoneHelper; @endphp
    <!-- Modal -->
    <div x-show="showFacebookLeadModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="showFacebookLeadModal = false">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                <!-- Close button -->
                <button @click="showFacebookLeadModal = false"
                    class="absolute top-3 right-4 bg-red-500 text-white rounded-full p-2 hover:bg-red-600 transition-colors duration-200 shadow-lg z-50">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>

                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4" id="facebook-lead-modal-content">
                    <!-- Facebook lead form content -->
                    <div class="max-w-3xl mx-auto">
                        <!-- Centered Logo -->
                        <div class="flex justify-center mb-6">
                            <a href="{{ route('home') }}" title="V General Contractors">
                                <img src="{{ asset('assets/logo/logo3.webp') }}" alt="V General Contractors Logo"
                                    class="h-12 md:h-16">
                            </a>
                        </div>

                        <!-- Header -->
                        <div class="px-4 py-3 mb-4 bg-yellow-500 sm:px-6 rounded-lg">
                            <h3 class="text-xl font-bold leading-6 text-center text-white">
                                {{ __('get_your_free_inspection') }}</h3>
                        </div>

                        <p class="mt-6 mb-4 text-sm text-gray-600 text-center">
                            {{ __('fill_form_below') }}
                        </p>

                        <!-- Success/Error Messages -->
                        <div id="success-message" class="hidden p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg"
                            role="alert"></div>
                        <div id="general-error-message"
                            class="hidden p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert"></div>

                        <!-- Form -->
                        <form id="facebook-lead-form" action="{{ secure_url(route('facebook.lead.store', [], false)) }}"
                            method="POST" class="space-y-4" novalidate>
                            @csrf

                            {{-- Set form start time in session --}}
                            @php
                                session(['form_start_time' => time()]);
                            @endphp

                            <!-- Hidden Inputs for Coordinates -->
                            <input type="hidden" name="latitude" id="latitude">
                            <input type="hidden" name="longitude" id="longitude">
                            <!-- Hidden Input for reCAPTCHA v3 Token -->
                            <input type="hidden" name="g-recaptcha-response" id="modal-g-recaptcha-response">

                            <div class="grid grid-cols-1 gap-x-4 gap-y-2 sm:grid-cols-2">
                                <!-- First Name -->
                                <div>
                                    <label for="first_name"
                                        class="block text-sm font-medium text-gray-700">{{ __('first_name') }}
                                        <span class="text-red-500">*</span></label>
                                    <input type="text" id="first_name" name="first_name"
                                        class="input-field block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm"
                                        autocomplete="given-name" maxlength="50" required>
                                    <span class="error-message text-xs text-red-500 mt-1 block h-4"
                                        data-field="first_name"></span>
                                </div>

                                <!-- Last Name -->
                                <div>
                                    <label for="last_name"
                                        class="block text-sm font-medium text-gray-700">{{ __('last_name') }}
                                        <span class="text-red-500">*</span></label>
                                    <input type="text" id="last_name" name="last_name"
                                        class="input-field block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm"
                                        autocomplete="family-name" maxlength="50" required>
                                    <span class="error-message text-xs text-red-500 mt-1 block h-4"
                                        data-field="last_name"></span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-x-4 gap-y-2 sm:grid-cols-2">
                                <!-- Phone -->
                                <div>
                                    <label for="phone"
                                        class="block text-sm font-medium text-gray-700">{{ __('phone') }} <span
                                            class="text-red-500">*</span></label>
                                    <input type="tel" id="phone" name="phone"
                                        placeholder="{{ __('phone_placeholder') }}"
                                        class="input-field block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm"
                                        autocomplete="tel" required maxlength="14">
                                    <span class="error-message text-xs text-red-500 mt-1 block h-4"
                                        data-field="phone"></span>
                                </div>

                                <!-- Email -->
                                <div>
                                    <label for="email"
                                        class="block text-sm font-medium text-gray-700">{{ __('email') }} <span
                                            class="text-red-500">*</span></label>
                                    <input type="email" id="email" name="email"
                                        class="input-field block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm"
                                        autocomplete="email" required>
                                    <span class="error-message text-xs text-red-500 mt-1 block h-4"
                                        data-field="email"></span>
                                </div>
                            </div>

                            <!-- Address Map Input Field -->
                            <div>
                                <label for="address_map_input"
                                    class="block text-sm font-medium text-gray-700">{{ __('address') }}
                                    <span class="text-red-500">*</span></label>
                                <input type="text" id="address_map_input" name="address_map_input"
                                    placeholder="{{ __('enter_complete_address') }}"
                                    class="input-field block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm"
                                    autocomplete="off" required>
                                <span class="error-message text-xs text-red-500 mt-1 block h-4"
                                    data-field="address_map_input"></span>
                            </div>

                            <!-- Hidden Address Fields -->
                            <input type="hidden" id="address" name="address">
                            <input type="hidden" id="city" name="city">
                            <input type="hidden" id="state" name="state">
                            <input type="hidden" id="zipcode" name="zipcode">
                            <input type="hidden" id="country" name="country" value="USA">

                            <!-- Address 2 -->
                            <div>
                                <label for="address_2"
                                    class="block text-sm font-medium text-gray-700">{{ __('address_2') }} <span
                                        class="text-xs text-gray-500">{{ __('optional_apt_suite') }}</span></label>
                                <input type="text" id="address_2" name="address_2"
                                    placeholder="{{ __('apt_suite_placeholder') }}"
                                    class="input-field block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm"
                                    autocomplete="address-line2">
                                <span class="error-message text-xs text-red-500 mt-1 block h-4"
                                    data-field="address_2"></span>
                            </div>

                            <!-- Map Display -->
                            <div class="mt-4 mb-4">
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2">{{ __('location_map') }}</label>
                                <div id="location-map"
                                    class="w-full h-48 bg-gray-200 rounded-lg border border-gray-300">
                                    <!-- Map will be initialized here -->
                                </div>
                            </div>

                            <!-- Comment/Message -->
                            <div>
                                <label for="message"
                                    class="block mt-8 text-sm font-medium text-gray-700">{{ __('comment_or_message') }}
                                    <span class="text-xs text-gray-500">{{ __('optional_label') }}</span></label>
                                <textarea id="message" name="message" rows="4"
                                    class="input-field block w-full  border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm"></textarea>
                                <span class="error-message text-xs text-red-500 mt-1 block h-4"
                                    data-field="message"></span>
                            </div>

                            <!-- Property Insurance -->
                            <div class="mt-6 text-center md:text-center sm:text-center">
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2">{{ __('property_insurance_question') }}
                                    <span class="text-red-500">*</span></label>
                                <fieldset class="mt-2">
                                    <legend class="sr-only">Property Insurance</legend>
                                    <div class="flex items-center justify-center space-x-4">
                                        <div class="radio-option flex items-center">
                                            <input id="insurance_yes" name="insurance_property" type="radio"
                                                value="1" class="radio-field sr-only" required>
                                            <label for="insurance_yes"
                                                class="insurance-label flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md cursor-pointer text-sm w-20">
                                                {{ __('yes') }}
                                            </label>
                                        </div>
                                        <div class="radio-option flex items-center">
                                            <input id="insurance_no" name="insurance_property" type="radio"
                                                value="0" class="radio-field sr-only" required>
                                            <label for="insurance_no"
                                                class="insurance-label flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md cursor-pointer text-sm w-20">
                                                {{ __('no') }}
                                            </label>
                                        </div>
                                    </div>
                                </fieldset>
                                <span class="error-message text-xs text-red-500 mt-1 block h-4"
                                    data-field="insurance_property"></span>
                            </div>

                            <!-- SMS Consent -->
                            <div class="mt-6">
                                <label class="inline-flex items-start cursor-pointer">
                                    <input id="sms_consent" name="sms_consent" type="checkbox" value="1"
                                        class="checkbox-field form-checkbox text-yellow-500 mt-1 h-5 w-5 border-gray-300 rounded focus:ring-yellow-500">
                                    <span class="ml-2 text-sm text-gray-600">
                                        {!! __('sms_consent_text') !!}
                                        <strong>{{ \App\Helpers\PhoneHelper::format($companyData->phone) }}</strong>
                                        <a href="{{ route('privacy-policy') }}" target="_blank"
                                            class="text-yellow-500 font-bold hover:text-yellow-600 no-underline">{{ __('privacy_policy') }}</a>
                                        {{ __('and') }} <a href="{{ route('terms-and-conditions') }}"
                                            target="_blank"
                                            class="text-yellow-500 font-bold hover:text-yellow-600 no-underline">{{ __('terms_of_service') }}</a>.
                                    </span>
                                </label>
                                <span class="error-message text-xs text-red-500 mt-1 block h-4"
                                    data-field="sms_consent"></span>
                            </div>

                            <!-- Submit Button -->
                            <div class="pt-6 mt-4 text-center">
                                <button type="submit" id="submit-button"
                                    class="group relative overflow-hidden bg-yellow-500 hover:bg-yellow-600 focus:ring-4 focus:ring-yellow-300 inline-flex items-center px-7 py-2.5 rounded-lg text-white justify-center disabled:opacity-75 disabled:cursor-not-allowed w-full sm:w-auto"
                                    disabled>
                                    <!-- Spinner -->
                                    <svg id="submit-spinner"
                                        class="hidden z-40 animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    <span id="submit-button-text" class="z-40">{{ __('send_request') }}</span>
                                    <div
                                        class="absolute inset-0 h-[200%] w-[200%] rotate-45 translate-x-[-70%] transition-all group-hover:scale-100 bg-white/30 group-hover:translate-x-[50%] z-20 duration-1000">
                                    </div>
                                </button>
                            </div>

                            <!-- Espacio adicional en la parte inferior -->
                            <div class="pb-6"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @once
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Resto del código JavaScript del formulario aquí...
                // (Todo el código JavaScript original debe copiarse dentro de este bloque)
                const form = document.getElementById('facebook-lead-form');
                const submitButton = document.getElementById('submit-button');
                const submitSpinner = document.getElementById('submit-spinner');
                const submitButtonText = document.getElementById('submit-button-text');
                const successMessageDiv = document.getElementById('success-message');
                const modalGeneralErrorDiv = document.getElementById('general-error-message');
                const csrfToken = document.querySelector('input[name="_token"]')?.value;
                const allInputs = form.querySelectorAll('.input-field, .radio-field, .checkbox-field');
                const firstNameInput = document.getElementById('first_name');
                const lastNameInput = document.getElementById('last_name');
                const phoneInput = document.getElementById('phone');
                const requiredInputs = form.querySelectorAll('[required]');
                let successTimeoutId = null;
                let errorTimeoutId = null;
                let map;
                let marker;

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

                // Implementar las funciones adicionales como initializeMap, initAutocomplete, etc.
                // ...
                // Puedes incluir todas las funciones de la versión original del formulario
            });

            function initGoogleMapsForModal() {
                console.log('Initializing Google Maps for Modal...');

                // Si Google Maps ya está cargado, inicializa el autocomplete
                if (window.google && window.google.maps && window.google.maps.places) {
                    console.log('Google Maps ya está cargado, inicializando autocomplete...');
                    initializeModalAutocomplete();
                } else {
                    // Si Google Maps no está cargado, carga el script dinámicamente
                    console.log('Google Maps no está cargado, cargando script...');
                    const script = document.createElement('script');
                    script.src =
                        `https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places&callback=initializeModalAutocomplete`;
                    script.async = true;
                    script.defer = true;
                    document.head.appendChild(script);
                }
            }

            function initializeModalAutocomplete() {
                console.log('Executing initializeModalAutocomplete...');
                const form = document.getElementById('facebook-lead-form');
                const addressMapInput = document.getElementById('address_map_input');
                const mapContainer = document.getElementById('location-map');

                if (!addressMapInput || !mapContainer) {
                    console.error('Address input or map container not found');
                    // Si los elementos no existen, es posible que el contenido del modal aún no se haya cargado
                    // Esperar un poco e intentar de nuevo
                    setTimeout(initializeModalAutocomplete, 500);
                    return;
                }

                console.log('Initializing map...');
                // Inicializar mapa
                const map = new google.maps.Map(mapContainer, {
                    center: {
                        lat: 37.0902,
                        lng: -95.7129
                    }, // Centro de USA por defecto
                    zoom: 4,
                    mapTypeControl: false,
                    streetViewControl: false,
                    fullscreenControl: false,
                    zoomControl: true
                });

                // Crear marcador (inicialmente oculto)
                const marker = new google.maps.Marker({
                    map: map,
                    visible: false
                });

                console.log('Initializing autocomplete...');
                // Inicializar autocomplete
                const autocomplete = new google.maps.places.Autocomplete(addressMapInput, {
                    types: ['address'],
                    componentRestrictions: {
                        country: 'us'
                    },
                    fields: ['address_components', 'geometry', 'formatted_address']
                });

                // Cuando se selecciona un lugar
                autocomplete.addListener('place_changed', function() {
                    console.log('Place changed event fired');
                    const place = autocomplete.getPlace();

                    if (!place.geometry) {
                        console.log("No details available for this place");
                        return;
                    }

                    // Obtener coordenadas
                    const lat = place.geometry.location.lat();
                    const lng = place.geometry.location.lng();

                    // Establecer coordenadas en campos ocultos
                    document.getElementById('latitude').value = lat;
                    document.getElementById('longitude').value = lng;

                    // Actualizar mapa con la ubicación seleccionada
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

                    // Llenar componentes de dirección
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

                    // Usar formatted_address si está disponible
                    if (place.formatted_address) {
                        addressMapInput.value = place.formatted_address;
                    }

                    // Llenar campos ocultos
                    if (addressLine1) document.getElementById('address').value = addressLine1;
                    if (city) document.getElementById('city').value = city;
                    if (state) document.getElementById('state').value = state;
                    if (zipcode) document.getElementById('zipcode').value = zipcode;
                    document.getElementById('country').value = 'USA';

                    // Disparar eventos de validación
                    const validateEvent = new Event('change', {
                        bubbles: true
                    });
                    addressMapInput.dispatchEvent(validateEvent);

                    // Copiar valor a campos ocultos para validación
                    ['address', 'city', 'state', 'zipcode'].forEach(fieldId => {
                        const hiddenField = document.getElementById(fieldId);
                        if (hiddenField) {
                            hiddenField.dispatchEvent(new Event('change', {
                                bubbles: true
                            }));
                        }
                    });

                    // Validar el campo del formulario
                    setupFormValidation();
                });

                // También configuramos el resto de funcionalidades del formulario
                setupFormValidation();
            }

            function setupFormValidation() {
                // Only configura si el form está presente
                const form = document.getElementById('facebook-lead-form');
                if (!form) return;

                // Verify if event listeners are already attached
                if (form.dataset.validationInitialized === 'true') return;

                console.log('Setting up form validation...');

                const submitButton = document.getElementById('submit-button');
                const submitSpinner = document.getElementById('submit-spinner');
                const submitButtonText = document.getElementById('submit-button-text');
                const successMessageDiv = document.getElementById('success-message');
                const modalGeneralErrorDiv = document.getElementById('general-error-message');
                const csrfToken = document.querySelector('input[name="_token"]')?.value;
                const recaptchaInput = document.getElementById('modal-g-recaptcha-response'); // Get recaptcha input
                const allInputs = form.querySelectorAll('.input-field, .radio-field, .checkbox-field');
                const firstNameInput = document.getElementById('first_name');
                const lastNameInput = document.getElementById('last_name');
                const phoneInput = document.getElementById('phone');
                const requiredInputs = form.querySelectorAll('[required]');
                let successTimeoutId = null;
                let errorTimeoutId = null;

                // Ensure displayErrors function is defined
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

                // Helper Functions
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
                    modalGeneralErrorDiv.classList.add('hidden');
                    modalGeneralErrorDiv.textContent = '';

                    checkFormValidity();
                }

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

                // Set up formatters
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
                        formattedValue = `(${value.substring(0, 3)}) ${value.substring(3, 6)}-${value.substring(6, 10)}`;
                    }
                    inputElement.value = formattedValue;

                    // Trigger validation after formatting
                    const form = document.getElementById('facebook-lead-form');
                    if (form && typeof validateField === 'function') {
                        validateField(inputElement);
                    }
                }

                // Event Listeners
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

                // Setup other inputs
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

                // Modal form submission handling
                form.addEventListener('submit', function(event) {
                    event.preventDefault();

                    // Disable button immediately to prevent multiple clicks
                    setLoadingState(true);
                    clearAllErrors();

                    // Hide previous general errors if any
                    if (modalGeneralErrorDiv) {
                        modalGeneralErrorDiv.classList.add('hidden');
                        modalGeneralErrorDiv.textContent = '';
                    }

                    // Execute reCAPTCHA safely using our helper function
                    executeModalRecaptcha('submit_modal_lead')
                        .then(function(token) {
                            // Token is now already set in the form by the executeModalRecaptcha function
                            // Now submit the form data via fetch
                            submitModalFormData(form, csrfToken);
                        })
                        .catch(function(error) {
                            console.error('reCAPTCHA execution failed:', error);

                            // Show error with SweetAlert if available
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    title: '{{ __('swal_verification_error') }}',
                                    text: '{{ __('swal_could_not_verify') }}',
                                    icon: 'error',
                                    confirmButtonText: '{{ __('swal_ok') }}',
                                    confirmButtonColor: '#f59e0b'
                                });
                            } else {
                                // Fallback to alert if SweetAlert is not available
                                alert('{{ __('swal_could_not_verify') }}');
                            }

                            setLoadingState(false); // Re-enable button on reCAPTCHA error
                        });
                });

                // Loading state
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

                // Initial check
                checkFormValidity();

                // Mark as initialized
                form.dataset.validationInitialized = 'true';
            }

            // Make functions available globally
            window.initializeModalAutocomplete = initializeModalAutocomplete;
            window.initGoogleMapsForModal = initGoogleMapsForModal;

            // Separate function for actual modal form submission
            function submitModalFormData(form, csrfToken) {
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
                            title: '{{ __('swal_address_incomplete') }}',
                            text: '{{ __('swal_select_complete_address') }}',
                            icon: 'warning',
                            confirmButtonText: '{{ __('swal_ok') }}',
                            confirmButtonColor: '#f59e0b'
                        });
                        // Find setLoadingState or define it within scope if needed
                        // setLoadingState(false); // Assuming setLoadingState is accessible
                        document.getElementById('submit-button').disabled = false; // Directly disable/enable
                        document.getElementById('submit-spinner').classList.add('hidden');
                        return;
                    }
                }

                const formData = new FormData(form);
                const submitButton = document.getElementById('submit-button'); // Get button inside function if needed

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
                        if (!response.ok && !(contentType && contentType.indexOf("application/json") !== -1 && response
                                .status === 422)) {
                            if (response.status === 403) { // Assuming 403 for potential reCAPTCHA failure indication
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
                        // Assuming displayErrors and other functions are accessible
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
                                // displayErrors(body.errors); // Make sure displayErrors is defined/accessible
                                // Temp alert for modal context
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
                            // Show success message (e.g., using SweetAlert)
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
                                    // Reset other elements like map marker, radio buttons if needed
                                },
                                willClose: () => {
                                    // Optionally close the modal
                                    // Find the Alpine.js component controlling the modal and set showFacebookLeadModal = false
                                    const modalComponent = form.closest('[x-data]');
                                    if (modalComponent && modalComponent.__x) {
                                        modalComponent.__x.data.showFacebookLeadModal = false;
                                    }
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
                        console.error('Modal Submission failed:', error);
                        Swal.fire({
                            title: '{{ __('swal_submission_error') }}',
                            text: error.message.includes('Security check failed') ?
                                '{{ __('swal_security_check_failed') }}' : '{{ __('swal_network_error') }}',
                            icon: 'error',
                            confirmButtonText: '{{ __('swal_ok') }}',
                            confirmButtonColor: '#f59e0b'
                        });
                    })
                    .finally(() => {
                        // Re-enable button
                        // Find setLoadingState or define it within scope if needed
                        // setLoadingState(false); // Assuming setLoadingState is accessible
                        document.getElementById('submit-button').disabled = false; // Directly disable/enable
                        document.getElementById('submit-spinner').classList.add('hidden');
                        document.getElementById('submit-button-text').textContent = '{{ __('send_request') }}';
                    });
            }

            // Function to get reCAPTCHA token safely
            function executeModalRecaptcha(action) {
                console.log('[executeModalRecaptcha] Called with action:', action);
                return new Promise((resolve, reject) => {
                    if (typeof grecaptcha === 'undefined') {
                        console.error('[executeModalRecaptcha] Error: reCAPTCHA API not loaded');

                        // Intentar cargar reCAPTCHA y reintentar
                        const script = document.createElement('script');
                        script.src = 'https://www.google.com/recaptcha/api.js?render={{ config('captcha.sitekey') }}';
                        script.async = true;
                        script.defer = true;
                        script.onload = function() {
                            console.log('[executeModalRecaptcha] reCAPTCHA script loaded, retrying...');
                            window.recaptchaLoaded = true;
                            setTimeout(() => {
                                executeModalRecaptcha(action).then(resolve).catch(reject);
                            }, 1000);
                        };
                        document.head.appendChild(script);
                        return;
                    }

                    try {
                        grecaptcha.ready(function() {
                            console.log('[executeModalRecaptcha] grecaptcha.ready callback fired.');
                            console.log('[executeModalRecaptcha] Attempting to execute with key:',
                                '{{ config('captcha.sitekey') }}');

                            // Asegurarse de que haya un pequeño retraso antes de ejecutar
                            setTimeout(() => {
                                grecaptcha.execute('{{ config('captcha.sitekey') }}', {
                                        action: action
                                    })
                                    .then(token => {
                                        console.log('[executeModalRecaptcha] Token received:',
                                            token ? 'success (length: ' + token.length + ')' :
                                            'null/undefined');

                                        if (!token) {
                                            console.error(
                                                '[executeModalRecaptcha] Received empty token');
                                            reject(new Error('Empty reCAPTCHA token'));
                                            return;
                                        }

                                        document.getElementById('modal-g-recaptcha-response')
                                            .value = token;
                                        resolve(token);
                                    })
                                    .catch(error => {
                                        console.error(
                                            '[executeModalRecaptcha] grecaptcha.execute() failed:',
                                            error);
                                        reject(error);
                                    });
                            }, 500);
                        });
                    } catch (error) {
                        console.error('[executeModalRecaptcha] Error during ready/execute:', error);
                        reject(error);
                    }
                });
            }
        </script>
    @endonce

    @once
        <style>
            /* Basic styling for error indication */
            .border-red-500 {
                border-color: #f56565 !important;
            }

            .error-message {
                min-height: 1rem;
            }

            /* Radio buttons styling */
            .insurance-label {
                transition: all 0.2s ease;
                background-color: white;
            }

            .insurance-label:hover {
                background-color: #facc15 !important;
                color: white !important;
                border-color: #eab308 !important;
            }

            .insurance-label.selected {
                background-color: #f59e0b !important;
                color: white !important;
                border-color: #d97706 !important;
            }
        </style>
    @endonce

    <!-- Google Maps API Script -->
    @once
        <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places"
            defer></script>
    @endonce

    <!-- SweetAlert2 for alerts -->
    @once
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @endonce

    <!-- reCAPTCHA v3 script -->
    @once
        {{-- Add reCAPTCHA v3 script --}}
        <script src="https://www.google.com/recaptcha/api.js?render={{ config('captcha.sitekey') }}" async defer></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Global variable to hold the reCAPTCHA site key
                window.recaptchaSiteKey = '{{ config('captcha.sitekey') }}';
                window.recaptchaLoaded = true;
            });
        </script>
    @endonce
</div>
