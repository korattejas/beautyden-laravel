<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Invoice - {{ $orderNumber ?? '-' }}</title>

    <style>
        @page { margin: 15px; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #333; line-height: 1.4; margin: 0; padding: 10px; }
        /* ================= HEADER ================= */
        .header-table { width: 100%; margin-bottom: 20px; border-bottom: 4px solid #000; padding-bottom: 10px; }
        .invoice-title { font-size: 48px; font-weight: bold; color: #000; text-align: right; margin-bottom: 0; }
        .invoice-meta { font-size: 13px; text-align: right; color: #444; }
        /* ================= ADDRESS SECTION ================= */
        .address-table { width: 100%; margin-bottom: 20px; background-color: #fcfcfc; }
        .address-col { width: 50%; vertical-align: top; padding: 10px; }
        .section-title { font-weight: bold; font-size: 14px; color: #2d5a27; text-transform: uppercase; margin-bottom: 5px; border-bottom: 1px solid #ddd; }
        .name { font-weight: bold; font-size: 15px; color: #111; }
        /* ================= ITEMS TABLE ================= */
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .items-table th { background-color: #000; color: #ffffff; padding: 10px; text-align: left; text-transform: uppercase; }
        .items-table td { padding: 10px; border-bottom: 1px solid #eee; }
        .items-table tr:nth-child(even) { background-color: #f2f7f2; }
        /* ================= SUMMARY ================= */
        .summary-wrapper { width: 100%; margin-top: 10px; }
        .summary-table { width: 280px; float: right; border-collapse: collapse; }
        .summary-table td { padding: 5px 8px; font-size: 13px; }
        .grand-total-row { background-color: #2d5a27; color: #fff; font-weight: bold; font-size: 16px; }
        /* ================= TERMS & FOOTER ================= */
        .bottom-section { clear: both; margin-top: 30px; }
        .terms { font-size: 11px; color: #555; background: #f9f9f9; padding: 10px; border-radius: 5px; border-left: 5px solid #2d5a27; }
        .footer { margin-top: 20px; text-align: center; font-size: 12px; color: #000; font-weight: bold; border-top: 1px solid #000; padding-top: 10px; }
    </style>
</head>

<body>
    <table class="header-table">
        <tr>
            <td width="50%" style="vertical-align: middle;">
                <img src="{{ public_path('uploads/logo/logo-new.png') }}" style="height:80px; width:auto;">
            </td>
            <td style="vertical-align: bottom;">
                <div class="invoice-title">INVOICE</div>
                <div class="invoice-meta">
                    <strong>Order:</strong> {{ $orderNumber ?? '' }}<br>
                    <strong>Date:</strong> {{ $order->created_at ? $order->created_at->timezone('Asia/Kolkata')->format('d-M-Y h:i A') : '' }}<br>
                    <strong>Status:</strong> {{ $order->order_status }}
                </div>
            </td>
        </tr>
    </table>

    <table class="address-table">
        <tr>
            <td class="address-col">
                <div class="section-title">Beautician Details</div>
                <div class="name">{{ $teamMember->name ?? $user->name ?? '' }}</div>
                Phone: {{ $teamMember->phone ?? $user->mobile_number ?? '' }}<br>
                Address: {{ $order->address ?? $teamMember->address ?? $user->address ?? 'N/A' }}
            </td>
            <td class="address-col" style="text-align: right;">
                <div class="section-title">BeautyDen Products</div>
                <div class="name">BeautyDen</div>
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
                <th>Product Name</th>
                <th style="width: 80px; text-align: right;">Price</th>
                <th style="width: 50px; text-align: center;">Qty</th>
                <th style="width: 100px; text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @if(is_array($order->order_data))
                @foreach($order->order_data as $key => $item)
                <tr>
                    <td align="center">{{ $key+1 }}</td>
                    <td style="font-weight: bold;">
                        {{ $item['name'] }} 
                        @if(!empty($item['variant_name']))
                            <br><small>({{ $item['variant_name'] }})</small>
                        @endif
                    </td>
                    <td align="right">₹{{ number_format($item['price'], 2) }}</td>
                    <td align="center">{{ $item['qty'] }}</td>
                    <td align="right">₹{{ number_format($item['total'], 2) }}</td>
                </tr>
                @endforeach
            @endif
        </tbody>
    </table>

    <div class="summary-wrapper">
        <table class="summary-table">
            <tr class="grand-total-row">
                <td>GRAND TOTAL</td>
                <td align="right">
                    ₹{{ number_format($order->total_amount, 2) }}
                </td>
            </tr>
        </table>
        <div style="clear: both;"></div>
    </div>

    <div class="bottom-section">
        <div class="terms">
            <strong>Terms & Conditions:</strong><br>
            • Orders once placed cannot be cancelled.<br>
            • This is an electronically generated invoice.
        </div>
        <div class="footer">
            Thank You for choosing BeautyDen! | www.beautyden.in | +91 95747 58282
        </div>
    </div>
</body>
</html>
