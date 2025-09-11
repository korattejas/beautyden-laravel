<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Services Report</title>
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
    <h2>Services Report</h2>

    <table>
        <thead>
            <tr>
                <th>Id</th>
                <th>Category</th>
                <th>Name</th>
                <th>Price</th>
                <th>Discount Price</th>
                <th>Total Price</th>
                <th>Discount %</th>
                <th>Duration</th>
                <th>Rating</th>
                <th>Reviews</th>
                <th>Description</th>
                <th>Includes</th>
                <th>Is Popular</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @php
                $grandTotal = 0;
                $grandDiscount = 0;
            @endphp
            @foreach ($services as $s)
                @php
                    $price = $s->price ?? 0;
                    $discount = $s->discount_price ?? 0;
                    $totalPrice = $price - $discount;
                    $discountPercent = $price > 0 ? ($discount / $price) * 100 : 0;
                @endphp
                <tr>
                    <td>{{ $s->id }}</td>
                    <td>{{ $s->category_name ?? '-' }}</td>
                    <td>{{ $s->name }}</td>
                    <td>{{ number_format($price, 2) }}</td>
                    <td>{{ $discount > 0 ? number_format($discount, 2) : '-' }}</td>
                    <td>{{ number_format($totalPrice, 2) }}</td>
                    <td>{{ number_format($discountPercent, 2) }}%</td>
                    <td>{{ $s->duration }}</td>
                    <td>{{ $s->rating ?? '-' }}</td>
                    <td>{{ $s->reviews ?? '-' }}</td>
                    <td class="desc">{{ Str::limit($s->description, 80) }}</td>
                    <td class="desc">
                        @if ($s->includes)
                            {{ implode(', ', json_decode($s->includes, true)) }}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $s->is_popular ? 'Yes' : 'No' }}</td>
                    <td>{{ $s->status ? 'Active' : 'Inactive' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
