{{-- 
    REFACTORED CALENDAR VIEW
    This is the simplified main calendar view that includes modular components
--}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Appointment Calendar') }}
        </h2>
    </x-slot>

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
        
        <!-- FullCalendar JS -->
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
        
        <!-- Tippy.js for tooltips -->
        <script src="https://unpkg.com/@popperjs/core@2"></script>
        <script src="https://unpkg.com/tippy.js@6"></script>

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
                'success': @json(__('Success')),
                'error': @json(__('Error')),
                'cancel': @json(__('Cancel')),
                'processing': @json(__('Processing')),
                'loading': @json(__('Loading')),
                'saving': @json(__('Saving')),
                'delete': @json(__('Delete')),
                'edit': @json(__('Edit')),
                'view': @json(__('View')),
                'close': @json(__('Close')),
                
                // Calendar specific
                'please_select_client': @json(__('Please select a client')),
                'appointment_created_successfully': @json(__('Appointment created successfully')),
                'appointment_updated_successfully': @json(__('Appointment updated successfully')),
                'appointment_deleted_successfully': @json(__('Appointment deleted successfully')),
                'unexpected_error': @json(__('An unexpected error occurred')),
                
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
                
                // Form validation
                'field_required': @json(__('This field is required')),
                'invalid_email': @json(__('Please enter a valid email address')),
                'invalid_phone': @json(__('Please enter a valid phone number')),
                'time_slot_unavailable': @json(__('This time slot is already booked')),
                
                // Client management
                'no_clients_available': @json(__('No clients available')),
                'loading_clients': @json(__('Loading clients...')),
                'client_load_error': @json(__('Error loading clients')),
                
                // Date/Time
                'select_date_time': @json(__('Please select a date and time')),
                'invalid_date': @json(__('Please select a valid date')),
                'invalid_time': @json(__('Please select a valid time')),
                'past_date_error': @json(__('Cannot schedule appointments in the past')),
                
                // Sharing
                'link_copied': @json(__('Link copied to clipboard')),
                'copy_failed': @json(__('Failed to copy link')),
                'share_location': @json(__('Share Location')),
                'open_in_maps': @json(__('Open in Maps')),
                
                // Status messages
                'loading_events': @json(__('Loading events...')),
                'no_events_found': @json(__('No events found')),
                'calendar_refresh': @json(__('Calendar refreshed')),
                
                // Keyboard shortcuts
                'keyboard_shortcuts_help': @json(__('Press ESC to close modals, F5 to refresh, Ctrl+N for new appointment'))
            };

            // Language change prevention fix
            document.addEventListener('click', function(e) {
                const target = e.target.closest('a[href*="/lang/"]');
                if (target) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    window.location.href = target.href;
                    return false;
                }
            }, true);
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
</x-app-layout>