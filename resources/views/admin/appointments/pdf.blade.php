<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Invoice - {{ $appointment->order_number ?? '-' }} - {{ !empty($appointment->appointment_date) ? \Carbon\Carbon::parse($appointment->appointment_date)->format('d-M-Y') : '' }}</title>

    <style>
        @page {
            margin: 15px;
            /* Margin ghataadi jethi badhu ek page ma aave */
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.4;
            margin: 0;
            padding: 10px;
        }

        /* ================= HEADER ================= */
        .header-table {
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 4px solid #000;
            /* Blue Accent */
            padding-bottom: 10px;
        }

        .logo {
            /* Logo size haji vadhari - Extra Large */
            height: 190px;
            width: auto;
        }

        .invoice-title {
            font-size: 48px;
            font-weight: bold;
            color: #000;
            text-align: right;
            margin-bottom: 0;
        }

        .invoice-meta {
            font-size: 13px;
            text-align: right;
            color: #444;
        }

        /* ================= ADDRESS SECTION ================= */
        .address-table {
            width: 100%;
            margin-bottom: 20px;
            background-color: #fcfcfc;
        }

        .address-col {
            width: 50%;
            vertical-align: top;
            padding: 10px;
        }

        .section-title {
            font-weight: bold;
            font-size: 14px;
            color: #2d5a27;
            /* Green Theme */
            text-transform: uppercase;
            margin-bottom: 5px;
            border-bottom: 1px solid #ddd;
        }

        .name {
            font-weight: bold;
            font-size: 15px;
            color: #111;
        }

        /* ================= ITEMS TABLE ================= */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .items-table th {
            background-color: #000;
            /* Blue Header */
            color: #ffffff;
            padding: 10px;
            text-align: left;
            text-transform: uppercase;
        }

        .items-table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        /* Zebra striping for readability */
        .items-table tr:nth-child(even) {
            background-color: #f2f7f2;
            /* Very light green */
        }

        /* ================= SUMMARY ================= */
        .summary-wrapper {
            width: 100%;
            margin-top: 10px;
        }

        .summary-table {
            width: 280px;
            float: right;
            border-collapse: collapse;
        }

        .summary-table td {
            padding: 5px 8px;
            font-size: 13px;
        }

        .grand-total-row {
            background-color: #2d5a27;
            color: #fff;
            font-weight: bold;
            font-size: 16px;
        }

        /* ================= TERMS & FOOTER ================= */
        .bottom-section {
            clear: both;
            margin-top: 30px;
        }

        .terms {
            font-size: 11px;
            color: #555;
            background: #f9f9f9;
            padding: 10px;
            border-radius: 5px;
            border-left: 5px solid #2d5a27;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #000;
            font-weight: bold;
            border-top: 1px solid #000;
            padding-top: 10px;
        }
    </style>
</head>

<body>

    <table class="header-table">
        <tr>
            <td width="50%" style="vertical-align: middle;">
                <img src="{{ public_path('uploads/logo/logo-new.png') }}"
                    style="height:80px; width:auto;">
            </td>
            <td style="vertical-align: bottom;">
                <div class="invoice-title">INVOICE</div>
                <div class="invoice-meta">
                    <strong>Order:</strong> {{ $appointment->order_number ?? '' }}<br>
                    <strong>Date:</strong> {{ !empty($appointment->appointment_date) ? \Carbon\Carbon::parse($appointment->appointment_date)->format('d-M-Y') : '' }}<br>
                    <strong>Time:</strong> {{ !empty($appointment->appointment_time) ? \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') : '' }}
                </div>
            </td>
        </tr>
    </table>

    <table class="address-table">
        <tr>
            <td class="address-col">
                <div class="section-title">Customer Details</div>
                <div class="name">{{ $appointment->first_name ?? '' }} {{ $appointment->last_name ?? '' }}</div>
                Phone: {{ $appointment->phone ?? '' }}<br>
                Email: {{ $appointment->email ?? 'N/A' }}<br>
                Address: {{ $appointment->service_address ?? '' }}
            </td>
            <td class="address-col" style="text-align: right;">
                <div class="section-title">Service Provider</div>
                <div class="name">BeautyDen Services</div>
                +91 95747 58282<br>
                contact@beautyden.com<br>
                www.beautyden.in
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 40px; text-align: center;">#</th>
                <th>Service Name</th>
                <th style="width: 80px; text-align: right;">Price</th>
                <th style="width: 50px; text-align: center;">Qty</th>
                <th style="width: 100px; text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($services as $key => $s)
            <tr>
                <td align="center">{{ $key+1 }}</td>
                <td style="font-weight: bold;">{{ $s['name'] }}</td>
                <td align="right">₹{{ number_format($s['price'], 2) }}</td>
                <td align="center">{{ $s['qty'] }}</td>
                <td align="right">₹{{ number_format($s['total'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary-wrapper">
        <table class="summary-table">
            <tr>
                <td>Subtotal</td>
                <td align="right">
                    ₹{{ number_format($summary['sub_total'] ?? 0, 2) }}
                </td>
            </tr>

            {{-- ✅ Show Discount Only If > 0 --}}
            @if(!empty($summary['discount_amount']) && $summary['discount_amount'] > 0)
            <tr>
                <td style="color: #d9534f;">Discount</td>
                <td align="right" style="color: #d9534f;">
                    - ₹{{ number_format($summary['discount_amount'], 2) }}
                </td>
            </tr>
            @endif

            <tr>
                <td>Travel Fee</td>
                <td align="right">
                    ₹{{ number_format($summary['travel_charges'] ?? 0, 2) }}
                </td>
            </tr>

            <tr class="grand-total-row">
                <td>GRAND TOTAL</td>
                <td align="right">
                    ₹{{ number_format($summary['grand_total'] ?? 0, 2) }}
                </td>
            </tr>
        </table>
        <div style="clear: both;"></div>
    </div>

    <div class="bottom-section">
        <div class="terms">
            <strong>Terms & Conditions:</strong><br>
            • Services once rendered are non-refundable.<br>
            • For cancellations, please notify us 12 hours in advance.<br>
            • This is an electronically generated invoice.
        </div>

        <div class="footer">
            Thank You for choosing BeautyDen! | www.beautyden.in | +91 95747 58282
        </div>
    </div>

</body>

</html>