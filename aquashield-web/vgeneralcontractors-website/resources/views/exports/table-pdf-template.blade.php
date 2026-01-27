@extends('exports.base-pdf-template')

@section('content')
    <!-- Data Table -->
    @if ($data->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    @foreach ($headers as $header => $config)
                        <th class="col-{{ $loop->index }} text-{{ $config['align'] ?? 'center' }}"
                            style="width: {{ $config['width'] ?? 'auto' }};">
                            {{ $header }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                    <tr>
                        @foreach ($headers as $header => $config)
                            @php
                                $field = match ($header) {
                                    'Nro' => 'number',
                                    '#' => 'number',
                                    'Company Name' => 'company_name',
                                    'Email' => 'email',
                                    'Phone' => 'phone',
                                    'Address' => 'address',
                                    'Website' => 'website',
                                    'Status' => 'status',
                                    'Assigned User' => 'assigned_user',
                                    'Created By' => 'assigned_user',
                                    'Created Date' => 'created_date',
                                    default => strtolower(str_replace(' ', '_', $header)),
                                };
                                $value = $row[$field] ?? 'N/A';
                                $align = $config['align'] ?? 'center';
                            @endphp

                            <td class="col-{{ $loop->index }} text-{{ $align }}">
                                @if ($field === 'status')
                                    @php
                                        // Determine status based on deleted_at field
                                        $statusValue =
                                            isset($row['deleted_at']) && $row['deleted_at'] !== null
                                                ? 'Inactive'
                                                : 'Active';
                                        $statusClass =
                                            isset($row['deleted_at']) && $row['deleted_at'] !== null
                                                ? 'status-inactive'
                                                : 'status-active';
                                    @endphp
                                    <span class="{{ $statusClass }}">
                                        {{ $statusValue }}
                                    </span>
                                @elseif($field === 'email' && $value !== 'N/A')
                                    <span class="email">{{ $value }}</span>
                                @elseif($field === 'website' && $value !== 'N/A')
                                    <span class="url">{{ $value }}</span>
                                @elseif($field === 'company_name')
                                    {{ $value }}
                                @elseif($field === 'number')
                                    <span class="number">{{ $value }}</span>
                                @else
                                    {{ $value }}
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div style="text-align: center; padding: 40px; color: #6B7280;">
            <div style="font-size: 16px; margin-bottom: 10px;">📄</div>
            <div style="font-size: 14px; font-weight: bold; margin-bottom: 5px;">No Data Available</div>
            <div style="font-size: 10px;">No records match the current filters or selection criteria.</div>
        </div>
    @endif
@endsection
