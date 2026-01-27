{{-- 
    REFACTORED CALENDAR VIEW
    This is the simplified main calendar view that includes modular components
--}}
<x-app-layout>
    {{-- Dark background container with consistent styling --}}
    <div class="min-h-screen bg-gray-900" style="background-color: #141414;">
        {{-- Header section with title and subtitle --}}
        <div class="p-4 sm:p-6">
            <div class="mb-4 sm:mb-8 text-center sm:text-center md:text-left lg:text-left">
                <div class="flex flex-col md:flex-row md:justify-between md:items-center">
                    <div>
                        <h2 class="text-base sm:text-base md:text-2xl lg:text-2xl font-bold text-white mb-2">
                            {{ __('appointment_calendar_title') }}
                        </h2>
                        <p class="text-base sm:text-base md:text-base lg:text-base text-gray-400">
                            {{ __('appointment_calendar_subtitle') }}
                        </p>
                    </div>

                </div>
            </div>
        </div>

        {{-- Main content area --}}
        <div class="py-2 sm:py-4 md:py-2 lg:py-2">
            <div class="max-w-7xl mx-auto py-2 px-4 sm:py-4 sm:px-6 lg:px-8">
                <!-- Main container -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-lg">
                    <div class="p-6">
                        <!-- Main Calendar Container -->
                        @include('appointments.partials.calendar-container')

                        <!-- Event Detail Modal -->
                        @include('appointments.partials.event-detail-modal')

                        <!-- New Appointment Modal -->
                        @include('appointments.partials.new-appointment-modal')

                        {{-- Push JavaScript to the stack --}}
                        @push('scripts')
                            <!-- FullCalendar CSS -->
                            <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />

                            <!-- jQuery (required dependency) -->
                            <script src="https://code.jquery.com/jquery-3.7.1.min.js"
                                integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

                            <!-- SweetAlert2 (required dependency) -->
                            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.4/dist/sweetalert2.all.min.js"></script>

                            <!-- FullCalendar JS -->
                            <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>

                            <!-- Tippy.js for tooltips -->
                            <script src="https://unpkg.com/@popperjs/core@2"></script>
                            <script src="https://unpkg.com/tippy.js@6"></script>

                            <!-- Google Maps API -->
                            <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places"
                                async defer></script>

                            <!-- Calendar JavaScript Modules -->
                            <script src="{{ asset('js/calendar-utils.js') }}"></script>
                            <script src="{{ asset('js/calendar-config.js') }}"></script>
                            <script src="{{ asset('js/calendar-api.js') }}"></script>
                            <script src="{{ asset('js/calendar-events.js') }}"></script>
                            <script src="{{ asset('js/calendar-modals.js') }}"></script>
                            <script src="{{ asset('js/calendar-main.js') }}"></script>

                            <!-- Initialize translations for JavaScript -->
                            <script>
                                // Global translations object for JavaScript modules
                                window.translations = {
                                    // General
                                    'success': @json(__('success')),
                                    'error': @json(__('error')),
                                    'ok': @json(__('ok')),
                                    'cancel': @json(__('cancel')),
                                    'processing': @json(__('Processing')),
                                    'loading': @json(__('Loading')),
                                    'saving': @json(__('Saving')),
                                    'delete': @json(__('Delete')),
                                    'edit': @json(__('Edit')),
                                    'view': @json(__('View')),
                                    'close': @json(__('Close')),

                                    // Calendar specific
                                    'please_select_client': @json(__('please_select_client')),
                                    'select_client_3_hours': @json(__('select_client_3_hours')),
                                    'select_lead_source': @json(__('select_lead_source')),
                                    'create_lead': @json(__('create_lead')),
                                    'create_new_client': @json(__('create_new_client')),
                                    'create_confirmed_appointment': @json(__('create_confirmed_appointment')),
                                    'appointment_created_successfully': @json(__('Appointment created successfully')),
                                    'appointment_updated_successfully': @json(__('Appointment updated successfully')),
                                    'appointment_deleted_successfully': @json(__('Appointment deleted successfully')),
                                    'unexpected_error': @json(__('an_unexpected_error_occurred')),
                                    'selected_appointment_time': @json(__('selected_appointment_time')),
                                    'select_time_from_calendar': @json(__('select_time_from_calendar')),
                                    'loading_clients': @json(__('loading_clients')),
                                    'no_clients_available': @json(__('no_clients_available')),
                                    'time_slot_unavailable': @json(__('time_slot_unavailable')),

                                    // Appointment actions
                                    'reschedule_appointment': @json(__('Reschedule Appointment')),
                                    'move_appointment_to': @json(__('Move appointment to {newTime}')),
                                    'yes_move': @json(__('Yes, move it')),
                                    'moved': @json(__('Moved!')),
                                    'could_not_update_appointment': @json(__('Could not update appointment')),

                                    // Confirm/Decline
                                    'confirm_appointment_title': @json(__('Confirm Appointment')),
                                    'confirm_appointment_text': @json(__('Are you sure you want to confirm this appointment?')),
                                    'yes_confirm': @json(__('Yes, confirm')),
                                    'confirmed': @json(__('Confirmed!')),
                                    'could_not_confirm_appointment': @json(__('Could not confirm appointment')),

                                    'decline_appointment_title': @json(__('Decline Appointment')),
                                    'decline_appointment_text': @json(__('Are you sure you want to decline this appointment?')),
                                    'yes_decline': @json(__('Yes, decline')),
                                    'declined': @json(__('Declined!')),
                                    'could_not_decline_appointment': @json(__('Could not decline appointment')),

                                    // Delete actions
                                    'deleted': @json(__('deleted')),
                                    'could_not_delete_event': @json(__('could_not_delete_event')),
                                    'confirm_delete_title': @json(__('Are you sure?')),
                                    'confirm_delete_text': @json(__('You won\'t be able to revert this!')),
                                    'yes_delete': @json(__('Yes, delete it!')),

                                    // Form validation
                                    'validation_error_title': @json(__('validation_error_title')),
                                    'please_correct_form_errors': @json(__('please_correct_form_errors')),
                                    'field_required': @json(__('This field is required')),
                                    'first_name_required': @json(__('first_name_required')),
                                    'last_name_required': @json(__('last_name_required')),
                                    'email_required': @json(__('email_required')),
                                    'phone_required': @json(__('phone_required')),
                                    'please_select_insurance_option': @json(__('please_select_insurance_option')),
                                    'invalid_email': @json(__('invalid_email')),
                                    'invalid_email_format': @json(__('invalid_email_format')),
                                    'invalid_phone_format': @json(__('invalid_phone_format')),
                                    'email_already_registered': @json(__('email_already_registered')),
                                    'email_already_exists': @json(__('email_already_exists')),
                                    'phone_already_exists': @json(__('phone_already_exists')),
                                    'invalid_name': @json(__('Please enter a valid name')),
                                    'invalid_name_format': @json(__('Please enter a valid {field}')),
                                    'username_required': @json(__('Username is required')),
                                    'username_min_length': @json(__('Username must be at least 7 characters')),
                                    'username_min_numbers': @json(__('Username must contain at least 2 numbers')),
                                    'username_already_exists': @json(__('This username is already in use')),
                                    'invalid_phone': @json(__('Please enter a valid phone number')),
                                    'time_slot_unavailable': @json(__('time_slot_unavailable')),

                                    // Client management
                                    'no_clients_available': @json(__('no_clients_available')),
                                    'loading_clients': @json(__('loading_clients')),
                                    'client_load_error': @json(__('client_load_error')),
                                    'new_appointment': @json(__('New Appointment')),

                                    // Date/Time
                                    'select_date_time': @json(__('Please select a date and time')),
                                    'invalid_date': @json(__('Please select a valid date')),
                                    'invalid_time': @json(__('Please select a valid time')),
                                    'past_date_error': @json(__('past_date_error')),

                                    // Sharing
                                    'link_copied': @json(__('Link copied to clipboard')),
                                    'copy_failed': @json(__('Failed to copy link')),
                                    'share_location': @json(__('Share Location')),
                                    'open_in_maps': @json(__('Open in Maps')),

                                    // Form placeholders
                                    'first_name_placeholder': @json(__('first_name_placeholder')),
                                    'last_name_placeholder': @json(__('last_name_placeholder')),
                                    'email_placeholder': @json(__('email_placeholder')),
                                    'phone_placeholder': @json(__('phone_placeholder')),
                                    'address_placeholder': @json(__('address_placeholder')),
                                    'notes_placeholder': @json(__('notes_placeholder')),

                                    // Process states
                                    'creating': @json(__('creating')),
                                    'updating': @json(__('updating')),
                                    'deleting': @json(__('deleting')),
                                    'hide': @json(__('hide')),
                                    'show': @json(__('show')),

                                    // Status messages
                                    'loading_events': @json(__('Loading events...')),
                                    'no_events_found': @json(__('No events found')),
                                    'calendar_refresh': @json(__('calendar_refresh')),

                                    // Keyboard shortcuts
                                    'keyboard_shortcuts_help': @json(__('keyboard_shortcuts_help'))
                                };

                                // Improved language change handling
                                document.addEventListener('click', function(e) {
                                    const target = e.target.closest('a[href*="/lang/"]');
                                    if (target) {
                                        e.preventDefault();

                                        // Get current page URL without the domain
                                        const currentPath = window.location.pathname;
                                        const targetUrl = new URL(target.href, window.location.origin);

                                        // Extract locale from the target URL
                                        const locale = targetUrl.pathname.split('/lang/')[1];

                                        // Construct the proper redirect URL
                                        const redirectUrl = `/lang/${locale}?redirect=${encodeURIComponent(currentPath)}`;

                                        window.location.href = redirectUrl;
                                        return false;
                                    }
                                }, true);

                                // Enhanced debug script to identify module loading issues
                                setTimeout(() => {
                                    console.log('=== ENHANCED CALENDAR DEBUG INFO ===');

                                    // Check basic dependencies
                                    console.log('Dependencies Check:');
                                    console.log('- Calendar element exists:', !!document.getElementById('calendar'));
                                    console.log('- FullCalendar loaded:', typeof FullCalendar !== 'undefined');
                                    console.log('- jQuery loaded:', typeof $ !== 'undefined');
                                    console.log('- SweetAlert2 loaded:', typeof Swal !== 'undefined');
                                    console.log('- Tippy.js loaded:', typeof tippy !== 'undefined');

                                    // Check calendar modules
                                    console.log('Calendar Modules Check:');
                                    console.log('- CalendarUtils:', typeof CalendarUtils !== 'undefined' ? '✓' : '✗');
                                    console.log('- CalendarConfig:', typeof CalendarConfig !== 'undefined' ? '✓' : '✗');
                                    console.log('- CalendarEvents:', typeof CalendarEvents !== 'undefined' ? '✓' : '✗');
                                    console.log('- CalendarModals:', typeof CalendarModals !== 'undefined' ? '✓' : '✗');
                                    console.log('- CalendarAPI:', typeof CalendarAPI !== 'undefined' ? '✓' : '✗');
                                    console.log('- CalendarMain:', typeof CalendarMain !== 'undefined' ? '✓' : '✗');

                                    // Check window objects
                                    console.log('Window Objects:');
                                    console.log('- window.CalendarUtils:', !!window.CalendarUtils ? '✓' : '✗');
                                    console.log('- window.CalendarConfig:', !!window.CalendarConfig ? '✗' : '✗');
                                    console.log('- window.CalendarEvents:', !!window.CalendarEvents ? '✗' : '✗');
                                    console.log('- window.CalendarModals:', !!window.CalendarModals ? '✗' : '✗');
                                    console.log('- window.CalendarAPI:', !!window.CalendarAPI ? '✗' : '✗');
                                    console.log('- window.calendarMain:', !!window.calendarMain ? '✓' : '✗');

                                    if (window.calendarMain) {
                                        console.log('Calendar Main Status:', window.calendarMain.getStatus());
                                    }

                                    // Check if calendar has any visible content
                                    const calendarEl = document.getElementById('calendar');
                                    if (calendarEl) {
                                        console.log('Calendar Element Info:', {
                                            width: calendarEl.offsetWidth,
                                            height: calendarEl.offsetHeight,
                                            display: getComputedStyle(calendarEl).display,
                                            visibility: getComputedStyle(calendarEl).visibility,
                                            innerHTML_length: calendarEl.innerHTML.length
                                        });
                                    }

                                    // Check for JavaScript errors
                                    console.log('Checking for script errors...');

                                    // Try to manually verify each module
                                    try {
                                        if (typeof CalendarUtils !== 'undefined') {
                                            console.log('CalendarUtils test:', CalendarUtils.formatPhoneNumber('1234567890'));
                                        }
                                    } catch (e) {
                                        console.error('CalendarUtils error:', e);
                                    }

                                    console.log('=== END ENHANCED DEBUG INFO ===');
                                }, 3000);
                            </script>
                        @endpush

                        {{-- Push CSS to the stack --}}
                        @push('styles')
                            <style>
                                /* Additional calendar-specific styles that need to be inline */
                                .fc-event-dragging .fc-event {
                                    opacity: 0.75;
                                }

                                .fc-event-resizing .fc-event {
                                    opacity: 0.75;
                                }

                                /* Loading state */
                                .calendar-loading {
                                    position: relative;
                                }

                                .calendar-loading::before {
                                    content: '';
                                    position: absolute;
                                    top: 0;
                                    left: 0;
                                    right: 0;
                                    bottom: 0;
                                    background: rgba(255, 255, 255, 0.8);
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    z-index: 1000;
                                }

                                /* Custom event styling */
                                .fc-event-content-custom {
                                    position: relative;
                                    background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
                                    backdrop-filter: blur(10px);
                                    border-radius: 8px;
                                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                                    transition: all 0.3s ease;
                                }

                                .fc-event-content-custom:hover {
                                    transform: translateY(-2px);
                                    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
                                    background: linear-gradient(135deg, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0.08) 100%);
                                }

                                .fc-event-content-custom:before {
                                    content: '';
                                    position: absolute;
                                    top: 0;
                                    left: 0;
                                    right: 0;
                                    height: 3px;
                                    background: linear-gradient(90deg, rgba(255, 255, 255, 0.6), rgba(255, 255, 255, 0.2));
                                    border-radius: 8px 8px 0 0;
                                }

                                /* Event content typography improvements */
                                .client-title {
                                    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
                                    line-height: 1.2;
                                }

                                .event-time {
                                    font-family: 'JetBrains Mono', 'SF Mono', Monaco, 'Cascadia Code', monospace;
                                    font-variant-numeric: tabular-nums;
                                }

                                .status-badge {
                                    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
                                    text-rendering: optimizeLegibility;
                                    -webkit-font-smoothing: antialiased;
                                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                                    transition: all 0.2s ease;
                                }

                                .status-badge:hover {
                                    transform: scale(1.05);
                                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
                                }

                                /* Specific hover effects for confirmed status */
                                .fc-event-confirmed .status-badge:hover {
                                    background-color: #f0fdf4 !important;
                                    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3) !important;
                                }

                                /* Specific event type styling */
                                .fc-event.fc-event-confirmed .fc-event-content-custom {
                                    border-left: 4px solid #10b981;
                                }

                                .fc-event.fc-event-completed .fc-event-content-custom {
                                    border-left: 4px solid #22c55e;
                                }

                                .fc-event.fc-event-pending .fc-event-content-custom {
                                    border-left: 4px solid #f59e0b;
                                }

                                .fc-event.fc-event-declined .fc-event-content-custom {
                                    border-left: 4px solid #ef4444;
                                }

                                /* Temporary selection event styling */
                                .temp-selection-event {
                                    opacity: 0.85 !important;
                                    border: 2px dashed #1d4ed8 !important;
                                    background: linear-gradient(45deg, #3b82f6, #60a5fa) !important;
                                    animation: pulse-selection 2s infinite;
                                }

                                .temp-selection-event .fc-event-title {
                                    font-weight: bold !important;
                                    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3) !important;
                                }

                                @keyframes pulse-selection {

                                    0%,
                                    100% {
                                        opacity: 0.85;
                                    }

                                    50% {
                                        opacity: 0.95;
                                    }
                                }

                                /* Mobile responsiveness */
                                @media (max-width: 768px) {
                                    .fc-event-content-custom {
                                        padding: 4px 6px !important;
                                    }

                                    .client-title {
                                        font-size: 11px !important;
                                    }

                                    .event-time {
                                        font-size: 9px !important;
                                    }

                                    .status-badge {
                                        font-size: 8px !important;
                                        padding: 1px 4px !important;
                                    }
                                }

                                /* Calendar grid improvements */
                                .fc-timegrid-slot {
                                    border-color: #e5e7eb;
                                }

                                .fc-timegrid-slot:hover {
                                    background-color: rgba(59, 130, 246, 0.05);
                                }

                                .fc-day-today {
                                    background-color: rgba(59, 130, 246, 0.08) !important;
                                }

                                .fc-timegrid-now-indicator-line {
                                    border-color: #ef4444;
                                    border-width: 2px;
                                }

                                .fc-timegrid-now-indicator-arrow {
                                    border-top-color: #ef4444;
                                    border-bottom-color: #ef4444;
                                }

                                /* Print styles */
                                @media print {
                                    .fc-header-toolbar {
                                        display: none;
                                    }

                                    .fc-button-group {
                                        display: none;
                                    }
                                }
                            </style>
                        @endpush
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
