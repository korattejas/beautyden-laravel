<!DOCTYPE html>
<html>
<head>
    <title>Appointments Export</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            font-family: sans-serif;
            font-size: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 5px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        h2 {
            text-align: center;
            font-family: sans-serif;
        }
    </style>
</head>
<body>
    <h2>Appointments Report</h2>
    <table>
        <thead>
            <tr>
                <th>Order #</th>
                <th>Client Name</th>
                <th>Date</th>
                <th>Time</th>
                <th>City</th>
                <th>Status</th>
                <th>Payment Type</th>
                <th>Amount (₹)</th>
                <th>Company Amount (₹)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($appointments as $appointment)
            <tr>
                <td>{{ $appointment->order_number }}</td>
                <td>{{ $appointment->first_name }} {{ $appointment->last_name }}</td>
                <td>{{ $appointment->appointment_date }}</td>
                <td>{{ $appointment->appointment_time }}</td>
                <td>{{ $appointment->city_name }}</td>
                <td>
                    @if($appointment->status == 1) Pending
                    @elseif($appointment->status == 2) Assigned
                    @elseif($appointment->status == 3) Completed
                    @elseif($appointment->status == 4) Cancelled
                    @else Unknown
                    @endif
                </td>
                <td>{{ strtoupper($appointment->payment_type ?? 'N/A') }}</td>
                <td>{{ number_format($appointment->services_data['summary']['grand_total'] ?? $appointment->price, 2) }}</td>
                <td>{{ number_format($appointment->company_amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
