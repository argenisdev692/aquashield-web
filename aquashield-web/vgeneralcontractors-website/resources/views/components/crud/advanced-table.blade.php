@props([
    'id' => 'crud-advanced-table',
    'columns' => [],
    'managerName' => 'crudManager',
    'loadingText' => 'Loading...',
    'noDataText' => 'No records found',
    'responsive' => true,
    'sortable' => true,
    'darkMode' => true,
])

<div
    class="{{ $responsive ? 'overflow-x-auto' : '' }} bg-gray-50 dark:bg-gray-700 rounded-lg shadow-inner border border-gray-200 dark:border-gray-600">
    <table id="{{ $id }}" class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                @foreach ($columns as $column)
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider {{ $sortable && ($column['sortable'] ?? true) ? 'cursor-pointer sort-header' : '' }}"
                        @if ($sortable && ($column['sortable'] ?? true)) data-field="{{ $column['field'] }}" @endif>
                        {{ $column['label'] }}
                        @if ($sortable && ($column['sortable'] ?? true))
                            <span class="sort-icon"></span>
                        @endif
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody id="{{ $id }}-body" class="dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            <!-- Loading row -->
            <tr id="loadingRow">
                <td colspan="{{ count($columns) }}" class="px-6 py-4 text-center">
                    <svg class="animate-spin h-5 w-5 mr-3 text-blue-500 inline-block" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    {{ $loadingText }}
                </td>
            </tr>
        </tbody>
    </table>
</div>

@push('styles')
    <style>
        /* Sort icons */
        .sort-header .sort-icon::after {
            content: '↕️';
            font-size: 0.75rem;
            margin-left: 0.25rem;
            opacity: 0.5;
        }

        .sort-header.sort-asc .sort-icon::after {
            content: '↑';
            opacity: 1;
            color: #3B82F6;
        }

        .sort-header.sort-desc .sort-icon::after {
            content: '↓';
            opacity: 1;
            color: #3B82F6;
        }

        .sort-header:hover .sort-icon::after {
            opacity: 0.8;
        }
    </style>
@endpush
