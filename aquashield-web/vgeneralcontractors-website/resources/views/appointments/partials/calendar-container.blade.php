{{-- Calendar Container Component --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="bg-white shadow-sm rounded-lg mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="flex-1 min-w-0">
                    <h1 class="text-2xl font-bold text-gray-900">
                        {{ __('appointment_calendar_title') }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ __('appointment_calendar_subtitle') }}
                    </p>
                </div>

            </div>
        </div>
    </div>

    <!-- Calendar Widget -->
    <div class="bg-white shadow-sm rounded-lg">
        <div class="p-6">
            <div id="calendar"></div>
        </div>
    </div>
</div>

{{-- Meta tags para rutas JavaScript --}}
<meta name="calendar-events-url" content="{{ url()->secure(route('appointment-calendar.events', [], false)) }}">
<meta name="calendar-create-url" content="{{ url()->secure(route('appointment-calendar.store', [], false)) }}">
<meta name="calendar-update-url" content="{{ url()->secure(route('appointment-calendar.update', ':id', false)) }}">
<meta name="calendar-status-url" content="{{ url()->secure(route('appointment-calendar.status', ':id', false)) }}">
<meta name="calendar-clients-url" content="{{ url()->secure(route('appointment-calendar.clients', [], false)) }}">
<meta name="calendar-create-appointment-url"
    content="{{ url()->secure(route('appointment-calendar.createAppointment', [], false)) }}">
