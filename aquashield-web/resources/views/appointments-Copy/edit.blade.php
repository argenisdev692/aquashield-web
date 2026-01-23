@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 sm:px-8">
        <div class="py-8">
            <div class="flex justify-between">
                <h2 class="text-2xl font-semibold leading-tight">{{ __('edit_appointment') }}</h2>
                <a href="{{ route('appointments.index') }}"
                    class="px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('back_to_list') }}
                </a>
            </div>
            <div class="my-4 overflow-hidden sm:rounded-md">
                <form id="appointmentEditForm" action="{{ route('appointments.update', $appointment->uuid) }}" method="POST"
                    class=" dark:bg-gray-800 shadow-md rounded-lg p-6">
                    @csrf
                    @method('PUT')
                    @include('appointments._form')
                    <div class="mt-10 mb-3 flex justify-center">
                        <button type="submit" id="submit-button" disabled
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-75 disabled:cursor-not-allowed opacity-50 cursor-not-allowed">

                            {{-- Spinner (hidden initially) --}}
                            <svg id="submit-spinner" class="hidden animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>

                            <span id="submit-button-text">{{ __('update_appointment_btn') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('appointmentEditForm');
            const submitButton = document.getElementById('submit-button');
            const submitSpinner = document.getElementById('submit-spinner');
            const submitButtonText = document.getElementById('submit-button-text');

            // Wait for the form validation system to be ready
            const waitForValidation = setInterval(() => {
                if (window.appointmentFormValidation) {
                    clearInterval(waitForValidation);
                    // Initial check to ensure button state is correct
                    window.appointmentFormValidation.checkFormValidity();
                }
            }, 100);

            // Extra code to ensure insurance radio buttons styling works
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

            // Add click event listeners directly to the labels (more responsive)
            insuranceLabels.forEach(label => {
                label.addEventListener('click', function() {
                    // Find the associated radio button and check it
                    const radioId = this.getAttribute('for');
                    const radio = document.getElementById(radioId);
                    if (radio) {
                        radio.checked = true;

                        // Update styles immediately
                        insuranceLabels.forEach(l => l.classList.remove('selected'));
                        this.classList.add('selected');
                    }
                });
            });

            // Change event handlers as backup
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
                });
            });

            // Function to set loading state
            function setLoadingState(isLoading) {
                if (isLoading) {
                    submitButton.disabled = true;
                    submitSpinner.classList.remove('hidden');
                    submitButtonText.textContent = '{{ __('sending') }}...';
                } else {
                    submitSpinner.classList.add('hidden');
                    submitButtonText.textContent = '{{ __('update_appointment_btn') }}';
                    // Re-check form validity after loading to restore proper button state
                    if (window.appointmentFormValidation) {
                        window.appointmentFormValidation.checkFormValidity();
                    }
                }
            }

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                // Show spinner and disable button
                setLoadingState(true);

                fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        body: new FormData(form)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Reset form before showing success message
                            form.reset();

                            // Reset button state as well
                            setLoadingState(false);

                            Swal.fire({
                                title: '{{ __('success_title') }}',
                                text: data.message,
                                icon: 'success',
                                confirmButtonText: '{{ __('swal_ok') }}'
                            }).then(() => {
                                // Use redirectUrl from response if available
                                window.location.href = data.redirectUrl ||
                                    "{{ route('appointments.index') }}";
                            });
                        } else {
                            let errorMessage = data.message;
                            if (data.errors) {
                                errorMessage += '\n';
                                Object.values(data.errors).forEach(error => {
                                    errorMessage += '\n• ' + error;
                                });
                            }

                            Swal.fire({
                                title: '{{ __('error_occurred') }}',
                                text: errorMessage,
                                icon: 'error',
                                confirmButtonText: '{{ __('swal_ok') }}'
                            });

                            // Hide spinner and enable button on error
                            setLoadingState(false);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: '{{ __('error_occurred') }}',
                            text: '{{ __('unexpected_error_occurred') }}',
                            icon: 'error',
                            confirmButtonText: '{{ __('swal_ok') }}'
                        });

                        // Hide spinner and enable button on error
                        setLoadingState(false);
                    });
            });
        });
    </script>
@endpush
