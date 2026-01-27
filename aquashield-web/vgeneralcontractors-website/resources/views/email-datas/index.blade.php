<x-crud.index-layout :title="__('email_datas_title')" :subtitle="__('email_datas_subtitle')" entity-name="{{ __('email_data_entity_name') }}"
    entity-name-plural="{{ __('email_data_entity_plural') }}" :search-placeholder="__('search_email_data')" :show-deleted-label="__('show_inactive_records')" :add-new-label="__('add_email_data')"
    manager-name="emailDataManager" table-id="emailDataTable" create-button-id="createEmailDataBtn" search-id="searchInput"
    show-deleted-id="showDeleted" per-page-id="perPage" pagination-id="pagination" alert-id="alertContainer"
    :table-columns="[
        ['field' => 'description', 'label' => __('description'), 'sortable' => true],
        ['field' => 'email', 'label' => __('email'), 'sortable' => true],
        ['field' => 'phone', 'label' => __('phone'), 'sortable' => false],
        ['field' => 'type', 'label' => __('type'), 'sortable' => true],
        ['field' => 'created_at', 'label' => __('created'), 'sortable' => true],
        ['field' => 'actions', 'label' => __('actions'), 'sortable' => false],
    ]">

    @push('scripts')
        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <!-- CrudManagerModal ya está disponible vía el sistema modular -->

        <script>
            $(document).ready(function() {
                // Recuperar estado del toggle de localStorage antes de inicializar el manager
                const showDeletedState = localStorage.getItem('showDeleted') === 'true';
                console.log('Estado inicial de showDeleted:', showDeletedState);

                // Make the manager globally accessible
                window.emailDataManager = new CrudManagerModal({
                    entityName: 'EmailData',
                    entityNamePlural: 'EmailData',
                    routes: {
                        index: "{{ secure_url(route('email-datas.index', [], false)) }}",
                        store: "{{ secure_url(route('email-datas.store', [], false)) }}",
                        edit: "{{ secure_url(route('email-datas.edit', ':id', false)) }}",
                        update: "{{ secure_url(route('email-datas.update', ':id', false)) }}",
                        destroy: "{{ secure_url(route('email-datas.destroy', ':id', false)) }}",
                        restore: "{{ secure_url(route('email-datas.restore', ':id', false)) }}",
                        checkEmail: "{{ secure_url(route('email-datas.check-email', [], false)) }}",
                        checkPhone: "{{ secure_url(route('email-datas.check-phone', [], false)) }}"
                    },
                    tableSelector: '#emailDataTable-body',
                    searchSelector: '#searchInput',
                    perPageSelector: '#perPage',
                    showDeletedSelector: '#showDeleted',
                    paginationSelector: '#pagination',
                    alertSelector: '#alertContainer',
                    createButtonSelector: '#createEmailDataBtn',
                    idField: 'uuid',
                    searchFields: ['description', 'email', 'phone', 'type'],
                    // Establecer el valor inicial basado en localStorage
                    showDeleted: showDeletedState,
                    formFields: [{
                            name: 'description',
                            type: 'text',
                            label: '{{ __('description') }}',
                            placeholder: '{{ __('enter_description') }}',
                            required: true,
                            validation: {
                                required: true,
                                maxLength: 255
                            },
                            capitalize: true
                        },
                        {
                            name: 'email',
                            type: 'email',
                            label: '{{ __('email') }}',
                            placeholder: '{{ __('enter_email_address') }}',
                            required: true,
                            validation: {
                                required: true,
                                email: true,
                                unique: {
                                    url: "{{ route('email-datas.check-email') }}",
                                    message: '{{ __('email_already_taken') }}'
                                }
                            }
                        },
                        {
                            name: 'phone',
                            type: 'tel',
                            label: '{{ __('phone') }}',
                            placeholder: '{{ __('enter_phone_number') }}',
                            required: true,
                            validation: {
                                required: true,
                                unique: {
                                    url: "{{ route('email-datas.check-phone') }}",
                                    message: '{{ __('phone_already_taken') }}'
                                }
                            }
                        },
                        {
                            name: 'type',
                            type: 'select',
                            label: '{{ __('type') }}',
                            placeholder: '{{ __('select_type') }}',
                            required: true,
                            options: [{
                                    value: 'Support',
                                    text: '{{ __('support') }}'
                                },
                                {
                                    value: 'Sales',
                                    text: '{{ __('sales') }}'
                                },
                                {
                                    value: 'General',
                                    text: '{{ __('general') }}'
                                },
                                {
                                    value: 'Technical',
                                    text: '{{ __('technical') }}'
                                },
                                {
                                    value: 'Billing',
                                    text: '{{ __('billing') }}'
                                },
                                {
                                    value: 'Collections',
                                    text: '{{ __('collections') }}'
                                },
                                {
                                    value: 'Admin',
                                    text: '{{ __('admin') }}'
                                },
                                {
                                    value: 'Info',
                                    text: '{{ __('info') }}'
                                }
                            ]
                        },
                        {
                            name: 'user_id',
                            type: 'select',
                            label: '{{ __('assigned_user') }}',
                            placeholder: '{{ __('no_user_assigned') }}',
                            required: false,
                            options: [
                                @if (isset($users))
                                    @foreach ($users as $user)
                                        {
                                            value: '{{ $user->id }}',
                                            text: '{{ $user->name }} ({{ $user->email }})'
                                        },
                                    @endforeach
                                @endif
                            ]
                        }
                    ],
                    tableHeaders: [{
                            field: 'description',
                            name: '{{ __('description') }}',
                            sortable: true
                        },
                        {
                            field: 'email',
                            name: '{{ __('email') }}',
                            sortable: true
                        },
                        {
                            field: 'phone',
                            name: '{{ __('phone') }}',
                            sortable: false,
                            getter: (entity) => {
                                if (!entity.phone) return 'N/A';

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
                            field: 'type',
                            name: '{{ __('type') }}',
                            sortable: true,
                            getter: (entity) => {
                                const typeMap = {
                                    'Support': '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">{{ __('support') }}</span>',
                                    'Sales': '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">{{ __('sales') }}</span>',
                                    'General': '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">{{ __('general') }}</span>',
                                    'Technical': '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">{{ __('technical') }}</span>',
                                    'Billing': '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">{{ __('billing') }}</span>',
                                    'Collections': '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">{{ __('collections') }}</span>',
                                    'Admin': '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">{{ __('admin') }}</span>',
                                    'Info': '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-cyan-100 text-cyan-800 dark:bg-cyan-900 dark:text-cyan-200">{{ __('info') }}</span>'
                                };
                                return entity.type ? typeMap[entity.type] || entity.type : 'N/A';
                            }
                        },
                        {
                            field: 'created_at',
                            name: '{{ __('created') }}',
                            sortable: true,
                            getter: (entity) => entity.created_at ? new Date(entity.created_at)
                                .toLocaleDateString() : 'N/A'
                        },
                        {
                            field: 'actions',
                            name: '{{ __('actions') }}',
                            sortable: false,
                            getter: (emailData) => {
                                let actionsHtml = `
                            <div class="flex justify-center space-x-2">
                                <button data-id="${emailData.uuid}" class="edit-btn inline-flex items-center justify-center w-9 h-9 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105" title="{{ __('edit_email_data') }}">
                                     <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                         <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                     </svg>
                                </button>`;

                                if (emailData.deleted_at) {
                                    actionsHtml += `
                                <button data-id="${emailData.uuid}" class="restore-btn inline-flex items-center justify-center w-9 h-9 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-lg hover:from-emerald-600 hover:to-emerald-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105" title="{{ __('restore_email_data') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                </button>`;
                                } else {
                                    actionsHtml += `
                                <button data-id="${emailData.uuid}" class="delete-btn inline-flex items-center justify-center w-9 h-9 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg hover:from-red-600 hover:to-red-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105" title="{{ __('delete_email_data') }}">
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
                    defaultSortField: 'created_at',
                    defaultSortDirection: 'desc',
                    translations: {
                        create: '{{ __('create_email_data') }}',
                        edit: '{{ __('edit_email_data') }}',
                        delete: '{{ __('delete_email_data') }}',
                        restore: '{{ __('restore_email_data') }}',
                        confirmDelete: '{{ __('are_you_sure_delete') }}',
                        confirmRestore: '{{ __('are_you_sure_restore') }}',
                        deleteMessage: '{{ __('confirm_delete_email_data') }}',
                        restoreMessage: '{{ __('confirm_restore_email_data') }}',
                        yesDelete: '{{ __('yes') }}, {{ __('delete') }}',
                        yesRestore: '{{ __('yes') }}, {{ __('restore') }}',
                        cancel: '{{ __('cancel') }}',
                        deletedSuccessfully: '{{ __('delete') }} {{ __('success') }}',
                        restoredSuccessfully: '{{ __('restore') }} {{ __('success') }}',
                        errorDeleting: '{{ __('error') }} {{ __('delete') }}',
                        errorRestoring: '{{ __('error') }} {{ __('restore') }}',
                        success: '{{ __('success') }}',
                        error: '{{ __('error') }}',
                        saving: '{{ __('saving') }}',
                        loading: '{{ __('loading') }}',
                        save: '{{ __('save') }}',
                        update: '{{ __('update') }}',
                        yes: '{{ __('yes') }}',
                        no: '{{ __('no') }}',
                        // Traducciones de validación faltantes
                        isRequired: '{{ __('is_required') }}',
                        descriptionRequired: '{{ __('description_required') }}',
                        emailRequired: '{{ __('email_required') }}',
                        phoneRequired: '{{ __('phone_required') }}',
                        pleaseCorrectErrors: '{{ __('please_correct_errors') }}',
                        invalidFormat: '{{ __('invalid_format') }}',
                        invalidEmail: '{{ __('invalid_email') }}',
                        minimumCharacters: '{{ __('minimum_characters') }}',
                        maximumCharacters: '{{ __('maximum_characters') }}'
                    },
                    entityConfig: {
                        identifierField: 'email',
                        displayName: 'correo electrónico',
                        fallbackFields: ['description', 'phone', 'type']
                    }
                });

                // Initialize loading of entities
                window.emailDataManager.loadEntities();
            });
        </script>
    @endpush
</x-crud.index-layout>
