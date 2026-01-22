@extends('exports.base-pdf-template')

@push('pdf-theme-styles')
    <style>
        /* Custom Green Theme Example */
        :root {
            --primary-color: #10B981;
            --primary-dark: #059669;
            --secondary-color: #34D399;
            --font-family: 'Arial', sans-serif;
        }

        /* Custom table styling for this theme */
        .data-table th {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        /* Custom report title */
        .report-title {
            color: var(--primary-color);
            border-bottom: 3px solid var(--secondary-color);
            padding-bottom: 10px;
        }

        /* Custom summary styling */
        .summary {
            background: linear-gradient(135deg, #F0FDF4, #ECFDF5);
            border-left: 4px solid var(--primary-color);
            box-shadow: 0 2px 4px rgba(16, 185, 129, 0.1);
        }

        /* Custom status colors for this theme */
        .status-active {
            background: #D1FAE5;
            color: #065F46;
            border: 1px solid var(--primary-color);
        }

        .status-inactive {
            background: #FEE2E2;
            color: #991B1B;
            border: 1px solid #EF4444;
        }
    </style>
@endpush

@section('content')
    <!-- This would be your custom content for this themed PDF -->
    <div style="text-align: center; padding: 20px;">
        <h2 style="color: var(--primary-color);">Custom Themed PDF Example</h2>
        <p>This demonstrates how to create custom themes using the base PDF template.</p>
    </div>
@endsection
