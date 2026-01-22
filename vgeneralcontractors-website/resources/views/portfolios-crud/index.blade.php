<x-app-layout>
    {{-- Dark background container with consistent styling --}}
    <div class="min-h-screen bg-gray-900" style="background-color: #141414;">
        {{-- Header section with title and subtitle --}}
        <div class="p-4 sm:p-6">
            <div class="mb-4 sm:mb-8 text-center sm:text-center md:text-left lg:text-left">
                <h2 class="text-base sm:text-base md:text-2xl lg:text-2xl font-bold text-white mb-2">
                    {{ __('portfolio_management_title') }}</h2>
                <p class="text-base sm:text-base md:text-base lg:text-base text-gray-400">
                    {{ __('portfolio_management_subtitle') }}
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
                    <x-crud.filter-bar entity-name="{{ __('portfolio_entity_name') }}" :show-search-bar="true"
                        :show-inactive-toggle="true" :show-per-page="true" :per-page-options="[5, 10, 15, 25, 50]" :default-per-page="10"
                        add-button-id="createPortfolioBtn" search-id="searchInput"
                        search-placeholder="{{ __('portfolio_search_placeholder') }}" show-deleted-id="showDeleted"
                        show-deleted-label="{{ __('show_inactive_items') }}" per-page-id="perPage"
                        create-button-id="createPortfolioBtn" add-new-label="{{ __('portfolio_add_new') }}"
                        manager-name="portfolioManager" />

                    <!-- Portfolios table -->
                    <div
                        class="overflow-x-auto bg-gray-50 dark:bg-gray-700 rounded-lg shadow-inner border border-gray-200 dark:border-gray-600">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer sort-header"
                                        data-field="title">
                                        {{ __('portfolio_title') }}
                                        <span class="sort-icon"></span>
                                    </th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('portfolio_description') }}
                                    </th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('portfolio_service_category') }}
                                    </th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('portfolio_images') }}
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer sort-header"
                                        data-field="created_at">
                                        {{ __('created') }}
                                        <span class="sort-icon"></span>
                                    </th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="portfolioTable"
                                class=" dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr id="loadingRow">
                                    <td colspan="6" class="px-6 py-4 text-center">
                                        <svg class="animate-spin h-8 w-8 mr-3 text-yellow-500 inline-block"
                                            viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        <span class="text-gray-400">{{ __('loading') }}
                                            {{ __('portfolios') }}...</span>
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

        @push('scripts')
            <!-- SweetAlert2 -->
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <!-- Sortable.js para drag & drop -->
            <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
            <!-- CrudManagerModal ya está disponible vía el sistema modular -->
            <!-- Portfolio CRUD específico -->
            <script src="{{ asset('js/portfolioCrud.js') }}"></script>

            <!-- Estilos específicos para portfolios -->
            <style>
                /* Estilos específicos para portfolios */
                .portfolio-crud-form {
                    max-height: 70vh;
                    overflow-y: auto;
                }

                .image-section {
                    border-top: 1px solid #e5e7eb;
                    padding-top: 1.5rem;
                    margin-top: 1.5rem;
                }

                .existing-image-item,
                .pending-image-item {
                    position: relative;
                    transition: all 0.2s ease;
                    cursor: move;
                }

                .existing-image-item:hover,
                .pending-image-item:hover {
                    transform: scale(1.02);
                    z-index: 10;
                }

                .existing-image-item img,
                .pending-image-item img {
                    aspect-ratio: 1;
                    object-fit: cover;
                    border-radius: 0.5rem;
                }

                /* Estilos para sortable */
                .sortable-ghost {
                    opacity: 0.4;
                }

                .sortable-chosen {
                    transform: scale(1.05);
                    z-index: 999;
                }

                .sortable-drag {
                    transform: rotate(5deg);
                }

                /* Estilos para límites de imágenes */
                .image-limits-info {
                    background: linear-gradient(135deg, #dbeafe, #bfdbfe);
                    border: 1px solid #93c5fd;
                }

                /* Animaciones para botones de imagen */
                .mark-for-deletion,
                .unmark-for-deletion,
                .remove-pending-image {
                    transition: all 0.2s ease;
                }

                .mark-for-deletion:hover,
                .unmark-for-deletion:hover,
                .remove-pending-image:hover {
                    transform: scale(1.1);
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
                }

                /* Responsive para grid de imágenes */
                @media (max-width: 640px) {

                    #existing-images-container,
                    #pending-images-container {
                        grid-template-columns: repeat(2, 1fr);
                    }
                }

                @media (min-width: 768px) {

                    #existing-images-container,
                    #pending-images-container {
                        grid-template-columns: repeat(3, 1fr);
                    }
                }

                @media (min-width: 1024px) {

                    #existing-images-container,
                    #pending-images-container {
                        grid-template-columns: repeat(4, 1fr);
                    }
                }

                /* Aspecto ratio para imágenes */
                .aspect-w-1 {
                    position: relative;
                    width: 100%;
                }

                .aspect-w-1::before {
                    content: '';
                    display: block;
                    padding-bottom: 100%;
                }

                .aspect-h-1>* {
                    position: absolute;
                    height: 100%;
                    width: 100%;
                    top: 0;
                    right: 0;
                    bottom: 0;
                    left: 0;
                }
            </style>

            <script>
                // Hacer las traducciones disponibles para JavaScript
                window.translations = {
                    // Títulos y acciones principales
                    'create_portfolio': @json(__('portfolio_create')),
                    'edit_portfolio': @json(__('portfolio_edit')),
                    'delete_portfolio': @json(__('portfolio_delete')),
                    'restore_portfolio': @json(__('portfolio_restore')),

                    // Mensajes de confirmación
                    'confirm_delete': @json(__('portfolio_confirm_delete')),
                    'confirm_restore': @json(__('portfolio_confirm_restore')),
                    'delete_message': @json(__('portfolio_delete_message')),
                    'restore_message': @json(__('portfolio_restore_message')),

                    // Botones
                    'yes_delete': @json(__('yes_delete')),
                    'yes_restore': @json(__('yes_restore')),
                    'cancel': @json(__('cancel')),
                    'save': @json(__('save')),
                    'update': @json(__('update')),
                    'create': @json(__('create')),

                    // Estados y mensajes
                    'success': @json(__('success')),
                    'error': @json(__('error')),
                    'saving': @json(__('saving')),
                    'loading': @json(__('loading')),

                    // Campos del formulario
                    'project_title': @json(__('portfolio_title')),
                    'project_description': @json(__('portfolio_description')),
                    'service_category': @json(__('portfolio_service_category')),
                    'select_category': @json(__('select_service_category')),

                    // Gestión de imágenes
                    'image_management': @json(__('images_management')),
                    'add_new_images': @json(__('add_new_images')),
                    'portfolio_images': @json(__('portfolio_images')),
                    'portfolio_current_images': @json(__('portfolio_current_images')),
                    'portfolio_new_images_pending': @json(__('portfolio_new_images_pending')),
                    'new_images_to_upload': @json(__('new_images_pending_upload')),
                    'max_images_info': @json(__('max_images_info')),
                    'portfolio_no_records_found': @json(__('portfolio_no_records_found')),
                    'portfolio_image_limits': @json(__('portfolio_image_limits')),
                    'portfolio_total_size': @json(__('portfolio_total_size')),
                    'portfolio_maximum': @json(__('portfolio_maximum')),
                    'portfolio_images_text': @json(__('portfolio_images_text')),
                    'portfolio_max_size_per_image': @json(__('portfolio_max_size_per_image')),
                    'portfolio_formats': @json(__('portfolio_formats')),
                    'portfolio_supported_formats': @json(__('portfolio_supported_formats')),

                    // Validaciones
                    'title_required': @json(__('portfolio_title_required')),
                    'title_min_length': @json(__('title_min_length')),
                    'title_already_exists': @json(__('portfolio_title_already_exists')),
                    'title_available': @json(__('title_available')),
                    'description_required': @json(__('description_required')),
                    'category_required': @json(__('category_required')),
                    'images_required': @json(__('portfolio_images_required')),
                    'max_images_exceeded': @json(__('max_images_exceeded')),
                    'max_size_exceeded': @json(__('max_size_exceeded')),
                    'invalid_file_type': @json(__('invalid_file_type')),
                    'file_too_large': @json(__('file_too_large')),

                    // Mensajes de éxito/error
                    'created_successfully': @json(__('portfolio_created_successfully')),
                    'updated_successfully': @json(__('portfolio_updated_successfully')),
                    'deleted_successfully': @json(__('portfolio_deleted_successfully')),
                    'restored_successfully': @json(__('portfolio_restored_successfully')),
                    'error_creating': @json(__('portfolio_error_creating')),
                    'error_updating': @json(__('portfolio_error_updating')),
                    'error_deleting': @json(__('portfolio_error_deleting')),
                    'error_restoring': @json(__('portfolio_error_restoring')),
                    'error_loading_service_categories': @json(__('error_loading_service_categories')),
                    'error_loading_portfolio': @json(__('portfolio_error_loading'))
                };

                $(document).ready(function() {
                    console.log('DOM ready, starting portfolio manager initialization...');

                    // Recuperar estado del toggle de localStorage
                    const showDeletedState = localStorage.getItem('showDeleted') === 'true';

                    console.log('About to create PortfolioCrudManager...');

                    // Inicializar Portfolio CRUD Manager
                    window.portfolioManager = new PortfolioCrudManager({
                        entityName: 'Portfolio',
                        entityNamePlural: 'Portfolios',
                        routes: {
                            index: "{{ secure_url(route('portfolios-crud.index', [], false)) }}",
                            store: "{{ secure_url(route('portfolios-crud.store', [], false)) }}",
                            edit: "{{ secure_url(route('portfolios-crud.edit', ':id', false)) }}",
                            update: "{{ secure_url(route('portfolios-crud.update', ':id', false)) }}",
                            destroy: "{{ secure_url(route('portfolios-crud.destroy', ':id', false)) }}",
                            restore: "{{ secure_url(route('portfolios-crud.restore', ':id', false)) }}",
                            checkTitle: "{{ secure_url(route('portfolios-crud.check-title', [], false)) }}"
                        },
                        tableSelector: '#portfolioTable',
                        searchSelector: '#searchInput',
                        perPageSelector: '#perPage',
                        showDeletedSelector: '#showDeleted',
                        paginationSelector: '#pagination',
                        alertSelector: '#alertContainer',
                        createButtonSelector: '#createPortfolioBtn',
                        idField: 'uuid',
                        searchFields: ['title', 'description'],
                        showDeleted: showDeletedState,

                        // Configuración específica para portfolios
                        maxFiles: 10,
                        maxSizeKb: 5120, // 5MB
                        maxTotalSizeKb: 20480, // 20MB

                        tableHeaders: [{
                                field: 'title',
                                name: @json(__('portfolio_title')),
                                sortable: true,
                                getter: (portfolio) => {
                                    if (portfolio.project_type && portfolio.project_type.title) {
                                        return `<div class="font-medium text-gray-900 dark:text-gray-100">${portfolio.project_type.title}</div>`;
                                    }
                                    return `<span class="text-gray-400">${@json(__('no_title'))}</span>`;
                                }
                            },
                            {
                                field: 'description',
                                name: @json(__('portfolio_description')),
                                sortable: false,
                                getter: (portfolio) => {
                                    if (portfolio.project_type && portfolio.project_type.description) {
                                        const description = portfolio.project_type.description;
                                        const truncated = description.length > 100 ?
                                            description.substring(0, 100) + '...' : description;
                                        return `<div class="text-sm text-gray-600 dark:text-gray-300">${truncated}</div>`;
                                    }
                                    return `<span class="text-gray-400">${@json(__('no_description'))}</span>`;
                                }
                            },
                            {
                                field: 'category',
                                name: @json(__('portfolio_service_category')),
                                sortable: false,
                                getter: (portfolio) => {
                                    if (portfolio.project_type &&
                                        portfolio.project_type.service_category &&
                                        portfolio.project_type.service_category.service_category_name) {
                                        return `<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            ${portfolio.project_type.service_category.service_category_name}
                                        </span>`;
                                    }
                                    return `<span class="text-gray-400">${@json(__('no_category'))}</span>`;
                                }
                            },
                            {
                                field: 'images',
                                name: @json(__('portfolio_images')),
                                sortable: false,
                                getter: (portfolio) => {
                                    const imageCount = portfolio.images ? portfolio.images.length : 0;
                                    if (imageCount > 0) {
                                        const firstImage = portfolio.images[0];
                                        const imageText = imageCount === 1 ?
                                            `${imageCount} ${@json(__('image'))}` :
                                            `${imageCount} ${@json(__('images_management'))}`;
                                        return `
                                            <div class="flex items-center space-x-2">
                                                <div class="w-12 h-12 rounded-lg overflow-hidden bg-gray-200">
                                                    <img src="${firstImage.path}" alt="Portfolio" class="w-full h-full object-cover">
                                                </div>
                                                <div class="text-sm">
                                                    <div class="font-medium text-gray-900 dark:text-gray-100">${imageText}</div>
                                                </div>
                                            </div>
                                        `;
                                    }
                                    return `<span class="text-gray-400">${@json(__('no_images'))}</span>`;
                                }
                            },
                            {
                                field: 'created_at',
                                name: @json(__('created')),
                                sortable: true,
                                getter: (portfolio) => {
                                    if (portfolio.created_at) {
                                        const date = new Date(portfolio.created_at);
                                        return `<div class="text-sm text-gray-600 dark:text-gray-300">${date.toLocaleDateString()}</div>`;
                                    }
                                    return `<span class="text-gray-400">${@json(__('n_a'))}</span>`;
                                }
                            },
                            {
                                field: 'actions',
                                name: @json(__('actions')),
                                sortable: false,
                                getter: (portfolio) => {
                                    let actionsHtml = `
                                        <div class="flex justify-center space-x-2">
                                            <button data-id="${portfolio.uuid}" class="edit-btn inline-flex items-center justify-center w-9 h-9 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105" title="${@json(__('portfolio_edit'))}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>`;

                                    if (portfolio.deleted_at) {
                                        actionsHtml += `
                                            <button data-id="${portfolio.uuid}" class="restore-btn inline-flex items-center justify-center w-9 h-9 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-lg hover:from-emerald-600 hover:to-emerald-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105" title="${@json(__('portfolio_restore'))}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                            </button>`;
                                    } else {
                                        actionsHtml += `
                                            <button data-id="${portfolio.uuid}" class="delete-btn inline-flex items-center justify-center w-9 h-9 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg hover:from-red-600 hover:to-red-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105" title="${@json(__('portfolio_delete'))}">
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
                            create: @json(__('portfolio_create')),
                            edit: @json(__('portfolio_edit')),
                            delete: @json(__('portfolio_delete')),
                            restore: @json(__('portfolio_restore')),
                            confirmDelete: @json(__('portfolio_confirm_delete')),
                            confirmRestore: @json(__('portfolio_confirm_restore')),
                            deleteMessage: @json(__('portfolio_delete_message')),
                            restoreMessage: @json(__('portfolio_restore_message')),
                            yesDelete: @json(__('yes_delete')),
                            yesRestore: @json(__('yes_restore')),
                            cancel: @json(__('cancel')),
                            deletedSuccessfully: @json(__('portfolio_deleted_successfully')),
                            restoredSuccessfully: @json(__('portfolio_restored_successfully')),
                            errorDeleting: @json(__('portfolio_error_deleting')),
                            errorRestoring: @json(__('portfolio_error_restoring')),
                            success: @json(__('success')),
                            error: @json(__('error')),
                            saving: @json(__('saving')),
                            loading: @json(__('loading')),
                            save: @json(__('save')),
                            update: @json(__('update')),
                            yes: @json(__('yes')),
                            no: @json(__('no'))
                        },
                        entityConfig: {
                            identifierField: 'title',
                            displayName: @json(__('portfolio_title')),
                            fallbackFields: ['description'],
                            detailFormat: (portfolio) => {
                                if (portfolio.project_type && portfolio.project_type.title) {
                                    return portfolio.project_type.title;
                                }
                                return @json(__('no_title'));
                            }
                        }
                    });

                    console.log('PortfolioCrudManager created, about to load entities...');

                    // Cargar portfolios iniciales
                    window.portfolioManager.loadEntities();

                    console.log('loadEntities called');
                });
            </script>
        @endpush
    </div>
</x-app-layout>
