<x-app-layout>
    {{-- Commenting out Jetstream header to avoid duplicate titles --}}
    {{-- <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Call Records') }}
        </h2>
    </x-slot> --}}

    <div style="background-color: #141414;" class="text-white min-h-screen">
        <!-- Page Header -->
        <div class="p-6">
            <div class="mb-8 text-center sm:text-center md:text-left lg:text-left">
                <h2 class="text-base sm:text-base md:text-2xl lg:text-2xl font-bold text-white mb-2">
                    {{ __('call_records_title') }}</h2>
                <p class="text-base sm:text-base md:text-base lg:text-base text-gray-400">
                    {{ __('call_records_subtitle') }}
                </p>
            </div>

            <!-- Call Records Content -->
            <div class="max-w-7xl mx-auto py-10">
                @if (session('error'))
                    <div class="mb-4 rounded-lg bg-red-100 p-4 text-sm text-red-700 dark:bg-red-800 dark:text-red-200"
                        role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-lg">
                <div class="p-6">
                    <!-- Alert Container -->
                    <div id="alertContainer"></div>
                    
                    <!-- Filter Bar -->
                    <div class="mb-6 flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                        <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center flex-1">
                            <!-- Search Input -->
                            <div class="relative">
                                <input type="text" id="searchInput" placeholder="{{ __('call_record_search_placeholder') }}" 
                                       class="w-full sm:w-64 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700  text-gray-900 dark:text-gray-200 placeholder-gray-500 dark:placeholder-gray-400">
                                <svg class="absolute right-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            
                            <!-- Date Range Picker -->
                            <div class="flex gap-2 items-center">
                                <input type="text" id="startDate" placeholder="{{ __('start_date') }}" 
                                       class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white text-gray-900 dark:text-gray-200 placeholder-gray-500 dark:placeholder-gray-400">
                                <span class="text-gray-500">{{ __('to') }}</span>
                                <input type="text" id="endDate" placeholder="{{ __('end_date') }}" 
                                       class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white text-gray-900 dark:text-gray-200 placeholder-gray-500 dark:placeholder-gray-400">
                                <button id="clearDates" class="px-3 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                                    {{ __('clear') }}
                                </button>
                            </div>
                        </div>
                        
                        <div class="flex gap-2 items-center">
                            <!-- Per Page Selector -->
                            <select id="perPage" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white text-gray-900 dark:text-gray-200">
                                <option value="10" class="text-gray-900 dark:text-gray-200">10 per page</option>
                                <option value="25" class="text-gray-900 dark:text-gray-200">25 per page</option>
                                <option value="50" class="text-gray-900 dark:text-gray-200">50 per page</option>
                                <option value="100" class="text-gray-900 dark:text-gray-200">100 per page</option>
                            </select>
                            
                            <!-- Refresh Button -->
                            <button id="refreshBtn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                {{ __('refresh_list') }}
                            </button>
                        </div>
                    </div>
                    
                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer" data-sort="start_timestamp">
                                        {{ __('call_record_start_time') }}
                                        <span class="sort-icon ml-1">↕</span>
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer" data-sort="from_number">
                                        {{ __('call_record_from') }}
                                        <span class="sort-icon ml-1">↕</span>
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer" data-sort="to_number">
                                        {{ __('call_record_to') }}
                                        <span class="sort-icon ml-1">↕</span>
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer" data-sort="duration_ms">
                                        {{ __('call_record_duration') }}
                                        <span class="sort-icon ml-1">↕</span>
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer" data-sort="call_status">
                                        {{ __('call_record_status') }}
                                        <span class="sort-icon ml-1">↕</span>
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer" data-sort="user_sentiment">
                                        {{ __('call_record_sentiment') }}
                                        <span class="sort-icon ml-1">↕</span>
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('call_record_actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="callRecordsTableBody" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <!-- Loading row -->
                                <tr id="loadingRow">
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                        <div class="flex items-center justify-center">
                                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            {{ __('call_record_loading') }}
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div id="pagination" class="mt-6"></div>
                </div>
            </div>
        </div>
    </div>
    
    <x-slot name="title">
        {{ __('call_records_title') }}
    </x-slot>
    
    @push('scripts')
        <!-- Flatpickr for date picker -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        
        <script>
            $(document).ready(function() {
                // State variables
                let currentPage = 1;
                let perPage = 10;
                let sortField = 'start_timestamp';
                let sortDirection = 'desc';
                let searchTerm = '';
                let startDate = '';
                let endDate = '';
                let apiCache = new Map();
                
                // DOM elements
                const $tableBody = $('#callRecordsTableBody');
                const $loadingRow = $('#loadingRow');
                const $pagination = $('#pagination');
                const $searchInput = $('#searchInput');
                const $perPageSelect = $('#perPage');
                const $startDateInput = $('#startDate');
                const $endDateInput = $('#endDate');
                const $clearDatesBtn = $('#clearDates');
                const $refreshBtn = $('#refreshBtn');
                
                // Initialize Flatpickr for date inputs
                const startDatePicker = flatpickr($startDateInput[0], {
                    dateFormat: 'Y-m-d',
                    maxDate: 'today',
                    onChange: function(selectedDates, dateStr) {
                        startDate = dateStr;
                        if (endDate && startDate) {
                            fetchCalls();
                        }
                    }
                });
                
                const endDatePicker = flatpickr($endDateInput[0], {
                    dateFormat: 'Y-m-d',
                    maxDate: 'today',
                    onChange: function(selectedDates, dateStr) {
                        endDate = dateStr;
                        if (startDate && endDate) {
                            fetchCalls();
                        }
                    }
                });
                
                // Clear date filters
                $clearDatesBtn.on('click', function() {
                    startDate = '';
                    endDate = '';
                    startDatePicker.clear();
                    endDatePicker.clear();
                    fetchCalls();
                });
                
                // Clear search
                 function clearSearch() {
                     searchTerm = '';
                     $searchInput.val('');
                     fetchCalls();
                 }
                 
                 // Helper functions for formatting
                 function formatPhoneNumber(phoneNumber) {
                     if (!phoneNumber) return 'N/A';
                     const cleaned = phoneNumber.replace(/\D/g, '');
                     if (cleaned.length === 10) {
                         return `(${cleaned.slice(0, 3)}) ${cleaned.slice(3, 6)}-${cleaned.slice(6)}`;
                     } else if (cleaned.length === 11 && cleaned[0] === '1') {
                         return `+1 (${cleaned.slice(1, 4)}) ${cleaned.slice(4, 7)}-${cleaned.slice(7)}`;
                     }
                     return phoneNumber;
                 }
                 
                 function formatTimestamp(timestamp) {
                     if (!timestamp) return 'N/A';
                     const date = new Date(timestamp);
                     if (isNaN(date.getTime())) return 'Invalid Date';
                     const month = (date.getMonth() + 1).toString().padStart(2, '0');
                     const day = date.getDate().toString().padStart(2, '0');
                     const year = date.getFullYear();
                     const hours = date.getHours().toString().padStart(2, '0');
                     const minutes = date.getMinutes().toString().padStart(2, '0');
                     return `${month}/${day}/${year} ${hours}:${minutes}`;
                 }
                 
                 function formatDate(timestamp) {
                     if (!timestamp) return 'N/A';
                     const date = new Date(timestamp);
                     if (isNaN(date.getTime())) return 'Invalid Date';
                     return date.toLocaleDateString('en-US', {
                         year: 'numeric',
                         month: '2-digit',
                         day: '2-digit'
                     });
                 }
                 
                 function formatDuration(durationMs) {
                     if (!durationMs) return 'N/A';
                     return Math.round(durationMs / 1000) + 's';
                 }
                 
                 // Fetch calls from API
                 function fetchCalls() {
                     const params = {
                         page: currentPage,
                         per_page: perPage,
                         sort_field: sortField,
                         sort_direction: sortDirection
                     };
                     
                     if (searchTerm) {
                         params.search = searchTerm;
                     }
                     
                     if (startDate && endDate) {
                         params.start_date = startDate;
                         params.end_date = endDate;
                     }
                     
                     const cacheKey = JSON.stringify(params);
                     
                     if (apiCache.has(cacheKey)) {
                         const cachedData = apiCache.get(cacheKey);
                         renderTable(cachedData.calls);
                         updatePagination(cachedData);
                         return;
                     }
                     
                     showLoading(true);
                     
                     $.ajax({
                         url: '/api/call-records',
                         type: 'GET',
                         data: params,
                         headers: {
                             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                             'Accept': 'application/json'
                         },
                         success: function(response) {
                             apiCache.set(cacheKey, response);
                             renderTable(response.calls);
                             updatePagination(response);
                             showLoading(false);
                         },
                         error: function(xhr) {
                             console.error('Error fetching calls:', xhr);
                             showError('{{ __('call_record_error') }}');
                             showLoading(false);
                         }
                     });
                 }
                 
                 // Render table with call data
                 function renderTable(calls) {
                     if (!calls || calls.length === 0) {
                         $tableBody.html(`
                             <tr>
                                 <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                     {{ __('call_record_no_records_found') }}
                                 </td>
                             </tr>
                         `);
                         return;
                     }
                     
                     let tableHtml = '';
                     calls.forEach(call => {
                         const callSuccessful = call.call_analysis && call.call_analysis.call_successful;
                         const status = call.call_status || 'Unknown';
                         const statusBadgeClass = callSuccessful ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                         
                         const sentiment = call.call_analysis && call.call_analysis.user_sentiment ? call.call_analysis.user_sentiment : 'Unknown';
                         let sentimentBadgeClass = 'bg-gray-100 text-gray-800';
                         if (sentiment === 'Positive') {
                             sentimentBadgeClass = 'bg-green-100 text-green-800';
                         } else if (sentiment === 'Negative') {
                             sentimentBadgeClass = 'bg-red-100 text-red-800';
                         }
                         
                         let actionsHtml = `
                             <div class="flex justify-center space-x-2">
                                 <button data-id="${call.call_id}" class="view-details-btn inline-flex items-center justify-center w-9 h-9 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105" title="View Details">
                                     <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                         <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                         <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                     </svg>
                                 </button>`;
                         
                         if (call.recording_url) {
                             actionsHtml += `
                                 <button data-audio-url="${call.recording_url}" class="play-audio-btn inline-flex items-center justify-center w-9 h-9 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:from-green-600 hover:to-green-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105" title="Play Audio">
                                     <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                         <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                         <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                     </svg>
                                 </button>`;
                         }
                         
                         actionsHtml += `</div>`;
                         
                         tableHtml += `
                             <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                 <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white text-center">${formatTimestamp(call.start_timestamp)}</td>
                                 <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white text-center">${formatPhoneNumber(call.from_number)}</td>
                                 <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white text-center">${formatPhoneNumber(call.to_number)}</td>
                                 <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white text-center">${formatDuration(call.duration_ms)}</td>
                                 <td class="px-6 py-4 whitespace-nowrap text-center">
                                     <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusBadgeClass}">${status}</span>
                                 </td>
                                 <td class="px-6 py-4 whitespace-nowrap text-center">
                                     <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${sentimentBadgeClass}">${sentiment}</span>
                                 </td>
                                 <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">${actionsHtml}</td>
                             </tr>
                         `;
                     });
                     
                     $tableBody.html(tableHtml);
                 }
                 
                 // Custom event handlers for call records
                 $(document).on('click', '.view-details-btn', function(e) {
                     e.preventDefault();
                     const callId = $(this).data('id');
                     showCallDetails(callId);
                 });

                 $(document).on('click', '.play-audio-btn', function(e) {
                     e.preventDefault();
                     const audioUrl = $(this).data('audio-url');
                     playAudio(audioUrl);
                 });

                // Show/hide loading state
                function showLoading(show) {
                    if (show) {
                        $tableBody.html(`
                            <tr id="loading-row">
                                <td colspan="7" class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600"></div>
                                        <span class="text-gray-500 dark:text-gray-400">{{ __('call_record_loading') }}</span>
                                    </div>
                                </td>
                            </tr>
                        `);
                    }
                }
                
                function showError(message) {
                    $tableBody.html(`
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-red-500">
                                ${message}
                            </td>
                        </tr>
                    `);
                }
                
                // Update pagination
                function updatePagination(data) {
                    const { current_page, last_page, per_page, total } = data;
                    
                    // Update pagination info
                    const startItem = ((current_page - 1) * per_page) + 1;
                    const endItem = Math.min(current_page * per_page, total);
                    
                    $('#pagination-info').html(`
                        {{ __('showing') }} ${startItem} {{ __('to_lowercase') }} ${endItem} {{ __('of') }} ${total} {{ __('call_record_display_name') }}
                    `);
                    
                    // Update pagination buttons
                    let paginationHtml = '';
                    
                    // Previous button
                    if (current_page > 1) {
                        paginationHtml += `
                            <button onclick="changePage(${current_page - 1})" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <span class="sr-only">Previous</span>
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        `;
                    } else {
                        paginationHtml += `
                            <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-400 cursor-not-allowed">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        `;
                    }
                    
                    // Page numbers
                    const startPage = Math.max(1, current_page - 2);
                    const endPage = Math.min(last_page, current_page + 2);
                    
                    for (let i = startPage; i <= endPage; i++) {
                        if (i === current_page) {
                            paginationHtml += `
                                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-blue-50 text-sm font-medium text-blue-600">
                                    ${i}
                                </span>
                            `;
                        } else {
                            paginationHtml += `
                                <button onclick="changePage(${i})" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                    ${i}
                                </button>
                            `;
                        }
                    }
                    
                    // Next button
                    if (current_page < last_page) {
                        paginationHtml += `
                            <button onclick="changePage(${current_page + 1})" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <span class="sr-only">Next</span>
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        `;
                    } else {
                        paginationHtml += `
                            <span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-400 cursor-not-allowed">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        `;
                    }
                    
                    $('#pagination-controls').html(paginationHtml);
                }
                
                // Pagination and sorting functions
                window.changePage = function(page) {
                    currentPage = page;
                    fetchCalls();
                };
                
                window.sortTable = function(field) {
                    if (sortField === field) {
                        sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
                    } else {
                        sortField = field;
                        sortDirection = 'asc';
                    }
                    currentPage = 1;
                    fetchCalls();
                };
                
                // Function to show call details
                function showCallDetails(callId) {
                    fetch(`/api/call-records/${callId}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            const call = data.call;
                            let html = `
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-600 dark:text-gray-300">From</h3>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                            ${formatPhoneNumber(call.from_number)}
                                        </p>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-600 dark:text-gray-300">To</h3>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                            ${formatPhoneNumber(call.to_number)}
                                        </p>
                                    </div>
                                </div>
                            `;

                            if (call.call_analysis && call.call_analysis.call_summary) {
                                html += `
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Summary</h3>
                                        <p class="mt-2 text-sm text-gray-700 dark:text-gray-200 leading-relaxed">
                                            ${call.call_analysis.call_summary}
                                        </p>
                                    </div>
                                `;
                            }

                            if (call.transcript) {
                                html += `
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Transcript</h3>
                                        <div class="mt-2 space-y-4">
                                            ${call.transcript.split('\n').map(line => `
                                                <p class="text-sm text-gray-700 dark:text-gray-200 leading-relaxed">${line}</p>
                                            `).join('')}
                                        </div>
                                    </div>
                                `;
                            }

                            if (call.metadata && Object.keys(call.metadata).length > 0) {
                                html += `
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Additional Information</h3>
                                        <dl class="mt-2 space-y-2">
                                            ${Object.entries(call.metadata).map(([key, value]) => `
                                                <div>
                                                    <dt class="text-sm font-medium text-gray-600 dark:text-gray-300">
                                                        ${key.charAt(0).toUpperCase() + key.slice(1).replace(/_/g, ' ')}
                                                    </dt>
                                                    <dd class="mt-1 text-sm text-gray-800 dark:text-gray-100">
                                                        ${typeof value === 'object' ? JSON.stringify(value) : value}
                                                    </dd>
                                                </div>
                                            `).join('')}
                                        </dl>
                                    </div>
                                `;
                            }
                            
                            Swal.fire({
                                title: '{{ __('call_details') }}',
                                html: html,
                                width: '800px',
                                showCloseButton: true,
                                showConfirmButton: false,
                                customClass: {
                                    popup: 'text-left'
                                }
                            });
                        })
                        .catch(error => {
                            console.error('Error fetching call details:', error);
                            Swal.fire({
                                title: 'Error',
                                text: 'Error loading call details: ' + error.message,
                                icon: 'error'
                            });
                        });
                }

                // Function to play audio
                function playAudio(audioUrl) {
                    const audioHtml = `
                        <div class="text-center">
                            <audio controls class="w-full mb-4">
                                <source src="${audioUrl}" type="audio/mpeg">
                                <source src="${audioUrl}" type="audio/wav">
                                <source src="${audioUrl}" type="audio/ogg">
                                Tu navegador no soporta el elemento de audio.
                            </audio>
                            <div class="mt-4">
                                <a href="${audioUrl}" download class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Descargar Audio
                                </a>
                            </div>
                        </div>
                    `;
                    
                    Swal.fire({
                        title: 'Reproducir Audio',
                        html: audioHtml,
                        width: '600px',
                        showCloseButton: true,
                        showConfirmButton: false
                    });
                }

                // Initialize the page
                fetchCalls();
            });
        </script>
    @endpush
</x-app-layout>
