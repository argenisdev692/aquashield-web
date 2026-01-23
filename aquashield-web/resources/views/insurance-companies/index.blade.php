<x-crud.index-layout title="{{ __('insurance_companies_management') }}"
    subtitle="{{ __('manage_insurance_companies_subtitle') }}" entity-name="{{ __('insurance_company') }}"
    entity-name-plural="{{ __('insurance_companies') }}" search-placeholder="{{ __('search_insurance_companies') }}"
    show-deleted-label="{{ __('show_inactive_records') }}" add-new-label="{{ __('add_insurance_company') }}"
    manager-name="insuranceCompanyManager" table-id="insuranceCompanyTable" create-button-id="createInsuranceCompanyBtn"
    search-id="searchInput" show-deleted-id="showDeleted" per-page-id="perPage" pagination-id="pagination"
    alert-id="alertContainer" :table-columns="[
        ['field' => 'nro', 'label' => __('nro'), 'sortable' => false],
        ['field' => 'insurance_company_name', 'label' => __('company_name'), 'sortable' => true],
        ['field' => 'email', 'label' => __('email'), 'sortable' => true],
        ['field' => 'phone', 'label' => __('phone'), 'sortable' => false],
        ['field' => 'website', 'label' => __('website'), 'sortable' => false],
        ['field' => 'created_at', 'label' => __('created'), 'sortable' => true],
        ['field' => 'actions', 'label' => __('actions'), 'sortable' => false],
    ]">

    @push('scripts')
        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            $(document).ready(function() {
                // Recuperar estado del toggle de localStorage antes de inicializar el manager
                const showDeletedState = localStorage.getItem('showDeleted') === 'true';
                console.log('Estado inicial de showDeleted:', showDeletedState);

                // Define translations object for use in table headers
                const translations = {
                    companyName: "{{ __('company_name') }}",
                    email: "{{ __('email') }}",
                    phone: "{{ __('phone') }}",
                    website: "{{ __('website') }}",
                    created: "{{ __('created') }}",
                    actions: "{{ __('actions') }}",
                    notAvailable: "{{ __('not_available') }}",
                    noRecordsFound: "{{ __('no_data') }}",
                    editInsuranceCompany: "{{ __('edit') }} {{ __('insurance_company') }}",
                    deleteInsuranceCompany: "{{ __('delete') }} {{ __('insurance_company') }}",
                    restoreInsuranceCompany: "{{ __('restore') }} {{ __('insurance_company') }}"
                };

                // Make the manager globally accessible
                window.insuranceCompanyManager = new CrudManagerModal({
                    entityName: 'Insurance Company',
                    entityNamePlural: 'Insurance Companies',
                    routes: {
                        index: "{{ secure_url(route('insurance-companies.index', [], false)) }}",
                        store: "{{ secure_url(route('insurance-companies.store', [], false)) }}",
                        edit: "{{ secure_url(route('insurance-companies.edit', ':id', false)) }}",
                        update: "{{ secure_url(route('insurance-companies.update', ':id', false)) }}",
                        destroy: "{{ secure_url(route('insurance-companies.destroy', ':id', false)) }}",
                        restore: "{{ secure_url(route('insurance-companies.restore', ':id', false)) }}",
                        checkEmail: "{{ secure_url(route('insurance-companies.check-email', [], false)) }}",
                        checkPhone: "{{ secure_url(route('insurance-companies.check-phone', [], false)) }}",
                        checkName: "{{ secure_url(route('insurance-companies.check-name', [], false)) }}",
                        exportExcel: "{{ secure_url(route('insurance-companies.export-excel', [], false)) }}",
                        exportPdf: "{{ secure_url(route('insurance-companies.export-pdf', [], false)) }}",
                        bulkExport: "{{ secure_url(route('insurance-companies.bulk-export', [], false)) }}"
                    },
                    tableSelector: '#insuranceCompanyTable-body',
                    searchSelector: '#searchInput',
                    perPageSelector: '#perPage',
                    showDeletedSelector: '#showDeleted',
                    paginationSelector: '#pagination',
                    alertSelector: '#alertContainer',
                    createButtonSelector: '#createInsuranceCompanyBtn',
                    idField: 'uuid',
                    searchFields: ['insurance_company_name', 'address', 'email', 'phone', 'website'],
                    // Establecer el valor inicial basado en localStorage
                    showDeleted: showDeletedState,
                    // Configuración de filtros de fecha
                    dateField: 'created_at', // Campo de fecha por defecto
                    // Configuración de numeración secuencial
                    showSequentialNumbers: true, // Deshabilitado porque ya está en table-columns
                    sequentialNumberLabel: "{{ __('nro') }}",
                    entityConfig: {
                        identifierField: 'insurance_company_name',
                        displayName: 'insurance company',
                        fallbackFields: ['email', 'address'],
                        detailFormat: (entity) => entity.insurance_company_name
                    },
                    formFields: [{
                            name: 'insurance_company_name',
                            type: 'text',
                            label: "{{ __('company_name') }}",
                            placeholder: "{{ __('enter_company_name') }}",
                            required: true,
                            validation: {
                                required: true,
                                minLength: 2,
                                maxLength: 255,
                                unique: {
                                    url: "{{ route('insurance-companies.check-name') }}",
                                    errorMessage: "{{ __('name_already_in_use') }}",
                                    successMessage: "{{ __('name_available') }}"
                                }
                            },
                            capitalize: true
                        },
                        {
                            name: 'address',
                            type: 'textarea',
                            label: "{{ __('address') }}",
                            placeholder: "{{ __('enter_address') }}",
                            required: false,
                            rows: 3,
                            validation: {
                                required: false,
                                minLength: 10,
                                maxLength: 500
                            },
                            capitalize: true
                        },
                        {
                            name: 'email',
                            type: 'email',
                            label: "{{ __('email') }}",
                            placeholder: "{{ __('email') }}",
                            required: false,
                            validation: {
                                required: false,
                                email: true,
                                unique: {
                                    url: "{{ route('insurance-companies.check-email') }}",
                                    errorMessage: "{{ __('email_already_in_use') }}",
                                    successMessage: "{{ __('email_available') }}"
                                }
                            }
                        },
                        {
                            name: 'phone',
                            type: 'tel',
                            label: "{{ __('phone') }}",
                            placeholder: "{{ __('enter_phone_number') }}",
                            required: false,
                            validation: {
                                required: false,
                                unique: {
                                    url: "{{ route('insurance-companies.check-phone') }}",
                                    message: "{{ __('phone_already_in_use') }}"
                                }
                            }
                        },
                        {
                            name: 'website',
                            type: 'url',
                            label: "{{ __('website') }}",
                            placeholder: "{{ __('website_placeholder') }}",
                            required: false,
                            validation: {
                                required: false,
                                url: true
                            }
                        },
                        {
                            name: 'user_id',
                            type: 'hidden',
                            value: '{{ auth()->id() }}'
                        }
                    ],
                    tableHeaders: [{
                            field: 'insurance_company_name',
                            name: translations.companyName || 'Company Name',
                            sortable: true
                        },
                        {
                            field: 'email',
                            name: translations.email || 'Email',
                            sortable: true,
                            getter: (entity) => {
                                return entity.email || translations.notAvailable || 'N/A';
                            }
                        },
                        {
                            field: 'phone',
                            name: translations.phone || 'Phone',
                            sortable: false,
                            getter: (entity) => {
                                if (!entity.phone) return translations.notAvailable || 'N/A';

                                // Extraer solo los dígitos
                                const cleaned = entity.phone.replace(/\D/g, '');

                                // Si tiene 11 dígitos y empieza con 1 (formato +1XXXXXXXXXX)
                                if (cleaned.length === 11 && cleaned.startsWith('1')) {
                                    const phoneDigits = cleaned.substring(1); // Remover el 1
                                    return `(${phoneDigits.substring(0, 3)}) ${phoneDigits.substring(3, 6)}-${phoneDigits.substring(6, 10)}`;
                                }
                                // Si tiene 10 dígitos (formato XXXXXXXXXX)
                                else if (cleaned.length === 10) {
                                    return `(${cleaned.substring(0, 3)}) ${cleaned.substring(3, 6)}-${cleaned.substring(6, 10)}`;
                                }

                                // Para otros formatos, devolver tal como está
                                return entity.phone;
                            }
                        },
                        {
                            field: 'website',
                            name: translations.website || 'Website',
                            sortable: false,
                            getter: (entity) => {
                                if (!entity.website) return translations.notAvailable || 'N/A';

                                // Create clickable link
                                const displayUrl = entity.website.replace(/^https?:\/\//, '');
                                return `<a href="${entity.website}" target="_blank" class="text-blue-600 hover:text-blue-800 underline">${displayUrl}</a>`;
                            }
                        },
                        {
                            field: 'created_at',
                            name: translations.created || 'Created',
                            sortable: true,
                            getter: (entity) => {
                                return entity.created_at ? new Date(entity.created_at)
                                    .toLocaleDateString() : translations.noRecordsFound || 'N/A';
                            }
                        },
                        {
                            field: 'actions',
                            name: translations.actions || 'Actions',
                            sortable: false,
                            getter: (entity) => {
                                const isDeleted = entity.deleted_at !== null;
                                let buttons = '';

                                // Edit button (always available)
                                buttons += `<button data-id="${entity.uuid}" class="edit-btn inline-flex items-center justify-center w-9 h-9 text-white rounded-md border border-white/10 bg-blue-500/40 backdrop-blur-md shadow-lg shadow-blue-500/20 hover:bg-blue-600/60 hover:shadow-xl hover:shadow-blue-500/40 transition-all duration-200 mr-2" title="${translations.editInsuranceCompany || 'Edit Insurance Company'}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>`;

                                if (isDeleted) {
                                    // Restore button
                                    buttons += `<button data-id="${entity.uuid}" class="restore-btn inline-flex items-center justify-center w-9 h-9 text-white rounded-md border border-white/10 bg-emerald-500/40 backdrop-blur-md shadow-lg shadow-emerald-500/20 hover:bg-emerald-600/60 hover:shadow-xl hover:shadow-emerald-500/40 transition-all duration-200" title="${translations.restoreInsuranceCompany || 'Restore Insurance Company'}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                    </svg>
                                                </button>`;
                                } else {
                                    // Delete button
                                    buttons += `<button data-id="${entity.uuid}" class="delete-btn inline-flex items-center justify-center w-9 h-9 text-white rounded-md border border-white/10 bg-red-500/40 backdrop-blur-md shadow-lg shadow-red-500/20 hover:bg-red-600/60 hover:shadow-xl hover:shadow-red-500/40 transition-all duration-200" title="${translations.deleteInsuranceCompany || 'Delete Insurance Company'}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>`;
                                }

                                return buttons;
                            }
                        }
                    ],
                    translations: {
                        confirmDelete: "{{ __('confirm_delete_entity') }}",
                        deleteMessage: "{{ __('delete_element_question') }}",
                        confirmRestore: "{{ __('confirm_restore_entity') }}",
                        restoreMessage: "{{ __('restore_element_question') }}",
                        yesDelete: "{{ __('yes_delete') }}",
                        yesRestore: "{{ __('yes_restore') }}",
                        cancel: "{{ __('cancel') }}",
                        deletedSuccessfully: "{{ __('deleted_successfully') }}",
                        restoredSuccessfully: "{{ __('restored_successfully') }}",
                        errorDeleting: "{{ __('errorDeleting') }}",
                        errorRestoring: "{{ __('errorRestoring') }}",
                        createdSuccessfully: "{{ __('insurance_company_messages.created_successfully') }}",
                        updatedSuccessfully: "{{ __('insurance_company_messages.updated_successfully') }}",
                        errorCreatingRecord: "{{ __('insurance_company_messages.creation_failed') }}",
                        errorUpdatingRecord: "{{ __('insurance_company_messages.update_failed') }}",
                        emailAlreadyInUse: "{{ __('email_already_in_use') }}",
                        phoneAlreadyInUse: "{{ __('phone_already_in_use') }}",
                        nameAlreadyInUse: "{{ __('name_already_in_use') }}",
                        emailAvailable: "{{ __('email_available') }}",
                        phoneAvailable: "{{ __('phone_available') }}",
                        nameAvailable: "{{ __('name_available') }}",
                        invalidEmail: "{{ __('invalid_email') }}",
                        minimumCharacters: "{{ __('minimum_characters') }}",
                        mustContainNumbers: "{{ __('must_contain_numbers') }}",
                        usernameAlreadyInUse: "{{ __('username_already_in_use') }}",
                        usernameAvailable: "{{ __('username_available') }}",
                        pleaseCorrectErrors: "{{ __('please_correct_errors') }}",
                        noRecordsFound: "{{ __('no_data') }}",
                        notAvailable: "N/A",
                        // Pagination translations
                        showing: "{{ __('showing') }}",
                        to: "{{ __('to') }}",
                        of: "{{ __('of') }}",
                        results: "{{ __('results') }}",
                        totalRecords: "{{ __('total_records') }}",
                        records: "{{ __('records') }}"
                    }
                });

                // Load initial data
                window.insuranceCompanyManager.loadEntities();
            });
        </script>
    @endpush

</x-crud.index-layout>
