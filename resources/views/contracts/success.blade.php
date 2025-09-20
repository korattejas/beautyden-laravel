<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contract Signed Successfully</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f0f4ff, #ffffff);
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 120px auto;
            background: #fff;
            padding: 50px 35px;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
            text-align: center;
            animation: fadeIn 1s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .success-icon {
            font-size: 80px;
            color: #4CAF50;
            margin-bottom: 25px;
        }

        h2 {
            font-size: 30px;
            color: #333;
            margin-bottom: 20px;
        }

        p {
            font-size: 18px;
            color: #555;
            line-height: 1.6;
            margin-bottom: 25px;
        }

        .thank-you {
            font-size: 20px;
            color: #6c63ff;
            font-weight: bold;
            margin-top: 20px;
        }

        @media (max-width: 600px) {
            .container {
                margin: 60px 15px;
                padding: 35px 20px;
            }
            .success-icon { font-size: 60px; }
            h2 { font-size: 26px; }
            p { font-size: 16px; }
            .thank-you { font-size: 18px; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="success-icon">✅</div>
    <h2>Contract Signed Successfully!</h2>
    <p>Thank you for signing the agreement. Our company representative will contact you shortly to proceed with the next steps.</p>
    <div class="thank-you">Thank you for joining BeautyDen!</div>
</div>

</body>
</html>
