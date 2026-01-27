@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 sm:px-8">
        <div class="py-8">
            <div class="flex justify-between my-5">
                <h2 class="text-2xl font-semibold leading-tight text-white">
                    {{ isset($appointment->uuid) ? __('edit_appointment') : __('create_appointment') }}
                </h2>
                <a href="{{ route('appointments.index') }}"
                    class="px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('back_to_list') }}
                </a>
            </div>
            <div class="my-4 overflow-hidden sm:rounded-md">
                <form id="{{ isset($appointment->uuid) ? 'appointmentEditForm' : 'appointmentCreateForm' }}"
                    action="{{ isset($appointment->uuid) ? secure_url(route('appointments.update', $appointment->uuid, false)) : secure_url(route('appointments.store', [], false)) }}"
                    method="POST" class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
                    @csrf
                    @if (isset($appointment->uuid))
                        @method('PUT')
                    @endif
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

                            <span
                                id="submit-button-text">{{ isset($appointment->uuid) ? __('update_appointment_btn') : __('create_appointment_btn') }}</span>
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
            const form = document.getElementById(
                '{{ isset($appointment->uuid) ? 'appointmentEditForm' : 'appointmentCreateForm' }}');
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

            // Function to set loading state
            function setLoadingState(isLoading) {
                if (isLoading) {
                    submitButton.disabled = true;
                    submitSpinner.classList.remove('hidden');
                    submitButtonText.textContent = '{{ __('sending') }}...';
                } else {
                    submitSpinner.classList.add('hidden');
                    submitButtonText.textContent =
                        '{{ isset($appointment->uuid) ? __('update_appointment_btn') : __('create_appointment_btn') }}';
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
                            // Check specifically for scheduling conflicts
                            if (data.errors && data.errors.schedule_conflict) {
                                Swal.fire({
                                    title: '{{ __('scheduling_conflict') }}',
                                    text: data.errors.schedule_conflict,
                                    icon: 'warning',
                                    confirmButtonText: '{{ __('swal_ok') }}'
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
                                    title: '{{ __('error_title') }}',
                                    text: errorMessage,
                                    icon: 'error',
                                    confirmButtonText: '{{ __('swal_ok') }}'
                                });
                            }

                            // Hide spinner and enable button on error
                            setLoadingState(false);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: '{{ __('error_title') }}',
                            text: '{{ __('unexpected_error_occurred') }}',
                            icon: 'error',
                            confirmButtonText: '{{ __('swal_ok') }}'
                        });

                        // Hide spinner and enable button on error
                        setLoadingState(false);
                    });
            });

            // Funcionalidad de compartir ubicación
            const shareWhatsApp = document.getElementById('share-whatsapp');
            const shareEmail = document.getElementById('share-email');
            const shareMaps = document.getElementById('share-maps');
            const copyAddress = document.getElementById('copy-address');

            if (shareWhatsApp && shareEmail && shareMaps && copyAddress) {
                const updateShareLinks = () => {
                    const lat = document.getElementById('latitude').value;
                    const lng = document.getElementById('longitude').value;
                    const address = document.getElementById('address_map_input').value;

                    if (!lat || !lng) {
                        // Deshabilitar botones si no hay coordenadas
                        [shareWhatsApp, shareEmail, shareMaps, copyAddress].forEach(btn => {
                            btn.classList.add('opacity-50', 'cursor-not-allowed');
                            btn.setAttribute('disabled', 'disabled');
                        });
                        return;
                    }

                    // Habilitar botones
                    [shareWhatsApp, shareEmail, shareMaps, copyAddress].forEach(btn => {
                        btn.classList.remove('opacity-50', 'cursor-not-allowed');
                        btn.removeAttribute('disabled');
                    });

                    const mapsUrl = `https://www.google.com/maps?q=${lat},${lng}`;

                    // WhatsApp
                    shareWhatsApp.href =
                        `https://wa.me/?text={{ __('location_for_inspection') }}: ${encodeURIComponent(address)} - ${encodeURIComponent(mapsUrl)}`;
                    shareWhatsApp.target = '_blank';

                    // Email
                    const subject = encodeURIComponent('{{ __('location_for_inspection') }}');
                    const body = encodeURIComponent(
                        `{{ __('location_for_inspection') }}: ${address}\n\n{{ __('view_google_maps') }}: ${mapsUrl}`
                    );
                    shareEmail.href = `mailto:?subject=${subject}&body=${body}`;

                    // Maps
                    shareMaps.href = mapsUrl;
                    shareMaps.target = '_blank';

                    // Copy link
                    copyAddress.addEventListener('click', function(e) {
                        e.preventDefault();
                        navigator.clipboard.writeText(mapsUrl).then(() => {
                            // Mostrar mensaje de confirmación
                            const originalText = this.innerHTML;
                            this.innerHTML =
                                '<svg class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> {{ __('copied') }}';
                            setTimeout(() => {
                                this.innerHTML = originalText;
                            }, 2000);
                        });
                    });
                };

                // Actualizar enlaces cuando cambie la dirección
                document.getElementById('address_map_input').addEventListener('change', updateShareLinks);

                // Inicializar enlaces
                updateShareLinks();

                // Actualizar enlaces cuando cambie el mapa
                if (typeof google !== 'undefined' && typeof google.maps !== 'undefined') {
                    // Si se usa autocomplete, escuchar ese evento también
                    if (typeof autocomplete !== 'undefined') {
                        google.maps.event.addListener(autocomplete, 'place_changed', updateShareLinks);
                    }
                }
            }
        });
    </script>
@endpush
