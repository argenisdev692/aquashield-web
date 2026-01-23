<x-crud.index-layout :title="__('model_ai_title')" :subtitle="__('model_ai_subtitle')" entity-name="{{ __('model_ai_entity_name') }}"
    entity-name-plural="{{ __('model_ai_entity_plural') }}" :search-placeholder="__('model_ai_search_placeholder')" :show-deleted-label="__('model_ai_show_inactive')" :add-new-label="__('model_ai_add_new')"
    manager-name="modelAIManager" table-id="modelAITable" create-button-id="createModelAIBtn" search-id="searchInput"
    show-deleted-id="showDeleted" per-page-id="perPage" pagination-id="pagination" alert-id="alertContainer"
    :table-columns="[
        ['field' => 'name', 'label' => __('model_ai_name'), 'sortable' => true],
        ['field' => 'email', 'label' => __('model_ai_email'), 'sortable' => true],
        ['field' => 'type', 'label' => __('model_ai_type'), 'sortable' => true],
        ['field' => 'description', 'label' => __('model_ai_description'), 'sortable' => true],
        ['field' => 'user_name', 'label' => __('model_ai_user'), 'sortable' => true],
        ['field' => 'api_key', 'label' => __('model_ai_api_key'), 'sortable' => false],
        ['field' => 'created_at', 'label' => __('model_ai_created'), 'sortable' => true],
        ['field' => 'actions', 'label' => __('model_ai_actions'), 'sortable' => false],
    ]">
    @push('scripts')
        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <!-- CrudManagerModal ya está disponible vía el sistema modular -->

        <!-- Estilos personalizados para SweetAlert2 -->
        <style>
            /* Estilos para modal de creación (verde) */
            .swal2-popup.swal-create .swal2-header,
            .swal2-popup.swal-create .swal2-title {
                background: linear-gradient(135deg, #10B981, #059669) !important;
                color: white !important;
            }

            /* Estilos para modal de edición (azul) */
            .swal2-popup.swal-edit .swal2-header,
            .swal2-popup.swal-edit .swal2-title {
                background: linear-gradient(135deg, #3B82F6, #2563EB) !important;
                color: white !important;
            }

            /* Forzar estilos del header */
            .swal2-header {
                padding: 0 !important;
                border-radius: 12px 12px 0 0 !important;
            }

            .swal2-popup.swal-create .swal2-header,
            .swal2-popup.swal-edit .swal2-header {
                border-radius: 12px 12px 0 0 !important;
            }

            .swal2-title {
                padding: 1.5rem !important;
                margin: 0 !important;
                width: 100% !important;
                text-align: center !important;
            }

            /* Estilos generales para el modal */
            .swal2-popup {
                border-radius: 12px !important;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
                overflow: hidden !important;
            }

            /* Asegurar que el header también tenga border radius */
            .swal2-header {
                border-top-left-radius: 12px !important;
                border-top-right-radius: 12px !important;
                border-bottom-left-radius: 0 !important;
                border-bottom-right-radius: 0 !important;
            }

            /* Asegurar que el contenido no sobresalga */
            .swal2-content {
                border-radius: 0 !important;
            }

            /* Asegurar que los botones tengan border radius inferior */
            .swal2-actions {
                border-bottom-left-radius: 12px !important;
                border-bottom-right-radius: 12px !important;
                border-top-left-radius: 0 !important;
                border-top-right-radius: 0 !important;
            }

            .swal2-title {
                font-size: 1.5rem !important;
                font-weight: 600 !important;
                margin: 0 !important;
                padding: 1rem !important;
            }

            .swal2-close {
                font-size: 1.5rem !important;
                font-weight: 300 !important;
                right: 1rem !important;
                top: 1rem !important;
            }

            .swal2-close:hover {
                background: rgba(255, 255, 255, 0.1) !important;
                border-radius: 50% !important;
            }

            /* Estilos para el formulario */
            .crud-modal-form {
                padding: 1rem;
            }

            .form-group label {
                font-weight: 500 !important;
                color: #374151 !important;
            }

            .form-group input,
            .form-group select,
            .form-group textarea {
                transition: all 0.2s ease !important;
            }

            .form-group input:focus,
            .form-group select:focus,
            .form-group textarea:focus {
                ring: 2px !important;
                ring-color: #3B82F6 !important;
                border-color: #3B82F6 !important;
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
            }

            /* Estilos para mensajes de validación */
            .error-message {
                font-size: 0.875rem !important;
                margin-top: 0.25rem !important;
                transition: all 0.2s ease !important;
            }

            .error-message.text-red-500 {
                color: #EF4444 !important;
            }

            .error-message.text-green-500 {
                color: #10B981 !important;
            }

            /* Estilos para campos con error */
            .form-group input.error,
            .form-group select.error,
            .form-group textarea.error {
                border-color: #EF4444 !important;
                box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important;
            }

            /* Estilos para campos válidos */
            .form-group input.valid,
            .form-group select.valid,
            .form-group textarea.valid {
                border-color: #10B981 !important;
                box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1) !important;
            }
        </style>
        <script>
            $(document).ready(function() {
                // Recuperar estado del toggle de localStorage antes de inicializar el manager
                const showDeletedState = localStorage.getItem('showDeleted') === 'true';
                console.log('Estado inicial de showDeleted:', showDeletedState);

                // Make the manager globally accessible
                window.modelAIManager = new CrudManagerModal({
                    entityName: "{{ __('model_ai_entity_name') }}",
                    entityNamePlural: "{{ __('model_ai_entity_plural') }}",
                    routes: {
                        index: "{{ secure_url(route('model-ais.index', [], false)) }}",
                        store: "{{ secure_url(route('model-ais.store', [], false)) }}",
                        edit: "{{ secure_url(route('model-ais.edit', ':id', false)) }}",
                        update: "{{ secure_url(route('model-ais.update', ':id', false)) }}",
                        destroy: "{{ secure_url(route('model-ais.destroy', ':id', false)) }}",
                        restore: "{{ secure_url(route('model-ais.restore', ':id', false)) }}",
                        checkName: "{{ secure_url(route('model-ais.check-name', [], false)) }}"
                    },
                    tableSelector: '#modelAITable-body',
                    searchSelector: '#searchInput',
                    perPageSelector: '#perPage',
                    showDeletedSelector: '#showDeleted',
                    paginationSelector: '#pagination',
                    alertSelector: '#alertContainer',
                    createButtonSelector: '#createModelAIBtn',
                    idField: 'uuid',
                    searchFields: ['name', 'email', 'type', 'description'],
                    // Establecer el valor inicial basado en localStorage
                    showDeleted: showDeletedState,
                    formFields: [{
                            name: 'name',
                            type: 'text',
                            label: "{{ __('model_ai_name') }}",
                            placeholder: "{{ __('model_ai_enter_name') }}",
                            required: true,
                            validation: {
                                required: true,
                                maxLength: 255,
                                unique: {
                                    url: "{{ route('model-ais.check-name') }}",
                                    message: "{{ __('model_ai_name_already_taken') }}"
                                }
                            },
                            capitalize: true
                        },
                        {
                            name: 'email',
                            type: 'email',
                            label: "{{ __('model_ai_email') }}",
                            placeholder: "{{ __('model_ai_enter_email') }}",
                            required: true,
                            validation: {
                                required: true,
                                maxLength: 255
                            }
                        },
                        {
                            name: 'type',
                            type: 'select',
                            label: "{{ __('model_ai_type') }}",
                            placeholder: "{{ __('model_ai_select_type') }}",
                            required: true,
                            options: [{
                                    value: 'Content',
                                    text: "{{ __('model_ai_type_content') }}"
                                },
                                {
                                    value: 'Image',
                                    text: "{{ __('model_ai_type_image') }}"
                                },
                                {
                                    value: 'Mixed',
                                    text: "{{ __('model_ai_type_mixed') }}"
                                }
                            ],
                            validation: {
                                required: true
                            }
                        },
                        {
                            name: 'description',
                            type: 'textarea',
                            label: "{{ __('model_ai_description') }}",
                            placeholder: "{{ __('model_ai_enter_description') }}",
                            required: false,
                            rows: 3,
                            validation: {
                                maxLength: 1000
                            },
                            capitalize: true
                        },
                        {
                            name: 'api_key',
                            type: 'textarea',
                            label: "{{ __('model_ai_api_key') }}",
                            placeholder: "{{ __('model_ai_enter_api_key') }}",
                            required: true,
                            rows: 3,
                            validation: {
                                required: true,
                                maxLength: 1000
                            }
                        }
                    ],
                    tableHeaders: [{
                            field: 'name',
                            name: "{{ __('model_ai_name') }}",
                            sortable: true
                        },
                        {
                            field: 'email',
                            name: "{{ __('model_ai_email') }}",
                            sortable: true
                        },
                        {
                            field: 'type',
                            name: "{{ __('model_ai_type') }}",
                            sortable: true,
                            getter: (entity) => {
                                const typeTranslations = {
                                    'Content': "{{ __('model_ai_type_content') }}",
                                    'Image': "{{ __('model_ai_type_image') }}",
                                    'Mixed': "{{ __('model_ai_type_mixed') }}"
                                };
                                return typeTranslations[entity.type] || entity.type;
                            }
                        },
                        {
                            field: 'description',
                            name: "{{ __('model_ai_description') }}",
                            sortable: true,
                            getter: (entity) => {
                                if (!entity.description) return 'N/A';
                                // Truncate long descriptions
                                return entity.description.length > 50 ?
                                    entity.description.substring(0, 50) + '...' :
                                    entity.description;
                            }
                        },
                        {
                            field: 'user_name',
                            name: "{{ __('model_ai_user') }}",
                            sortable: true,
                            getter: (entity) => {
                                if (!entity.user_name) return 'N/A';
                                return entity.user_name;
                            }
                        },
                        {
                            field: 'api_key',
                            name: "{{ __('model_ai_api_key') }}",
                            sortable: false,
                            getter: (entity) => {
                                if (!entity.api_key) return 'N/A';
                                // Show only first and last 4 characters for security
                                const key = entity.api_key;
                                if (key.length <= 8) {
                                    return '****';
                                }
                                return key.substring(0, 4) + '...' + key.substring(key.length - 4);
                            }
                        },
                        {
                            field: 'created_at',
                            name: "{{ __('model_ai_created') }}",
                            sortable: true,
                            getter: (entity) => entity.created_at ? new Date(entity.created_at)
                                .toLocaleDateString() : 'N/A'
                        },
                        {
                            field: 'actions',
                            name: "{{ __('model_ai_actions') }}",
                            sortable: false,
                            getter: (modelAI) => {
                                let actionsHtml = `
                            <div class="flex justify-center space-x-2">
                                <button data-id="${modelAI.uuid}" class="edit-btn inline-flex items-center justify-center w-9 h-9 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105" title="Edit AI Model">
                                     <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                         <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                     </svg>
                                </button>`;

                                if (modelAI.deleted_at) {
                                    actionsHtml += `
                                <button data-id="${modelAI.uuid}" class="restore-btn inline-flex items-center justify-center w-9 h-9 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-lg hover:from-emerald-600 hover:to-emerald-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105" title="Restore AI Model">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                </button>`;
                                } else {
                                    actionsHtml += `
                                <button data-id="${modelAI.uuid}" class="delete-btn inline-flex items-center justify-center w-9 h-9 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg hover:from-red-600 hover:to-red-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105" title="Delete AI Model">
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
                        create: "{{ __('model_ai_create') }}",
                        edit: "{{ __('model_ai_edit') }}",
                        delete: "{{ __('model_ai_delete') }}",
                        restore: "{{ __('model_ai_restore') }}",
                        confirmDelete: "{{ __('model_ai_confirm_delete') }}",
                        confirmRestore: "{{ __('model_ai_confirm_restore') }}",
                        deleteMessage: "{{ __('model_ai_delete_message') }}",
                        restoreMessage: "{{ __('model_ai_restore_message') }}",
                        yesDelete: "{{ __('model_ai_yes_delete') }}",
                        yesRestore: "{{ __('model_ai_yes_restore') }}",
                        cancel: "{{ __('model_ai_cancel') }}",
                        deletedSuccessfully: "{{ __('model_ai_deleted_successfully') }}",
                        restoredSuccessfully: "{{ __('model_ai_restored_successfully') }}",
                        errorDeleting: "{{ __('model_ai_error_deleting') }}",
                        errorRestoring: "{{ __('model_ai_error_restoring') }}",
                        success: "{{ __('model_ai_success') }}",
                        error: "{{ __('model_ai_error') }}",
                        saving: "{{ __('model_ai_saving') }}",
                        loading: "{{ __('model_ai_loading') }}",
                        save: "{{ __('model_ai_save') }}",
                        update: "{{ __('model_ai_update') }}",
                        yes: "{{ __('model_ai_yes') }}",
                        no: "{{ __('model_ai_no') }}",
                        noRecordsFound: "{{ __('model_ai_no_records_found') }}"
                    },
                    entityConfig: {
                        identifierField: 'name',
                        displayName: "{{ __('model_ai_display_name') }}",
                        fallbackFields: ['description']
                    }
                });

                // Initialize loading of entities
                window.modelAIManager.loadEntities();
            });
        </script>
    @endpush
</x-crud.index-layout>
