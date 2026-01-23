<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoices Report</title>
    <style>
        @page {
            margin: 15mm;
            size: A4 landscape;
        }
        body {
            font-family: 'Arial', sans-serif;
            font-size: 10px;
            color: #333;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            margin: 0;
            padding: 0;
        }
        .header {
            width: 100%;
            display: table;
            margin-bottom: 20px;
            border-bottom: 2px solid #4F46E5;
            padding-bottom: 15px;
        }
        .header-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .header-right {
            display: table-cell;
            width: 50%;
            text-align: right;
            vertical-align: top;
        }
        .logo {
            max-width: 120px;
            height: auto;
        }
        .company-info {
            font-size: 9px;
            line-height: 1.2;
            margin-top: 5px;
            color: #666;
        }
        .company-name {
            font-size: 14px;
            font-weight: bold;
            color: #4F46E5;
            margin-bottom: 5px;
        }
        .report-title {
            color: #4F46E5;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
        }
        .report-info {
            text-align: center;
            font-size: 9px;
            color: #666;
            margin-bottom: 20px;
        }
        table.invoices-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 8px;
        }
        table.invoices-table th {
            background-color: #4F46E5;
            color: white;
            font-weight: bold;
            text-align: center;
            padding: 6px 4px;
            border: 1px solid #333;
            font-size: 8px;
        }
        table.invoices-table td {
            padding: 4px;
            border: 1px solid #ddd;
            vertical-align: middle;
            text-align: center;
            font-size: 8px;
        }
        table.invoices-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        table.invoices-table tr:hover {
            background-color: #e3f2fd;
        }
        .text-right {
            text-align: right !important;
        }
        .text-center {
            text-align: center !important;
        }
        .text-left {
            text-align: left !important;
        }
        .status {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status.paid {
            background-color: #d4edda;
            color: #155724;
        }
        .status.pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status.overdue {
            background-color: #f8d7da;
            color: #721c24;
        }
        .status.print_pdf {
            background-color: #e1d4f7;
            color: #6b46c1;
        }
        .footer {
            margin-top: 20px;
            font-size: 8px;
            color: #666;
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .summary {
            margin-top: 15px;
            font-size: 9px;
        }
        .summary-item {
            display: inline-block;
            margin-right: 20px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Section -->
        <div class="header">
            <div class="header-left">
                <img src="{{ public_path('assets/logo/logo-png.png') }}" alt="{{ $company->company_name ?? 'V General Contractors' }}" class="logo">
            </div>
            <div class="header-right">
                <div class="company-name">{{ $company->company_name ?? 'V GENERAL CONTRACTORS' }}</div>
                <div class="company-info">
                    {{ $company->address ?? '1522 Waugh Dr # 510, Houston, TX 77019' }}<br>
                    @php
                        $phone = $collectionsEmail->phone ?? '+17133646240';
                        // Format phone number as (xxx) xxx-xxxx
                        if (strlen($phone) >= 10) {
                            $phone = preg_replace('/[^0-9]/', '', $phone);
                            $phone = '(' . substr($phone, -10, 3) . ') ' . substr($phone, -7, 3) . '-' . substr($phone, -4);
                        }
                    @endphp
                    {{ $phone }}<br>
                    {{ $collectionsEmail->email ?? 'collection@vgeneralcontractors.com' }}<br>
                    {{ $company->website ?? 'https://vgeneralcontractors.com/' }}
                </div>
            </div>
        </div>
        
        <!-- Report Title -->
        <div class="report-title">INVOICES REPORT</div>
        
        <!-- Report Info -->
        <div class="report-info">
            Generated on {{ now()->format('F j, Y \\a\\t g:i A') }} | Total Invoices: {{ count($invoices) }}
        </div>
        
        <!-- Invoices Table -->
        <table class="invoices-table">
            <thead>
                <tr>
                    <th>Invoice #</th>
                    <th>Bill To</th>
                    <th>Company</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th class="text-right">Subtotal</th>
                    <th class="text-right">Tax</th>
                    <th class="text-right">Total</th>
                    <th class="text-center">Date</th>
                    <th class="text-center">Items</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalSubtotal = 0;
                    $totalTax = 0;
                    $totalAmount = 0;
                @endphp
                @foreach($invoices as $invoice)
                    @php
                        $totalSubtotal += $invoice->subtotal ?? 0;
                        $totalTax += $invoice->tax_amount ?? 0;
                        $totalAmount += $invoice->balance_due ?? 0;
                    @endphp
                    <tr>
                        <td class="text-left"><strong>{{ $invoice->invoice_number }}</strong></td>
                        <td class="text-left">{{ $invoice->bill_to_name }}</td>
                        <td class="text-left">{{ $invoice->bill_to_company ?? '-' }}</td>
                        <td class="text-left">{{ $invoice->bill_to_email ?? '-' }}</td>
                        <td class="text-center">{{ $invoice->bill_to_phone ?? '-' }}</td>
                        <td class="text-center">
                            <span class="status {{ strtolower($invoice->status) }}">
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </td>
                        <td class="text-right">${{ number_format($invoice->subtotal ?? 0, 2) }}</td>
                        <td class="text-right">${{ number_format($invoice->tax_amount ?? 0, 2) }}</td>
                        <td class="text-right"><strong>${{ number_format($invoice->balance_due ?? 0, 2) }}</strong></td>
                        <td class="text-center">{{ $invoice->created_at->format('m/d/Y') }}</td>
                        <td class="text-center">{{ $invoice->items ? $invoice->items->count() : 0 }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #f0f0f0; font-weight: bold;">
                    <td colspan="6" class="text-right"><strong>TOTALS:</strong></td>
                    <td class="text-right"><strong>${{ number_format($totalSubtotal, 2) }}</strong></td>
                    <td class="text-right"><strong>${{ number_format($totalTax, 2) }}</strong></td>
                    <td class="text-right"><strong>${{ number_format($totalAmount, 2) }}</strong></td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
        
        <!-- Summary -->
        <div class="summary">
            <div class="summary-item">Total Invoices: {{ count($invoices) }}</div>
            <div class="summary-item">Total Amount: ${{ number_format($totalAmount, 2) }}</div>
            @php
                $statusCounts = $invoices->groupBy('status')->map->count();
            @endphp
            @foreach($statusCounts as $status => $count)
                <div class="summary-item">{{ ucfirst($status) }}: {{ $count }}</div>
            @endforeach
        </div>
        
        <!-- Footer -->
        <div class="footer">
            This report was generated automatically by {{ $company->company_name ?? 'V General Contractors' }} invoice management system.
        </div>
    </div>
</body>
</html>