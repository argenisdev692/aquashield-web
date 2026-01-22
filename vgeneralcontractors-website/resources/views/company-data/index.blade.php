<x-app-layout>
    {{-- Dark background container with consistent styling --}}
    <div class="min-h-screen bg-gray-900" style="background-color: #141414;">
        {{-- Header section with title and subtitle --}}
        <div class="p-4 sm:p-6">
            <div class="mb-4 sm:mb-8 text-center sm:text-center md:text-left lg:text-left">
                <h2 class="text-base sm:text-base md:text-2xl lg:text-2xl font-bold text-white mb-2">
                    {{ __('company_data_title') }}</h2>
                <p class="text-base sm:text-base md:text-base lg:text-base text-gray-400">
                    {{ __('company_data_subtitle') }}
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
                    <!-- Header with edit button only -->
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ __('company_data_title') }}
                        </h3>

                        <!-- Edit company data button -->
                        <div>
                            <button id="editCompanyDataBtn"
                                class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring focus:ring-blue-200 disabled:opacity-25">
                                <span class="mr-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </span>
                                {{ __('edit') }} {{ __('company_data_title') }}
                            </button>
                        </div>
                    </div>

                    <!-- Company Data Display -->
                    <div
                        class="bg-gray-50 dark:bg-gray-700 rounded-lg shadow-inner border border-gray-200 dark:border-gray-600 p-6">
                        @if ($companyData)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Basic Information -->
                                <div class="space-y-4">
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('ceo_name') }}</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                            {{ $companyData->name ?? 'N/A' }}</p>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('company_name') }}</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                            {{ $companyData->company_name ?? 'N/A' }}</p>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('email') }}</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                            {{ $companyData->email ?? 'N/A' }}</p>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('phone') }}</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                            @if ($companyData->phone)
                                                @php
                                                    $phone = $companyData->phone;
                                                    $cleaned = preg_replace('/[^0-9]/', '', $phone);
                                                    if (strlen($cleaned) === 11 && substr($cleaned, 0, 1) === '1') {
                                                        $phoneDigits = substr($cleaned, 1);
                                                        $formatted = sprintf(
                                                            '(%s) %s-%s',
                                                            substr($phoneDigits, 0, 3),
                                                            substr($phoneDigits, 3, 3),
                                                            substr($phoneDigits, 6, 4),
                                                        );
                                                    } elseif (strlen($cleaned) === 10) {
                                                        $formatted = sprintf(
                                                            '(%s) %s-%s',
                                                            substr($cleaned, 0, 3),
                                                            substr($cleaned, 3, 3),
                                                            substr($cleaned, 6, 4),
                                                        );
                                                    } else {
                                                        $formatted = $phone;
                                                    }
                                                @endphp
                                                {{ $formatted }}
                                            @else
                                                N/A
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <!-- Additional Information -->
                                <div class="space-y-4">
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('address') }}</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                            {{ $companyData->address ?? 'N/A' }}</p>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('website') }}</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                            @if ($companyData->website)
                                                <a href="{{ $companyData->website }}" target="_blank"
                                                    class="text-blue-600 hover:text-blue-800">{{ $companyData->website }}</a>
                                            @else
                                                N/A
                                            @endif
                                        </p>
                                    </div>

                                    <!-- Social Media Links -->
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('social_media') }}</label>
                                        <div class="mt-1 flex space-x-4">
                                            @if ($companyData->facebook_link)
                                                <a href="{{ $companyData->facebook_link }}" target="_blank"
                                                    class="text-blue-600 hover:text-blue-800">
                                                    <span class="sr-only">{{ __('facebook') }}</span>
                                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M20 10C20 4.477 15.523 0 10 0S0 4.477 0 10c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V10h2.54V7.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V10h2.773l-.443 2.89h-2.33v6.988C16.343 19.128 20 14.991 20 10z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </a>
                                            @endif
                                            @if ($companyData->instagram_link)
                                                <a href="{{ $companyData->instagram_link }}" target="_blank"
                                                    class="text-pink-600 hover:text-pink-800">
                                                    <span class="sr-only">{{ __('instagram') }}</span>
                                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M10 0C4.477 0 0 4.477 0 10s4.477 10 10 10 10-4.477 10-10S15.523 0 10 0zm4.7 3.5c.3 0 .5.2.5.5v1.4c0 .3-.2.5-.5.5h-1.4c-.3 0-.5-.2-.5-.5V4c0-.3.2-.5.5-.5h1.4zM10 5.5c2.5 0 4.5 2 4.5 4.5s-2 4.5-4.5 4.5-4.5-2-4.5-4.5 2-4.5 4.5-4.5zm0 7.3c1.5 0 2.8-1.3 2.8-2.8S11.5 7.2 10 7.2 7.2 8.5 7.2 10s1.3 2.8 2.8 2.8z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </a>
                                            @endif
                                            @if ($companyData->linkedin_link)
                                                <a href="{{ $companyData->linkedin_link }}" target="_blank"
                                                    class="text-blue-700 hover:text-blue-900">
                                                    <span class="sr-only">{{ __('linkedin') }}</span>
                                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M16.338 16.338H13.67V12.16c0-.995-.017-2.277-1.387-2.277-1.39 0-1.601 1.086-1.601 2.207v4.248H8.014v-8.59h2.559v1.174h.037c.356-.675 1.227-1.387 2.526-1.387 2.703 0 3.203 1.778 3.203 4.092v4.711zM5.005 6.575a1.548 1.548 0 11-.003-3.096 1.548 1.548 0 01.003 3.096zm-1.337 9.763H6.34v-8.59H3.667v8.59zM17.668 1H2.328C1.595 1 1 1.581 1 2.298v15.403C1 18.418 1.595 19 2.328 19h15.34c.734 0 1.332-.582 1.332-1.299V2.298C19 1.581 18.402 1 17.668 1z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </a>
                                            @endif
                                            @if ($companyData->twitter_link)
                                                <a href="{{ $companyData->twitter_link }}" target="_blank"
                                                    class="text-blue-400 hover:text-blue-600">
                                                    <span class="sr-only">{{ __('twitter') }}</span>
                                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path
                                                            d="M6.29 18.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0020 3.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.073 4.073 0 01.8 7.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 010 16.407a11.616 11.616 0 006.29 1.84" />
                                                    </svg>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Location Information -->
                            @if ($companyData->latitude && $companyData->longitude)
                                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-600">
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('location') }}</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        {{ __('latitude') }}: {{ $companyData->latitude }}, {{ __('longitude') }}:
                                        {{ $companyData->longitude }}
                                    </p>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-8">
                                <p class="text-gray-500 dark:text-gray-400">{{ __('no_company_data_found') }}</p>
                                <button id="editCompanyDataBtn"
                                    class="mt-4 inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                    {{ __('add_company_information') }}
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @push('scripts')
            <!-- SweetAlert2 -->
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <!-- CrudManagerModal ya está disponible vía el sistema modular -->

            <!-- Estilos personalizados para SweetAlert2 -->
            <style>
                /* Estilos para modal de edición (azul) */
                .swal2-popup.swal-edit .swal2-header,
                .swal2-popup.swal-edit .swal2-title {
                    background: linear-gradient(135deg, #3B82F6, #2563EB) !important;
                    color: white !important;
                }

                /* Estilos generales para el modal */
                .swal2-popup {
                    border-radius: 12px !important;
                    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
                    overflow: hidden !important;
                }

                .swal2-title {
                    font-size: 1.5rem !important;
                    font-weight: 600 !important;
                    margin: 0 !important;
                    padding: 1rem !important;
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
            </style>
            <script>
                $(document).ready(function() {
                    // Make the manager globally accessible
                    window.companyDataManager = new CrudManagerModal({
                        entityName: 'CompanyData',
                        entityNamePlural: 'CompanyData',
                        routes: {
                            index: "{{ secure_url(route('company-data.index', [], false)) }}",
                            store: "{{ secure_url(route('company-data.store', [], false)) }}",
                            edit: "{{ secure_url(route('company-data.edit', 'UUID_PLACEHOLDER', false)) }}"
                                .replace('UUID_PLACEHOLDER', ':id'),
                            update: "{{ secure_url(route('company-data.update', 'UUID_PLACEHOLDER', false)) }}"
                                .replace('UUID_PLACEHOLDER', ':id'),
                            destroy: "{{ secure_url(route('company-data.destroy', 'UUID_PLACEHOLDER', false)) }}"
                                .replace('UUID_PLACEHOLDER', ':id'),
                            restore: "{{ secure_url(route('company-data.restore', 'UUID_PLACEHOLDER', false)) }}"
                                .replace('UUID_PLACEHOLDER', ':id'),
                            checkEmail: "{{ secure_url(route('company-data.check-email', [], false)) }}",
                            checkPhone: "{{ secure_url(route('company-data.check-phone', [], false)) }}"
                        },
                        tableSelector: '#companyDataTable-body',
                        searchSelector: '#searchInput',
                        perPageSelector: '#perPage',
                        showDeletedSelector: '#showDeleted',
                        paginationSelector: '#pagination',
                        alertSelector: '#alertContainer',
                        createButtonSelector: '#editCompanyDataBtn',
                        idField: 'uuid',
                        searchFields: ['name', 'company_name', 'email', 'phone'],
                        showDeleted: false,
                        // Modo de registro único - solo editar, no crear/eliminar
                        singleRecordMode: true,
                        formFields: [{
                                name: 'name',
                                type: 'text',
                                label: '{{ __('ceo_name') }}',
                                placeholder: '{{ __('enter_ceo_name') }}',
                                required: true,
                                validation: {
                                    required: true,
                                    maxLength: 255
                                },
                                capitalize: true
                            },
                            {
                                name: 'company_name',
                                type: 'text',
                                label: '{{ __('company_name') }}',
                                placeholder: '{{ __('enter_company_name') }}',
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
                                        url: "{{ route('company-data.check-email') }}",
                                        message: '{{ __('email_already_in_use') }}'
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
                                        url: "{{ route('company-data.check-phone') }}",
                                        message: '{{ __('phone_already_in_use') }}'
                                    }
                                }
                            },
                            {
                                name: 'address',
                                type: 'textarea',
                                label: '{{ __('address') }}',
                                placeholder: '{{ __('enter_address') }}',
                                required: false,
                                rows: 3,
                                validation: {
                                    maxLength: 500
                                },
                                capitalize: true
                            },
                            {
                                name: 'website',
                                type: 'url',
                                label: '{{ __('website') }}',
                                placeholder: '{{ __('website_placeholder') }}',
                                required: false,
                                validation: {
                                    maxLength: 255
                                }
                            },
                            {
                                name: 'facebook_link',
                                type: 'url',
                                label: '{{ __('facebook') }}',
                                placeholder: 'https://facebook.com/...',
                                required: false,
                                validation: {
                                    maxLength: 255
                                }
                            },
                            {
                                name: 'instagram_link',
                                type: 'url',
                                label: '{{ __('instagram') }}',
                                placeholder: 'https://instagram.com/...',
                                required: false,
                                validation: {
                                    maxLength: 255
                                }
                            },
                            {
                                name: 'linkedin_link',
                                type: 'url',
                                label: '{{ __('linkedin') }}',
                                placeholder: 'https://linkedin.com/...',
                                required: false,
                                validation: {
                                    maxLength: 255
                                }
                            },
                            {
                                name: 'twitter_link',
                                type: 'url',
                                label: '{{ __('twitter') }}',
                                placeholder: 'https://twitter.com/...',
                                required: false,
                                validation: {
                                    maxLength: 255
                                }
                            }
                        ],
                        tableHeaders: [], // Not used in single record mode
                        defaultSortField: 'created_at',
                        defaultSortDirection: 'desc',
                        translations: {
                            edit: '{{ __('edit_company_data') }}',
                            save: '{{ __('save') }}',
                            update: '{{ __('update') }}',
                            cancel: '{{ __('cancel') }}',
                            success: '{{ __('success') }}',
                            error: '{{ __('error') }}',
                            saving: '{{ __('saving') }}',
                            loading: '{{ __('loading') }}',
                            errorLoadingDataForEdit: '{{ __('error_loading_data_for_edit') }}',
                            // Traducciones de validación faltantes
                            isRequired: '{{ __('is_required') }}',
                            nameRequired: '{{ __('name_required') }}',
                            emailRequired: '{{ __('email_required') }}',
                            phoneRequired: '{{ __('phone_required') }}',
                            pleaseCorrectErrors: '{{ __('please_correct_errors') }}',
                            invalidFormat: '{{ __('invalid_format') }}',
                            invalidEmail: '{{ __('invalid_email') }}',
                            minimumCharacters: '{{ __('minimum_characters') }}',
                            maximumCharacters: '{{ __('maximum_characters') }}'
                        },
                        entityConfig: {
                            identifierField: 'company_name',
                            displayName: '{{ __('company_information_entity') }}',
                            fallbackFields: ['name', 'email']
                        }
                    });

                    // Custom event handler for edit button
                    $(document).on('click', '#editCompanyDataBtn', function() {
                        @if ($companyData && $companyData->uuid)
                            console.log('Company data UUID:', '{{ $companyData->uuid }}');
                            window.companyDataManager.showEditModal('{{ $companyData->uuid }}');
                        @else
                            console.log('No company data found, loading entities...');
                            // Debug: Log the routes being used
                            console.log('Routes configuration:', window.companyDataManager.routes);
                            console.log('Index route URL:', window.companyDataManager.routes.index);

                            // If no company data exists, load entities first to get or create the record
                            window.companyDataManager.loadEntities().then((response) => {
                                console.log('Loaded entities response:', response);
                                const data = window.companyDataManager.currentData;
                                console.log('Current data:', data);
                                console.log('Data type:', typeof data);
                                console.log('Data.data exists:', !!(data && data.data));
                                console.log('Data.data length:', data && data.data ? data.data.length :
                                    'N/A');

                                if (data && data.data && data.data.length > 0) {
                                    console.log('First entity:', data.data[0]);
                                    console.log('First entity UUID:', data.data[0].uuid);
                                    console.log('UUID type:', typeof data.data[0].uuid);
                                    console.log('UUID exists:', !!(data.data[0].uuid));

                                    if (data.data[0].uuid) {
                                        console.log('About to call showEditModal with UUID:', data.data[0]
                                            .uuid);
                                        window.companyDataManager.showEditModal(data.data[0].uuid);
                                    } else {
                                        console.error('UUID is falsy:', data.data[0].uuid);
                                        window.companyDataManager.showAlert('error', 'No valid UUID found');
                                    }
                                } else {
                                    console.error('No valid data structure found:', data);
                                    // Show error if still no data available
                                    window.companyDataManager.showAlert('error',
                                        '{{ __('could_not_load_company_info') }}');
                                }
                            }).catch((error) => {
                                console.error('Error loading company data:', error);
                                console.error('Error details:', {
                                    status: error.status,
                                    statusText: error.statusText,
                                    responseText: error.responseText,
                                    responseJSON: error.responseJSON
                                });
                                window.companyDataManager.showAlert('error',
                                    '{{ __('error_loading_company_info') }}');
                            });
                        @endif
                    });

                    // Don't initialize loadEntities automatically - only load when needed
                    console.log('CompanyDataManager initialized, ready for use');
                });
            </script>
        @endpush
    </div>
</x-app-layout>
