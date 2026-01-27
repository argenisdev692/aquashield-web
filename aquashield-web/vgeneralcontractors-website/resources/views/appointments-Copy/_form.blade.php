@csrf
{{-- Display validation errors --}}
@if ($errors->any())
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative dark:bg-red-900 dark:border-red-700 dark:text-red-300"
        role="alert">
        <strong class="font-bold">Validation Error!</strong>
        <ul class="mt-2 list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    {{-- First Name --}}
    <div>
        <label for="first_name" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
            {{ __('first_name') }} <span class="text-red-500">*</span>
        </label>
        <x-input id="first_name" class="block mt-1 w-full capitalize" type="text" name="first_name" :value="old('first_name', $appointment->first_name ?? '')"
            autofocus maxlength="50" pattern="[A-Za-z\s\\'-]+" title="{{ __('first_name_validation') }}" />
        <x-input-error for="first_name" class="mt-2" />
    </div>

    {{-- Last Name --}}
    <div>
        <label for="last_name" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
            {{ __('last_name') }} <span class="text-red-500">*</span>
        </label>
        <x-input id="last_name" class="block mt-1 w-full capitalize" type="text" name="last_name" :value="old('last_name', $appointment->last_name ?? '')"
            maxlength="50" pattern="[A-Za-z\s\\'-]+" title="{{ __('last_name_validation') }}" />
        <x-input-error for="last_name" class="mt-2" />
    </div>

    {{-- Phone --}}
    <div>
        <label for="phone" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
            {{ __('phone') }} <span class="text-red-500">*</span>
        </label>
        <x-input id="phone" class="block mt-1 w-full" type="tel" name="phone" placeholder="(XXX) XXX-XXXX"
            :value="old('phone', $appointment->phone ?? '')" />
        <x-input-error for="phone" class="mt-2" />
    </div>

    {{-- Email --}}
    <div>
        <label for="email" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
            {{ __('email') }} <span class="text-red-500">*</span>
        </label>
        <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $appointment->email ?? '')" />
        <x-input-error for="email" class="mt-2" />
    </div>

    {{-- Address Map Input (for Google Maps Autocomplete) --}}
    <div class="md:col-span-2">
        <label for="address_map_input" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
            {{ __('address') }} <span class="text-red-500">*</span>
        </label>
        <x-input id="address_map_input" class="block mt-1 w-full" type="text" name="address_map_input"
            placeholder="Enter complete address for autocomplete" :value="old('address', $appointment->address ?? '')" autocomplete="off" />
        <x-input-error for="address_map_input" class="mt-2" />
    </div>

    {{-- Map Display --}}
    <div class="md:col-span-2 mt-2 mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Location Map</label>
        <div id="location-map"
            class="w-full h-48 bg-gray-200 rounded-lg border border-gray-300 dark:bg-gray-700 dark:border-gray-600">
        </div>

        {{-- Share location buttons --}}
        <div class="mt-2 flex flex-wrap gap-2">
            <button type="button" id="show-address-modal"
                class="inline-flex items-center px-3 py-2 bg-indigo-500 text-white text-sm font-medium rounded-md hover:bg-indigo-600">
                <svg class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                {{ __('view_complete_address') }}
            </button>
            <button type="button" id="share-location-options"
                class="inline-flex items-center px-3 py-2 bg-blue-500 text-white text-sm font-medium rounded-md hover:bg-blue-600">
                <svg class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z" />
                </svg>
                {{ __('choose_how_share_location') }}
            </button>
        </div>

        {{-- Share options dropdown (hidden by default) --}}
        <div id="share-options" class="hidden mt-2">
            <a href="#" id="share-whatsapp"
                class="inline-flex items-center px-3 py-2 bg-green-500 text-white text-sm font-medium rounded-md hover:bg-green-600">
                <svg class="h-5 w-5 mr-1" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M17.498 14.382l-1.87-1.147c-0.308-0.24-0.705-0.242-1.058-0.046l-1.103 0.69c-0.26 0.16-0.563 0.217-0.858 0.147c-0.893-0.216-2.404-1.511-3.122-2.251c-0.483-0.502-1.038-1.489-1.254-2.362c-0.09-0.351 0.014-0.721 0.269-0.974l0.74-0.73c0.243-0.241 0.37-0.567 0.354-0.904l-0.075-1.78c-0.035-0.843-0.913-1.384-1.693-1.041l-0.807 0.353c-0.905 0.405-1.467 1.268-1.457 2.241c0.01 0.935 0.307 3.375 2.301 6.123c2.035 2.809 4.372 3.526 5.338 3.628c0.975 0.103 1.926-0.621 2.251-1.505l0.282-0.861c0.256-0.788-0.343-1.623-1.238-1.724z" />
                </svg>
                WhatsApp
            </a>
            <a href="#" id="share-email"
                class="inline-flex items-center px-3 py-2 bg-blue-500 text-white text-sm font-medium rounded-md hover:bg-blue-600">
                <svg class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Email
            </a>
            <a href="#" id="share-maps"
                class="inline-flex items-center px-3 py-2 bg-red-500 text-white text-sm font-medium rounded-md hover:bg-red-600">
                <svg class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                {{ __('open_in_maps') }}
            </a>
            <button type="button" id="copy-address"
                class="inline-flex items-center px-3 py-2 bg-gray-500 text-white text-sm font-medium rounded-md hover:bg-gray-600">
                <svg class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
                {{ __('copy_address') }}
            </button>
        </div>
    </div>

    {{-- Hidden Address Fields --}}
    <input type="hidden" id="address" name="address" value="{{ old('address', $appointment->address ?? '') }}">
    <input type="hidden" id="latitude" name="latitude"
        value="{{ old('latitude', $appointment->latitude ?? '') }}">
    <input type="hidden" id="longitude" name="longitude"
        value="{{ old('longitude', $appointment->longitude ?? '') }}">

    {{-- Address 2 --}}
    <div class="md:col-span-2">
        <label for="address_2" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
            {{ __('address_2') }}
        </label>
        <x-input id="address_2" class="block mt-1 w-full" type="text" name="address_2"
            placeholder="Apt #, Suite #, etc." :value="old('address_2', $appointment->address_2 ?? '')" />
        <x-input-error for="address_2" class="mt-2" />
    </div>

    {{-- City --}}
    <div>
        <label for="city" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
            {{ __('city') }} <span class="text-red-500">*</span>
        </label>
        <x-input id="city" class="block mt-1 w-full" type="text" name="city" :value="old('city', $appointment->city ?? '')" />
        <x-input-error for="city" class="mt-2" />
    </div>

    {{-- State --}}
    <div>
        <label for="state" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
            {{ __('state') }} <span class="text-red-500">*</span>
        </label>
        <x-input id="state" class="block mt-1 w-full" type="text" name="state" :value="old('state', $appointment->state ?? '')" />
        <x-input-error for="state" class="mt-2" />
    </div>

    {{-- Zipcode --}}
    <div>
        <label for="zipcode" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
            {{ __('zip_code') }} <span class="text-red-500">*</span>
        </label>
        <x-input id="zipcode" class="block mt-1 w-full" type="text" name="zipcode" :value="old('zipcode', $appointment->zipcode ?? '')" />
        <x-input-error for="zipcode" class="mt-2" />
    </div>

    {{-- Country --}}
    <div>
        <label for="country" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
            {{ __('country') }} <span class="text-red-500">*</span>
        </label>
        <x-input id="country" class="block mt-1 w-full" type="text" name="country" :value="old('country', $appointment->country ?? 'USA')" />
        <x-input-error for="country" class="mt-2" />
    </div>

    {{-- Inspection Date --}}
    <div>
        <label for="inspection_date" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
            {{ __('inspection_date') }}
        </label>
        <x-input id="inspection_date" class="block mt-1 w-full" type="date" name="inspection_date"
            min="{{ date('Y-m-d') }}" :value="old(
                'inspection_date',
                isset($appointment->inspection_date) ? $appointment->inspection_date->format('Y-m-d') : '',
            )" />
        <x-input-error for="inspection_date" class="mt-2" />
    </div>

    {{-- Inspection Time --}}
    <div>
        <label for="inspection_time_hour" class="block font-medium text-sm text-gray-700 dark:text-gray-300"
            id="inspection_time_label">
            {{ __('inspection_time') }}
        </label>
        <div class="flex mt-1 space-x-2">
            <select id="inspection_time_hour" name="inspection_time_hour"
                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                <option value="">Hour</option>
                @for ($hour = 9; $hour <= 17; $hour++)
                    <option value="{{ sprintf('%02d', $hour) }}"
                        {{ old('inspection_time_hour', isset($appointment->inspection_time) ? $appointment->inspection_time->format('H') : '') == sprintf('%02d', $hour) ? 'selected' : '' }}>
                        {{ sprintf('%02d', $hour) }}
                    </option>
                @endfor
            </select>
            <span class="self-center">:</span>
            <select id="inspection_time_minute" name="inspection_time_minute"
                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                <option value="">Min</option>
                <option value="00"
                    {{ old('inspection_time_minute', isset($appointment->inspection_time) ? $appointment->inspection_time->format('i') : '') == '00' ? 'selected' : '' }}>
                    00</option>
                <option value="30"
                    {{ old('inspection_time_minute', isset($appointment->inspection_time) ? $appointment->inspection_time->format('i') : '') == '30' ? 'selected' : '' }}>
                    30</option>
            </select>
            <!-- Hidden input to store the combined time value -->
            <input type="hidden" id="inspection_time" name="inspection_time"
                value="{{ old('inspection_time', isset($appointment->inspection_time) ? $appointment->inspection_time->format('H:i') : '') }}">
        </div>
        <x-input-error for="inspection_time" class="mt-2" />
        <span id="time_required_message" class="hidden text-red-500 text-xs mt-1">Time is required when date is
            selected</span>
        <span class="text-xs text-gray-500 mt-1">Available times: 9:00 AM - 17:00 PM</span>
    </div>

    {{-- Inspection Status --}}
    <div>
        <label for="inspection_status" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
            {{ __('inspection_status') }} <span class="text-red-500">*</span>
        </label>
        <select id="inspection_status" name="inspection_status"
            class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full">
            <option value="">Select Status</option>
            <option value="Confirmed"
                {{ old('inspection_status', $appointment->inspection_status ?? '') == 'Confirmed' ? 'selected' : '' }}>
                Confirmed</option>
            <option value="Completed"
                {{ old('inspection_status', $appointment->inspection_status ?? '') == 'Completed' ? 'selected' : '' }}>
                Completed</option>
            <option value="Pending"
                {{ old('inspection_status', $appointment->inspection_status ?? 'Pending') == 'Pending' ? 'selected' : '' }}>
                Pending</option>
            <option value="Declined"
                {{ old('inspection_status', $appointment->inspection_status ?? '') == 'Declined' ? 'selected' : '' }}>
                Declined</option>
        </select>
        <x-input-error for="inspection_status" class="mt-2" />
    </div>

    {{-- Status Lead --}}
    <div>
        <label for="status_lead" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
            {{ __('lead_status') }} <span class="text-red-500">*</span>
        </label>
        <select id="status_lead" name="status_lead"
            class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full">
            <option value="">Select Lead Status</option>
            <option value="New"
                {{ old('status_lead', $appointment->status_lead ?? 'New') == 'New' ? 'selected' : '' }}>
                New</option>
            <option value="Called"
                {{ old('status_lead', $appointment->status_lead ?? '') == 'Called' ? 'selected' : '' }}>
                Called</option>
            <option value="Pending"
                {{ old('status_lead', $appointment->status_lead ?? '') == 'Pending' ? 'selected' : '' }}>
                Pending</option>
            <option value="Declined"
                {{ old('status_lead', $appointment->status_lead ?? '') == 'Declined' ? 'selected' : '' }}>
                Declined</option>
        </select>
        <x-input-error for="status_lead" class="mt-2" />
    </div>

    {{-- Lead Source --}}
    <div>
        <label for="lead_source" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
            {{ __('lead_source') }} <span class="text-red-500">*</span>
        </label>
        <select id="lead_source" name="lead_source"
            class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full">
            <option value="">Select Lead Source</option>
            <option value="Website"
                {{ old('lead_source', $appointment->lead_source ?? '') == 'Website' ? 'selected' : '' }}>
                Website</option>
            <option value="Facebook Ads"
                {{ old('lead_source', $appointment->lead_source ?? '') == 'Facebook Ads' ? 'selected' : '' }}>
                Facebook Ads</option>
            <option value="Reference"
                {{ old('lead_source', $appointment->lead_source ?? '') == 'Reference' ? 'selected' : '' }}>
                Reference</option>
            <option value="Retell AI"
                {{ old('lead_source', $appointment->lead_source ?? '') == 'Retell AI' ? 'selected' : '' }}>
                Retell AI</option>
        </select>
        <x-input-error for="lead_source" class="mt-2" />
    </div>

    {{-- Owner --}}
    <div>
        <label for="owner" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
            {{ __('owner') }}
        </label>
        <x-input id="owner" class="block mt-1 w-full capitalize-first" type="text" name="owner"
            :value="old('owner', $appointment->owner ?? '')" />
        <x-input-error for="owner" class="mt-2" />
    </div>

    {{-- Notes --}}
    <div class="md:col-span-2 mb-6">
        <label for="notes" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
            {{ __('notes') }}
        </label>
        <textarea id="notes" name="notes" rows="4"
            class="capitalize-first border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full">{{ old('notes', $appointment->notes ?? '') }}</textarea>
        <x-input-error for="notes" class="mt-2" />
    </div>
</div>

{{-- Damage Detail --}}
<div class="md:col-span-2 mb-6">
    <label for="damage_detail" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
        {{ __('damage_detail') }}
    </label>
    <textarea id="damage_detail" name="damage_detail" rows="4"
        class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full">{{ old('damage_detail', $appointment->damage_detail ?? '') }}</textarea>
    <x-input-error for="damage_detail" class="mt-2" />
</div>

{{-- Additional Note --}}
<div class="md:col-span-2 mb-6">
    <label for="additional_note" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
        {{ __('additional_note') }}
    </label>
    <textarea id="additional_note" name="additional_note" rows="4"
        class="capitalize-first border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full">{{ old('additional_note', $appointment->additional_note ?? '') }}</textarea>
    <x-input-error for="additional_note" class="mt-2" />
</div>

{{-- Address Modal --}}
<div id="address-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('complete_address_details') }}
                </h3>
                <button type="button" id="close-address-modal"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="space-y-3">
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <h4 class="font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('formatted_address') }}</h4>
                    <p id="modal-formatted-address" class="text-gray-900 dark:text-gray-100 font-medium">-</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('city') }}</label>
                        <p id="modal-city" class="text-gray-900 dark:text-gray-100">-</p>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('state') }}</label>
                        <p id="modal-state" class="text-gray-900 dark:text-gray-100">-</p>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('zip_code') }}</label>
                        <p id="modal-zipcode" class="text-gray-900 dark:text-gray-100 font-bold text-lg">-</p>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('country') }}</label>
                        <p id="modal-country" class="text-gray-900 dark:text-gray-100">-</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('latitude') }}</label>
                        <p id="modal-latitude" class="text-gray-600 dark:text-gray-400 text-sm font-mono">-</p>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('longitude') }}</label>
                        <p id="modal-longitude" class="text-gray-600 dark:text-gray-400 text-sm font-mono">-</p>
                    </div>
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" id="copy-complete-address"
                    class="px-4 py-2 bg-indigo-500 text-white text-sm font-medium rounded-md hover:bg-indigo-600">
                    {{ __('copy_complete_address') }}
                </button>
                <button type="button" id="close-modal-btn"
                    class="px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-400">
                    {{ __('close') }}
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Checkboxes section transformed to radio buttons for insurance_property --}}
<div class="md:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6 my-10 py-5">
    {{-- Insurance Property as Radio Buttons --}}
    <div class="block">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ __('property_insurance') }} <span class="text-red-500">*</span>
        </label>
        <fieldset class="mt-2">
            <legend class="sr-only">Property Insurance</legend>
            <div class="flex items-center space-x-4">
                <div class="radio-option flex items-center">
                    <input id="insurance_yes" name="insurance_property" type="radio" value="1"
                        class="radio-field sr-only"
                        {{ old('insurance_property', $appointment->insurance_property ?? false) ? 'checked' : '' }}>
                    <label for="insurance_yes"
                        class="insurance-label flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md cursor-pointer text-sm w-20">
                        {{ __('yes') }}
                    </label>
                </div>
                <div class="radio-option flex items-center">
                    <input id="insurance_no" name="insurance_property" type="radio" value="0"
                        class="radio-field sr-only"
                        {{ old('insurance_property', $appointment->insurance_property ?? false) ? '' : 'checked' }}>
                    <label for="insurance_no"
                        class="insurance-label flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md cursor-pointer text-sm w-20">
                        {{ __('no') }}
                    </label>
                </div>
            </div>
        </fieldset>
        <x-input-error for="insurance_property" class="mt-2" />
        <span class="text-xs text-red-500 mt-1 block hidden" data-field="insurance_property"></span>
    </div>

    {{-- Other checkboxes --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="block">
            <label for="sms_consent" class="flex items-center">
                <x-checkbox id="sms_consent" name="sms_consent" :checked="old('sms_consent', $appointment->sms_consent ?? false)" />
                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ __('sms_consent') }}</span>
            </label>
        </div>
        <div class="block">
            <label for="intent_to_claim" class="flex items-center">
                <x-checkbox id="intent_to_claim" name="intent_to_claim" :checked="old('intent_to_claim', $appointment->intent_to_claim ?? false)" />
                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ __('intent_to_claim') }}</span>
            </label>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places"
        defer></script>
    <script>
        // Immediate initialization - disable submit button as soon as possible
        document.addEventListener('DOMContentLoaded', function() {
            // Try to find and disable submit button immediately
            const immediateSubmitButton = document.querySelector('button[type="submit"]') || document
                .getElementById('submit-button');
            if (immediateSubmitButton) {
                immediateSubmitButton.disabled = true;
                immediateSubmitButton.classList.add('opacity-50', 'cursor-not-allowed');
                immediateSubmitButton.classList.remove('hover:bg-gray-700');
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Global variables for map functionality
            let appointmentMap;
            let appointmentMarker;
            let autocomplete;

            // Initialize map if Google Maps API is loaded
            initAppointmentMap();

            // Handle inspection date and time relationship
            const inspectionDateField = document.getElementById('inspection_date');
            const inspectionTimeField = document.getElementById('inspection_time');
            const inspectionTimeHourField = document.getElementById('inspection_time_hour');
            const inspectionTimeMinuteField = document.getElementById('inspection_time_minute');
            const inspectionTimeLabel = document.getElementById('inspection_time_label');
            const timeRequiredMessage = document.getElementById('time_required_message');

            // Function to update the hidden inspection_time field with the combined hour and minute
            function updateHiddenTimeField() {
                const hour = inspectionTimeHourField.value;
                const minute = inspectionTimeMinuteField.value;

                if (hour && minute) {
                    inspectionTimeField.value = `${hour}:${minute}`;
                } else {
                    inspectionTimeField.value = '';
                }
            }

            // Add event listeners to the hour and minute selects
            if (inspectionTimeHourField && inspectionTimeMinuteField) {
                inspectionTimeHourField.addEventListener('change', updateHiddenTimeField);
                inspectionTimeMinuteField.addEventListener('change', updateHiddenTimeField);
            }

            // Function to manage time fields based on date selection:
            // - Enables/disables time fields based on date presence
            // - Makes time fields required when date is selected
            // - Resets time values when date is cleared
            function updateTimeFieldRequirement() {
                if (inspectionDateField.value) {
                    // If date is set, enable and make time required
                    inspectionTimeHourField.removeAttribute('disabled');
                    inspectionTimeMinuteField.removeAttribute('disabled');
                    inspectionTimeHourField.setAttribute('required', 'required');
                    inspectionTimeMinuteField.setAttribute('required', 'required');

                    // Add visual indicator to the label (red asterisk)
                    if (!inspectionTimeLabel.innerHTML.includes('*')) {
                        inspectionTimeLabel.innerHTML += ' <span class="text-red-500">*</span>';
                    }

                    // Show message if time is empty
                    if (!inspectionTimeHourField.value || !inspectionTimeMinuteField.value) {
                        timeRequiredMessage.classList.remove('hidden');
                        if (!inspectionTimeHourField.value) {
                            inspectionTimeHourField.classList.add('border-red-300');
                            inspectionTimeHourField.classList.remove('border-green-500');
                        }
                        if (!inspectionTimeMinuteField.value) {
                            inspectionTimeMinuteField.classList.add('border-red-300');
                            inspectionTimeMinuteField.classList.remove('border-green-500');
                        }
                    } else {
                        timeRequiredMessage.classList.add('hidden');
                        inspectionTimeHourField.classList.remove('border-red-300');
                        inspectionTimeHourField.classList.add('border-green-500');
                        inspectionTimeMinuteField.classList.remove('border-red-300');
                        inspectionTimeMinuteField.classList.add('border-green-500');

                        // Update the hidden field
                        updateHiddenTimeField();
                    }
                } else {
                    // If date is not set, disable and reset time fields
                    inspectionTimeHourField.setAttribute('disabled', 'disabled');
                    inspectionTimeMinuteField.setAttribute('disabled', 'disabled');
                    inspectionTimeHourField.removeAttribute('required');
                    inspectionTimeMinuteField.removeAttribute('required');

                    // Reset time values
                    inspectionTimeHourField.value = '';
                    inspectionTimeMinuteField.value = '';
                    inspectionTimeField.value = '';

                    // Reset styles
                    inspectionTimeHourField.classList.remove('border-red-300');
                    inspectionTimeHourField.classList.remove('border-green-500');
                    inspectionTimeMinuteField.classList.remove('border-red-300');
                    inspectionTimeMinuteField.classList.remove('border-green-500');

                    // Remove visual indicator from label
                    inspectionTimeLabel.innerHTML = inspectionTimeLabel.innerHTML.replace(
                        ' <span class="text-red-500">*</span>', '');

                    // Hide message
                    timeRequiredMessage.classList.add('hidden');
                }
            }

            // Add event listeners
            if (inspectionDateField) {
                inspectionDateField.addEventListener('change', updateTimeFieldRequirement);
                inspectionDateField.addEventListener('input', updateTimeFieldRequirement);
            }

            // Add handlers for the time fields
            if (inspectionTimeHourField && inspectionTimeMinuteField) {
                // Hour field change handler
                inspectionTimeHourField.addEventListener('change', function() {
                    if (inspectionDateField.value) {
                        if (!this.value) {
                            this.classList.add('border-red-300');
                            this.classList.remove('border-green-500');
                        } else {
                            this.classList.remove('border-red-300');
                            this.classList.add('border-green-500');

                            // If minute is also selected, hide the error
                            if (inspectionTimeMinuteField.value) {
                                timeRequiredMessage.classList.add('hidden');
                            }
                        }

                        // Update the hidden field
                        updateHiddenTimeField();
                    }
                });

                // Minute field change handler
                inspectionTimeMinuteField.addEventListener('change', function() {
                    if (inspectionDateField.value) {
                        if (!this.value) {
                            this.classList.add('border-red-300');
                            this.classList.remove('border-green-500');
                        } else {
                            this.classList.remove('border-red-300');
                            this.classList.add('border-green-500');

                            // If hour is also selected, hide the error
                            if (inspectionTimeHourField.value) {
                                timeRequiredMessage.classList.add('hidden');
                            }
                        }

                        // Update the hidden field
                        updateHiddenTimeField();
                    }
                });
            }

            // Validate both fields before form submission
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (inspectionDateField && inspectionTimeHourField &&
                        inspectionTimeMinuteField) {
                        if (inspectionDateField.value && (!inspectionTimeHourField.value || !
                                inspectionTimeMinuteField.value)) {
                            e.preventDefault();
                            timeRequiredMessage.classList.remove('hidden');

                            if (!inspectionTimeHourField.value) {
                                inspectionTimeHourField.focus();
                            } else if (!inspectionTimeMinuteField.value) {
                                inspectionTimeMinuteField.focus();
                            }

                            // Scroll to the time field
                            inspectionTimeLabel.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });
                        } else if (inspectionDateField.value) {
                            // Ensure hidden field is updated before submission
                            updateHiddenTimeField();
                        }
                    }
                });
            });

            // Run initial checks
            if (inspectionDateField && inspectionTimeField) {
                updateTimeFieldRequirement();
                updateStatusBasedOnDateTime(); // Run this function on page load as well
            }

            // Set minimum date for inspection date field to today
            if (inspectionDateField) {
                // Get today's date in YYYY-MM-DD format
                const today = new Date();
                const year = today.getFullYear();
                const month = String(today.getMonth() + 1).padStart(2, '0');
                const day = String(today.getDate()).padStart(2, '0');
                const todayFormatted = `${year}-${month}-${day}`;

                // Set the min attribute
                inspectionDateField.setAttribute('min', todayFormatted);

                // Validate the current value
                inspectionDateField.addEventListener('change', function() {
                    const selectedDate = new Date(this.value);
                    const todayDate = new Date(todayFormatted);

                    // Reset time portion for accurate comparison
                    selectedDate.setHours(0, 0, 0, 0);
                    todayDate.setHours(0, 0, 0, 0);

                    if (selectedDate < todayDate) {
                        alert('Please select today or a future date for the inspection.');
                        this.value = todayFormatted;
                    }
                });

                // Check current value on page load
                if (inspectionDateField.value) {
                    const selectedDate = new Date(inspectionDateField.value);
                    const todayDate = new Date(todayFormatted);

                    // Reset time portion for accurate comparison
                    selectedDate.setHours(0, 0, 0, 0);
                    todayDate.setHours(0, 0, 0, 0);

                    if (selectedDate < todayDate) {
                        inspectionDateField.value = todayFormatted;
                    }
                }
            }

            // Formatting for first_name and last_name fields
            const firstNameInput = document.getElementById('first_name');
            const lastNameInput = document.getElementById('last_name');
            const ownerInput = document.getElementById('owner');

            // Function to format name fields
            function formatNameField(input) {
                // Allow letters, spaces, apostrophes, and hyphens
                input.value = input.value.replace(/[^A-Za-z\s'\-]/g, '');
            }

            if (firstNameInput) {
                firstNameInput.addEventListener('input', function() {
                    formatNameField(this);
                });
                firstNameInput.addEventListener('blur', function() {
                    formatNameField(this);
                });
            }

            if (lastNameInput) {
                lastNameInput.addEventListener('input', function() {
                    formatNameField(this);
                });
                lastNameInput.addEventListener('blur', function() {
                    formatNameField(this);
                });
            }

            // Add formatting for owner field
            if (ownerInput) {
                ownerInput.addEventListener('input', function() {
                    // Capitaliza cada palabra, no solo la primera
                    this.value = this.value.replace(/\b\w/g, function(l) {
                        return l.toUpperCase();
                    });
                });
                ownerInput.addEventListener('blur', function() {
                    // Capitaliza cada palabra al perder el foco
                    this.value = this.value.replace(/\b\w/g, function(l) {
                        return l.toUpperCase();
                    });
                });
            }

            // Format Notes and Additional Notes fields to capitalize first letter
            const notesFields = document.querySelectorAll('.capitalize-first');
            notesFields.forEach(function(field) {
                field.addEventListener('input', function() {
                    if (this.value.length > 0) {
                        this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1);
                    }
                });
            });

            const phoneInput = document.getElementById('phone');
            if (phoneInput) {
                phoneInput.addEventListener('input', function(event) {
                    const isBackspace = event?.inputType === 'deleteContentBackward';
                    let value = this.value.replace(/\D/g, '');

                    value = value.substring(0, 10);

                    let formattedValue = '';
                    if (value.length === 0) {
                        formattedValue = '';
                    } else if (value.length <= 3) {
                        formattedValue = `(${value}`;
                    } else if (value.length <= 6) {
                        formattedValue = `(${value.substring(0, 3)}) ${value.substring(3)}`;
                    } else {
                        formattedValue =
                            `(${value.substring(0, 3)}) ${value.substring(3, 6)}-${value.substring(6)}`;
                    }
                    this.value = formattedValue;
                });
            }

            // Function to update inspection status and lead status based on date and time selection
            function updateStatusBasedOnDateTime() {
                // Check if both date and time are selected
                if (inspectionDateField && inspectionTimeField &&
                    inspectionDateField.value && inspectionTimeField.value) {

                    // Get the status fields
                    const inspectionStatusField = document.getElementById('inspection_status');
                    const statusLeadField = document.getElementById('status_lead');

                    if (inspectionStatusField) {
                        // Store the current value
                        const currentValue = inspectionStatusField.value;

                        // We'll allow only Confirmed and Completed options
                        const allowedStatuses = ['Confirmed', 'Completed'];

                        // If current value is not in allowed statuses, default to Confirmed
                        if (!allowedStatuses.includes(currentValue)) {
                            inspectionStatusField.value = 'Confirmed';
                        }

                        // Enable/disable options based on allowed statuses
                        Array.from(inspectionStatusField.options).forEach(option => {
                            if (option.value && !allowedStatuses.includes(option.value)) {
                                option.disabled = true;
                                option.style.display = 'none';
                            } else {
                                option.disabled = false;
                                option.style.display = '';
                            }
                        });
                    }

                    // Set status_lead to Called and limit to only this option
                    if (statusLeadField) {
                        // Set value to Called
                        statusLeadField.value = 'Called';

                        // Only allow "Called" option
                        Array.from(statusLeadField.options).forEach(option => {
                            if (option.value && option.value !== 'Called') {
                                option.disabled = true;
                                option.style.display = 'none';
                            } else {
                                option.disabled = false;
                                option.style.display = '';
                            }
                        });
                    }
                } else {
                    // If date or time is not selected, set inspection_status to Pending by default
                    // and only allow Pending and Declined options
                    const inspectionStatusField = document.getElementById('inspection_status');
                    const statusLeadField = document.getElementById('status_lead');

                    if (inspectionStatusField) {
                        // Default to Pending if not already set to Declined
                        if (inspectionStatusField.value !== 'Declined') {
                            inspectionStatusField.value = 'Pending';
                        }

                        // Only allow Pending and Declined options
                        const allowedStatuses = ['Pending', 'Declined'];
                        Array.from(inspectionStatusField.options).forEach(option => {
                            if (option.value && !allowedStatuses.includes(option.value)) {
                                option.disabled = true;
                                option.style.display = 'none';
                            } else {
                                option.disabled = false;
                                option.style.display = '';
                            }
                        });
                    }

                    // For status_lead, if inspection_status is not Declined, allow New and Pending
                    if (statusLeadField && inspectionStatusField) {
                        if (inspectionStatusField.value !== 'Declined') {
                            // Default to New if not already set to Pending
                            if (statusLeadField.value !== 'Pending') {
                                statusLeadField.value = 'New';
                            }

                            // Only allow New and Pending options
                            const allowedLeadStatuses = ['New', 'Pending'];
                            Array.from(statusLeadField.options).forEach(option => {
                                if (option.value && !allowedLeadStatuses.includes(option.value)) {
                                    option.disabled = true;
                                    option.style.display = 'none';
                                } else {
                                    option.disabled = false;
                                    option.style.display = '';
                                }
                            });
                        }
                    }
                }
            }

            // Add the function call to the date and time change events
            if (inspectionDateField) {
                inspectionDateField.addEventListener('change', function() {
                    updateTimeFieldRequirement();
                    updateStatusBasedOnDateTime();
                });
            }

            // Add the function call to the hour and minute change events
            if (inspectionTimeHourField && inspectionTimeMinuteField) {
                inspectionTimeHourField.addEventListener('change', function() {
                    updateHiddenTimeField();
                    updateStatusBasedOnDateTime();
                });

                inspectionTimeMinuteField.addEventListener('change', function() {
                    updateHiddenTimeField();
                    updateStatusBasedOnDateTime();
                });
            }

            // Function to update lead status options based on inspection status
            function updateLeadStatusBasedOnInspectionStatus() {
                const inspectionStatusField = document.getElementById('inspection_status');
                const statusLeadField = document.getElementById('status_lead');

                if (inspectionStatusField && statusLeadField) {
                    // Check if inspection status is selected
                    if (!inspectionStatusField.value) {
                        // Disable lead status until inspection status is selected
                        statusLeadField.disabled = true;
                        statusLeadField.value = ''; // Clear selection
                        return;
                    } else {
                        // Enable lead status field
                        statusLeadField.disabled = false;
                    }

                    // Get current values
                    const inspectionStatus = inspectionStatusField.value;

                    // If inspection status is Declined, set lead status to Declined
                    if (inspectionStatus === 'Declined') {
                        statusLeadField.value = 'Declined';

                        // Only allow Declined option
                        Array.from(statusLeadField.options).forEach(option => {
                            if (option.value && option.value !== 'Declined') {
                                option.disabled = true;
                                option.style.display = 'none';
                            } else {
                                option.disabled = false;
                                option.style.display = '';
                            }
                        });
                    }
                    // If inspection status is Confirmed or Completed, limit lead status to Called
                    else if (inspectionStatus === 'Confirmed' || inspectionStatus === 'Completed') {
                        statusLeadField.value = 'Called';

                        // Only allow Called option
                        Array.from(statusLeadField.options).forEach(option => {
                            if (option.value && option.value !== 'Called') {
                                option.disabled = true;
                                option.style.display = 'none';
                            } else {
                                option.disabled = false;
                                option.style.display = '';
                            }
                        });
                    }
                    // If inspection status is Pending, limit to New and Pending
                    else if (inspectionStatus === 'Pending') {
                        if (statusLeadField.value !== 'New' && statusLeadField.value !== 'Pending') {
                            statusLeadField.value = 'New';
                        }

                        // Only allow New and Pending options
                        const allowedStatuses = ['New', 'Pending'];
                        Array.from(statusLeadField.options).forEach(option => {
                            if (option.value && !allowedStatuses.includes(option.value)) {
                                option.disabled = true;
                                option.style.display = 'none';
                            } else {
                                option.disabled = false;
                                option.style.display = '';
                            }
                        });
                    }
                }
            }

            // Add an event listener to the inspection status dropdown
            const inspectionStatusField = document.getElementById('inspection_status');
            if (inspectionStatusField) {
                inspectionStatusField.addEventListener('change', updateLeadStatusBasedOnInspectionStatus);

                // Clear inspection status on page load to force selection
                if (!inspectionStatusField.value) {
                    // Default to Pending if empty
                    inspectionStatusField.value = 'Pending';
                }
            }

            // Disable lead status field initially if inspection status is not selected
            const statusLeadField = document.getElementById('status_lead');
            if (statusLeadField && inspectionStatusField) {
                if (!inspectionStatusField.value) {
                    statusLeadField.disabled = true;
                }
            }

            // Run initial status checks
            if (inspectionDateField && inspectionTimeField) {
                updateTimeFieldRequirement();
                updateStatusBasedOnDateTime();
            }
            if (inspectionStatusField) {
                updateLeadStatusBasedOnInspectionStatus();
            }

            // Set the time fields disabled by default when the page loads
            // Note: This initialization is redundant with updateTimeFieldRequirement,
            // but ensures correct initial state if the function doesn't run
            if (inspectionTimeHourField && inspectionTimeMinuteField && inspectionDateField) {
                // Only disable if no date is selected
                if (!inspectionDateField.value) {
                    inspectionTimeHourField.setAttribute('disabled', 'disabled');
                    inspectionTimeMinuteField.setAttribute('disabled', 'disabled');
                }
            }
        });

        // Initialize map
        function initAppointmentMap() {
            try {
                // Check if Google Maps API is loaded
                if (typeof google === 'undefined' || typeof google.maps === 'undefined') {
                    console.error('Google Maps API not loaded');
                    return;
                }

                // Get the map container
                const mapContainer = document.getElementById('location-map');
                if (!mapContainer) {
                    console.error('Map container not found');
                    return;
                }

                // Default location (United States center)
                const defaultLocation = {
                    lat: 37.0902,
                    lng: -95.7129
                };

                // Check if we have existing coordinates
                const latInput = document.getElementById('latitude');
                const lngInput = document.getElementById('longitude');
                const hasCoordinates = latInput && latInput.value && lngInput && lngInput.value;

                let mapCenter = defaultLocation;
                let zoomLevel = 4;

                if (hasCoordinates) {
                    mapCenter = {
                        lat: parseFloat(latInput.value),
                        lng: parseFloat(lngInput.value)
                    };
                    zoomLevel = 16;
                }

                // Create map
                appointmentMap = new google.maps.Map(mapContainer, {
                    center: mapCenter,
                    zoom: zoomLevel,
                    mapTypeControl: false,
                    streetViewControl: false,
                    fullscreenControl: false,
                    zoomControl: true
                });

                // Create marker
                appointmentMarker = new google.maps.Marker({
                    map: appointmentMap,
                    position: hasCoordinates ? mapCenter : null,
                    visible: hasCoordinates
                });

                // Initialize address autocomplete
                initAppointmentAutocomplete();
            } catch (error) {
                console.error('Error initializing map:', error);
            }
        }

        // Initialize Google Maps Address Autocomplete
        function initAppointmentAutocomplete() {
            try {
                const addressMapInput = document.getElementById('address_map_input');
                if (!addressMapInput) {
                    console.error('Address input not found');
                    return;
                }

                autocomplete = new google.maps.places.Autocomplete(addressMapInput, {
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
                    if (appointmentMap && appointmentMarker) {
                        appointmentMap.setCenter({
                            lat,
                            lng
                        });
                        appointmentMap.setZoom(16);
                        appointmentMarker.setPosition({
                            lat,
                            lng
                        });
                        appointmentMarker.setVisible(true);
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

                    // Fill in form fields
                    if (addressLine1) document.getElementById('address').value = addressLine1;
                    if (city) document.getElementById('city').value = city;
                    if (state) document.getElementById('state').value = state;
                    if (zipcode) document.getElementById('zipcode').value = zipcode;
                    document.getElementById('country').value = 'USA';
                });
            } catch (error) {
                console.error('Error initializing autocomplete:', error);
            }
        }

        // Modal and sharing functionality
        document.addEventListener('DOMContentLoaded', function() {
            const showAddressModal = document.getElementById('show-address-modal');
            const addressModal = document.getElementById('address-modal');
            const closeAddressModal = document.getElementById('close-address-modal');
            const closeModalBtn = document.getElementById('close-modal-btn');
            const shareLocationOptions = document.getElementById('share-location-options');
            const shareOptions = document.getElementById('share-options');
            const copyCompleteAddress = document.getElementById('copy-complete-address');
            const copyAddress = document.getElementById('copy-address');

            // Show address modal
            if (showAddressModal) {
                showAddressModal.addEventListener('click', function() {
                    updateModalContent();
                    addressModal.classList.remove('hidden');
                });
            }

            // Close modal events
            [closeAddressModal, closeModalBtn].forEach(btn => {
                if (btn) {
                    btn.addEventListener('click', function() {
                        addressModal.classList.add('hidden');
                    });
                }
            });

            // Close modal when clicking outside
            if (addressModal) {
                addressModal.addEventListener('click', function(e) {
                    if (e.target === addressModal) {
                        addressModal.classList.add('hidden');
                    }
                });
            }

            // Toggle share options
            if (shareLocationOptions) {
                shareLocationOptions.addEventListener('click', function() {
                    shareOptions.classList.toggle('hidden');
                    const isHidden = shareOptions.classList.contains('hidden');
                    this.textContent = isHidden ? '{{ __('choose_how_share_location') }}' :
                        '{{ __('hide_share_options') }}';
                });
            }

            // Copy complete address functionality
            if (copyCompleteAddress) {
                copyCompleteAddress.addEventListener('click', function() {
                    const completeAddress = getCompleteAddressString();
                    copyToClipboard(completeAddress, '{{ __('complete_address_copied') }}');
                });
            }

            // Copy address functionality
            if (copyAddress) {
                copyAddress.addEventListener('click', function() {
                    const address = document.getElementById('address_map_input').value;
                    copyToClipboard(address, '{{ __('address_copied') }}');
                });
            }

            // Share functionality
            const shareWhatsApp = document.getElementById('share-whatsapp');
            const shareEmail = document.getElementById('share-email');
            const shareMaps = document.getElementById('share-maps');

            if (shareWhatsApp) {
                shareWhatsApp.addEventListener('click', function(e) {
                    e.preventDefault();
                    const address = document.getElementById('address_map_input').value;
                    const whatsappUrl =
                        `https://wa.me/?text=${encodeURIComponent('{{ __('check_this_location') }}: ' + address)}`;
                    window.open(whatsappUrl, '_blank');
                });
            }

            if (shareEmail) {
                shareEmail.addEventListener('click', function(e) {
                    e.preventDefault();
                    const address = document.getElementById('address_map_input').value;
                    const subject = encodeURIComponent('{{ __('location_shared') }}');
                    const body = encodeURIComponent('{{ __('check_this_location') }}: ' + address);
                    window.location.href = `mailto:?subject=${subject}&body=${body}`;
                });
            }

            if (shareMaps) {
                shareMaps.addEventListener('click', function(e) {
                    e.preventDefault();
                    const lat = document.getElementById('latitude').value;
                    const lng = document.getElementById('longitude').value;
                    if (lat && lng) {
                        const mapsUrl = `https://www.google.com/maps?q=${lat},${lng}`;
                        window.open(mapsUrl, '_blank');
                    } else {
                        alert('{{ __('no_coordinates_available') }}');
                    }
                });
            }

            // Update modal content
            function updateModalContent() {
                const formattedAddress = document.getElementById('address_map_input').value || '-';
                const city = document.getElementById('city').value || '-';
                const state = document.getElementById('state').value || '-';
                const zipcode = document.getElementById('zipcode').value || '-';
                const country = document.getElementById('country').value || '-';
                const latitude = document.getElementById('latitude').value || '-';
                const longitude = document.getElementById('longitude').value || '-';

                document.getElementById('modal-formatted-address').textContent = formattedAddress;
                document.getElementById('modal-city').textContent = city;
                document.getElementById('modal-state').textContent = state;
                document.getElementById('modal-zipcode').textContent = zipcode;
                document.getElementById('modal-country').textContent = country;
                document.getElementById('modal-latitude').textContent = latitude;
                document.getElementById('modal-longitude').textContent = longitude;
            }

            // Get complete address string
            function getCompleteAddressString() {
                const address = document.getElementById('address').value || '';
                const address2 = document.getElementById('address_2').value || '';
                const city = document.getElementById('city').value || '';
                const state = document.getElementById('state').value || '';
                const zipcode = document.getElementById('zipcode').value || '';
                const country = document.getElementById('country').value || '';

                let completeAddress = address;
                if (address2) completeAddress += ', ' + address2;
                if (city) completeAddress += ', ' + city;
                if (state) completeAddress += ', ' + state;
                if (zipcode) completeAddress += ' ' + zipcode;
                if (country) completeAddress += ', ' + country;

                return completeAddress;
            }

            // Copy to clipboard utility
            function copyToClipboard(text, successMessage) {
                navigator.clipboard.writeText(text).then(function() {
                    // Show success message
                    const toast = document.createElement('div');
                    toast.className =
                        'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-md shadow-lg z-50';
                    toast.textContent = successMessage;
                    document.body.appendChild(toast);

                    setTimeout(() => {
                        document.body.removeChild(toast);
                    }, 3000);
                }).catch(function(err) {
                    console.error('Could not copy text: ', err);
                    alert('{{ __('copy_failed') }}');
                });
            }

            // Insurance radio buttons styling setup
            const insuranceRadios = document.querySelectorAll('input[name="insurance_property"]');
            const insuranceLabels = document.querySelectorAll('.insurance-label');

            // Initial setup - ensure selected radio has its label styled
            insuranceRadios.forEach(radio => {
                if (radio.checked) {
                    const label = document.querySelector(`label[for="${radio.id}"]`);
                    if (label) {
                        label.classList.add('selected');
                    }
                }
            });

            // Add click event listeners to labels for better UX
            insuranceLabels.forEach(label => {
                label.addEventListener('click', function() {
                    const radioId = this.getAttribute('for');
                    const radio = document.getElementById(radioId);
                    if (radio) {
                        radio.checked = true;
                        radio.dispatchEvent(new Event('change', {
                            bubbles: true
                        }));
                    }
                });
            });

            // Change event handlers for radio buttons
            insuranceRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    // Remove selected class from all labels
                    insuranceLabels.forEach(label => {
                        label.classList.remove('selected');
                    });

                    // Add selected class to the checked radio's label
                    if (this.checked) {
                        const label = document.querySelector(`label[for="${this.id}"]`);
                        if (label) {
                            label.classList.add('selected');
                        }
                    }

                    // Clear any validation errors
                    clearFieldError('insurance_property');
                });
            });

            // ===== REAL-TIME VALIDATION SYSTEM =====

            // Get all form inputs for validation
            const formInputs = document.querySelectorAll('input[required], select[required], textarea[required]');
            const submitButton = document.querySelector('button[type="submit"]') || document.getElementById(
                'submit-button');

            // Function to clear field errors
            function clearFieldError(fieldName) {
                const errorElement = document.querySelector(`.text-red-500[data-field="${fieldName}"]`);
                if (errorElement) {
                    errorElement.textContent = '';
                    errorElement.classList.add('hidden');
                }

                // Remove red border from field
                const field = document.querySelector(`[name="${fieldName}"]`);
                if (field) {
                    field.classList.remove('border-red-500', 'border-red-300');
                    field.classList.add('border-gray-300');
                }

                checkFormValidity();
            }

            // Function to show field error
            function showFieldError(fieldName, message) {
                let errorElement = document.querySelector(`.text-red-500[data-field="${fieldName}"]`);

                if (!errorElement) {
                    // Create error element if it doesn't exist
                    const field = document.querySelector(`[name="${fieldName}"]`);
                    if (field) {
                        errorElement = document.createElement('span');
                        errorElement.className = 'text-xs text-red-500 mt-1 block';
                        errorElement.setAttribute('data-field', fieldName);
                        field.parentNode.appendChild(errorElement);
                    }
                }

                if (errorElement) {
                    errorElement.textContent = message;
                    errorElement.classList.remove('hidden');
                }

                // Add red border to field
                const field = document.querySelector(`[name="${fieldName}"]`);
                if (field) {
                    field.classList.add('border-red-500');
                    field.classList.remove('border-gray-300', 'border-green-500');
                }

                checkFormValidity();
            }

            // Helper function to capitalize first letter
            function capitalizeFirst(str) {
                return str.charAt(0).toUpperCase() + str.slice(1);
            }

            // Helper function to format field names for display
            function formatFieldName(fieldName) {
                return capitalizeFirst(fieldName.replace('_', ' '));
            }

            // Function to validate individual field
            function validateField(fieldName, value) {
                switch (fieldName) {
                    case 'first_name':
                        if (!value || value.trim() === '') {
                            showFieldError(fieldName, '{{ __('first_name_required') }}');
                            return false;
                        } else if (value.length > 50) {
                            showFieldError(fieldName, '{{ __('first_name_max') }}');
                            return false;
                        } else if (!/^[A-Za-z\s\'-]+$/.test(value)) {
                            showFieldError(fieldName, '{{ __('first_name_regex') }}');
                            return false;
                        } else {
                            clearFieldError(fieldName);
                            return true;
                        }

                    case 'last_name':
                        if (!value || value.trim() === '') {
                            showFieldError(fieldName, '{{ __('last_name_required') }}');
                            return false;
                        } else if (value.length > 50) {
                            showFieldError(fieldName, '{{ __('last_name_max') }}');
                            return false;
                        } else if (!/^[A-Za-z\s\'-]+$/.test(value)) {
                            showFieldError(fieldName, '{{ __('last_name_regex') }}');
                            return false;
                        } else {
                            clearFieldError(fieldName);
                            return true;
                        }

                    case 'email':
                        if (!value || value.trim() === '') {
                            showFieldError(fieldName, '{{ __('email_required') }}');
                            return false;
                        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                            showFieldError(fieldName, '{{ __('email_invalid') }}');
                            return false;
                        } else {
                            clearFieldError(fieldName);
                            return true;
                        }

                    case 'phone':
                        if (!value || value.trim() === '') {
                            showFieldError(fieldName, '{{ __('phone_required') }}');
                            return false;
                        } else if (value.replace(/\D/g, '').length !== 10) {
                            showFieldError(fieldName, '{{ __('phone_max') }}');
                            return false;
                        } else {
                            clearFieldError(fieldName);
                            return true;
                        }

                    case 'address':
                        if (!value || value.trim() === '') {
                            showFieldError(fieldName, '{{ __('address_required') }}');
                            return false;
                        } else {
                            clearFieldError(fieldName);
                            return true;
                        }

                    case 'city':
                        if (!value || value.trim() === '') {
                            showFieldError(fieldName, '{{ __('city_required') }}');
                            return false;
                        } else {
                            clearFieldError(fieldName);
                            return true;
                        }

                    case 'state':
                        if (!value || value.trim() === '') {
                            showFieldError(fieldName, '{{ __('state_required') }}');
                            return false;
                        } else {
                            clearFieldError(fieldName);
                            return true;
                        }

                    case 'zipcode':
                        if (!value || value.trim() === '') {
                            showFieldError(fieldName, '{{ __('zipcode_required') }}');
                            return false;
                        } else {
                            clearFieldError(fieldName);
                            return true;
                        }

                    case 'country':
                        if (!value || value.trim() === '') {
                            showFieldError(fieldName, '{{ __('country_required') }}');
                            return false;
                        } else {
                            clearFieldError(fieldName);
                            return true;
                        }

                    case 'insurance_property':
                        const checkedRadio = document.querySelector('input[name="insurance_property"]:checked');
                        if (!checkedRadio) {
                            showFieldError(fieldName, '{{ __('insurance_property_required') }}');
                            return false;
                        } else {
                            clearFieldError(fieldName);
                            return true;
                        }

                    case 'lead_source':
                        if (!value || value === '') {
                            showFieldError(fieldName, '{{ __('lead_source_required') }}');
                            return false;
                        } else {
                            clearFieldError(fieldName);
                            return true;
                        }

                    case 'inspection_status':
                        if (!value || value === '') {
                            showFieldError(fieldName, '{{ __('inspection_status_required') }}');
                            return false;
                        } else {
                            clearFieldError(fieldName);
                            return true;
                        }

                    case 'status_lead':
                        if (!value || value === '') {
                            showFieldError(fieldName, '{{ __('status_lead_required') }}');
                            return false;
                        } else {
                            clearFieldError(fieldName);
                            return true;
                        }

                    default:
                        clearFieldError(fieldName);
                        return true;
                }
            }

            // Function to check overall form validity
            function checkFormValidity() {
                const requiredFields = [
                    'first_name', 'last_name', 'email', 'phone', 'address',
                    'city', 'state', 'zipcode', 'country', 'insurance_property',
                    'lead_source', 'inspection_status', 'status_lead'
                ];

                let isValid = true;
                let allFieldsFilled = true;

                requiredFields.forEach(fieldName => {
                    let value = '';
                    let hasValue = false;

                    if (fieldName === 'insurance_property') {
                        const checkedRadio = document.querySelector(
                            'input[name="insurance_property"]:checked');
                        value = checkedRadio ? checkedRadio.value : '';
                        hasValue = !!checkedRadio;
                    } else {
                        const field = document.querySelector(`[name="${fieldName}"]`);
                        value = field ? field.value.trim() : '';
                        hasValue = value !== '';
                    }

                    // Check if field has value
                    if (!hasValue) {
                        allFieldsFilled = false;
                    }

                    // Only validate fields that have been touched or have values
                    const field = document.querySelector(`[name="${fieldName}"]`);
                    const hasBeenTouched = field && (field.dataset.touched === 'true');

                    if (hasValue || hasBeenTouched) {
                        if (!validateField(fieldName, value)) {
                            isValid = false;
                        }
                    }
                });

                // Enable submit button only if ALL required fields are filled AND valid
                const shouldEnable = allFieldsFilled && isValid;

                if (submitButton) {
                    submitButton.disabled = !shouldEnable;
                    if (shouldEnable) {
                        submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
                        submitButton.classList.add('hover:bg-gray-700');
                    } else {
                        submitButton.classList.add('opacity-50', 'cursor-not-allowed');
                        submitButton.classList.remove('hover:bg-gray-700');
                    }
                }

                return shouldEnable;
            }

            // Add event listeners for real-time validation
            const fieldsToValidate = [
                'first_name', 'last_name', 'email', 'phone', 'address',
                'city', 'state', 'zipcode', 'country', 'lead_source',
                'inspection_status', 'status_lead'
            ];

            fieldsToValidate.forEach(fieldName => {
                const field = document.querySelector(`[name="${fieldName}"]`);
                if (field) {
                    // Mark field as touched when user interacts with it
                    field.addEventListener('focus', function() {
                        this.dataset.touched = 'true';
                    });

                    // Validate on blur (when user leaves field)
                    field.addEventListener('blur', function() {
                        this.dataset.touched = 'true';
                        validateField(fieldName, this.value);
                        checkFormValidity();
                    });

                    // Validate on input for immediate feedback
                    field.addEventListener('input', function() {
                        this.dataset.touched = 'true';
                        // Clear errors immediately when user starts typing
                        if (this.value.trim() !== '') {
                            clearFieldError(fieldName);
                        }
                        checkFormValidity();
                    });

                    // For select fields, also listen to change events
                    if (field.tagName === 'SELECT') {
                        field.addEventListener('change', function() {
                            this.dataset.touched = 'true';
                            validateField(fieldName, this.value);
                            checkFormValidity();
                        });
                    }
                }
            });

            // Special handling for insurance radio buttons
            insuranceRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    // Mark all radio buttons in the group as touched
                    insuranceRadios.forEach(r => r.dataset.touched = 'true');
                    validateField('insurance_property', this.value);
                    checkFormValidity();
                });
            });

            // Initialize submit button as disabled immediately
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.classList.add('opacity-50', 'cursor-not-allowed');
                submitButton.classList.remove('hover:bg-gray-700');
            }

            // Make checkFormValidity available globally for other scripts
            window.appointmentFormValidation = {
                checkFormValidity: checkFormValidity,
                validateField: validateField,
                submitButton: submitButton
            };

            // Initial form validation check - immediate and with timeout as backup
            checkFormValidity();
            setTimeout(() => {
                checkFormValidity();
            }, 50);
        });
    </script>
@endpush

@push('styles')
    <style>
        /* Map height */
        #location-map {
            min-height: 200px;
        }

        /* Capitalize only first letter */
        .capitalize-first::first-letter {
            text-transform: uppercase;
        }
    </style>
@endpush
