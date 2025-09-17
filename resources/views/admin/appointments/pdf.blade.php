<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Appointment - {{ $appointment->order_number ?? '-' }}</title>
    <style>
        body {
            font-family: 'Segoe UI', 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            color: #2e2e2e;
            background: #fff;
            line-height: 1.5;
        }

        .page {
            width: 100%;
            padding: 15px;
        }

        /* ---------- HEADER ---------- */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #c59d5f;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #c59d5f, #e1c699);
            border-radius: 8px;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 18px;
        }

        .brand-text h1 {
            margin: 0;
            font-size: 18px;
            color: #2e2e2e;
            font-weight: 700;
        }

        .brand-text p {
            margin: 0;
            font-size: 10px;
            color: #6b5b73;
        }

        .header-right {
            text-align: right;
        }

        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 10px;
            color: #fff;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .badge-pending {
            background: #f59e0b;
        }

        .badge-assigned {
            background: #3b82f6;
        }

        .badge-completed {
            background: #10b981;
        }

        .badge-rejected {
            background: #ef4444;
        }

        .date-time {
            font-size: 10px;
            padding: 3px 8px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            background: #fafafa;
            display: inline-block;
        }

        /* ---------- GRID CONTENT ---------- */
        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }

        .card {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 10px;
            background: #fafafa;
        }

        .card-title {
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 4px;
            color: #c59d5f;
        }

        .info-row {
            display: flex;
            margin-bottom: 6px;
        }

        .info-label {
            width: 65px;
            font-size: 9px;
            color: #666;
            font-weight: 600;
            text-transform: uppercase;
        }

        .info-value {
            flex: 1;
            font-size: 11px;
            font-weight: 500;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }

        .detail-item {
            border: 1px solid #eee;
            border-radius: 6px;
            padding: 6px;
            text-align: center;
            background: #fff;
        }

        .detail-label {
            font-size: 8px;
            color: #888;
            text-transform: uppercase;
        }

        .detail-value {
            font-size: 11px;
            font-weight: 700;
            color: #2e2e2e;
        }

        .chip-section {
            margin-top: 8px;
        }

        .section-label {
            font-size: 9px;
            text-transform: uppercase;
            color: #555;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .chips {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
        }

        .chip {
            background: #f1eadf;
            color: #5b4636;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: 600;
        }

        /* ---------- NOTES ---------- */
        .notes {
            border-left: 3px solid #c59d5f;
            padding: 10px;
            margin-bottom: 15px;
            background: #fdfaf5;
            border-radius: 6px;
        }

        .notes-title {
            font-size: 10px;
            font-weight: 700;
            margin-bottom: 4px;
            text-transform: uppercase;
            color: #5b4636;
        }

        .notes-content {
            font-size: 10px;
            color: #333;
        }

        /* ---------- FOOTER ---------- */
        .footer {
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }

        .footer-top {
            display: flex;
            justify-content: space-between;
            font-size: 9px;
            margin-bottom: 8px;
        }

        .company-info {
            text-align: center;
            background: #fafafa;
            padding: 6px;
            border-radius: 6px;
            font-size: 9px;
            line-height: 1.4;
            color: #555;
        }

        .company-name {
            font-size: 11px;
            font-weight: 700;
            color: #2e2e2e;
        }

        @page {
            size: A4;
            margin: 10mm;
        }
    </style>
</head>

<body>
    <div class="page">
        <!-- HEADER -->
        <div class="header">
            <div class="brand">
                <div class="logo">B</div>
                <div class="brand-text">
                    <h1>BeautyDen</h1>
                    <p>Trusted Beauty Service at Your Doorstep
                    </p>
                </div>
            </div>
            <div class="header-right">
                @php $s=$appointment->status; @endphp
                @if ($s == 1)
                    <div class="badge badge-pending">Pending</div>
                @elseif($s == 2)
                    <div class="badge badge-assigned">Assigned</div>
                @elseif($s == 3)
                    <div class="badge badge-completed">Completed</div>
                @elseif($s == 4)
                    <div class="badge badge-rejected">Rejected</div>
                @else
                    <div class="badge" style="background:#a0895c">Unknown</div>
                @endif
                @if ($appointment->appointment_date || $appointment->appointment_time)
                    <div class="date-time">
                        @if ($appointment->appointment_date)
                            {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d M, Y') }}
                        @endif
                        @if ($appointment->appointment_time)
                            <br>{{ $appointment->appointment_time }}
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- GRID CONTENT -->
        <div class="grid">
            <div class="card">
                <div class="card-title">Customer Information</div>
                @if ($appointment->first_name || $appointment->last_name)
                    <div class="info-row">
                        <div class="info-label">Name</div>
                        <div class="info-value">{{ $appointment->first_name }} {{ $appointment->last_name }}</div>
                    </div>
                @endif
                @if ($appointment->phone)
                    <div class="info-row">
                        <div class="info-label">Phone</div>
                        <div class="info-value">{{ $appointment->phone }}</div>
                    </div>
                @endif
                @if ($appointment->email)
                    <div class="info-row">
                        <div class="info-label">Email</div>
                        <div class="info-value">{{ $appointment->email }}</div>
                    </div>
                @endif
                @if ($appointment->service_address)
                    <div class="info-row">
                        <div class="info-label">Address</div>
                        <div class="info-value">{{ $appointment->service_address }}</div>
                    </div>
                @endif
                @if ($appointment->order_number)
                    <div class="info-row">
                        <div class="info-label">Order #</div>
                        <div class="info-value"><strong>{{ $appointment->order_number }}</strong></div>
                    </div>
                @endif
            </div>

            <div class="card">
                <div class="card-title">Order Details</div>
                <div class="detail-grid">
                    @if ($appointment->city_name)
                        <div class="detail-item">
                            <div class="detail-label">City</div>
                            <div class="detail-value">{{ $appointment->city_name }}</div>
                        </div>
                    @endif
                    @if ($appointment->price)
                        <div class="detail-item">
                            <div class="detail-label">Price</div>
                            <div class="detail-value">₹{{ number_format($appointment->price, 2) }}</div>
                        </div>
                    @endif
                    @if ($appointment->discount_price)
                        <div class="detail-item" style="grid-column:1/-1">
                            <div class="detail-label">Discount</div>
                            <div class="detail-value">₹{{ number_format($appointment->discount_price, 2) }}</div>
                        </div>
                    @endif
                </div>
                @if (!empty($services))
                    <div class="chip-section">
                        <div class="section-label">Services</div>
                        <div class="chips">
                            @foreach ($services as $s)
                                <div class="chip">{{ $s }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif
                @if (!empty($team_members))
                    <div class="chip-section">
                        <div class="section-label">Team</div>
                        <div class="chips">
                            @foreach ($team_members as $m)
                                <div class="chip">{{ $m }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- NOTES -->
        @if ($appointment->special_notes)
            <div class="notes">
                <div class="notes-title">Special Notes</div>
                <div class="notes-content">{{ $appointment->special_notes }}</div>
            </div>
        @endif

        <!-- FOOTER -->
        <div class="footer">
            <div class="footer-top">
                @if ($appointment->assigned_by)
                    <div>Assigned by: <strong>{{ $appointment->assigned_by }}</strong></div>
                @endif
                @if ($appointment->created_at)
                    <div>Created:
                        <strong>{{ \Carbon\Carbon::parse($appointment->created_at)->format('d M, Y H:i') }}</strong>
                    </div>
                @endif
            </div>
            <div class="company-info">
                <div class="company-name">BeautyDen Professional Services</div>
                +91 95747 58282 | contact@beautyden.com | www.beautyden.in
            </div>
        </div>
    </div>
</body>

</html>
