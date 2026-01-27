<div class="bg-white py-8 fade-in-section relative" x-data="{
    formData: {
        first_name: '',
        last_name: '',
        phone: '',
        email: '',
        address: '',
        address_2: '',
        city: '',
        state: '',
        zipcode: '',
        country: 'USA',
        insurance_property: '',
        message: '',
        sms_consent: false,
        latitude: null,
        longitude: null
    },
    errors: {},
    get hasAddressError() {
        return !!this.errors.address;
    },
    formatName(field) {
        let value = this.formData[field];
        if (typeof value === 'string' && value.length > 0) {
            let parts = value.trim().split(' ');
            let firstWord = parts[0];
            this.formData[field] = firstWord.charAt(0).toUpperCase() + firstWord.slice(1).toLowerCase();
        }
        this.validateField(field);
    },
    validateField(field) {
        switch (field) {
            case 'first_name':
                if (!this.formData.first_name) {
                    this.errors.first_name = '{{ __('first_name_required') }}';
                } else if (!/^[A-Za-z'-]+$/.test(this.formData.first_name)) {
                    this.errors.first_name = 'First Name must contain only letters, hyphens, or apostrophes (no spaces)';
                } else {
                    delete this.errors.first_name;
                }
                break;
            case 'last_name':
                if (!this.formData.last_name) {
                    this.errors.last_name = '{{ __('last_name_required') }}';
                } else if (!/^[A-Za-z'-]+$/.test(this.formData.last_name)) {
                    this.errors.last_name = 'Last Name must contain only letters, hyphens, or apostrophes (no spaces)';
                } else {
                    delete this.errors.last_name;
                }
                break;
            case 'phone':
                if (!this.formData.phone) {
                    this.errors.phone = '{{ __('phone_required') }}';
                } else if (!/^\(\d{3}\)\s\d{3}-\d{4}$/.test(this.formData.phone)) {
                    this.errors.phone = 'Phone must be in format (XXX) XXX-XXXX';
                } else {
                    delete this.errors.phone;
                }
                break;
            case 'email':
                if (!this.formData.email) {
                    this.errors.email = '{{ __('email_required') }}';
                } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.formData.email)) {
                    this.errors.email = 'Please enter a valid email';
                } else {
                    delete this.errors.email;
                }
                break;
            case 'address':
                if (!this.formData.address) {
                    this.errors.address = '{{ __('address_required') }}';
                } else {
                    delete this.errors.address;
                }
                break;
            case 'city':
                if (!this.formData.city) {
                    this.errors.city = '{{ __('city_required') }}';
                } else {
                    delete this.errors.city;
                }
                break;
            case 'state':
                if (!this.formData.state) {
                    this.errors.state = '{{ __('state_required') }}';
                } else {
                    delete this.errors.state;
                }
                break;
            case 'zipcode':
                if (!this.formData.zipcode) {
                    this.errors.zipcode = '{{ __('zipcode_required') }}';
                } else if (!/^\d{5}(-\d{4})?$/.test(this.formData.zipcode)) {
                    this.errors.zipcode = 'Please enter a valid US zip code (5 or 9 digits)';
                } else {
                    delete this.errors.zipcode;
                }
                break;
            case 'country':
                if (!this.formData.country) {
                    this.errors.country = '{{ __('country_required') }}';
                } else {
                    delete this.errors.country;
                }
                break;
            case 'insurance_property':
                if (!this.formData.insurance_property) {
                    this.errors.insurance_property = 'Please select an option';
                } else {
                    delete this.errors.insurance_property;
                }
                break;
        }
    },
    validateForm() {
        this.errors = {};
        ['first_name', 'last_name', 'phone', 'email', 'address', 'city', 'state', 'zipcode', 'country', 'insurance_property'].forEach(field => this.validateField(field));
        return Object.keys(this.errors).length === 0;
    },
    formatPhone(e) {
        if (e.inputType === 'deleteContentBackward') {
            let value = this.formData.phone.replace(/\D/g, '');
            value = value.substring(0, value.length - 1);

            if (value.length === 0) {
                this.formData.phone = '';
            } else if (value.length <= 3) {
                this.formData.phone = `(${value}`;
            } else if (value.length <= 6) {
                this.formData.phone = `(${value.substring(0,3)}) ${value.substring(3)}`;
            } else {
                this.formData.phone = `(${value.substring(0,3)}) ${value.substring(3,6)}-${value.substring(6)}`;
            }
            this.validateField('phone');
            return;
        }

        let value = e.target.value.replace(/\D/g, '').substring(0, 10);
        if (value.length >= 6) {
            this.formData.phone = `(${value.substring(0,3)}) ${value.substring(3,6)}-${value.substring(6)}`;
        } else if (value.length >= 3) {
            this.formData.phone = `(${value.substring(0,3)}) ${value.substring(3)}`;
        } else if (value.length > 0) {
            this.formData.phone = `(${value}`;
        }
        this.validateField('phone');
    },
    initAutocomplete() {
        console.log('Attempting to initialize Google Maps Place Autocomplete Element...');

        const placeAutocompleteElement = document.getElementById('address-input');
        if (!placeAutocompleteElement || !google.maps.places.PlaceAutocompleteElement) {
            console.error('Place Autocomplete Element not found or Google Maps Places library not ready.');
            return;
        }

        if (placeAutocompleteElement.getAttribute('listener-attached')) {
            console.log('Listener already attached.');
            return;
        }

        try {
            // Set strict US-only restriction
            if (google.maps.places.PlaceAutocompleteElement.prototype.setOptions) {
                placeAutocompleteElement.setOptions({
                    componentRestrictions: { country: 'us' },
                    types: ['address']
                });
            }

            console.log('Attaching gmp-placechange listener...');
            placeAutocompleteElement.addEventListener('gmp-placechange', ({ target }) => {
                const place = target.place;
                console.log('--- gmp-placechange event fired ---');
                const placeAutocompleteInput = target;

                delete this.errors.address;
                delete this.errors.city;
                delete this.errors.state;
                delete this.errors.zipcode;
                delete this.errors.country;

                let currentAddressInput = placeAutocompleteInput.value || '';
                this.formData.address = currentAddressInput;
                this.formData.city = '';
                this.formData.state = '';
                this.formData.zipcode = '';
                this.formData.country = 'USA';

                console.log('Selected Place object:', place);

                if (!place || !place.addressComponents) {
                    console.warn('Selected item is not a valid place or is missing address components.');
                    $wire.set('address', currentAddressInput);
                    $wire.set('city', '');
                    $wire.set('state', '');
                    $wire.set('zipcode', '');
                    $wire.set('country', 'USA');
                    this.validateField('address');
                    return;
                }

                console.log('Processing Address Components:', place.addressComponents);

                let streetNumber = '';
                let route = '';
                let city = '';
                let state = '';
                let zipcode = '';
                let country = 'USA';

                place.addressComponents.forEach(component => {
                    const types = component.types;
                    const longText = component.longText;
                    const shortText = component.shortText;

                    if (types.includes('street_number')) streetNumber = longText;
                    else if (types.includes('route')) route = longText;
                    else if (types.includes('locality')) city = longText;
                    else if (types.includes('administrative_area_level_1')) state = shortText;
                    else if (types.includes('postal_code')) zipcode = longText;
                    else if (types.includes('country')) country = shortText;
                });

                const finalAddress = ((streetNumber ? streetNumber + ' ' : '') + route).trim();
                this.formData.address = finalAddress;
                this.formData.city = city;
                this.formData.state = state;
                this.formData.zipcode = zipcode;
                this.formData.country = country;

                console.log(`Final Address Data: Addr: ${this.formData.address}, City: ${this.formData.city}, State: ${this.formData.state}, Zip: ${this.formData.zipcode}, Country: ${this.formData.country}`);

                $wire.set('address', finalAddress);
                $wire.set('city', city);
                $wire.set('state', state);
                $wire.set('zipcode', zipcode);
                $wire.set('country', country);

                this.$nextTick(() => {
                    document.getElementById('address-input').value = finalAddress;
                    document.getElementById('city').value = city;
                    document.getElementById('state').value = state;
                    document.getElementById('zipcode').value = zipcode;
                    document.getElementById('country').value = country;

                    this.validateField('address');
                });
            });

            placeAutocompleteElement.setAttribute('listener-attached', 'true');
            console.log('Listener attached successfully.');

        } catch (error) {
            console.error('Error setting up Google Maps Place Autocomplete listener:', error);
        }
    }
}">
    <style>
        gmp-place-autocomplete::part(input) {
            display: block;
            width: 100%;
            padding: 0.5rem 1rem;
            border-width: 1px;
            border-color: #d1d5db;
            border-radius: 0.5rem;
            color: #1f2937;
            transition-property: border-color, box-shadow;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 150ms;
            box-shadow: 0 0 #0000, 0 0 #0000;
            -webkit-appearance: none;
            appearance: none;
        }

        gmp-place-autocomplete::part(input):focus {
            outline: 2px solid transparent;
            outline-offset: 2px;
            --tw-ring-inset: inset;
            --tw-ring-offset-width: 0px;
            --tw-ring-offset-color: #fff;
            --tw-ring-color: #f59e0b;
            --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
            --tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(2px + var(--tw-ring-offset-width)) var(--tw-ring-color);
            box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000);
            border-color: transparent;
        }

        gmp-place-autocomplete[invalid]::part(input) {
            border-color: #ef4444;
        }

        gmp-place-autocomplete[invalid]::part(input):focus {
            --tw-ring-color: #ef4444;
            border-color: transparent;
        }
    </style>

    <button @click="$wire.closeModal()" wire:click="closeModal"
        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-2 hover:bg-red-600 transition-colors duration-200 shadow-lg z-50">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>

    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-white mb-4 bg-yellow-500 py-4 rounded-lg shadow-lg">Get Your Free
                    Inspection</h2>
                <p class="text-lg text-gray-600">Fill out the form below and our team will contact you shortly to
                    schedule your free inspection.</p>
            </div>

            <form wire:submit.prevent="submit"
                @submit.prevent="if(!validateForm()) { console.log('Client validation failed', errors); return false; } else { console.log('Client validation passed, submitting...'); $wire.submit() }"
                class="space-y-6" x-init="console.log('Form x-init running...');
                $watch('$store.maps.loaded', (value) => {
                    console.log('Maps loaded state changed:', value);
                    if (value) {
                        console.log('Maps loaded, calling initAutocomplete...');
                        initAutocomplete();
                    } else {
                        console.log('Maps not loaded yet.');
                    }
                });
                if ($store.maps.loaded) {
                    console.log('Maps already loaded on init, calling initAutocomplete...');
                    initAutocomplete();
                }">
                @csrf

                {{-- Hidden fields for latitude and longitude --}}
                <input type="hidden" x-model="formData.latitude" wire:model.defer="latitude">
                <input type="hidden" x-model="formData.longitude" wire:model.defer="longitude">

                {{-- Success message removed --}}
                {{-- @if ($success)
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6"
                        role="alert">
                        <strong class="font-bold">Success!</strong>
                        <span class="block sm:inline">We've received your request and will contact you shortly.</span>
                    </div>
                @endif --}}

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                        <input type="text" id="first_name" x-model="formData.first_name" wire:model="first_name"
                            @input="formatName('first_name')"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition text-gray-900"
                            :class="{ 'border-red-500': errors.first_name }">
                        <div x-show="errors.first_name" x-text="errors.first_name" class="text-red-500 text-sm mt-1">
                        </div>
                        @error('first_name')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                        <input type="text" id="last_name" x-model="formData.last_name" wire:model="last_name"
                            @input="formatName('last_name')"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition text-gray-900"
                            :class="{ 'border-red-500': errors.last_name }">
                        <div x-show="errors.last_name" x-text="errors.last_name" class="text-red-500 text-sm mt-1">
                        </div>
                        @error('last_name')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input type="tel" id="phone" x-model="formData.phone" wire:model="phone"
                            @input="formatPhone($event)"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition text-gray-900"
                            :class="{ 'border-red-500': errors.phone }">
                        <div x-show="errors.phone" x-text="errors.phone" class="text-red-500 text-sm mt-1"></div>
                        @error('phone')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" id="email" x-model="formData.email" wire:model="email"
                            @input="validateField('email')"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition text-gray-900"
                            :class="{ 'border-red-500': errors.email }">
                        <div x-show="errors.email" x-text="errors.email" class="text-red-500 text-sm mt-1"></div>
                        @error('email')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="address-input" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                        <gmp-place-autocomplete id="address-input" class="block w-full"
                            placeholder="Start typing your street address..." country-codes="us"
                            request-priority="DISTANCE" types="address" :invalid="hasAddressError">
                        </gmp-place-autocomplete>
                        <div x-show="errors.address" x-text="errors.address" class="text-red-500 text-sm mt-1"></div>
                        @error('address')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="address_2" class="block text-sm font-medium text-gray-700 mb-1">Address 2 <span
                                class="text-xs text-gray-500">(Optional, e.g., Apt, Suite, Unit)</span></label>
                        <input type="text" id="address_2" x-model="formData.address_2" wire:model.defer="address_2"
                            placeholder="Apt #, Suite #, etc."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition text-gray-900">
                        @error('address_2')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City</label>
                        <input type="text" id="city" x-model="formData.city" wire:model.defer="city"
                            @input="validateField('city')"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition text-gray-900 bg-gray-100"
                            :class="{ 'border-red-500': errors.city }">
                        <div x-show="errors.city" x-text="errors.city" class="text-red-500 text-sm mt-1"></div>
                        @error('city')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700 mb-1">State</label>
                        <input type="text" id="state" x-model="formData.state" wire:model.defer="state"
                            @input="validateField('state')"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition text-gray-900 bg-gray-100"
                            :class="{ 'border-red-500': errors.state }">
                        <div x-show="errors.state" x-text="errors.state" class="text-red-500 text-sm mt-1"></div>
                        @error('state')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="zipcode" class="block text-sm font-medium text-gray-700 mb-1">Zip Code</label>
                        <input type="text" id="zipcode" x-model="formData.zipcode" wire:model.defer="zipcode"
                            @input="validateField('zipcode'); if(formData.zipcode.length > 5) formData.zipcode = formData.zipcode.slice(0, 5);"
                            maxlength="10" pattern="[0-9-]*" inputmode="numeric"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition text-gray-900 bg-gray-100"
                            :class="{ 'border-red-500': errors.zipcode }">
                        <div x-show="errors.zipcode" x-text="errors.zipcode" class="text-red-500 text-sm mt-1"></div>
                        @error('zipcode')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                        <input type="text" id="country" x-model="formData.country" wire:model.defer="country"
                            @input="validateField('country')"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition text-gray-900 bg-gray-100"
                            :class="{ 'border-red-500': errors.country }" readonly>
                        <div x-show="errors.country" x-text="errors.country" class="text-red-500 text-sm mt-1"></div>
                        @error('country')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Do you have property
                            insurance?</label>
                        <div class="flex space-x-4">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" x-model="formData.insurance_property"
                                    wire:model="insurance_property" value="yes"
                                    @change="validateField('insurance_property')"
                                    class="form-radio text-yellow-500 h-5 w-5">
                                <span class="ml-2 text-gray-700">Yes</span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" x-model="formData.insurance_property"
                                    wire:model="insurance_property" value="no"
                                    @change="validateField('insurance_property')"
                                    class="form-radio text-yellow-500 h-5 w-5">
                                <span class="ml-2 text-gray-700">No</span>
                            </label>
                        </div>
                        <div x-show="errors.insurance_property" x-text="errors.insurance_property"
                            class="text-red-500 text-sm mt-1">
                        </div>
                        @error('insurance_property')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Comment or
                            Message <span class="text-xs text-gray-500">(Optional)</span></label>
                        <textarea id="message" x-model="formData.message" wire:model="message" rows="4"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition resize-none text-gray-900"></textarea>
                        @error('message')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-6">
                    <label class="inline-flex items-start cursor-pointer">
                        <input type="checkbox" x-model="formData.sms_consent" wire:model="sms_consent"
                            class="form-checkbox text-yellow-500 mt-1 h-5 w-5">
                        <span class="ml-2 text-sm text-gray-600">
                            {!! __('sms_consent_text') !!}
                            <strong>{{ \App\Helpers\PhoneHelper::format($companyData->phone) }}</strong>
                            <a href="{{ route('privacy-policy') }}" target="_blank"
                                class="text-yellow-500 hover:text-yellow-600 underline">{{ __('privacy_policy') }}</a>
                            {{ __('and') }} <a href="{{ route('terms-and-conditions') }}" target="_blank"
                                class="text-yellow-500 hover:text-yellow-600 underline">{{ __('terms_of_service') }}</a>.
                        </span>
                    </label>
                    @error('sms_consent')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="text-center mt-8">
                    <button type="submit"
                        class="w-full md:w-auto inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-yellow-500 hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition shadow-lg"
                        wire:loading.attr="disabled" wire:loading.class="opacity-75 cursor-not-allowed">
                        <span wire:loading wire:target="submit" class="animate-spin mr-2">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </span>
                        <span wire:loading.remove>Send Request</span>
                        <span wire:loading>Sending...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @once
        <script
            src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places,maps&callback=initGoogleMaps&v=beta&region=US"
            defer async></script>
    @endonce

</div>

<script>
    document.addEventListener('alpine:init', () => {
        console.log('alpine:init event fired');
        Alpine.store('maps', {
            loaded: false
        });

        Alpine.effect(() => {
            const componentElement = document.querySelector('[x-data]');
            if (!componentElement || !Alpine.closestDataStack(componentElement)) {
                return;
            }
            const errors = Alpine.closestDataStack(componentElement)[0].errors;
            const autocompleteElement = document.getElementById('address-input');
            if (autocompleteElement) {
                if (errors?.address) {
                    autocompleteElement.setAttribute('invalid', '');
                } else {
                    autocompleteElement.removeAttribute('invalid');
                }
            }
        });
    });

    window.initGoogleMaps = function() {
        console.log('Google Maps API script loaded and callback executed.');
        customElements.whenDefined('gmp-place-autocomplete').then(() => {
            console.log('gmp-place-autocomplete custom element is defined.');
            if (Alpine.store('maps')) {
                Alpine.store('maps').loaded = true;
                console.log('Alpine store maps.loaded set to true.');
            } else {
                console.error('Alpine store `maps` not found when trying to set loaded = true.');
            }
        }).catch(error => {
            console.error('Error waiting for gmp-place-autocomplete definition:', error);
        });
    }

    if (!window.Alpine) {
        document.addEventListener('alpine:init', () => {
            if (window.google && google.maps && google.maps.places && google.maps.places
                .PlaceAutocompleteElement) {
                console.log('Fallback: Alpine ready after Maps, ensuring store exists.');
                if (!Alpine.store('maps')) {
                    Alpine.store('maps', {
                        loaded: false
                    });
                }
                if (!Alpine.store('maps').loaded) {
                    window.initGoogleMaps();
                }
            }
        });
    }
</script>
