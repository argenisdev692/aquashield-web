<x-crud.index-layout title="{{ __('users_title') }}" subtitle="{{ __('users_subtitle') }}" entity-name="User"
    entity-name-plural="Users" search-placeholder="{{ __('search_users') }}"
    show-deleted-label="{{ __('show_inactive_users') }}" add-new-label="{{ __('add_user') }}" manager-name="userManager"
    table-id="userTable" create-button-id="createUserBtn" search-id="searchInput" show-deleted-id="showDeleted"
    per-page-id="perPage" pagination-id="pagination" alert-id="alertContainer" :table-columns="[
        ['field' => 'name', 'label' => __('name'), 'sortable' => true],
        ['field' => 'email', 'label' => __('email'), 'sortable' => true],
        ['field' => 'username', 'label' => __('username'), 'sortable' => true],
        ['field' => 'phone', 'label' => __('phone'), 'sortable' => false],
        ['field' => 'role', 'label' => __('role'), 'sortable' => true],
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
                window.userManager = new CrudManagerModal({
                    entityName: 'User',
                    entityNamePlural: 'Users',
                    routes: {
                        index: "{{ secure_url(route('users.index', [], false)) }}",
                        store: "{{ secure_url(route('users.store', [], false)) }}",
                        edit: "{{ secure_url(route('users.edit', ':id', false)) }}",
                        update: "{{ secure_url(route('users.update', ':id', false)) }}",
                        destroy: "{{ secure_url(route('users.destroy', ':id', false)) }}",
                        restore: "{{ secure_url(route('users.restore', ':id', false)) }}",
                        checkEmail: "{{ secure_url(route('users.check-email', [], false)) }}",
                        checkPhone: "{{ secure_url(route('users.check-phone', [], false)) }}",
                        checkUsername: "{{ secure_url(route('users.check-username', [], false)) }}"
                    },
                    tableSelector: '#userTable-body',
                    searchSelector: '#searchInput',
                    perPageSelector: '#perPage',
                    showDeletedSelector: '#showDeleted',
                    paginationSelector: '#pagination',
                    alertSelector: '#alertContainer',
                    createButtonSelector: '#createUserBtn',
                    idField: 'uuid',
                    searchFields: ['name', 'last_name', 'email', 'username', 'address'],
                    // Establecer el valor inicial basado en localStorage
                    showDeleted: showDeletedState,
                    formFields: [{
                            name: 'name',
                            type: 'text',
                            label: '{{ __('first_name') }}',
                            placeholder: '{{ __('enter_first_name') }}',
                            required: true,
                            validation: {
                                required: true,
                                maxLength: 255
                            },
                            capitalize: true
                        },
                        {
                            name: 'last_name',
                            type: 'text',
                            label: '{{ __('last_name') }}',
                            placeholder: '{{ __('enter_last_name') }}',
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
                                    url: "{{ route('users.check-email') }}",
                                    message: '{{ __('email_already_taken') }}'
                                }
                            }
                        },
                        {
                            name: 'username',
                            type: 'text',
                            label: '{{ __('username') }}',
                            placeholder: '{{ __('username_will_be_generated') }}',
                            required: false,
                            showInCreate: true,
                            showInEdit: false,
                            readonly: false,
                            help: `{!! __('username_generated_automatically') !!}`,
                            validation: {
                                minLength: 7,
                                unique: {
                                    url: "{{ route('users.check-username') }}",
                                    message: '{{ __('username_already_taken') }}'
                                }
                            }
                        },
                        {
                            name: 'username',
                            type: 'text',
                            label: '{{ __('username') }}',
                            placeholder: '{{ __('username') }}',
                            required: false,
                            showInCreate: false,
                            showInEdit: true,
                            readonly: false,
                            help: '',
                            validation: {
                                minLength: 7,
                                unique: {
                                    url: "{{ route('users.check-username') }}",
                                    message: '{{ __('username_already_taken') }}'
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
                                    url: "{{ route('users.check-phone') }}",
                                    message: '{{ __('phone_already_taken') }}'
                                }
                            }
                        },
                        {
                            name: 'address',
                            type: 'text',
                            label: '{{ __('address') }}',
                            placeholder: '{{ __('enter_address') }}',
                            required: false,
                            validation: {
                                maxLength: 255
                            },
                            capitalize: true
                        },
                        {
                            name: 'city',
                            type: 'text',
                            label: '{{ __('city') }}',
                            placeholder: '{{ __('enter_city') }}',
                            required: false,
                            validation: {
                                maxLength: 100
                            },
                            capitalize: true
                        },
                        {
                            name: 'state',
                            type: 'text',
                            label: '{{ __('state') }}',
                            placeholder: '{{ __('enter_state') }}',
                            required: false,
                            validation: {
                                maxLength: 100
                            },
                            capitalize: true
                        },
                        {
                            name: 'zip_code',
                            type: 'text',
                            label: '{{ __('zip_code') }}',
                            placeholder: '{{ __('enter_zip_code') }}',
                            required: false,
                            validation: {
                                maxLength: 20
                            }
                        },
                        {
                            name: 'country',
                            type: 'text',
                            label: '{{ __('country') }}',
                            placeholder: '{{ __('enter_country') }}',
                            required: false,
                            validation: {
                                maxLength: 100
                            },
                            capitalize: true
                        },
                        {
                            name: 'gender',
                            type: 'select',
                            label: '{{ __('gender') }}',
                            placeholder: '{{ __('select_gender') }}',
                            required: false,
                            options: [{
                                    value: 'male',
                                    text: '{{ __('male') }}'
                                },
                                {
                                    value: 'female',
                                    text: '{{ __('female') }}'
                                },
                                {
                                    value: 'other',
                                    text: '{{ __('other') }}'
                                }
                            ]
                        },
                        {
                            name: 'role',
                            type: 'select',
                            label: '{{ __('role') }}',
                            placeholder: '{{ __('select_role') }}',
                            required: true,
                            options: [
                                @if (isset($roles))
                                    @foreach ($roles as $key => $role)
                                        {
                                            value: '{{ $key }}',
                                            text: '{{ $role }}'
                                        },
                                    @endforeach
                                @endif
                            ]
                        },
                        {
                            name: 'date_of_birth',
                            type: 'date',
                            label: '{{ __('date_of_birth') }}',
                            required: false,
                            showInCreate: false // Solo mostrar en edición
                        },
                        {
                            name: 'send_password_reset',
                            type: 'checkbox',
                            label: '{{ __('send_password_reset') }}',
                            checkboxLabel: '{{ __('send_new_password_email') }}',
                            required: false,
                            showInCreate: false // Solo mostrar en edición
                        }
                    ],
                    tableHeaders: [{
                            field: 'name',
                            name: '{{ __('name') }}',
                            sortable: true,
                            getter: (entity) => {
                                return `${entity.name || ''} ${entity.last_name || ''}`.trim();
                            }
                        },
                        {
                            field: 'email',
                            name: '{{ __('email') }}',
                            sortable: true
                        },
                        {
                            field: 'username',
                            name: '{{ __('username') }}',
                            sortable: true
                        },
                        {
                            field: 'phone',
                            name: '{{ __('phone') }}',
                            sortable: false,
                            getter: (entity) => {
                                if (!entity.phone) return '-';

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
                            field: 'role',
                            name: '{{ __('role') }}',
                            sortable: true,
                            getter: (entity) => {
                                // Mostrar el primer rol del usuario con badge de color o 'Sin Rol' si no tiene
                                if (entity.roles && entity.roles.length > 0) {
                                    const roleName = entity.roles[0].name;
                                    let badgeClass = '';

                                    // Asignar colores específicos para cada rol
                                    switch (roleName) {
                                        case 'Super Admin':
                                            badgeClass =
                                                'bg-gradient-to-r from-purple-500 to-purple-600 text-white border-purple-200';
                                            break;
                                        case 'Admin':
                                            badgeClass =
                                                'bg-gradient-to-r from-blue-500 to-blue-600 text-white border-blue-200';
                                            break;
                                        case 'User':
                                            badgeClass =
                                                'bg-gradient-to-r from-green-500 to-green-600 text-white border-green-200';
                                            break;
                                        default:
                                            badgeClass =
                                                'bg-gradient-to-r from-gray-500 to-gray-600 text-white border-gray-200';
                                    }

                                    return `<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold ${badgeClass} shadow-sm border">
                                        ${roleName}
                                    </span>`;
                                }
                                return `<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                    {{ __('no_role') }}
                                </span>`;
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
                            getter: (user) => {
                                let actionsHtml = `
                                <div class="flex justify-center space-x-2">
                                    <button data-id="${user.uuid}" class="edit-btn inline-flex items-center justify-center w-9 h-9 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105" title="{{ __('edit_user') }}">
                                         <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                             <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                         </svg>
                                    </button>`;

                                if (user.deleted_at) {
                                    actionsHtml += `
                                    <button data-id="${user.uuid}" class="restore-btn inline-flex items-center justify-center w-9 h-9 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-lg hover:from-emerald-600 hover:to-emerald-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105" title="{{ __('restore_user') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                    </button>`;
                                } else {
                                    actionsHtml += `
                                    <button data-id="${user.uuid}" class="delete-btn inline-flex items-center justify-center w-9 h-9 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg hover:from-red-600 hover:to-red-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105" title="{{ __('delete_user') }}">
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
                        create: '{{ __('create_user') }}',
                        edit: '{{ __('edit_user') }}',
                        delete: '{{ __('delete_user') }}',
                        restore: '{{ __('restore_user') }}',
                        confirmDelete: '{{ __('are_you_sure_delete') }}',
                        confirmRestore: '{{ __('are_you_sure_restore') }}',
                        deleteMessage: '{{ __('confirm_delete_user') }}',
                        restoreMessage: '{{ __('confirm_restore_user') }}',
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
                        // Validation messages
                        pleaseCorrectErrors: '{{ __('please_correct_errors') }}',
                        minimumCharacters: '{{ __('minimum_characters') }}',
                        maximumCharacters: '{{ __('maximum_characters') }}',
                        lastNameRequired: '{{ __('last_name_required') }}',
                        validationErrors: '{{ __('validation_errors') }}',
                        invalidFormat: '{{ __('invalid_format') }}',
                        invalidEmail: '{{ __('invalid_email') }}',
                        emailAlreadyInUse: '{{ __('email_already_in_use') }}',
                        phoneAlreadyInUse: '{{ __('phone_already_in_use') }}',
                        nameAlreadyInUse: '{{ __('name_already_in_use') }}',
                        usernameAlreadyInUse: '{{ __('username_already_in_use') }}',
                        mustContainNumbers: '{{ __('must_contain_numbers') }}',
                        phoneAvailable: '{{ __('phone_available') }}',
                        emailAvailable: '{{ __('email_available') }}',
                        usernameAvailable: '{{ __('username_available') }}',
                        correctValidationErrors: '{{ __('correct_validation_errors') }}',
                        isRequired: '{{ __('is_required') }}',
                        nameAvailable: '{{ __('name_available') }}'
                    },
                    entityConfig: {
                        identifierField: 'email',
                        displayName: '{{ __('user') }}',
                        fallbackFields: ['name', 'last_name', 'username'],
                        detailFormat: (entity) => {
                            return `${entity.name || ''} ${entity.last_name || ''} (${entity.email || ''})`
                                .trim();
                        }
                    }
                });

                // Initialize loading of entities
                window.userManager.loadEntities();
            });
        </script>
    @endpush
</x-crud.index-layout>
