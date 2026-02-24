<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $contract_type }}</title>
    <style>
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            color: #333;
            line-height: 1.6;
            margin: 40px;
        }

        h1, h2, h3 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 20px;
        }

        h2 {
            font-size: 24px;
            border-bottom: 2px solid #6c63ff;
            padding-bottom: 10px;
        }

        .provider-info {
            margin: 30px 0;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background: #f9f9f9;
        }

        .provider-info p {
            font-size: 16px;
            margin: 8px 0;
        }

        .signature-section {
            margin-top: 40px;
        }

        .signature-label {
            margin-bottom: 5px;
            font-weight: bold;
        }

        .signature-img {
            width: 250px;
            height: auto;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .footer {
            margin-top: 60px;
            text-align: center;
            font-size: 12px;
            color: #999;
        }
    </style>
</head>
<body>

    <h1>Non-Disclosure & Non-Circumvention Agreement (NDA) And Provider Appointment & Service Agreement</h1>

    <div class="provider-info">
        <p><strong>Provider Name:</strong> {{ $provider['provider_name'] }}</p>
        <p><strong>Mobile:</strong> {{ $provider['provider_mobile'] }}</p>
        <p><strong>Full Address:</strong> {{ $provider['provider_address'] }}</p>
        <p><strong>Date:</strong> {{ now()->format('d-m-Y') }}</p>
    </div>

    <div class="signature-section">
        <p class="signature-label">Provider Signature:</p>
        <img src="{{ public_path($signature_path) }}" class="signature-img">
    </div>

    <div class="signature-section" style="margin-top: 50px;">
        <p class="signature-label">Authorized Representative:</p>
        <div style="width: 250px; height: 50px; border-bottom: 1px solid #333;"></div>
    </div>

    <div class="footer">
        <p>BeautyDen | Confidential Document</p>
    </div>

</body>
</html>
