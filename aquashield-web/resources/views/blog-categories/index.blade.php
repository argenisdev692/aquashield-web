<x-crud.index-layout :title="__('blog_categories_title')" :subtitle="__('blog_categories_subtitle')" entity-name="{{ __('blog_category_entity_name') }}"
    entity-name-plural="{{ __('blog_category_entity_plural') }}" :search-placeholder="__('search_blog_categories')" :show-deleted-label="__('show_inactive_records')" :add-new-label="__('add_blog_category')"
    manager-name="blogCategoryManager" table-id="blogCategoryTable" create-button-id="createBlogCategoryBtn"
    search-id="searchInput" show-deleted-id="showDeleted" per-page-id="perPage" pagination-id="pagination"
    alert-id="alertContainer" :table-columns="[
        ['field' => 'blog_category_name', 'label' => __('category_name'), 'sortable' => true],
        ['field' => 'blog_category_description', 'label' => __('description'), 'sortable' => true],
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

                // Make the manager globally accessible
                window.blogCategoryManager = new CrudManagerModal({
                    entityName: 'BlogCategory',
                    entityNamePlural: 'BlogCategories',
                    routes: {
                        index: "{{ secure_url(route('blog-categories.index', [], false)) }}",
                        store: "{{ secure_url(route('blog-categories.store', [], false)) }}",
                        edit: "{{ secure_url(route('blog-categories.edit', ':id', false)) }}",
                        update: "{{ secure_url(route('blog-categories.update', ':id', false)) }}",
                        destroy: "{{ secure_url(route('blog-categories.destroy', ':id', false)) }}",
                        restore: "{{ secure_url(route('blog-categories.restore', ':id', false)) }}",
                        checkCategoryName: "{{ secure_url(route('blog-categories.check-category-name', [], false)) }}"
                    },
                    tableSelector: '#blogCategoryTable-body',
                    searchSelector: '#searchInput',
                    perPageSelector: '#perPage',
                    showDeletedSelector: '#showDeleted',
                    paginationSelector: '#pagination',
                    alertSelector: '#alertContainer',
                    createButtonSelector: '#createBlogCategoryBtn',
                    idField: 'uuid',
                    searchFields: ['blog_category_name', 'blog_category_description'],
                    // Establecer el valor inicial basado en localStorage
                    showDeleted: showDeletedState,
                    formFields: [{
                            name: 'blog_category_name',
                            type: 'text',
                            label: '{{ __('category_name') }}',
                            placeholder: '{{ __('enter_category_name') }}',
                            required: true,
                            validation: {
                                required: true,
                                minLength: 3,
                                maxLength: 100,
                                unique: {
                                    url: "{{ route('blog-categories.check-category-name') }}",
                                    message: '{{ __('category_name_already_taken') }}'
                                }
                            },
                            // Habilitar capitalización automática para este campo
                            capitalize: true
                        },
                        {
                            name: 'blog_category_description',
                            type: 'textarea',
                            label: '{{ __('description') }}',
                            placeholder: '{{ __('enter_description_optional') }}',
                            required: false,
                            rows: 3,
                            validation: {
                                maxLength: 500
                            },
                            capitalize: true
                        }
                    ],
                    tableHeaders: [{
                            field: 'blog_category_name',
                            name: '{{ __('category_name') }}',
                            sortable: true
                        },
                        {
                            field: 'blog_category_description',
                            name: '{{ __('description') }}',
                            sortable: true,
                            getter: (entity) => {
                                if (!entity.blog_category_description)
                                    return '<span class="text-gray-400 italic">{{ __('no_description') }}</span>';
                                // Truncar descripción si es muy larga
                                const maxLength = 50;
                                if (entity.blog_category_description.length > maxLength) {
                                    return entity.blog_category_description.substring(0, maxLength) +
                                        '...';
                                }
                                return entity.blog_category_description;
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
                            getter: (blogCategory) => {
                                let actionsHtml = `
                                <div class="flex justify-center space-x-2">
                                    <button data-id="${blogCategory.uuid}" class="edit-btn inline-flex items-center justify-center w-9 h-9 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105" title="{{ __('edit_blog_category') }}">
                                         <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                             <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                         </svg>
                                    </button>`;

                                if (blogCategory.deleted_at) {
                                    actionsHtml += `
                                    <button data-id="${blogCategory.uuid}" class="restore-btn inline-flex items-center justify-center w-9 h-9 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-lg hover:from-emerald-600 hover:to-emerald-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105" title="{{ __('restore_blog_category') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                    </button>`;
                                } else {
                                    actionsHtml += `
                                    <button data-id="${blogCategory.uuid}" class="delete-btn inline-flex items-center justify-center w-9 h-9 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg hover:from-red-600 hover:to-red-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105" title="{{ __('delete_blog_category') }}">
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
                        create: '{{ __('create_blog_category') }}',
                        edit: '{{ __('edit_blog_category') }}',
                        delete: '{{ __('delete_blog_category') }}',
                        restore: '{{ __('restore_blog_category') }}',
                        confirmDelete: '{{ __('are_you_sure_delete') }}',
                        confirmRestore: '{{ __('are_you_sure_restore') }}',
                        deleteMessage: '{{ __('confirm_delete_blog_category') }}',
                        restoreMessage: '{{ __('confirm_restore_blog_category') }}',
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
                        blogCategoryNameRequired: '{{ __('blog_category_name_required') }}',
                        descriptionRequired: '{{ __('description_required') }}',
                        pleaseCorrectErrors: '{{ __('please_correct_errors') }}',
                        invalidFormat: '{{ __('invalid_format') }}',
                        minimumCharacters: '{{ __('minimum_characters') }}',
                        maximumCharacters: '{{ __('maximum_characters') }}'
                    },
                    entityConfig: {
                        identifierField: 'blog_category_name',
                        displayName: 'categoría de blog',
                        fallbackFields: ['blog_category_description'],
                        // Configuración adicional para mostrar información más detallada
                        detailFormat: (entity) => {
                            // Mostrar categoría con información adicional si está disponible
                            return entity.blog_category_name || 'categoría de blog';
                        }
                    }
                });

                // Initialize loading of entities
                window.blogCategoryManager.loadEntities();
            });
        </script>
    @endpush
</x-crud.index-layout>
