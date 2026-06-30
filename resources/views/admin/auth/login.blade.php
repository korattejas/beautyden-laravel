<!doctype html>
<html lang="en" data-textdirection="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <title>BeautyDen | Admin Login</title>

    <link rel="shortcut icon" type="image/x-icon" href="{{ URL::asset('panel-assets/admin-logo/logo.png') }}">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" type="text/css" href="{{ URL::asset('panel-assets/vendors/css/extensions/toastr.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('panel-assets/css/bootstrap-extended.css') }}">

    <style>
        :root {
            --black: #0a0a0a;
            --black-soft: #111827;
            --blue: #4f46e5;
            --blue-bright: #6366f1;
            --blue-light: #818cf8;
            --blue-glow: rgba(79, 70, 229, 0.15);
            --text: #111827;
            --text-muted: #6b7280;
            --border: #e5e7eb;
            --white: #ffffff;
            --surface: #f9fafb;
            --shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.12);
            --radius: 12px;
            --transition: 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        *, *::before, *::after { box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            margin: 0;
            min-height: 100vh;
            background: var(--surface);
            color: var(--text);
            -webkit-font-smoothing: antialiased;
        }

        .login-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* ── Left Brand Panel ── */
        .brand-panel {
            flex: 1;
            position: relative;
            background: var(--black);
            display: none;
            overflow: hidden;
        }

        @media (min-width: 992px) {
            .brand-panel { display: flex; }
        }

        .brand-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 80% 60% at 20% 80%, rgba(79, 70, 229, 0.25) 0%, transparent 60%),
                radial-gradient(ellipse 60% 50% at 80% 20%, rgba(129, 140, 248, 0.15) 0%, transparent 55%),
                linear-gradient(160deg, var(--black) 0%, #312e81 100%);
        }

        .brand-grid {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 48px 48px;
            mask-image: linear-gradient(to bottom, transparent, black 20%, black 80%, transparent);
        }

        .brand-content {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 3rem 4rem;
            width: 100%;
            color: #fff;
        }

        .brand-hero {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            max-width: 480px;
        }

        .brand-hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 700;
            line-height: 1.2;
            margin: 0 0 1.25rem;
            color: #fff;
        }

        .brand-hero h1 em {
            font-style: normal;
            color: var(--blue-light);
        }

        .brand-hero p {
            font-size: 1.05rem;
            line-height: 1.7;
            color: rgba(255, 255, 255, 0.65);
            margin: 0 0 2.5rem;
        }

        .brand-features {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .brand-feature {
            display: flex;
            align-items: center;
            gap: 0.875rem;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.75);
        }

        .brand-feature-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: rgba(59, 130, 246, 0.12);
            border: 1px solid rgba(59, 130, 246, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--blue-light);
            font-size: 0.85rem;
            flex-shrink: 0;
        }

        .brand-footer {
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.35);
        }

        /* ── Right Form Panel ── */
        .form-panel {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.5rem;
            background: var(--white);
            position: relative;
        }

        .form-panel::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(30, 64, 175, 0.06) 0%, transparent 70%);
            pointer-events: none;
        }

        .form-container {
            width: 100%;
            max-width: 420px;
            position: relative;
            z-index: 1;
        }

        .form-logo {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 1.75rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--border);
        }

        .form-logo img {
            height: 80px;
            width: auto;
            margin-bottom: 0.4rem;
        }

        .form-logo span {
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--text-muted);
        }

        .form-header {
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .form-header h2 {
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--black);
            margin: 0 0 0.35rem;
            letter-spacing: -0.02em;
        }

        .form-header p {
            font-size: 0.875rem;
            color: var(--text-muted);
            margin: 0;
        }

        .auth-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
        }

        /* ── Form Fields ── */
        .field-group {
            margin-bottom: 1.25rem;
        }

        .field-group label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 0.5rem;
            letter-spacing: 0.01em;
        }

        .field-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .field-icon {
            position: absolute;
            left: 14px;
            color: var(--text-muted);
            font-size: 0.9rem;
            pointer-events: none;
            transition: color var(--transition);
            z-index: 1;
        }

        .field-wrap input {
            width: 100%;
            height: 48px;
            padding: 0 44px 0 42px;
            background: var(--surface);
            border: 1.5px solid var(--border);
            border-radius: var(--radius);
            font-family: inherit;
            font-size: 0.95rem;
            font-weight: 400;
            color: var(--text);
            transition: border-color var(--transition), box-shadow var(--transition), background var(--transition);
            outline: none;
        }

        .field-wrap input::placeholder {
            color: #94a3b8;
        }

        .field-wrap input:focus {
            border-color: var(--blue-bright);
            background: var(--white);
            box-shadow: 0 0 0 3px var(--blue-glow);
        }

        .field-wrap input:focus ~ .field-icon,
        .field-wrap:focus-within .field-icon {
            color: var(--blue-bright);
        }

        .toggle-password {
            position: absolute;
            right: 14px;
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 4px;
            font-size: 0.9rem;
            transition: color var(--transition);
            z-index: 1;
        }

        .toggle-password:hover { color: var(--blue-bright); }

        /* ── Submit Button ── */
        .btn-login {
            width: 100%;
            height: 50px;
            margin-top: 0.5rem;
            background: var(--blue);
            color: #fff;
            border: none;
            border-radius: var(--radius);
            font-family: inherit;
            font-size: 0.95rem;
            font-weight: 600;
            letter-spacing: 0.02em;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: background var(--transition), transform var(--transition), box-shadow var(--transition);
            position: relative;
            overflow: hidden;
        }

        .btn-login:hover {
            background: var(--blue-bright);
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.35);
        }

        .btn-login:active { transform: translateY(0); }

        .btn-login .btn-text { transition: opacity var(--transition); }
        .btn-login.loading .btn-text { opacity: 0; }
        .btn-login.loading .btn-spinner { opacity: 1; }

        .btn-spinner {
            position: absolute;
            opacity: 0;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
            transition: opacity var(--transition);
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        /* ── Security Note ── */
        .security-note {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1.5rem;
            font-size: 0.78rem;
            color: var(--text-muted);
        }

        .security-note i { color: var(--blue-bright); font-size: 0.75rem; }

        .page-footer {
            margin-top: 2rem;
            text-align: center;
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        /* ── Animations ── */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .anim { animation: fadeUp 0.6s ease forwards; opacity: 0; }
        .anim-d1 { animation-delay: 0.1s; }
        .anim-d2 { animation-delay: 0.2s; }
        .anim-d3 { animation-delay: 0.3s; }

        /* ── Responsive ── */
        @media (max-width: 991px) {
            .form-panel { min-height: 100vh; }
            .auth-card { padding: 1.75rem 1.5rem; }
        }

        @media (max-width: 480px) {
            .form-header h2 { font-size: 1.5rem; }
            .auth-card { border-radius: 12px; padding: 1.5rem 1.25rem; }
        }
    </style>
</head>

<body>
    <div class="login-wrapper">

        <!-- Brand Panel (Desktop) -->
        <div class="brand-panel">
            <div class="brand-grid"></div>
            <div class="brand-content">
                <div class="brand-hero">
                    <h1 class="anim anim-d1">Manage Your <em>Beauty Empire</em></h1>
                    <p class="anim anim-d2">Secure administrative portal for BeautyDen. Manage bookings, services, and your business with confidence.</p>

                    <div class="brand-features anim anim-d3">
                        <div class="brand-feature">
                            <div class="brand-feature-icon"><i class="fa-solid fa-shield-halved"></i></div>
                            <span>Enterprise-grade security & encrypted sessions</span>
                        </div>
                        <div class="brand-feature">
                            <div class="brand-feature-icon"><i class="fa-solid fa-chart-line"></i></div>
                            <span>Real-time analytics & business insights</span>
                        </div>
                        <div class="brand-feature">
                            <div class="brand-feature-icon"><i class="fa-solid fa-calendar-check"></i></div>
                            <span>Complete booking & appointment management</span>
                        </div>
                    </div>
                </div>

                <div class="brand-footer anim anim-d3">
                    &copy; {{ date('Y') }} BeautyDen. All rights reserved.
                </div>
            </div>
        </div>

        <!-- Form Panel -->
        <div class="form-panel">
            <div class="form-container">

                <div class="auth-card anim anim-d1">

                    <div class="form-logo">
                        <img src="{{ URL::asset('panel-assets/admin-logo/sidebar-Logo.png') }}" alt="BeautyDen">
                        <span>Administrator Portal</span>
                    </div>

                    <div class="form-header">
                        <h2>Welcome back</h2>
                        <p>Sign in to your admin account to continue</p>
                    </div>

                    <form class="auth-login-form" method="POST" id="addEditForm">
                        <div class="field-group">
                            <label for="login_email">Email Address</label>
                            <div class="field-wrap">
                                <input type="email" id="login_email" name="login_email" placeholder="admin@beautyden.com" required autofocus tabindex="1">
                                <i class="fa-regular fa-envelope field-icon"></i>
                            </div>
                        </div>

                        <div class="field-group">
                            <label for="login_password">Password</label>
                            <div class="field-wrap">
                                <input type="password" id="login_password" name="login_password" placeholder="Enter your password" required tabindex="2">
                                <i class="fa-solid fa-lock field-icon"></i>
                                <button type="button" class="toggle-password" id="togglePassword" tabindex="-1" aria-label="Toggle password visibility">
                                    <i class="fa-regular fa-eye" id="toggleIcon"></i>
                                </button>
                            </div>
                        </div>

                        <button class="btn-login" type="submit" tabindex="3" id="loginBtn">
                            <span class="btn-text">Sign In <i class="fa-solid fa-arrow-right" style="font-size:0.8rem;"></i></span>
                            <span class="btn-spinner"></span>
                        </button>
                    </form>

                    <div class="security-note">
                        <i class="fa-solid fa-lock"></i>
                        <span>Your connection is secure and encrypted</span>
                    </div>
                </div>

                <div class="page-footer anim anim-d2">
                    &copy; {{ date('Y') }} BeautyDen &mdash; Premium Beauty Solutions
                </div>
            </div>
        </div>
    </div>

    <script src="{{ URL::asset('panel-assets/vendors/js/vendors.min.js') }}"></script>
    <script src="{{ URL::asset('panel-assets/vendors/js/extensions/toastr.min.js') }}"></script>
    <script src="{{ URL::asset('panel-assets/vendors/js/forms/validation/jquery.validate.min.js') }}"></script>
    <script src="{{ URL::asset('panel-assets/js/scripts/axios.min.js') }}"></script>
    <script src="{{ URL::asset('panel-assets/js/scripts/parsley.min.js') }}"></script>
    <script src="{{ URL::asset('panel-assets/js/core/app.js') }}"></script>
    <script src="{{ URL::asset('panel-assets/js/core/custom.js') }}"></script>

    <script>
        let APP_URL = {!! json_encode(url('/admin')) !!};
        let form_url = 'login-check';
        let redirect_url = 'dashboard';

        document.getElementById('togglePassword').addEventListener('click', function () {
            const input = document.getElementById('login_password');
            const icon = document.getElementById('toggleIcon');
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            icon.className = isPassword ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye';
        });

        document.getElementById('addEditForm').addEventListener('submit', function () {
            document.getElementById('loginBtn').classList.add('loading');
        });
    </script>
    <script src="{{ URL::asset('panel-assets/js/core/login-form.js') }}"></script>
</body>
</html>
