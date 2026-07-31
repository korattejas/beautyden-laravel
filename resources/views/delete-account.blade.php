<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Account - BeautyDen</title>
    
    <!-- Premium Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #d4a373; /* Elegant Gold/Warm Bronze */
            --primary-hover: #c39262;
            --accent: #ffb5a7; /* Soft Pastel Rose */
            --bg-gradient: linear-gradient(135deg, #FFF0EE 0%, #F8E2DE 100%);
            --card-bg: rgba(255, 255, 255, 0.85);
            --text-main: #3d342e; /* Soft dark brown instead of harsh black */
            --text-muted: #7d6e65;
            --border-radius: 24px;
            --shadow: 0 20px 40px rgba(220, 180, 175, 0.25);
        }

        /* Dark mode compatibility */
        @media (prefers-color-scheme: dark) {
            :root {
                --primary: #e6b89c;
                --primary-hover: #ffd9c0;
                --accent: #f28482;
                --bg-gradient: linear-gradient(135deg, #1e1715 0%, #120e0d 100%);
                --card-bg: rgba(30, 24, 22, 0.8);
                --text-main: #f5eae4;
                --text-muted: #bdaea6;
                --shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
            }
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Outfit', sans-serif;
            transition: all 0.3s ease;
        }

        body {
            background: var(--bg-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            color: var(--text-main);
            overflow-x: hidden;
        }

        .container {
            width: 100%;
            max-width: 580px;
            perspective: 1000px;
        }

        .card {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: var(--border-radius);
            padding: 48px 40px;
            box-shadow: var(--shadow);
            transform: translateY(0);
            animation: floatIn 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes floatIn {
            0% {
                opacity: 0;
                transform: translateY(30px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header {
            text-align: center;
            margin-bottom: 36px;
        }

        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem;
            font-weight: 700;
            letter-spacing: 1px;
            color: var(--text-main);
            margin-bottom: 8px;
            display: inline-block;
        }

        .logo span {
            color: var(--primary);
            font-style: italic;
        }

        .subtitle {
            font-size: 1.1rem;
            color: var(--text-muted);
            font-weight: 400;
        }

        h2 {
            font-family: 'Playfair Display', serif;
            font-size: 1.6rem;
            font-weight: 600;
            margin-bottom: 24px;
            text-align: center;
            color: var(--text-main);
        }

        .info-box {
            background: rgba(212, 163, 115, 0.1);
            border-left: 4px solid var(--primary);
            padding: 16px 20px;
            border-radius: 8px;
            margin-bottom: 28px;
            font-size: 0.95rem;
            line-height: 1.6;
            color: var(--text-main);
        }

        .steps-title {
            font-weight: 600;
            font-size: 1.05rem;
            margin-bottom: 16px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--primary);
        }

        .steps-list {
            list-style: none;
            margin-bottom: 28px;
        }

        .step-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 16px;
            font-size: 1rem;
            line-height: 1.5;
        }

        .step-number {
            background: var(--primary);
            color: #fff;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            font-weight: 600;
            margin-right: 14px;
            flex-shrink: 0;
            margin-top: 2px;
            box-shadow: 0 4px 10px rgba(212, 163, 115, 0.3);
        }

        .step-text {
            color: var(--text-main);
        }

        .disclaimer {
            font-size: 0.9rem;
            color: var(--text-muted);
            line-height: 1.6;
            border-top: 1px solid rgba(0, 0, 0, 0.08);
            padding-top: 20px;
            margin-bottom: 28px;
        }

        @media (prefers-color-scheme: dark) {
            .disclaimer {
                border-top: 1px solid rgba(255, 255, 255, 0.08);
            }
        }

        .footer-support {
            text-align: center;
            background: rgba(255, 255, 255, 0.05);
            padding: 16px;
            border-radius: 12px;
            font-size: 0.95rem;
        }

        .footer-support a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .footer-support a:hover {
            text-decoration: underline;
            color: var(--primary-hover);
        }

        /* Responsive adjustments */
        @media (max-width: 480px) {
            .card {
                padding: 32px 24px;
            }
            .logo {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="card">
            <div class="header">
                <div class="subtitle">Premium Beauty & Wellness Services</div>
            </div>
            
            <h2>Delete Your BeautyDen Account</h2>
            
            <div class="info-box">
                You can permanently delete your BeautyDen account and all associated personal data directly from inside the mobile application.
            </div>

            <div class="steps-title">Steps to Delete Account</div>
            <ul class="steps-list">
                <li class="step-item">
                    <span class="step-number">1</span>
                    <span class="step-text">Open the <strong>BeautyDen</strong> App on your device.</span>
                </li>
                <li class="step-item">
                    <span class="step-number">2</span>
                    <span class="step-text">Navigate to your <strong>Profile</strong> tab.</span>
                </li>
                <li class="step-item">
                    <span class="step-number">3</span>
                    <span class="step-text">Tap on <strong>Delete Account</strong> option.</span>
                </li>
                <li class="step-item">
                    <span class="step-number">4</span>
                    <span class="step-text">Confirm the deletion in the confirmation dialog.</span>
                </li>
            </ul>

            <div class="disclaimer">
                Upon confirmation, your profile, authentication data, and personal records will be permanently removed from our databases. Please note that some transaction records or booking histories may be retained for a limited period where required by law or regulatory authorities.
            </div>

            <div class="footer-support">
                Need help or have questions? Contact us at<br>
                <a href="mailto:contact@beautyden.in">support@beautyden.in</a>
            </div>
        </div>
    </div>

</body>
</html>
