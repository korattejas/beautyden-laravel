<!doctype html>
<html class="loading" lang="en" data-textdirection="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <title>BeautyDen | Signature Admin Access</title>

    <link rel="shortcut icon" type="image/x-icon" href="{{ URL::asset('panel-assets/admin-logo/logo.png') }}">

    <!-- Typography -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Vendor CSS -->
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('panel-assets/vendors/css/extensions/toastr.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('panel-assets/css/bootstrap-extended.css') }}">
    
    <style>
        :root {
            --primary: #c5a059; /* Luxury Gold */
            --secondary: #1a1a1a; /* Onyx */
            --text-main: #2d3436;
            --text-muted: #636e72;
            --bg-soft: #fcf8f5;
            --glass: rgba(255, 255, 255, 0.85);
            --transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: #000;
            margin: 0;
            display: flex;
            height: 100vh;
        }

        /* Side Visual */
        .visual-panel {
            flex: 1.2;
            position: relative;
            background: url('https://images.pexels.com/photos/3762882/pexels-photo-3762882.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2') center/cover;
            display: none;
        }

        @media (min-width: 992px) {
            .visual-panel { display: block; }
        }

        .visual-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to right, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0.7) 100%);
        }

        .visual-content {
            position: relative;
            z-index: 2;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 10% 8%;
            color: #fff;
        }

        .visual-content h1 {
            font-family: 'Playfair Display', serif;
            font-size: 4rem;
            margin-bottom: 1.5rem;
            line-height: 1.1;
        }

        .visual-content h1 span {
            color: var(--primary);
            display: block;
            font-style: italic;
        }

        .visual-content p {
            font-size: 1.25rem;
            max-width: 450px;
            opacity: 0.8;
            line-height: 1.6;
        }

        /* Form Panel */
        .form-panel {
            flex: 1;
            background: var(--bg-soft);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 2rem;
            position: relative;
        }

        .form-container {
            width: 100%;
            max-width: 440px;
            display: flex;
            flex-direction: column;
            min-height: 80vh;
            justify-content: space-between;
            position: relative;
            z-index: 2;
        }

        .brand-box {
            margin-top: 1rem;
            /* margin-bottom: 2rem; */
            text-align: center;
        }

        .main-brand-logo {
            width: 220px;
            height: auto;
            margin-bottom: 0.5rem;
            filter: drop-shadow(0 8px 25px rgba(0,0,0,0.08));
            transition: var(--transition);
        }

        .main-brand-logo:hover {
            transform: scale(1.02);
        }

        .brand-subtitle {
            color: var(--text-muted);
            font-size: 0.85rem;
            font-weight: 500;
            letter-spacing: 2px;
            text-transform: uppercase;
            opacity: 0.7;
        }

        /* Premium Form */
        .auth-card {
            background: #fff;
            padding: 2.5rem 3rem;
            border-radius: 40px;
            box-shadow: 0 40px 100px rgba(0,0,0,0.03), 0 10px 40px rgba(0,0,0,0.02);
            border: 1px solid rgba(0, 0, 0, 0.02);
            margin: 1.5rem 0;
        }

        .input-group-custom {
            margin-bottom: 2rem;
            position: relative;
        }

        .input-group-custom label {
            display: block;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--primary);
            margin-bottom: 0.75rem;
            transition: var(--transition);
        }

        .input-group-custom .control-wrap {
            position: relative;
        }

        .input-group-custom input {
            width: 100%;
            padding: 1rem 0;
            background: transparent;
            border: none;
            border-bottom: 2px solid #e0e0e0;
            font-size: 1.1rem;
            font-weight: 500;
            color: var(--secondary);
            transition: var(--transition);
            border-radius: 0;
        }

        .input-group-custom input:focus {
            outline: none;
            border-bottom-color: var(--primary);
        }

        .input-group-custom input::placeholder {
            color: #ccc;
            opacity: 0.6;
        }

        .input-focus-line {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary);
            transition: var(--transition);
        }

        .input-group-custom input:focus ~ .input-focus-line {
            width: 100%;
        }

        /* Access Button */
        .btn-signature {
            background: var(--secondary);
            color: #fff;
            width: 100%;
            height: 60px;
            border: none;
            border-radius: 15px;
            font-size: 1rem;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            position: relative;
            overflow: hidden;
        }

        .btn-signature:hover {
            background: var(--primary);
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(197, 160, 89, 0.4);
        }

        .btn-signature::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 300%;
            height: 300%;
            background: rgba(255,255,255,0.1);
            transition: var(--transition);
            transform: translate(-50%, -50%) rotate(45deg) translateY(100%);
        }

        .btn-signature:hover::after {
            transform: translate(-50%, -50%) rotate(45deg) translateY(-100%);
        }

        .footer-minimal {
            margin-bottom: 1rem;
            text-align: center;
            color: var(--text-muted);
            font-size: 0.85rem;
            opacity: 0.6;
        }

        /* Responsiveness */
        @media (max-width: 1200px) {
            .visual-content h1 { font-size: 3rem; }
        }

        @media (max-width: 991px) {
            body { 
                display: block; 
                overflow-y: auto; 
                height: auto;
                min-height: 100vh;
            }
            .form-panel {
                min-height: 100vh;
                padding: 4rem 1.5rem;
            }
            .form-container {
                min-height: unset;
                height: auto;
            }
            .auth-card {
                padding: 2.5rem 2rem;
            }
        }

        @media (max-width: 480px) {
            .visual-panel { display: none; }
            .brand-logo { width: 160px; }
            .auth-card {
                padding: 2rem 1.5rem;
                margin: 2rem 0;
            }
            .brand-title { font-size: 1.75rem; }
            .btn-signature { height: 55px; }
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fade {
            animation: fadeIn 0.8s ease forwards;
        }

        .delay-1 { animation-delay: 0.2s; }
        .delay-2 { animation-delay: 0.4s; }
        .delay-3 { animation-delay: 0.6s; }

        @import url('https://fonts.googleapis.com/css2?family=Dancing+Script:wght@600&display=swap');
        
        .beauty-script {
            font-family: 'Dancing Script', cursive;
            background: linear-gradient(45deg, #c5a059, #e2c08d);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .den-text {
            font-family: 'Playfair Display', serif;
            color: var(--secondary);
            margin-left: 5px;
        }

    </style>
</head>

<body>
    <!-- Visual Section -->
    <div class="visual-panel">
        <div class="visual-overlay"></div>
        <div class="visual-content">
            <h1 class="animate-fade">Crafting <span>Timeless Beauty</span></h1>
            <p class="animate-fade delay-1">Signature administrative access to BeautyDen. Manage elegance with precision and power.</p>
        </div>
    </div>

    <!-- Form Section -->
    <div class="form-panel">
        <div class="form-container">
            <div class="brand-box animate-fade">
                <img src="{{ URL::asset('panel-assets/admin-logo/sidebar-Logo.png') }}" class="main-brand-logo" alt="BeautyDen Logo">
                <p class="brand-subtitle">Administrator Portal</p>
            </div>

            <div class="auth-card animate-fade delay-1">
                <form class="auth-login-form" method="POST" id="addEditForm">
                    <div class="input-group-custom">
                        <label for="login_email">Email Address</label>
                        <div class="control-wrap">
                            <input type="email" id="login_email" name="login_email" placeholder="email@example.com" required autofocus tabindex="1">
                            <div class="input-focus-line"></div>
                        </div>
                    </div>

                    <div class="input-group-custom">
                        <label for="login_password">Secure Password</label>
                        <div class="control-wrap">
                            <input type="password" id="login_password" name="login_password" placeholder="••••••••" required tabindex="2">
                            <div class="input-focus-line"></div>
                        </div>
                    </div>

                    <button class="btn-signature" type="submit" tabindex="3">
                        Enter Workspace
                        <i class="fa-solid fa-arrow-right-long"></i>
                    </button>
                </form>
            </div>

            <div class="footer-minimal animate-fade delay-2">
                &copy; {{ date('Y') }} BeautyDen. Premium Beauty Solutions.
            </div>
        </div>
    </div>

    <!-- Scripts -->
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
    </script>
    <script src="{{ URL::asset('panel-assets/js/core/login-form.js') }}"></script>
</body>
</html>
