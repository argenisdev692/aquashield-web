{{-- New Lead Modal Component --}}
<div id="newAppointmentModal" class="fixed inset-0 z-50 overflow-y-auto hidden" style="display: none;">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

        <!-- Modal panel -->
        <div
            class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4">
                <div class="flex items-center justify-center relative">
                    <h3 class="text-lg leading-6 font-medium text-white text-center">
                        {{ __('create_new_lead') }}
                    </h3>
                    <button id="closeNewAppointmentModalBtn"
                        class="absolute right-0 text-white hover:text-gray-200 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <form id="newAppointmentForm" class="bg-white px-6 py-4" action="{{ route('appointment-calendar.store') }}"
                method="POST">
                @csrf

                <!-- Hidden Inputs for Coordinates -->
                <input type="hidden" name="latitude" id="latitude">
                <input type="hidden" name="longitude" id="longitude">
                
                <!-- Hidden Inputs for Appointment Date/Time -->
                <input type="hidden" id="appointmentDate" name="appointment_date">
                <input type="hidden" id="appointmentTime" name="appointment_time">

                <!-- Selected Date and Time Display -->
                <div class="mb-6 bg-gradient-to-r from-purple-50 to-indigo-50 border border-purple-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-medium text-gray-900">{{ __('selected_appointment_time') }}</h4>
                            <input type="text" id="selectedDateTime" readonly
                                class="mt-1 text-lg font-semibold text-purple-700 bg-transparent border-none p-0 focus:ring-0 w-full"
                                placeholder="{{ __('select_time_from_calendar') }}">
                        </div>
                    </div>
                </div>

                <!-- Existing Client Selector -->
                <div id="existingClientSection" class="mb-6">
                    <label for="clientSelector" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('select_client_3_hours') }} <span class="text-red-500">*</span>
                    </label>
                    <select id="clientSelector" name="client_uuid"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">{{ __('please_select_client') }}</option>
                    </select>
                    <span class="error-message text-xs text-red-500 mt-1 block h-4" data-field="client_uuid"></span>
                    
                    <!-- Create New Client Button -->
                    <button type="button" id="createNewClientToggle"
                        class="mt-3 w-full bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 px-4 rounded-md border border-gray-300 transition-colors duration-200 flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        {{ __('create_new_client') }}
                    </button>
                </div>

                <!-- New Client Fields -->
                <div id="newClientSection" class="hidden">
                    <!-- Hide Button -->
                    <div class="mb-4 flex justify-end">
                        <button type="button" id="hideNewClientBtn"
                            class="bg-red-100 hover:bg-red-200 text-red-700 py-2 px-4 rounded-md border border-red-300 transition-colors duration-200 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            {{ __('hide') }}
                        </button>
                    </div>

                    <!-- Basic Information -->
                    <div class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2 mb-6">
                        <!-- First Name -->
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700">
                                {{ __('first_name') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="first_name" name="first_name" required maxlength="50"
                                placeholder="{{ __('first_name_placeholder') }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                autocomplete="given-name">
                            <span class="error-message text-xs text-red-500 mt-1 block h-4"
                                data-field="first_name"></span>
                        </div>

                        <!-- Last Name -->
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700">
                                {{ __('last_name') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="last_name" name="last_name" required maxlength="50"
                                placeholder="{{ __('last_name_placeholder') }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                autocomplete="family-name">
                            <span class="error-message text-xs text-red-500 mt-1 block h-4"
                                data-field="last_name"></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2 mb-6">
                        <!-- Phone -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">
                                {{ __('phone') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" id="phone" name="phone" required maxlength="14"
                                placeholder="{{ __('phone_placeholder') }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                autocomplete="tel">
                            <span class="error-message text-xs text-red-500 mt-1 block h-4" data-field="phone"></span>
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">
                                {{ __('email') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="email" id="email" name="email" required
                                placeholder="{{ __('email_placeholder') }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                autocomplete="email">
                            <span class="error-message text-xs text-red-500 mt-1 block h-4" data-field="email"></span>
                        </div>
                    </div>

                    <!-- Address Map Input Field -->
                    <div class="mb-6">
                        <label for="address_map_input" class="block text-sm font-medium text-gray-700">
                            {{ __('address') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="address_map_input" name="address_map_input" required
                            placeholder="{{ __('enter_complete_address') }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                            autocomplete="off">
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
                    <div class="mb-6">
                        <label for="address_2" class="block text-sm font-medium text-gray-700">
                            {{ __('address_2') }} <span
                                class="text-xs text-gray-500">{{ __('optional_apt_suite') }}</span>
                        </label>
                        <input type="text" id="address_2" name="address_2"
                            placeholder="{{ __('apt_suite_placeholder') }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                            autocomplete="address-line2">
                        <span class="error-message text-xs text-red-500 mt-1 block h-4" data-field="address_2"></span>
                    </div>

                    <!-- Map Display -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('location_map') }}</label>
                        <div id="location-map" class="w-full h-48 bg-gray-200 rounded-lg border border-gray-300">
                            <!-- Map will be initialized here -->
                        </div>
                    </div>

                    <!-- Property Insurance -->
                    <div class="mb-6 text-center">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('property_insurance_question') }} <span class="text-red-500">*</span>
                        </label>
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
                                    <input id="insurance_no" name="insurance_property" type="radio" value="0"
                                        class="radio-field sr-only" required>
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

                    <!-- Lead Source -->
                    <div class="mb-6">
                        <label for="lead_source" class="block text-sm font-medium text-gray-700">
                            {{ __('lead_source') }} <span class="text-red-500">*</span>
                        </label>
                        <select id="lead_source" name="lead_source" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                            <option value="">{{ __('select_lead_source') }}</option>
                            <option value="Website">Website</option>
                            <option value="Facebook Ads">Facebook Ads</option>
                            <option value="Reference">Reference</option>
                            <option value="Retell AI">Retell AI</option>
                        </select>
                        <span class="error-message text-xs text-red-500 mt-1 block h-4"
                            data-field="lead_source"></span>
                    </div>

                    <!-- Intent to Claim -->
                    <div class="mb-6 text-center">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('intent_to_claim') }} <span class="text-xs text-gray-500">{{ __('optional_label') }}</span>
                        </label>
                        <fieldset class="mt-2">
                            <legend class="sr-only">Intent to Claim</legend>
                            <div class="flex items-center justify-center space-x-4">
                                <div class="radio-option flex items-center">
                                    <input id="intent_yes" name="intent_to_claim" type="radio" value="1"
                                        class="radio-field sr-only">
                                    <label for="intent_yes"
                                        class="insurance-label flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md cursor-pointer text-sm w-20">
                                        {{ __('yes') }}
                                    </label>
                                </div>
                                <div class="radio-option flex items-center">
                                    <input id="intent_no" name="intent_to_claim" type="radio" value="0"
                                        class="radio-field sr-only">
                                    <label for="intent_no"
                                        class="insurance-label flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md cursor-pointer text-sm w-20">
                                        {{ __('no') }}
                                    </label>
                                </div>
                            </div>
                        </fieldset>
                        <span class="error-message text-xs text-red-500 mt-1 block h-4"
                            data-field="intent_to_claim"></span>
                    </div>

                    <!-- Damage Detail -->
                    <div class="mb-6">
                        <label for="damage_detail" class="block text-sm font-medium text-gray-700">
                            {{ __('damage_detail') }} <span
                                class="text-xs text-gray-500">{{ __('optional_label') }}</span>
                        </label>
                        <textarea id="damage_detail" name="damage_detail" rows="3" placeholder="{{ __('enter_description') }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"></textarea>
                        <span class="error-message text-xs text-red-500 mt-1 block h-4"
                            data-field="damage_detail"></span>
                    </div>

                    <!-- Notes -->
                    <div class="mb-6">
                        <label for="notes" class="block text-sm font-medium text-gray-700">
                            {{ __('notes') }} <span class="text-xs text-gray-500">{{ __('optional_label') }}</span>
                        </label>
                        <textarea id="notes" name="notes" rows="3" placeholder="{{ __('notes_placeholder') }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"></textarea>
                        <span class="error-message text-xs text-red-500 mt-1 block h-4" data-field="notes"></span>
                    </div>

                    <!-- SMS Consent -->
                    <div class="mb-6">
                        <label class="inline-flex items-start cursor-pointer">
                            <input id="sms_consent" name="sms_consent" type="checkbox" value="1"
                                class="checkbox-field form-checkbox text-yellow-500 mt-1 h-5 w-5 border-gray-300 rounded focus:ring-yellow-500">
                            <span class="ml-2 text-sm text-gray-600">
                                {!! __('sms_consent_modal_lead') !!}
                            </span>
                        </label>
                        <span class="error-message text-xs text-red-500 mt-1 block h-4"
                            data-field="sms_consent"></span>
                    </div>

                    <!-- Inspection Date/Time (Optional) -->
                    <div class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2 mb-6">
                        <div>
                            <label for="inspection_date" class="block text-sm font-medium text-gray-700">
                                {{ __('inspection_date') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="inspection_date" name="inspection_date"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                            <span class="error-message text-xs text-red-500 mt-1 block h-4"
                                data-field="inspection_date"></span>
                        </div>

                        <div>
                            <label for="inspection_time" class="block text-sm font-medium text-gray-700">
                                {{ __('inspection_time') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="time" id="inspection_time" name="inspection_time"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                            <span class="error-message text-xs text-red-500 mt-1 block h-4"
                                data-field="inspection_time"></span>
                        </div>
                    </div>

                </div> <!-- End newClientSection -->
            </form>

            <!-- Modal Footer -->
            <div class="bg-gray-50 px-6 py-3">
                <div class="flex justify-center space-x-3">
                    <button id="closeNewAppointmentModalBtn2" type="button"
                        class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        {{ __('cancel') }}
                    </button>
                    <button id="createAppointmentBtn" type="button"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        <span class="normal-btn-text flex items-center">
                            <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            <span id="createBtnText">{{ __('create_lead') }}</span>
                        </span>
                        <span class="loading-btn-text hidden flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            {{ __('creating') }}...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Additional event listener for the second close button
    document.addEventListener('DOMContentLoaded', function() {
        const closeBtn2 = document.getElementById('closeNewAppointmentModalBtn2');
        if (closeBtn2) {
            closeBtn2.addEventListener('click', function() {
                if (window.CalendarModals) {
                    window.CalendarModals.closeNewAppointmentModal();
                }
            });
        }
    });
</script>
