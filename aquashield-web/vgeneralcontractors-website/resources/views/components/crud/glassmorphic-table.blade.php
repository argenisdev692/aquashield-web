@props([
    'id' => 'crud-glassmorphic-table',
    'columns' => [],
    'managerName' => 'crudManager',
    'loadingText' => 'Loading...',
    'noDataText' => 'No records found',
    'responsive' => true,
    'sortable' => true,
    'darkMode' => true,
])

<div class="relative overflow-hidden rounded-[6px]">
    {{-- Animated gradient border with enhanced glow --}}
    <div class="absolute inset-0 rounded-[6px] p-[2px] animate-border-glow">
        <div class="absolute inset-0 rounded-[6px] bg-gradient-to-r from-yellow-400 via-purple-500 via-orange-500 to-yellow-400 bg-[length:300%_300%] animate-gradient-border opacity-70"></div>
        <div class="relative w-full h-full bg-black/80 filter blur-[1px] rounded-[4px] border border-white/5"></div>
    </div>

    {{-- Table container with enhanced animated shadows --}}
    <div class="relative filter blur-[0.5px] bg-black/40 border-0 rounded-[4px] overflow-hidden m-[2px] animate-table-shadow shadow-lg shadow-purple-500/30">
        <div class="{{ $responsive ? 'overflow-x-auto' : '' }}">
            <table id="{{ $id }}" class="w-full">
                <thead>
                    <tr class="border-b border-white/5 relative">
                        @foreach ($columns as $index => $column)
                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-300 filter blur-[0.5px] relative {{ $sortable && ($column['sortable'] ?? true) ? 'cursor-pointer sort-header' : '' }}"
                                @if ($sortable && ($column['sortable'] ?? true)) data-field="{{ $column['field'] }}" @endif>
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-{{ ['yellow', 'purple', 'orange', 'yellow', 'purple'][($index % 5)]}}-500/5 to-transparent animate-shimmer{{ $index > 0 ? '-delay-' . $index : '' }}"></div>
                                <span class="relative z-10">{{ $column['label'] }}</span>
                                @if ($sortable && ($column['sortable'] ?? true))
                                    <span class="sort-icon relative z-10"></span>
                                @endif
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody id="{{ $id }}-body" class="divide-y divide-white/5 bg-black/90 filter blur-[0.5px]">
                    {{-- Loading row --}}
                    <tr id="loadingRow">
                        <td colspan="{{ count($columns) }}" class="px-6 py-4 text-center text-white">
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
    </div>
</div>

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/glassmorphic-table.css') }}">
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

@push('scripts')
    <script src="{{ asset('js/glassmorphic-table.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initGlassmorphicTable('{{ $id }}');
        });
    </script>
@endpush