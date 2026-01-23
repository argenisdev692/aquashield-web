<x-app-layout>
    {{-- Commenting out Jetstream header to avoid duplicate headers --}}
    {{-- <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Appointments Management') }}
        </h2>
    </x-slot> --}}

    {{-- Dark background container with consistent styling --}}
    <div class="min-h-screen bg-gray-900" style="background-color: #141414;">
        {{-- Header section with title and subtitle --}}
        <div class="p-4 sm:p-6">
            <div class="mb-4 sm:mb-8 text-center sm:text-center md:text-left lg:text-left">
                <h2 class="text-base sm:text-base md:text-2xl lg:text-2xl font-bold text-white mb-2">
                    {{ __('appointments_management_title') }}</h2>
                <p class="text-base sm:text-base md:text-base lg:text-base text-gray-400">
                    {{ __('appointments_management_subtitle') }}
                </p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto py-2 sm:py-4 md:py-2 lg:py-2 px-4 sm:px-6 lg:px-8">
            <!-- Success and error messages -->
            <div id="alertContainer"></div>
            @if (session()->has('message'))
                <x-alerts.success :message="session('message')" />
            @endif
            @if (session()->has('error'))
                <x-alerts.error :message="session('error')" />
            @endif

            <!-- Main container -->
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg">
                <div class="p-6">
                    <!-- Filter and action bar -->
                    <div
                        class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 space-y-4 md:space-y-0">
                        <!-- Search input -->
                        <div class="w-full md:w-1/2 lg:w-2/5">
                            <x-crud.input-search id="searchInput" placeholder="{{ __('search_appointments') }}" />
                        </div>

                        <div
                            class="flex flex-col sm:flex-row items-center w-full md:w-auto space-y-3 sm:space-y-0 sm:space-x-4">
                            <!-- Toggle to show inactive appointments -->
                            <x-crud.toggle-deleted id="showDeleted" label="{{ __('show_inactive_appointments') }}" />

                            <!-- Per page dropdown -->
                            <x-select-input-per-pages name="perPage" id="perPage" class="sm:w-32">
                                <option value="5">5 {{ __('per_page') }}</option>
                                <option value="10" selected>10 {{ __('per_page') }}</option>
                                <option value="15">15 {{ __('per_page') }}</option>
                                <option value="25">25 {{ __('per_page') }}</option>
                                <option value="50">50 {{ __('per_page') }}</option>
                            </x-select-input-per-pages>

                            <!-- Add appointment button -->
                            <div class="w-full sm:w-auto">
                                <a href="{{ route('appointments.create') }}"
                                    class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-800 focus:outline-none focus:border-green-800 focus:ring focus:ring-green-200 disabled:opacity-25">
                                    <span class="mr-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                    </span>
                                    {{ __('add_appointment') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Date range filters -->
                    <div class="flex flex-col sm:flex-row sm:flex-wrap sm:items-end gap-3 py-5 mb-4">
                        <div class="w-full sm:w-auto">
                            <label for="start_date"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('start_date') }}</label>
                            <input type="date" id="start_date" name="start_date"
                                class="w-full sm:w-44 md:w-48 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                        </div>
                        <div class="w-full sm:w-auto">
                            <label for="end_date"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('end_date') }}</label>
                            <input type="date" id="end_date" name="end_date"
                                class="w-full sm:w-44 md:w-48 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                        </div>
                        <div class="w-full sm:w-auto">
                            <label for="status_lead_filter"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('lead_status') }}</label>
                            <select id="status_lead_filter" name="status_lead_filter"
                                class="w-full sm:w-44 md:w-48 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="">{{ __('all_statuses') }}</option>
                                <option value="New">{{ __('new_status') }}</option>
                                <option value="Called">{{ __('called_status') }}</option>
                                <option value="Pending">{{ __('pending_status') }}</option>
                                <option value="Declined">{{ __('declined_status') }}</option>
                            </select>
                        </div>
                        <button id="clearDateFilters" type="button"
                            class="w-full sm:w-auto px-3 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-400 focus:ring focus:ring-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                            {{ __('clear') }}
                        </button>
                        <div class="w-full sm:ml-auto flex flex-col sm:flex-row gap-2 sm:space-x-2 sm:gap-0">
                            <button id="sendRejectionBtn" disabled
                                class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-800 focus:outline-none focus:border-red-800 focus:ring focus:ring-red-200 disabled:opacity-25">
                                <span class="mr-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </span>
                                {{ __('send_rejection') }}
                            </button>
                            <button id="exportToExcel"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring focus:ring-blue-200 disabled:opacity-25">
                                <span class="mr-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                </span>
                                {{ __('excel_export') }}
                            </button>
                        </div>
                    </div>

                    <!-- Appointments table -->
                    <div
                        class="overflow-x-auto bg-gray-50 dark:bg-gray-700 rounded-lg shadow-inner border border-gray-200 dark:border-gray-600">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        <input type="checkbox" id="selectAll"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer sort-header"
                                        data-field="first_name">
                                        {{ __('name') }}
                                        <span class="sort-icon"></span>
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer sort-header"
                                        data-field="email">
                                        {{ __('email') }}
                                        <span class="sort-icon"></span>
                                    </th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('phone') }}
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer sort-header"
                                        data-field="inspection_date">
                                        {{ __('inspection_date') }}
                                        <span class="sort-icon"></span>
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer sort-header"
                                        data-field="inspection_time">
                                        {{ __('inspection_time') }}
                                        <span class="sort-icon"></span>
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer sort-header"
                                        data-field="insurance_property">
                                        {{ __('insurance') }}
                                        <span class="sort-icon"></span>
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer sort-header"
                                        data-field="status_lead">
                                        {{ __('status_lead') }}
                                        <span class="sort-icon"></span>
                                    </th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('inspection_status') }}
                                    </th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="appointmentsTable"
                                class=" dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr id="loadingRow">
                                    <td colspan="10" class="px-6 py-4 text-center">
                                        <svg class="animate-spin h-5 w-5 mr-3 text-blue-500 inline-block"
                                            viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        {{ __('loading_appointments') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div id="pagination" class="mt-4 flex justify-between items-center"></div>
                </div>
            </div>
        </div>

        <!-- Rejection Notification Modal -->
        <div id="rejectionModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title"
            role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div
                    class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100"
                                    id="modal-title">
                                    {{ __('send_rejection_notification') }}
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                    {{ __('select_reason_rejecting') }}
                                </p>

                                <div class="mt-4">
                                    <div class="flex items-start mb-3">
                                        <div class="flex items-center h-5">
                                            <input id="reason_no_contact" name="rejection_reason" type="radio"
                                                value="no_contact"
                                                class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="reason_no_contact"
                                                class="font-medium text-gray-700 dark:text-gray-300">{{ __('unable_to_contact') }}</label>
                                        </div>
                                    </div>

                                    <div class="flex items-start mb-3">
                                        <div class="flex items-center h-5">
                                            <input id="reason_no_insurance" name="rejection_reason" type="radio"
                                                value="no_insurance"
                                                class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="reason_no_insurance"
                                                class="font-medium text-gray-700 dark:text-gray-300">{{ __('no_property_insurance') }}</label>
                                        </div>
                                    </div>

                                    <div class="flex items-start mb-3">
                                        <div class="flex items-center h-5">
                                            <input id="reason_other_option" name="rejection_reason" type="radio"
                                                value="other"
                                                class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="reason_other_option"
                                                class="font-medium text-gray-700 dark:text-gray-300">{{ __('other_reason') }}</label>
                                        </div>
                                    </div>

                                    <div id="other_reason_container" class="mt-4 hidden">
                                        <label for="reason_other"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('specify_other_reason') }}</label>
                                        <textarea id="reason_other" name="reason_other" rows="3"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" id="sendRejectionNotification"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            {{ __('send_notification') }}
                        </button>
                        <button type="button" id="cancelRejection"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                            {{ __('cancel') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @push('scripts')
            <script>
                $(document).ready(function() {
                    // Recuperar estado del toggle de localStorage antes de inicializar el manager
                    const showDeletedState = localStorage.getItem('showDeleted') === 'true';
                    console.log('Estado inicial de showDeleted:', showDeletedState);

                    // Make the manager globally accessible
                    window.appointmentManager = new CrudManager({
                        entityName: 'Appointment',
                        entityNamePlural: 'Appointments',
                        routes: {
                            index: "{{ secure_url(route('appointments.index', [], false)) }}",
                            store: "{{ secure_url(route('appointments.store', [], false)) }}",
                            edit: "{{ secure_url(route('appointments.edit', ':id', false)) }}",
                            update: "{{ secure_url(route('appointments.update', ':id', false)) }}",
                            destroy: "{{ secure_url(route('appointments.destroy', ':id', false)) }}",
                            restore: "{{ secure_url(route('appointments.restore', ':id', false)) }}",
                            checkName: "{{ secure_url(route('appointments.check-email', [], false)) }}",
                            sendRejection: "{{ secure_url(route('appointments.send-rejection', [], false)) }}"
                        },
                        tableSelector: '#appointmentsTable',
                        searchSelector: '#searchInput',
                        perPageSelector: '#perPage',
                        showDeletedSelector: '#showDeleted',
                        paginationSelector: '#pagination',
                        alertSelector: '#alertContainer',
                        // Date filter selectors
                        startDateSelector: '#start_date',
                        endDateSelector: '#end_date',
                        clearDateFilterSelector: '#clearDateFilters',
                        statusLeadFilterSelector: '#status_lead_filter',
                        idField: 'uuid',
                        searchFields: ['first_name', 'last_name', 'email', 'status_lead', 'phone'],
                        // Establecer el valor inicial basado en localStorage
                        showDeleted: showDeletedState,
                        tableHeaders: [{
                                field: 'checkbox',
                                name: '',
                                sortable: false,
                                getter: (entity) =>
                                    entity.deleted_at ? '' :
                                    `<input type="checkbox" class="appointment-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" data-id="${entity.uuid}">`
                            },
                            {
                                field: 'first_name',
                                name: 'Name',
                                sortable: true,
                                getter: (entity) => `${entity.first_name} ${entity.last_name}`
                            },
                            {
                                field: 'email',
                                name: 'Email',
                                sortable: true
                            },
                            {
                                field: 'phone',
                                name: 'Phone',
                                sortable: false
                            },
                            {
                                field: 'inspection_date',
                                name: 'Inspection Date',
                                sortable: true,
                                getter: (entity) => entity.inspection_date ? new Date(entity.inspection_date)
                                    .toLocaleDateString() : 'N/A'
                            },
                            {
                                field: 'inspection_time',
                                name: 'Inspection Time',
                                sortable: true,
                                getter: (entity) => entity.inspection_time ? new Date(
                                        `2000-01-01T${entity.inspection_time}`)
                                    .toLocaleTimeString([], {
                                        hour: '2-digit',
                                        minute: '2-digit'
                                    }) : 'N/A'
                            },
                            {
                                field: 'insurance_property',
                                name: 'Insurance',
                                sortable: true,
                                getter: (entity) => {
                                    if (entity.insurance_property === true) {
                                        return '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Yes</span>';
                                    } else {
                                        return '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">No</span>';
                                    }
                                }
                            },
                            {
                                field: 'status_lead',
                                name: 'Status',
                                sortable: true,
                                getter: (entity) => {
                                    const statusMap = {
                                        'New': '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">New</span>',
                                        'Called': '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Called</span>',
                                        'Pending': '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">Pending</span>',
                                        'Declined': '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">Declined</span>'
                                    };
                                    return entity.status_lead ? statusMap[entity.status_lead] || entity
                                        .status_lead : 'N/A';
                                }
                            },
                            {
                                field: 'inspection_status',
                                name: 'Inspection Status',
                                sortable: true,
                                getter: (entity) => {
                                    const statusMap = {
                                        'Confirmed': '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">Confirmed</span>',
                                        'Completed': '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Completed</span>',
                                        'Pending': '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">Pending</span>',
                                        'Declined': '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">Declined</span>'
                                    };
                                    return entity.inspection_status ? statusMap[entity.inspection_status] ||
                                        entity
                                        .inspection_status : 'N/A';
                                }
                            },
                            {
                                field: 'actions',
                                name: 'Actions',
                                sortable: false,
                                getter: (appointment) => {
                                    const editUrl = `/appointments/${appointment.uuid}/edit`;

                                    let actionsHtml = `
                                <div class="flex justify-center space-x-2">
                                    <a href="${editUrl}" class="inline-flex items-center justify-center w-9 h-9 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105" title="Edit Appointment">
                                         <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                             <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                         </svg>
                                    </a>`;

                                    // Botón para compartir ubicación - Se agrega independientemente del estado de borrado
                                    if (appointment.latitude && appointment.longitude) {
                                        const address =
                                            `${appointment.address || ''}, ${appointment.city || ''}, ${appointment.state || ''} ${appointment.zipcode || ''}`;
                                        actionsHtml += `
                                    <button data-id="${appointment.uuid}" data-lat="${appointment.latitude}" data-lng="${appointment.longitude}" data-address="${address}" class="share-location inline-flex items-center justify-center w-9 h-9 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:from-green-600 hover:to-green-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105" title="Share Location">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </button>`;
                                    } else {
                                        // Si no hay coordenadas, aún mostrar el botón pero con un comportamiento diferente
                                        actionsHtml += `
                                    <button data-id="${appointment.uuid}" data-no-coords="true" class="share-location inline-flex items-center justify-center w-9 h-9 bg-gradient-to-r from-gray-400 to-gray-500 text-white rounded-lg cursor-not-allowed opacity-60" title="No Location Available">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"/>
                                        </svg>
                                    </button>`;
                                    }

                                    if (appointment.deleted_at) {
                                        actionsHtml += `
                                    <button data-id="${appointment.uuid}" class="restore-btn inline-flex items-center justify-center w-9 h-9 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-lg hover:from-emerald-600 hover:to-emerald-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105" title="Restore Appointment">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                    </button>`;
                                    } else {
                                        actionsHtml += `
                                    <button data-id="${appointment.uuid}" class="delete-btn inline-flex items-center justify-center w-9 h-9 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg hover:from-red-600 hover:to-red-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105" title="Delete Appointment">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>`;
                                    }

                                    actionsHtml += `</div>`;
                                    return actionsHtml;
                                }
                            }
                        ],
                        validationFields: [{
                                name: 'first_name',
                                errorMessage: 'Please enter a valid first name.'
                            },
                            {
                                name: 'last_name',
                                errorMessage: 'Please enter a valid last name.'
                            },
                            {
                                name: 'email',
                                validation: {
                                    url: "{{ route('appointments.check-email') }}",
                                    delay: 500,
                                    minLength: 5,
                                    errorMessage: 'This email is already taken.',
                                    successMessage: 'Email is available.'
                                },
                                errorMessage: 'Please choose a different email.'
                            },
                            {
                                name: 'phone',
                                errorMessage: 'Please enter a valid phone number.'
                            },
                            {
                                name: 'inspection_date',
                                errorMessage: 'Please enter a valid inspection date.'
                            },
                            {
                                name: 'status_lead',
                                errorMessage: 'Please select a valid lead status.'
                            }
                        ],
                        defaultSortField: 'inspection_date',
                        defaultSortDirection: 'desc'
                    });

                    // Add statusLeadFilter property to appointmentManager
                    window.appointmentManager.statusLeadFilter = '';

                    // Extend the original loadEntities method
                    const originalLoadEntities = window.appointmentManager.loadEntities;
                    window.appointmentManager.loadEntities = function(page = 1) {
                        // Set current page
                        this.currentPage = page;

                        // Show loading state
                        $(this.tableSelector + ' #loadingRow').show();
                        $(this.tableSelector + ' tr:not(#loadingRow)').remove();

                        // Prepare request data
                        const requestData = {
                            page: this.currentPage,
                            per_page: this.perPage,
                            sort_field: this.sortField,
                            sort_direction: this.sortDirection,
                            search: this.searchTerm,
                            show_deleted: this.showDeleted ? "true" : "false",
                        };

                        // Add date filters if they exist
                        if (this.startDate) {
                            requestData.start_date = this.startDate;
                        }

                        if (this.endDate) {
                            requestData.end_date = this.endDate;
                        }

                        // Add status_lead_filter if it exists
                        if (this.statusLeadFilter) {
                            requestData.status_lead_filter = this.statusLeadFilter;
                        }

                        // Make AJAX request
                        $.ajax({
                            url: this.routes.index,
                            type: 'GET',
                            dataType: 'json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                'Accept': 'application/json'
                            },
                            data: requestData,
                            success: (response) => {
                                this.renderAppointmentsTable(response);
                                this.renderPagination(response);
                            },
                            error: (xhr) => {
                                console.error(`Error loading ${this.entityNamePlural}:`, xhr.responseText);

                                // Show error message in table
                                $(this.tableSelector).html(`
                                <tr>
                                    <td colspan="${this.tableHeaders.length}" class="px-6 py-4 text-center text-sm text-red-500">
                                        Error loading ${this.entityNamePlural}. Please check the console for details.
                                    </td>
                                </tr>
                            `);
                            },
                            complete: () => {
                                $(this.tableSelector + ' #loadingRow').hide();
                            }
                        });
                    };

                    // Custom render table method for appointments
                    window.appointmentManager.renderAppointmentsTable = function(data) {
                        const self = this;
                        const entities = data.data;
                        let html = "";

                        if (entities.length === 0) {
                            html =
                                `<tr><td colspan="10" class="px-6 py-4 text-center text-sm text-gray-500">{{ __('no_appointments_found_matching_criteria') }}</td></tr>`;
                        } else {
                            entities.forEach((entity) => {
                                const isDeleted = entity.deleted_at !== null;
                                const rowClass = isDeleted ? "bg-red-50 dark:bg-red-900 opacity-60" : "";

                                html += `<tr class="${rowClass}">`;

                                // Use the table headers to render each cell
                                self.tableHeaders.forEach((header) => {
                                    if (header.field === 'checkbox') {
                                        const checkboxHtml = header.getter ? header.getter(entity) : '';
                                        html +=
                                            `<td class="px-4 py-3 text-center">${checkboxHtml}</td>`;
                                    } else if (header.field === 'actions') {
                                        const actionsHtml = header.getter ? header.getter(entity) : '';
                                        html +=
                                            `<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">${actionsHtml}</td>`;
                                    } else {
                                        let value = header.getter ? header.getter(entity) : entity[
                                            header.field];
                                        html += `<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100 text-center">
                                        ${value}
                                        ${header.field === "first_name" && isDeleted ? '<span class="ml-2 text-xs text-red-500 dark:text-red-400">(Inactive)</span>' : ""}
                                    </td>`;
                                    }
                                });

                                html += `</tr>`;
                            });
                        }

                        // Replace table content
                        $(self.tableSelector).html(html);

                        // Don't attach edit-btn event handlers since we're using direct links
                        // But still attach delete and restore handlers
                        $(self.tableSelector + " .delete-btn").off('click').on("click", function(e) {
                            e.preventDefault();
                            const id = $(this).data("id");
                            self.deleteEntity(id);
                        });

                        $(self.tableSelector + " .restore-btn").off('click').on("click", function(e) {
                            e.preventDefault();
                            const id = $(this).data("id");
                            self.restoreEntity(id);
                        });
                    };

                    // Add event listeners for delete and restore buttons
                    $(document).on('click', '.delete-btn', function() {
                        const id = $(this).data('id');
                        window.appointmentManager.deleteEntity(id);
                    });

                    $(document).on('click', '.restore-btn', function() {
                        const id = $(this).data('id');
                        window.appointmentManager.restoreEntity(id);
                    });

                    // Add event listener for status lead filter
                    $('#status_lead_filter').on('change', function() {
                        // Update the statusLeadFilter property
                        window.appointmentManager.statusLeadFilter = $(this).val();
                        // Reset to first page when changing filter
                        window.appointmentManager.currentPage = 1;
                        // Load entities with new filter
                        window.appointmentManager.loadEntities();
                    });

                    // Add event listeners for date filters to update properties
                    $('#start_date').on('change', function() {
                        window.appointmentManager.startDate = $(this).val();
                        window.appointmentManager.currentPage = 1; // Reset to first page
                        window.appointmentManager.loadEntities();
                    });

                    $('#end_date').on('change', function() {
                        window.appointmentManager.endDate = $(this).val();
                        window.appointmentManager.currentPage = 1; // Reset to first page
                        window.appointmentManager.loadEntities();
                    });

                    // Initialize loading of entities
                    window.appointmentManager.loadEntities();

                    // Update the clear filters button to also clear status filter
                    $('#clearDateFilters').on('click', function() {
                        $('#start_date, #end_date').val('');
                        $('#status_lead_filter').val('');
                        window.appointmentManager.startDate = '';
                        window.appointmentManager.endDate = '';
                        window.appointmentManager.statusLeadFilter = '';
                        window.appointmentManager.loadEntities();
                    });

                    // Handle export to Excel with status filter
                    function exportAppointmentsToExcel() {
                        // Show loading indicator
                        const originalButtonContent = $('#exportToExcel').html();
                        $('#exportToExcel').html(`
                    <svg class="animate-spin h-4 w-4 mr-2 text-white inline-block" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Exporting...
                `).prop('disabled', true);

                        // Gather filter parameters
                        const searchValue = $(window.appointmentManager.searchSelector).val();
                        const showDeleted = $(window.appointmentManager.showDeletedSelector).is(':checked') ? 'true' :
                            'false';
                        const startDate = window.appointmentManager.startDate;
                        const endDate = window.appointmentManager.endDate;
                        const statusLeadFilter = window.appointmentManager.statusLeadFilter;
                        const sortField = window.appointmentManager.sortField;
                        const sortDirection = window.appointmentManager.sortDirection;

                        // Create the URL with query parameters
                        const exportUrl = new URL(window.appointmentManager.routes.index, window.location.origin);
                        exportUrl.searchParams.append('export', 'excel');
                        exportUrl.searchParams.append('search', searchValue);
                        exportUrl.searchParams.append('show_deleted', showDeleted);
                        if (startDate) exportUrl.searchParams.append('start_date', startDate);
                        if (endDate) exportUrl.searchParams.append('end_date', endDate);
                        if (statusLeadFilter) exportUrl.searchParams.append('status_lead_filter', statusLeadFilter);
                        exportUrl.searchParams.append('sort_field', sortField);
                        exportUrl.searchParams.append('sort_direction', sortDirection);

                        // Ensure the button resets after a max time (fallback)
                        const resetTimeout = setTimeout(function() {
                            $('#exportToExcel').html(originalButtonContent).prop('disabled', false);
                        }, 10000); // 10 seconds timeout as fallback

                        try {
                            // Use fetch API instead of iframe for better control
                            fetch(exportUrl.toString())
                                .then(response => {
                                    clearTimeout(resetTimeout);

                                    if (!response.ok) {
                                        throw new Error('Export failed');
                                    }

                                    // Check content disposition to confirm it's a file download
                                    const contentDisposition = response.headers.get('content-disposition');
                                    if (!contentDisposition || !contentDisposition.includes('attachment')) {
                                        throw new Error('Invalid response format');
                                    }

                                    return response.blob();
                                })
                                .then(blob => {
                                    // Create download link
                                    const url = window.URL.createObjectURL(blob);
                                    const a = document.createElement('a');
                                    const filename = 'appointments_export_' + new Date().toISOString().slice(0, 10) +
                                        '.xlsx';

                                    a.href = url;
                                    a.download = filename;
                                    document.body.appendChild(a);
                                    a.click();

                                    // Cleanup
                                    window.URL.revokeObjectURL(url);
                                    a.remove();

                                    // Reset button and show success message
                                    $('#exportToExcel').html(originalButtonContent).prop('disabled', false);

                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Export Successful',
                                        text: 'Your appointments have been exported to Excel',
                                        confirmButtonColor: '#3B82F6'
                                    });
                                })
                                .catch(error => {
                                    console.error('Export error:', error);

                                    // Reset button and show error message
                                    $('#exportToExcel').html(originalButtonContent).prop('disabled', false);

                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Export Failed',
                                        text: 'There was an error exporting to Excel. Please try again.',
                                        confirmButtonColor: '#3B82F6'
                                    });
                                });
                        } catch (error) {
                            // Handle any unexpected errors
                            clearTimeout(resetTimeout);
                            console.error('Unexpected export error:', error);

                            // Reset button and show error message
                            $('#exportToExcel').html(originalButtonContent).prop('disabled', false);

                            Swal.fire({
                                icon: 'error',
                                title: 'Export Failed',
                                text: 'There was an unexpected error. Please try again.',
                                confirmButtonColor: '#3B82F6'
                            });
                        }
                    }

                    // Replace the handle export to Excel
                    $('#exportToExcel').on('click', function() {
                        exportAppointmentsToExcel();
                    });

                    // Selected appointments tracking
                    let selectedAppointments = [];

                    // Handle select all checkbox
                    $(document).on('change', '#selectAll', function() {
                        const isChecked = $(this).prop('checked');
                        $('.appointment-checkbox').prop('checked', isChecked);

                        // Update selected appointments array
                        selectedAppointments = isChecked ?
                            $('.appointment-checkbox').map(function() {
                                return $(this).data('id');
                            }).get() : [];

                        // Enable/disable rejection button
                        updateRejectionButtonState();
                    });

                    // Handle individual checkbox changes
                    $(document).on('change', '.appointment-checkbox', function() {
                        const id = $(this).data('id');

                        if ($(this).prop('checked')) {
                            // Add to selected if not already there
                            if (!selectedAppointments.includes(id)) {
                                selectedAppointments.push(id);
                            }
                        } else {
                            // Remove from selected
                            selectedAppointments = selectedAppointments.filter(item => item !== id);
                            // Uncheck "select all" if any individual checkbox is unchecked
                            $('#selectAll').prop('checked', false);
                        }

                        // Enable/disable rejection button
                        updateRejectionButtonState();
                    });

                    // Update rejection button state
                    function updateRejectionButtonState() {
                        $('#sendRejectionBtn').prop('disabled', selectedAppointments.length === 0);
                    }

                    // Open rejection modal
                    $('#sendRejectionBtn').on('click', function() {
                        $('#rejectionModal').removeClass('hidden');
                    });

                    // Close rejection modal
                    $('#cancelRejection').on('click', function() {
                        $('#rejectionModal').addClass('hidden');
                        resetRejectionForm();
                    });

                    // Submit rejection notification
                    $('#sendRejectionNotification').on('click', function() {
                        // Get selected reason
                        const selectedReason = $('input[name="rejection_reason"]:checked').val();
                        const otherReason = $('#reason_other').val().trim();

                        // Validate a reason is selected
                        if (!selectedReason) {
                            Swal.fire({
                                icon: 'error',
                                title: '{{ __('error_occurred') }}',
                                text: '{{ __('please_select_reason') }}',
                                confirmButtonColor: '#3B82F6'
                            });
                            return;
                        }

                        // If "other" is selected, validate text is provided
                        if (selectedReason === 'other' && otherReason === '') {
                            Swal.fire({
                                icon: 'error',
                                title: '{{ __('error_occurred') }}',
                                text: '{{ __('please_provide_other_reason') }}',
                                confirmButtonColor: '#3B82F6'
                            });
                            return;
                        }

                        // Show loading state
                        const originalBtnText = $('#sendRejectionNotification').text();
                        $('#sendRejectionNotification').html(`
                    <svg class="animate-spin h-4 w-4 mr-2 text-white inline-block" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ __('sending') }}...
                `).prop('disabled', true);

                        // Prepare data based on selected reason
                        const requestData = {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            appointment_ids: selectedAppointments,
                            no_contact: selectedReason === 'no_contact',
                            no_insurance: selectedReason === 'no_insurance',
                            other_reason: selectedReason === 'other' ? otherReason : null
                        };

                        // Send the rejection notification
                        $.ajax({
                            url: window.appointmentManager.routes.sendRejection,
                            type: 'POST',
                            data: requestData,
                            success: function(response) {
                                $('#rejectionModal').addClass('hidden');
                                resetRejectionForm();

                                Swal.fire({
                                    icon: 'success',
                                    title: '{{ __('success_title') }}',
                                    text: '{{ __('rejection_notifications_sent') }}',
                                    confirmButtonColor: '#3B82F6'
                                });

                                // Clear selected appointments
                                selectedAppointments = [];
                                $('.appointment-checkbox, #selectAll').prop('checked', false);
                                updateRejectionButtonState();

                                // Refresh the table
                                window.appointmentManager.loadEntities();
                            },
                            error: function(xhr) {
                                let errorMessage =
                                    '{{ __('rejection_error') }}';

                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }

                                Swal.fire({
                                    icon: 'error',
                                    title: '{{ __('error_occurred') }}',
                                    text: errorMessage,
                                    confirmButtonColor: '#3B82F6'
                                });
                            },
                            complete: function() {
                                $('#sendRejectionNotification').text(originalBtnText).prop('disabled',
                                    false);
                            }
                        });
                    });

                    // Reset rejection form
                    function resetRejectionForm() {
                        $('input[name="rejection_reason"]').prop('checked', false);
                        $('#reason_other').val('');
                        $('#other_reason_container').addClass('hidden');
                    }

                    // Toggle other reason textarea visibility
                    $(document).on('change', 'input[name="rejection_reason"]', function() {
                        if ($(this).val() === 'other') {
                            $('#other_reason_container').removeClass('hidden');
                        } else {
                            $('#other_reason_container').addClass('hidden');
                        }
                    });

                    // Compartir ubicación desde el listado
                    $(document).on('click', '.share-location', function(e) {
                        e.preventDefault();

                        // Verificar si no hay coordenadas disponibles
                        if ($(this).data('no-coords')) {
                            Swal.fire({
                                icon: 'warning',
                                title: '{{ __('no_location_title') }}',
                                text: '{{ __('no_coordinates_edit_appointment') }}',
                                confirmButtonColor: '#3B82F6'
                            });
                            return;
                        }

                        const lat = $(this).data('lat');
                        const lng = $(this).data('lng');
                        const address = $(this).data('address');

                        if (!lat || !lng) {
                            Swal.fire({
                                icon: 'error',
                                title: '{{ __('error_title') }}',
                                text: '{{ __('no_coordinates_address') }}',
                                confirmButtonColor: '#3B82F6'
                            });
                            return;
                        }

                        const mapsUrl = `https://www.google.com/maps?q=${lat},${lng}`;

                        // Mostrar opciones de compartir
                        Swal.fire({
                            title: '{{ __('share_location_title') }}',
                            html: `
                        <div class="p-6">
                            <div class="mb-6 text-center">
                                <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 dark:bg-blue-900 rounded-full mb-4">
                                    <svg class="w-8 h-8 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <p class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">${address}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('choose_how_share_location') }}</p>
                            </div>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
                                <a href="https://wa.me/?text=${encodeURIComponent('{{ __('location_for_inspection') }} ' + address + ' - ' + mapsUrl)}" target="_blank" class="flex flex-col items-center justify-center p-4 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-all duration-200 transform hover:scale-105 shadow-md">
                                    <svg class="w-6 h-6 mb-2" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                                    </svg>
                                    <span class="text-sm font-medium">{{ __('whatsapp') }}</span>
                                </a>
                                
                                <a href="mailto:?subject=${encodeURIComponent('{{ __('location_for_inspection') }}')}&body=${encodeURIComponent('{{ __('location_for_inspection') }} ' + address + '\n\n{{ __('view_google_maps') }} ' + mapsUrl)}" class="flex flex-col items-center justify-center p-4 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-all duration-200 transform hover:scale-105 shadow-md">
                                    <svg class="w-6 h-6 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-sm font-medium">{{ __('email_share') }}</span>
                                </a>
                                
                                <a href="${mapsUrl}" target="_blank" class="flex flex-col items-center justify-center p-4 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-all duration-200 transform hover:scale-105 shadow-md">
                                    <svg class="w-6 h-6 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                    </svg>
                                    <span class="text-sm font-medium">{{ __('open_maps') }}</span>
                                </a>
                                
                                <button id="copy-map-link" class="flex flex-col items-center justify-center p-4 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-all duration-200 transform hover:scale-105 shadow-md">
                                    <svg class="w-6 h-6 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-sm font-medium">{{ __('copy_link') }}</span>
                            </button>
                            </div>
                            
                            <div class="text-center text-sm text-gray-500 dark:text-gray-400">
                                <p>Coordinates: ${lat}, ${lng}</p>
                            </div>
                        </div>
                    `,
                            showConfirmButton: false,
                            showCloseButton: true,
                            customClass: {
                                container: 'swal-fullscreen',
                                popup: 'swal-fullscreen-popup',
                                closeButton: 'custom-close-button'
                            },
                            width: '95%',
                            heightAuto: false
                        });

                        // Copiar enlace
                        $(document).on('click', '#copy-map-link', function() {
                            navigator.clipboard.writeText(mapsUrl).then(() => {
                                $(this).text('{{ __('copied') }}');
                                setTimeout(() => {
                                    $(this).text('{{ __('copy_link') }}');
                                }, 2000);
                            });
                        });
                    });
                });
            </script>
        @endpush

        <style>
            .swal-fullscreen .swal2-container {
                padding: 1rem !important;
            }

            .swal-fullscreen-popup {
                width: 95vw !important;
                max-width: none !important;
                height: 90vh !important;
                max-height: none !important;
                margin: 0 !important;
                border-radius: 1rem !important;
                overflow-y: auto;
            }

            .swal-fullscreen .swal2-popup {
                width: 95vw !important;
                max-width: none !important;
                height: 90vh !important;
                max-height: none !important;
                display: flex !important;
                flex-direction: column !important;
            }

            .swal-fullscreen .swal2-html-container {
                flex: 1 !important;
                overflow-y: auto !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            /* Custom close button styles */
            .custom-close-button {
                background-color: #dc2626 !important;
                color: white !important;
                border-radius: 50% !important;
                width: 40px !important;
                height: 40px !important;
                font-size: 20px !important;
                font-weight: bold !important;
                border: none !important;
                position: absolute !important;
                top: 15px !important;
                right: 15px !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                transition: all 0.3s ease !important;
                box-shadow: 0 2px 8px rgba(220, 38, 38, 0.3) !important;
            }

            .custom-close-button:hover {
                background-color: #b91c1c !important;
                transform: scale(1.1) !important;
                box-shadow: 0 4px 12px rgba(220, 38, 38, 0.4) !important;
            }

            .custom-close-button:focus {
                outline: none !important;
                box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.2) !important;
            }

            @media (max-width: 640px) {
                .swal-fullscreen-popup {
                    width: 98vw !important;
                    height: 95vh !important;
                }

                .swal-fullscreen .swal2-popup {
                    width: 98vw !important;
                    height: 95vh !important;
                }
            }
        </style>
    </div>
</x-app-layout>
