<!-- Contact Form Section -->
<div class="bg-white py-16 fade-in-section">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Get Your Free Inspection</h2>
                <p class="text-lg text-gray-600">Fill out the form below and our team will contact you shortly to
                    schedule your free inspection.</p>
            </div>

            <form x-data="{
                formData: {
                    name: '',
                    phone: '',
                    email: '',
                    city: '',
                    zipcode: '',
                    insurance: '',
                    message: '',
                    sms_consent: false
                },
                errors: {},
                validateField(field) {
                    switch (field) {
                        case 'name':
                            if (!this.formData.name) {
                                this.errors.name = '{{ __('name_required') }}';
                            } else if (!/^[A-Za-z\s]+$/.test(this.formData.name)) {
                                this.errors.name = 'Name must contain only letters';
                            } else {
                                delete this.errors.name;
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
                        case 'city':
                            if (!this.formData.city) {
                                this.errors.city = '{{ __('city_required') }}';
                            } else {
                                delete this.errors.city;
                            }
                            break;
                        case 'zipcode':
                            if (!this.formData.zipcode) {
                                this.errors.zipcode = '{{ __('zipcode_required') }}';
                            } else {
                                delete this.errors.zipcode;
                            }
                            break;
                        case 'insurance':
                            if (!this.formData.insurance) {
                                this.errors.insurance = 'Please select an option';
                            } else {
                                delete this.errors.insurance;
                            }
                            break;
                    }
                },
                validateForm() {
                    this.errors = {};
                    ['name', 'phone', 'email', 'city', 'zipcode', 'insurance'].forEach(field => this.validateField(field));
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
                }
            }" @submit.prevent="if(validateForm()) $el.submit()" action="#" method="POST"
                class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name Field -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input type="text" name="name" id="name" x-model="formData.name"
                            @input="validateField('name')"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition"
                            :class="{ 'border-red-500': errors.name }">
                        <span x-show="errors.name" x-text="errors.name" class="text-red-500 text-sm mt-1"></span>
                    </div>

                    <!-- Phone Field -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input type="tel" name="phone" id="phone" x-model="formData.phone"
                            @input="formatPhone($event)"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition"
                            :class="{ 'border-red-500': errors.phone }">
                        <span x-show="errors.phone" x-text="errors.phone" class="text-red-500 text-sm mt-1"></span>
                    </div>

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" id="email" x-model="formData.email"
                            @input="validateField('email')"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition"
                            :class="{ 'border-red-500': errors.email }">
                        <span x-show="errors.email" x-text="errors.email" class="text-red-500 text-sm mt-1"></span>
                    </div>

                    <!-- City Field -->
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City</label>
                        <select name="city" id="city" x-model="formData.city" @change="validateField('city')"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition"
                            :class="{ 'border-red-500': errors.city }">
                            <option value="">Select a city</option>
                            <option value="Houston">Houston</option>
                            <option value="Dallas">Dallas</option>
                        </select>
                        <span x-show="errors.city" x-text="errors.city" class="text-red-500 text-sm mt-1"></span>
                    </div>

                    <!-- Zip Code Field -->
                    <div>
                        <label for="zipcode" class="block text-sm font-medium text-gray-700 mb-1">Zip Code</label>
                        <input type="text" name="zipcode" id="zipcode" x-model="formData.zipcode"
                            @input="validateField('zipcode')"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition"
                            :class="{ 'border-red-500': errors.zipcode }">
                        <span x-show="errors.zipcode" x-text="errors.zipcode" class="text-red-500 text-sm mt-1"></span>
                    </div>

                    <!-- Insurance Property Field -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Insurance Property</label>
                        <div class="flex space-x-4">
                            <label class="inline-flex items-center">
                                <input type="radio" name="insurance" value="yes" x-model="formData.insurance"
                                    @change="validateField('insurance')" class="form-radio text-yellow-500 h-5 w-5">
                                <span class="ml-2">Yes</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="insurance" value="no" x-model="formData.insurance"
                                    @change="validateField('insurance')" class="form-radio text-yellow-500 h-5 w-5">
                                <span class="ml-2">No</span>
                            </label>
                        </div>
                        <span x-show="errors.insurance" x-text="errors.insurance"
                            class="text-red-500 text-sm mt-1"></span>
                    </div>

                    <!-- Message Field -->
                    <div class="md:col-span-2">
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Comment or
                            Message</label>
                        <textarea name="message" id="message" rows="4" x-model="formData.message"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition resize-none"></textarea>
                    </div>
                </div>

                <!-- SMS Consent Checkbox -->
                <div class="mt-6">
                    <label class="inline-flex items-start">
                        <input type="checkbox" name="sms_consent" x-model="formData.sms_consent"
                            class="form-checkbox text-yellow-500 mt-1">
                        <span class="ml-2 text-sm text-gray-600">
                            {!! __('sms_consent_text') !!}
                            <strong>{{ \App\Helpers\PhoneHelper::format($companyData->phone) }}</strong>
                            <a href="{{ route('privacy-policy') }}" target="_blank"
                                class="text-yellow-500 hover:text-yellow-600 underline">{{ __('privacy_policy') }}</a>
                            {{ __('and') }} <a href="{{ route('terms-and-conditions') }}" target="_blank"
                                class="text-yellow-500 hover:text-yellow-600 underline">{{ __('terms_of_service') }}</a>.
                        </span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="text-center mt-8">
                    <x-primary-button type="submit" x-bind:disabled="Object.keys(errors).length > 0"
                        x-data="{ loading: false }" @click="if(validateForm()) loading = true">
                        <span x-show="!loading">Schedule Free Inspection</span>
                        <span x-show="loading" class="inline-flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Processing...
                        </span>
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</div>
