<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-wi        .company-details {
            flex: 1;
            text-align: right;
            padding-left: 40px;
            padding-top: 8px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: flex-end;
        }ial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        /* CSS Variables for Theming */
        :root {
            --primary-color: #4F46E5;
            --primary-dark: #4338CA;
            --secondary-color: #6c9bd0;
            --text-primary: #1F2937;
            --text-secondary: #6B7280;
            --text-muted: #9CA3AF;
            --background-light: #F9FAFB;
            --background-alt: #F3F4F6;
            --border-light: #E5E7EB;
            --success-bg: #D1FAE5;
            --success-text: #065F46;
            --error-bg: #FEE2E2;
            --error-text: #991B1B;
            --highlight-bg: #FEF3C7;
            --link-color: #3B82F6;
            --font-family: 'DejaVu Sans', Arial, sans-serif;
            --font-family-mono: 'Courier New', monospace;
        }

        /* Base PDF Styles */
        @page {
            margin: 20mm 15mm 25mm 15mm;
            size: A4;

            @top-center {
                content: "{{ $title }}";
                font-size: 10px;
                color: #666;
            }

            @bottom-left {
                content: "Generated: {{ $exportDate }}";
                font-size: 8px;
                color: #666;
            }

            @bottom-center {
                content: "Page " counter(page) " of " counter(pages);
                font-size: 9px;
                color: #666;
                font-weight: bold;
            }

            @bottom-right {
                content: "{{ $totalRecords }} records";
                font-size: 8px;
                color: #666;
            }
        }

        /* First page keeps original spacing */
        @page :first {
            margin-top: 20mm;
        }

        /* Subsequent pages need more space for continued content */
        @page :not(:first) {
            margin-top: 30mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-family);
            font-size: 10px;
            line-height: 1.4;
            color: #333;
            background: #fff;
        }

        /* Header Styles */
        .header {
            width: 90%;
            margin: 18px auto 25px auto;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--primary-color);
            page-break-inside: avoid;
            position: running(header);
        }

        /* Ensure proper spacing for continued content on new pages */
        .data-table {
            margin-top: 60px;
            /* Aumentado para dar espacio al header reducido */
            width: 90%;
            margin-left: auto;
            margin-right: auto;
            border-collapse: collapse;
            font-size: 12px;
        }

        /* Header repetition for all pages */
        @page :not(:first) {
            margin-top: 50mm;
            /* Aumentado para acomodar el header reducido */

            @top-left {
                content: element(header-continuation);
            }
        }

        /* Header reducido para páginas siguientes */
        .header-continuation {
            position: running(header-continuation);
            width: 90%;
            margin: 10px auto 15px auto;
            /* Más espacio arriba y abajo */
            padding: 10px 0;
            border-bottom: 1px solid var(--primary-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
        }

        .header-continuation .mini-logo {
            max-height: 40px;
            width: auto;
        }

        .header-continuation .mini-title {
            font-size: 12px;
            font-weight: bold;
            color: var(--primary-color);
            text-transform: uppercase;
        }

        /* New dedicated container for logo and company data */
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            width: 100%;
            margin-bottom: 15px;
            padding: 15px 0;
            min-height: 90px;
        }

        .company-info {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            width: 100%;
            gap: 40px;
        }

        .company-logo {
            flex-shrink: 0;
            width: 1200px;
            display: flex;
            align-items: flex-start;
            justify-content: flex-start;
            margin-top: 5px;
        }

        .company-logo img {
            max-width: 1200px;
            height: auto;
            max-height: 80px;
            object-fit: contain;
        }

        .company-details {
            flex: 1;
            text-align: right;
            padding-left: 40px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: flex-end;
        }

        .company-name {
            font-size: 14px;
            font-weight: bold;
            color: var(--text-primary);
            margin-bottom: 8px;
            text-align: right;
            line-height: 1.2;
        }

        .company-contact {
            font-size: 11px;
            color: var(--text-secondary);
            line-height: 1.4;
            text-align: right;
        }

        .report-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            color: var(--text-primary);
            margin: 10px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Report Information */
        .report-info {
            display: table;
            width: 100%;
            margin-bottom: 15px;
            font-size: 13px;
            /* Aumenta el tamaño de fuente del encabezado de exportación */
            color: var(--text-primary);
            font-weight: bold;
            padding: 0 5px;
        }

        .report-info-left {
            display: table-cell;
            width: 50%;
        }

        .report-info-right {
            display: table-cell;
            width: 50%;
            text-align: right;
        }

        .filters-title {
            font-weight: bold;
            margin-bottom: 3px;
        }

        /* Table Styles */
        .data-table {
            width: 90%;
            margin: 0 auto 50px auto;
            border-collapse: collapse;
            font-size: 12px;
        }

        @if (($options['repeat_headers'] ?? false) === true)
            .data-table thead {
                display: table-header-group !important;
            }
        @else
            .data-table thead {
                display: table-row-group;
            }
        @endif

        .data-table th {
            background: var(--primary-color);
            color: white;
            padding: 6px 8px;
            text-align: center;
            font-weight: bold;
            font-size: 11px;
            border: 1px solid var(--primary-dark);
            line-height: 1.2;
            height: auto;
        }

        .data-table td {
            padding: 6px 8px;
            border: 1px solid var(--border-light);
            vertical-align: middle;
            text-align: center;
            font-size: 11px;
            line-height: 1.3;
            height: auto;
        }

        .data-table tbody tr:nth-child(even) {
            background: var(--background-light);
        }

        .data-table tbody tr:hover {
            background: var(--background-alt);
        }

        /* Table pagination and page breaks */
        .data-table tbody tr {
            page-break-inside: avoid;
            break-inside: avoid;
            height: auto;
        }

        .data-table thead tr {
            page-break-after: avoid;
            break-after: avoid;
        }

        /* Conditional header repetition across pages */
        .data-table {
            page-break-inside: auto;
            break-inside: auto;
        }

        .data-table tbody {
            display: table-row-group;
        }

        .data-table tfoot {
            display: table-footer-group;
        }

        /* Header repetition - Simplified */
        .header {
            page-break-inside: avoid;
        }

        /* Content starts immediately after header */
        .content {
            margin-top: 0;
        }

        /* Prevent orphaned headers */

        /* Optimize row height */
        .data-table tr {
            min-height: 25px;
            height: auto;
        }

        /* Status Styles */
        .status-active {
            background: var(--success-bg);
            color: var(--success-text);
            padding: 2px 6px;
            border-radius: 12px;
            font-size: 8px;
            font-weight: bold;
            text-align: center;
            display: inline-block;
            min-width: 50px;
        }

        .status-inactive {
            background: var(--error-bg);
            color: var(--error-text);
            padding: 2px 6px;
            border-radius: 12px;
            font-size: 8px;
            font-weight: bold;
            text-align: center;
            display: inline-block;
            min-width: 50px;
        }

        /* Text Alignment */
        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        /* Summary Section */
        .summary {
            width: 90%;
            margin: 60px auto 70px auto;
            padding: 20px;
            background: var(--background-light);
            border-radius: 6px;
            border-left: 4px solid var(--primary-color);
        }

        .summary-title {
            font-size: 12px;
            font-weight: bold;
            color: var(--text-primary);
            margin-bottom: 8px;
        }

        .summary-stats {
            display: table;
            width: 100%;
        }

        .summary-stat {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 5px;
        }

        .summary-stat-value {
            font-size: 14px;
            font-weight: bold;
            color: var(--primary-color);
            display: block;
        }

        .summary-stat-label {
            font-size: 8px;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Footer */
        .footer {
            width: 90%;
            margin: 60px auto 0 auto;
            padding-top: 25px;
            border-top: 1px solid var(--border-light);
            font-size: 8px;
            color: var(--text-muted);
            text-align: center;
        }

        /* Page Break */
        .page-break {
            page-break-before: always;
            break-before: page;
        }

        .page-break-avoid {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        /* Better page flow control */
        .keep-together {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .allow-page-break {
            page-break-inside: auto;
            break-inside: auto;
        }

        .data-table {
            page-break-inside: auto;
            break-inside: auto;
        }

        /* Responsive adjustments for PDF */
        @media print {
            .no-print {
                display: none;
            }
        }

        /* Long text handling */
        .text-wrap {
            word-wrap: break-word;
            word-break: break-all;
        }

        .text-ellipsis {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Number formatting */
        .number {
            text-align: right;
            font-family: var(--font-family-mono);
        }

        /* Highlight important data */
        .highlight {
            background: var(--highlight-bg);
            padding: 1px 3px;
            border-radius: 2px;
        }

        /* Email and URL styling */
        .email,
        .url {
            color: var(--link-color);
            text-decoration: none;
        }

        /* Container for full-width layouts */
        .container {
            width: 90%;
            max-width: 90%;
            margin: 0 auto;
            padding: 0;
        }

        /* Alternative header layout */
        .header-alternative {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            padding: 10px 0;
        }

        .header-left {
            flex-shrink: 0;
            width: 50%;
            vertical-align: top;
            text-align: left;
        }

        .header-right {
            flex: 1;
            text-align: right;
            vertical-align: top;
            padding-left: 20px;
            padding-top: 23px;
        }

        .logo {
            max-width: 164px;
            height: auto;
            margin-top: 10px;
            vertical-align: top;
        }

        /* No data message styling */
        .no-data {
            text-align: center;
            padding: 40px;
            color: var(--text-secondary);
        }

        .no-data-icon {
            font-size: 16px;
            margin-bottom: 10px;
        }

        .no-data-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .no-data-text {
            font-size: 10px;
        }

        /* Record count styling */
        .record-count {
            margin-top: 15px;
            text-align: center;
            font-size: 9px;
            color: var(--text-secondary);
        }

        /* Custom column widths based on headers */
        @if (isset($headers))
            @foreach ($headers as $header => $config)
                .col-{{ $loop->index }} {
                    width: {{ $config['width'] ?? 'auto' }};
                    text-align: {{ $config['align'] ?? 'left' }};
                }
            @endforeach
        @endif

        @stack('pdf-theme-styles')

        @stack('pdf-custom-styles')
    </style>

    <!-- Additional head content stack -->
    @stack('pdf-head')
</head>

<body>
    @if ($options['show_header'] ?? true)
        <!-- Header Section -->
        <div class="header">
            @if ($options['show_company_info'] ?? true)
                <div class="header-content">
                    <div class="company-info">
                        @if (isset($companyInfo['logo_path']) && file_exists($companyInfo['logo_path']))
                            <div class="company-logo">
                                <img src="{{ $companyInfo['logo_path'] }}"
                                    alt="{{ $companyInfo['name'] ?? 'Company Logo' }}">
                            </div>
                        @endif
                        <div class="company-details">
                            <div class="company-name">{{ $companyInfo['name'] ?? 'V GENERAL CONTRACTORS' }}
                            </div>
                            <div class="company-contact">
                                {{ $companyInfo['address'] ?? '1302 Waugh Dr # 810, Houston, TX 77019' }}<br>
                                {{ $companyInfo['phone'] ?? '+1 (713) 587-6423' }}<br>
                                {{ $companyInfo['email'] ?? 'info@vgeneralcontractors.com' }}<br>
                                {{ $companyInfo['website'] ?? 'https://vgeneralcontractors.com' }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <div class="report-title">{{ $title }}</div>

            @if ($options['show_export_info'] ?? true)
                <div class="report-info">
                    <div class="report-info-left">
                        <div><strong>Generated:</strong> {{ $exportDate }}</div>
                        <div><strong>By:</strong> {{ $exportedBy }}</div>
                    </div>
                    <div class="report-info-right">
                        <div><strong>Total Records:</strong> {{ $totalRecords }}</div>
                        @if (isset($additionalData['date_range']))
                            <div><strong>Period:</strong> {{ $additionalData['date_range'] }}</div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Header reducido para páginas siguientes (estilo Word) -->
    <div class="header-continuation">
        @if (isset($companyInfo['logo_path']) && file_exists($companyInfo['logo_path']))
            <img src="{{ $companyInfo['logo_path'] }}" alt="Logo" class="mini-logo">
        @endif
        <div class="mini-title">{{ $title }}</div>
        <div style="font-size: 9px; color: var(--text-muted);">
            Page <span class="page-number"></span>
        </div>
    </div>

    <!-- Content Section -->
    <div class="content">
        @yield('content')
    </div>

    @if ($options['show_summary'] ?? false && isset($additionalData['summary']))
        <!-- Summary Section -->
        <div class="page-break-avoid">
            <div class="summary">
                <div class="summary-title">Report Summary</div>
                <div class="summary-stats">
                    @if (isset($additionalData['summary']['total_companies']))
                        <div class="summary-stat">
                            <span class="summary-stat-value">{{ $additionalData['summary']['total_companies'] }}</span>
                            <span class="summary-stat-label">Total</span>
                        </div>
                    @endif
                    @if (isset($additionalData['summary']['active_companies']))
                        <div class="summary-stat">
                            <span
                                class="summary-stat-value">{{ $additionalData['summary']['active_companies'] }}</span>
                            <span class="summary-stat-label">Active</span>
                        </div>
                    @endif
                    @if (isset($additionalData['summary']['inactive_companies']))
                        <div class="summary-stat">
                            <span
                                class="summary-stat-value">{{ $additionalData['summary']['inactive_companies'] }}</span>
                            <span class="summary-stat-label">Inactive</span>
                        </div>
                    @endif
                    @if (isset($additionalData['summary']['active_percentage']))
                        <div class="summary-stat">
                            <span
                                class="summary-stat-value">{{ $additionalData['summary']['active_percentage'] }}%</span>
                            <span class="summary-stat-label">Active Rate</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    @if ($options['show_footer'] ?? true)
        <!-- Footer Section -->
        <div class="footer">
            <div>This report was generated automatically by {{ config('app.name') }}</div>
            @if (isset($additionalData['filters_applied']) && $additionalData['filters_applied'] !== 'No filters applied')
                <div style="margin-top: 5px; text-align: center; font-size: 9px;">
                    <strong>Applied Filters:</strong> {{ $additionalData['filters_applied'] }}
                </div>
            @endif
        </div>
    @endif
</body>

</html>
