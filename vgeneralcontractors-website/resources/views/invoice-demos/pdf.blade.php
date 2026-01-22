<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: 'Roboto', Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.4;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            width: 100%;
            display: table;
            margin-bottom: 20px;
        }
        .header-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            text-align: left;
        }
        .header-right {
            display: table-cell;
            width: 50%;
            text-align: left;
            vertical-align: top;
            padding-left: 120px;
            padding-top: 23px;
        }
        .logo {
            max-width: 164px;
            height: auto;
            margin-top: 10px;
            vertical-align: top;
        }
        .company-info {
            font-size: 10px;
            line-height: 1.2;
            margin-top: 5px;
        }
        .company-name {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 5px;
        }
        .invoice-title {
            color: #6c9bd0;
            font-size: 18px;
            font-weight: bold;
            margin-top: 30px;
            margin-bottom: 10px;
        }
        .invoice-details {
            width: 100%;
            display: table;
            margin-bottom: 20px;
        }
        .bill-to {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .invoice-info {
            display: table-cell;
            width: 50%;
            text-align: left;
            vertical-align: top;
        }
        .section-title {
            font-weight: normal;
            color: #666;
            margin-bottom: 5px;
        }
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.items th {
            background-color: #e6f0fa;
            color: #6c9bd0;
            font-weight: bold;
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        table.items td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            font-weight: 600;
        }
        table.items .amount {
            text-align: right;
        }
        table.items .qty {
            text-align: center;
        }
        table.items .rate {
            text-align: right;
        }
        .totals-table {
            width: 100%;
            margin-top: 20px;
        }
        .totals-table td {
            padding: 5px;
        }
        .totals-table .label {
            text-align: right;
            font-weight: normal;
        }
        .totals-table .value {
            text-align: right;
            width: 120px;
        }
        .balance-due {
            font-weight: bold;
            font-size: 14px;
            color: #333;
        }
        .footer {
            margin-top: 30px;
            font-size: 10px;
            color: #666;
            text-align: center;
        }
        .claim-info {
            margin-top: 20px;
            font-size: 10px;
        }
        .claim-info .value {
            font-weight: 600;
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
        
        <!-- Invoice Title -->
        <div class="invoice-title">INVOICE</div>
        
        <!-- Invoice Details -->
        <div class="invoice-details">
            <div class="bill-to">
                <div class="section-title" style="color: #666;">BILL TO</div>
                <strong>{{ $invoice->bill_to_name }}</strong><br>
                <strong>{{ $invoice->bill_to_address }}</strong><br>
                @if($invoice->bill_to_address_2)
                {{ $invoice->bill_to_address_2 }}<br>
                @endif
                {{ $invoice->bill_to_city }}{{ !empty($invoice->bill_to_state) ? ', '.$invoice->bill_to_state : '' }}{{ !empty($invoice->bill_to_zip) ? ' '.$invoice->bill_to_zip : '' }}
            </div>
            <div class="invoice-info">
                <table style="width: auto; margin-left: 120px;">
                    <tr>
                        <td class="section-title" style="padding-right: 50px;">INVOICE</td>
                        <td style="font-weight: bold;">{{ $invoice->invoice_number }}</td>
                    </tr>
                    <tr>
                        <td class="section-title" style="padding-right: 50px;">DATE</td>
                        <td style="font-weight: bold;">{{ $invoice->invoice_date->format('m/d/Y') }}</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Invoice Items -->
        <table class="items">
            <thead>
                <tr>
                    <th>SERVICE</th>
                    <th>DESCRIPTION</th>
                    <th class="qty">QTY</th>
                    <th class="rate">RATE</th>
                    <th class="amount">AMOUNT</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->service_name }}</td>
                    <td>{{ $item->description }}</td>
                    <td class="qty">{{ number_format($item->quantity, 2) }}</td>
                    <td class="rate">${{ number_format($item->rate, 2) }}</td>
                    <td class="amount">${{ number_format($item->amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Totals -->
        <table class="totals-table">
            <tr>
                <td></td>
                <td class="label">BALANCE DUE</td>
                <td class="value balance-due">${{ number_format($invoice->balance_due, 2) }}</td>
            </tr>
        </table>
        
        <!-- Claim Information -->
        <div class="claim-info">
            <table>
                <tr>
                    <td>INVOICE #: <span class="value">{{ $invoice->invoice_number }}</span></td>
                </tr>
                <tr>
                    <td>CLAIM #: <span class="value">{{ $invoice->claim_number }}</span></td>
                </tr>
                <tr>
                    <td>INSURANCE COMPANY: <span class="value">{{ $invoice->insurance_company }}</span></td>
                </tr>
                <tr>
                    <td>POLICY NUMBER: <span class="value">{{ $invoice->policy_number }}</span></td>
                </tr>
                @if($invoice->date_of_loss)
                <tr>
                    <td>DOL: <span class="value">{{ $invoice->date_of_loss->format('m/d/Y') }}</span></td>
                </tr>
                @endif
            </table>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            Payment is due within {{ $invoice->payment_terms ?? 30 }} days of the invoice date. Thank you for your business!
        </div>
    </div>
</body>
</html>