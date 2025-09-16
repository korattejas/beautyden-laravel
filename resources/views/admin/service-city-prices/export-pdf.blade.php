<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Service City Prices Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #333;
        }

        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: center;
        }

        th {
            background: #2c3e50;
            color: #fff;
            font-size: 11px;
        }

        tr:nth-child(even) {
            background: #f9f9f9;
        }

        .desc {
            text-align: left;
            font-size: 10px;
        }
    </style>
</head>

<body>
    <h2>Service City Prices Report</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>City</th>
                <th>Category</th>
                <th>Sub Category</th>
                <th>Service</th>
                <th>Price</th>
                <th>Discount Price</th>
                {{-- <th>Total Price</th>
                <th>Discount %</th> --}}
                <th>Status</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($prices as $p)
                @php
                    // $price = (float) $p->price;
                    // $discount = (float) $p->discount_price;
                    // $totalPrice = $discount > 0 ? $price - $discount : $price;
                    // $discountPercent = ($price > 0 && $discount > 0) ? ($discount / $price) * 100 : 0;
                @endphp
                <tr>
                    <td>{{ $p->id }}</td>
                    <td>{{ $p->city_name ?? '-' }}</td>
                    <td>{{ $p->category_name ?? '-' }}</td>
                    <td>{{ $p->sub_category_name ?? '-' }}</td>
                    <td>{{ $p->service_name ?? '-' }}</td>
                    <td>{{ $p->price ?? 0 }}</td>
                    <td>{{ $p->discount_price ?? 0}}</td>
                    {{-- <td>{{ number_format($totalPrice, 2) }}</td>
                    <td>{{ number_format($discountPercent, 2) }}%</td> --}}
                    <td>{{ $p->status == 1 ? 'Active' : 'Inactive' }}</td>
                    <td>{{ \Carbon\Carbon::parse($p->created_at)->format('d-m-Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
