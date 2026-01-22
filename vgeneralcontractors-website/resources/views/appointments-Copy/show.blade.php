<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Appointment Details') }}: {{ $appointment->first_name }} {{ $appointment->last_name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    {{-- Display Appointment Details --}}
                    <div><strong class="text-gray-600 dark:text-gray-400">Name:</strong> {{ $appointment->first_name }}
                        {{ $appointment->last_name }}</div>
                    <div><strong class="text-gray-600 dark:text-gray-400">Email:</strong> {{ $appointment->email }}</div>
                    <div><strong class="text-gray-600 dark:text-gray-400">Phone:</strong> {{ $appointment->phone }}</div>
                    <div><strong class="text-gray-600 dark:text-gray-400">Address:</strong> {{ $appointment->address }}
                    </div>
                    @if ($appointment->address_2)
                        <div><strong class="text-gray-600 dark:text-gray-400">Address 2:</strong>
                            {{ $appointment->address_2 }}</div>
                    @endif
                    <div><strong class="text-gray-600 dark:text-gray-400">City:</strong> {{ $appointment->city }}</div>
                    <div><strong class="text-gray-600 dark:text-gray-400">State:</strong> {{ $appointment->state }}
                    </div>
                    <div><strong class="text-gray-600 dark:text-gray-400">Zip Code:</strong>
                        {{ $appointment->zipcode }}</div>
                    <div><strong class="text-gray-600 dark:text-gray-400">Country:</strong> {{ $appointment->country }}
                    </div>
                    <div><strong class="text-gray-600 dark:text-gray-400">Inspection Date:</strong>
                        {{ $appointment->inspection_date ? $appointment->inspection_date->format('Y-m-d') : 'N/A' }}
                    </div>
                    <div><strong class="text-gray-600 dark:text-gray-400">Inspection Time:</strong>
                        {{ $appointment->inspection_time ? $appointment->inspection_time->format('H:i:s') : 'N/A' }}
                    </div>
                    <div><strong class="text-gray-600 dark:text-gray-400">Inspection Status:</strong>
                        {{ $appointment->inspection_status ?? 'N/A' }}</div>
                    <div><strong class="text-gray-600 dark:text-gray-400">Lead Source:</strong>
                        {{ $appointment->lead_source ?? 'N/A' }}</div>
                    <div><strong class="text-gray-600 dark:text-gray-400">Owner:</strong>
                        {{ $appointment->owner ?? 'N/A' }}</div>
                    <div><strong class="text-gray-600 dark:text-gray-400">Follow Up Date:</strong>
                        {{ $appointment->follow_up_date ? $appointment->follow_up_date->format('Y-m-d') : 'N/A' }}
                    </div>
                    <div><strong class="text-gray-600 dark:text-gray-400">Registration Date:</strong>
                        {{ $appointment->registration_date ? $appointment->registration_date->format('Y-m-d H:i:s') : 'N/A' }}
                    </div>
                    <div><strong class="text-gray-600 dark:text-gray-400">Latitude:</strong>
                        {{ $appointment->latitude ?? 'N/A' }}</div>
                    <div><strong class="text-gray-600 dark:text-gray-400">Longitude:</strong>
                        {{ $appointment->longitude ?? 'N/A' }}</div>
                    <div><strong class="text-gray-600 dark:text-gray-400">SMS Consent:</strong>
                        {{ $appointment->sms_consent ? 'Yes' : 'No' }}</div>
                    <div><strong class="text-gray-600 dark:text-gray-400">Insurance Property:</strong>
                        {{ $appointment->insurance_property ? 'Yes' : 'No' }}</div>
                    <div><strong class="text-gray-600 dark:text-gray-400">Intent to Claim:</strong>
                        {{ $appointment->intent_to_claim ? 'Yes' : 'No' }}</div>
                    <div><strong class="text-gray-600 dark:text-gray-400">Inspection Confirmed:</strong>
                        {{ $appointment->inspection_status === 'Confirmed' ? 'Yes' : 'No' }}</div>
                </div>

                {{-- Text Areas --}}
                @if ($appointment->message)
                    <div class="mb-4">
                        <strong class="block text-gray-600 dark:text-gray-400 mb-1">Message / Initial Request:</strong>
                        <p class="text-gray-800 dark:text-gray-200 whitespace-pre-wrap">{{ $appointment->message }}</p>
                    </div>
                @endif
                @if ($appointment->damage_detail)
                    <div class="mb-4">
                        <strong class="block text-gray-600 dark:text-gray-400 mb-1">Damage Detail:</strong>
                        <p class="text-gray-800 dark:text-gray-200 whitespace-pre-wrap">
                            {{ $appointment->damage_detail }}</p>
                    </div>
                @endif
                @if ($appointment->notes)
                    <div class="mb-4">
                        <strong class="block text-gray-600 dark:text-gray-400 mb-1">Internal Notes:</strong>
                        <p class="text-gray-800 dark:text-gray-200 whitespace-pre-wrap">{{ $appointment->notes }}</p>
                    </div>
                @endif
                @if ($appointment->additional_note)
                    <div class="mb-4">
                        <strong class="block text-gray-600 dark:text-gray-400 mb-1">Additional Notes
                            (Post-Inspection):</strong>
                        <p class="text-gray-800 dark:text-gray-200 whitespace-pre-wrap">
                            {{ $appointment->additional_note }}</p>
                    </div>
                @endif

                {{-- Action Buttons --}}
                <div class="flex items-center justify-end mt-6 space-x-4">
                    <a href="{{ route('appointments.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-500 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                        {{ __('Back to List') }}
                    </a>
                    <a href="{{ route('appointments.edit', $appointment) }}"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring focus:ring-indigo-300 disabled:opacity-25 transition">
                        {{ __('Edit Appointment') }}
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
