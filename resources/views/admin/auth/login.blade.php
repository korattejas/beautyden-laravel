<!doctype html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="description" content="BeautyDen - Premium Beauty Services at Your Doorstep">
    <meta name="keywords" content="beauty parlor, home service, beauty treatments, skincare, makeup, BeautyDen">
    <meta name="author" content="BeautyDen">
    <title>Admin Portal - BeautyDen | Premium Beauty Home Services</title>

    <link rel="apple-touch-icon" href="{{ URL::asset('panel-assets/admin-logo/logo.png') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ URL::asset('panel-assets/admin-logo/logo.png') }}">

    <!-- Premium Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css"
        href="{{ URL::asset('panel-assets/vendors/css/extensions/toastr.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('panel-assets/vendors/css/vendors.min.css') }}">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    {{-- <link rel="stylesheet" type="text/css" href="{{ URL::asset('panel-assets/css/bootstrap.css') }}"> --}}
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('panel-assets/css/bootstrap-extended.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('panel-assets/css/colors.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('panel-assets/css/components.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('panel-assets/css/themes/dark-layout.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('panel-assets/css/themes/bordered-layout.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('panel-assets/css/themes/semi-dark-layout.css') }}">

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css"
        href="{{ URL::asset('panel-assets/css/core/menu/menu-types/horizontal-menu.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ URL::asset('panel-assets/css/plugins/forms/form-validation.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('panel-assets/css/pages/authentication.css') }}">
    <!-- END: Page CSS-->

    <style>
        /* BeautyDen Premium Theme Styles */
        :root {
            /* Premium Color Palette */
            --primary-rose: #E91E63;
            --primary-rose-light: #F8BBD9;
            --secondary-gold: #D4AF37;
            --accent-champagne: #F7E7CE;
            --deep-plum: #4A148C;
            --soft-lavender: #E1BEE7;
            --pearl-white: #FEFEFE;
            --charcoal: #1A1A1A;
            --warm-gray: #F5F5F5;
            --success: #00C851;
            --warning: #FF8F00;
            --error: #FF1744;

            /* Premium Gradients */
            --primary-gradient: linear-gradient(135deg, #E91E63 0%, #AD1457 50%, #880E4F 100%);
            --gold-gradient: linear-gradient(135deg, #D4AF37 0%, #FFD700 50%, #FFA000 100%);
            --luxury-gradient: linear-gradient(135deg, #4A148C 0%, #7B1FA2 50%, #E91E63 100%);
            --pearl-gradient: linear-gradient(135deg, #FEFEFE 0%, #F8F9FA 100%);
            --glass-gradient: linear-gradient(135deg, rgba(255, 255, 255, 0.25) 0%, rgba(255, 255, 255, 0.1) 100%);

            /* Advanced Shadows */
            --shadow-soft: 0 4px 20px rgba(233, 30, 99, 0.08);
            --shadow-medium: 0 8px 40px rgba(233, 30, 99, 0.12);
            --shadow-strong: 0 20px 60px rgba(233, 30, 99, 0.15);
            --shadow-glow: 0 0 40px rgba(212, 175, 55, 0.3);

            /* Spacing System (8px grid) */
            --spacing-xs: 4px;
            --spacing-sm: 8px;
            --spacing-md: 16px;
            --spacing-lg: 24px;
            --spacing-xl: 32px;
            --spacing-2xl: 48px;
            --spacing-3xl: 64px;
            --spacing-4xl: 80px;

            /* Typography Scale */
            --font-xs: 0.75rem;
            --font-sm: 0.875rem;
            --font-base: 1rem;
            --font-lg: 1.125rem;
            --font-xl: 1.25rem;
            --font-2xl: 1.5rem;
            --font-3xl: 1.875rem;
            --font-4xl: 2.25rem;
            --font-5xl: 3rem;
            --font-6xl: 3.75rem;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--pearl-white);
            overflow-x: hidden;
            height: 100vh;
            position: relative;
        }

        body.loaded .beautyden-login-container {
            opacity: 1;
            transform: translateY(0);
        }

        /* Animated Background */
        .animated-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, #ffeef7, #fff8f0, #f3e5f5);
            z-index: -1;
        }

        .floating-particles {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            width: 6px;
            height: 6px;
            background: var(--secondary-gold);
            border-radius: 50%;
            opacity: 0.6;
            animation: float-particle 8s infinite ease-in-out;
        }

        .particle-1 {
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .particle-2 {
            top: 60%;
            left: 20%;
            animation-delay: 2s;
        }

        .particle-3 {
            top: 80%;
            left: 80%;
            animation-delay: 4s;
        }

        .particle-4 {
            top: 30%;
            left: 70%;
            animation-delay: 1s;
        }

        .particle-5 {
            top: 70%;
            left: 60%;
            animation-delay: 3s;
        }

        .particle-6 {
            top: 10%;
            left: 90%;
            animation-delay: 5s;
        }

        @keyframes float-particle {

            0%,
            100% {
                transform: translateY(0px) translateX(0px) scale(1);
                opacity: 0.6;
            }

            25% {
                transform: translateY(-20px) translateX(10px) scale(1.2);
                opacity: 0.8;
            }

            50% {
                transform: translateY(-40px) translateX(-10px) scale(0.8);
                opacity: 0.4;
            }

            75% {
                transform: translateY(-20px) translateX(15px) scale(1.1);
                opacity: 0.7;
            }
        }

        /* Main Container */
        .beautyden-login-container {
            display: flex;
            height: 100vh;
            position: relative;
            opacity: 0;
            transform: translateY(20px);
            transition: all 1s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Left Side - Beauty Showcase */
        .beauty-showcase-section {
            flex: 1.4;
            background:
                linear-gradient(135deg,
                    rgba(233, 30, 99, 0.85) 0%,
                    rgba(173, 20, 87, 0.75) 25%,
                    rgba(74, 20, 140, 0.65) 75%,
                    rgba(212, 175, 55, 0.55) 100%),
                url('https://images.pexels.com/photos/3993449/pexels-photo-3993449.jpeg?auto=compress&cs=tinysrgb&w=1920&h=1080&fit=crop') center/cover;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .beauty-showcase-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.15"/><circle cx="20" cy="80" r="0.5" fill="white" opacity="0.15"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            pointer-events: none;
        }

        .showcase-overlay {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 2;
        }

        .showcase-content {
            text-align: center;
            color: white;
            padding: var(--spacing-3xl);
            max-width: 600px;
            animation: slideInLeft 1s cubic-bezier(0.4, 0, 0.2, 1) 0.3s both;
        }

        /* Brand Header */
        .brand-header {
            margin-bottom: var(--spacing-3xl);
        }

        .brand-logo-container {
            margin-bottom: var(--spacing-xl);
            position: relative;
        }

        .logo-wrapper {
            position: relative;
            display: inline-block;
        }

        .premium-logo {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid rgba(255, 255, 255, 0.3);
            box-shadow:
                0 0 0 8px rgba(255, 255, 255, 0.1),
                0 20px 40px rgba(0, 0, 0, 0.2);
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            z-index: 2;
        }

        .logo-glow {
            position: absolute;
            top: -10px;
            left: -10px;
            right: -10px;
            bottom: -10px;
            background: var(--gold-gradient);
            border-radius: 50%;
            opacity: 0;
            filter: blur(20px);
            transition: opacity 0.6s ease;
            z-index: 1;
        }

        .premium-logo:hover+.logo-glow {
            opacity: 0.6;
        }

        .premium-logo:hover {
            transform: scale(1.1) rotate(5deg);
            box-shadow:
                0 0 0 8px rgba(255, 255, 255, 0.2),
                0 30px 60px rgba(0, 0, 0, 0.3);
        }

        .brand-identity {
            margin-bottom: var(--spacing-2xl);
        }

        .brand-name {
            font-size: var(--font-5xl);
            font-weight: 700;
            margin-bottom: var(--spacing-sm);
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.3);
            position: relative;
        }

        .beauty-script {
            font-family: 'Dancing Script', cursive;
            font-weight: 600;
            background: linear-gradient(45deg, #FFD700, #FFA000);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .den-text {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            color: white;
            margin-left: var(--spacing-xs);
        }

        .brand-tagline {
            font-size: var(--font-lg);
            font-weight: 500;
            color: rgba(255, 255, 255, 0.9);
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.3);
        }

        /* Hero Content */
        .hero-content {
            margin-bottom: var(--spacing-3xl);
        }

        .hero-title {
            font-family: 'Playfair Display', serif;
            font-size: var(--font-4xl);
            font-weight: 600;
            line-height: 1.2;
            margin-bottom: var(--spacing-lg);
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.3);
        }

        .hero-line-1,
        .hero-line-2 {
            display: block;
        }

        .hero-line-1 {
            font-style: italic;
            opacity: 0.95;
        }

        .hero-line-2 {
            background: linear-gradient(45deg, #FFD700, #FFA000);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-description {
            font-size: var(--font-lg);
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.9);
            text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.2);
            max-width: 500px;
            margin: 0 auto;
        }

        /* Premium Services Showcase */
        .services-showcase {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: var(--spacing-lg);
            margin-bottom: var(--spacing-3xl);
        }

        .service-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: var(--spacing-lg);
            display: flex;
            align-items: center;
            gap: var(--spacing-md);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            opacity: 0;
            transform: translateY(30px);
        }

        .service-card.animate-in {
            opacity: 1;
            transform: translateY(0);
        }

        .service-card:hover {
            transform: translateY(-8px) scale(1.02);
            background: rgba(255, 255, 255, 0.25);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        .service-icon {
            width: 50px;
            height: 50px;
            background: var(--gold-gradient);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: var(--font-xl);
            color: white;
            box-shadow: 0 8px 20px rgba(212, 175, 55, 0.3);
            transition: all 0.3s ease;
        }

        .service-card:hover .service-icon {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 12px 30px rgba(212, 175, 55, 0.4);
        }

        .service-info h4 {
            font-size: var(--font-base);
            font-weight: 600;
            color: white;
            margin-bottom: var(--spacing-xs);
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
        }

        .service-info p {
            font-size: var(--font-sm);
            color: rgba(255, 255, 255, 0.8);
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
        }

        /* Trust Indicators */
        .trust-indicators {
            display: flex;
            justify-content: space-around;
            gap: var(--spacing-lg);
        }

        .trust-item {
            text-align: center;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            padding: var(--spacing-lg);
            transition: all 0.3s ease;
            flex: 1;
        }

        .trust-item:hover {
            transform: translateY(-4px);
            background: rgba(255, 255, 255, 0.2);
        }

        .trust-number {
            font-size: var(--font-3xl);
            font-weight: 700;
            color: var(--secondary-gold);
            text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.3);
            margin-bottom: var(--spacing-xs);
        }

        .trust-label {
            font-size: var(--font-sm);
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.2);
        }

        /* Decorative Floating Elements */
        .decorative-elements {
            position: absolute;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        .floating-icon {
            position: absolute;
            color: rgba(255, 255, 255, 0.2);
            font-size: var(--font-3xl);
            animation: float-advanced 8s ease-in-out infinite;
        }

        .floating-icon-1 {
            top: 15%;
            left: 8%;
            animation-delay: 0s;
        }

        .floating-icon-2 {
            top: 25%;
            right: 12%;
            animation-delay: 1.5s;
        }

        .floating-icon-3 {
            bottom: 35%;
            left: 15%;
            animation-delay: 3s;
        }

        .floating-icon-4 {
            top: 70%;
            right: 20%;
            animation-delay: 4.5s;
        }

        .floating-icon-5 {
            bottom: 15%;
            right: 8%;
            animation-delay: 6s;
        }

        @keyframes float-advanced {

            0%,
            100% {
                transform: translateY(0px) translateX(0px) rotate(0deg) scale(1);
                opacity: 0.2;
            }

            25% {
                transform: translateY(-15px) translateX(8px) rotate(90deg) scale(1.1);
                opacity: 0.4;
            }

            50% {
                transform: translateY(-30px) translateX(-8px) rotate(180deg) scale(0.9);
                opacity: 0.6;
            }

            75% {
                transform: translateY(-15px) translateX(12px) rotate(270deg) scale(1.05);
                opacity: 0.3;
            }
        }

        /* Right Side - Login Section */
        .login-section {
            flex: 1;
            background: var(--pearl-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: var(--spacing-2xl);
            position: relative;
        }

        .login-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('https://images.pexels.com/photos/7163619/pexels-photo-7163619.jpeg?auto=compress&cs=tinysrgb&w=200&h=200&fit=crop') repeat;
            opacity: 0.02;
            pointer-events: none;
        }

        .login-container {
            width: 100%;
            max-width: 480px;
            position: relative;
            z-index: 2;
            animation: slideInRight 1s cubic-bezier(0.4, 0, 0.2, 1) 0.5s both;
        }

        /* Admin Badge */
        .admin-badge {
            display: inline-flex;
            align-items: center;
            gap: var(--spacing-sm);
            background: var(--luxury-gradient);
            color: white;
            padding: var(--spacing-sm) var(--spacing-lg);
            border-radius: 25px;
            font-size: var(--font-sm);
            font-weight: 600;
            margin-bottom: var(--spacing-xl);
            box-shadow: var(--shadow-medium);
            position: relative;
            overflow: hidden;
        }

        .admin-badge::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.6s ease;
        }

        .admin-badge:hover::before {
            left: 100%;
        }

        /* Login Card */
        .login-card {
            background: white;
            padding: var(--spacing-3xl);
            border-radius: 32px;
            box-shadow:
                0 0 0 1px rgba(233, 30, 99, 0.05),
                var(--shadow-strong);
            border: 1px solid rgba(233, 30, 99, 0.08);
            position: relative;
            overflow: hidden;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary-gradient);
        }

        .card-header {
            text-align: center;
            margin-bottom: var(--spacing-2xl);
        }

        .welcome-icon {
            width: 80px;
            height: 80px;
            background: var(--luxury-gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto var(--spacing-lg);
            font-size: var(--font-2xl);
            color: white;
            box-shadow: var(--shadow-glow);
            position: relative;
            animation: pulse-glow 3s ease-in-out infinite;
        }

        @keyframes pulse-glow {

            0%,
            100% {
                box-shadow: var(--shadow-glow);
            }

            50% {
                box-shadow: 0 0 60px rgba(212, 175, 55, 0.5);
            }
        }

        .welcome-title {
            font-family: 'Playfair Display', serif;
            font-size: var(--font-3xl);
            font-weight: 600;
            color: var(--charcoal);
            margin-bottom: var(--spacing-sm);
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .welcome-subtitle {
            font-size: var(--font-base);
            color: #666;
            font-weight: 500;
        }

        /* Premium Form Styles */
        .form-group {
            margin-bottom: var(--spacing-xl);
            position: relative;
        }

        .form-label {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
            font-weight: 600;
            color: var(--charcoal);
            margin-bottom: var(--spacing-md);
            font-size: var(--font-sm);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-label i {
            color: var(--primary-rose);
            font-size: var(--font-base);
        }

        .input-wrapper {
            position: relative;
        }

        .premium-input {
            width: 100%;
            padding: 18px 20px;
            border: 2px solid #E8E8E8;
            border-radius: 16px;
            font-size: var(--font-base);
            font-weight: 500;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: #FAFAFA;
            color: var(--charcoal);
            position: relative;
            z-index: 2;
        }

        .premium-input:focus {
            outline: none;
            border-color: var(--primary-rose);
            background: white;
            transform: translateY(-2px);
            box-shadow:
                0 0 0 4px rgba(233, 30, 99, 0.1),
                0 8px 25px rgba(233, 30, 99, 0.15);
        }

        .input-wrapper.focused .input-border {
            transform: scaleX(1);
        }

        .input-border {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--primary-gradient);
            border-radius: 0 0 16px 16px;
            transform: scaleX(0);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1;
        }

        .input-group-text {
            background: transparent;
            border: none;
            color: #999;
            cursor: pointer;
            padding: var(--spacing-md);
            transition: all 0.3s ease;
            border-radius: 0 16px 16px 0;
        }

        .input-group-text:hover {
            color: var(--primary-rose);
            background: rgba(233, 30, 99, 0.05);
        }

        /* Form Options */
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: var(--spacing-2xl);
        }

        .premium-checkbox {
            display: flex;
            align-items: center;
            gap: var(--spacing-md);
            cursor: pointer;
            position: relative;
        }

        .premium-checkbox input[type="checkbox"] {
            display: none;
        }

        .checkmark {
            width: 20px;
            height: 20px;
            border: 2px solid #E8E8E8;
            border-radius: 6px;
            position: relative;
            transition: all 0.3s ease;
            background: white;
        }

        .premium-checkbox input:checked+label .checkmark {
            background: var(--primary-gradient);
            border-color: var(--primary-rose);
            transform: scale(1.1);
        }

        .premium-checkbox input:checked+label .checkmark::after {
            content: '\f00c';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 12px;
        }

        .form-check-label {
            font-size: var(--font-sm);
            color: var(--charcoal);
            font-weight: 500;
            cursor: pointer;
            user-select: none;
        }

        .forgot-password {
            font-size: var(--font-sm);
            color: var(--primary-rose);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: var(--spacing-xs);
        }

        .forgot-password:hover {
            color: var(--deep-plum);
            transform: translateX(4px);
        }

        /* Premium Button */
        .btn-premium {
            background: var(--primary-gradient);
            border: none;
            padding: 18px 32px;
            border-radius: 16px;
            color: white;
            font-weight: 700;
            font-size: var(--font-base);
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--spacing-md);
            position: relative;
            overflow: hidden;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow:
                0 8px 25px rgba(233, 30, 99, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }

        .btn-premium::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.6s ease;
        }

        .btn-premium:hover::before {
            left: 100%;
        }

        .btn-premium:hover {
            transform: translateY(-3px);
            box-shadow:
                0 15px 35px rgba(233, 30, 99, 0.4),
                inset 0 1px 0 rgba(255, 255, 255, 0.3);
        }

        .btn-premium:active {
            transform: translateY(-1px);
        }

        .btn-shimmer {
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            0% {
                left: -100%;
            }

            100% {
                left: 100%;
            }
        }

        .btn-premium.loading {
            pointer-events: none;
            opacity: 0.8;
        }

        .btn-premium.loading .btn-text {
            opacity: 0.7;
        }

        .btn-premium.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Elegant Divider */
        .divider {
            text-align: center;
            margin: var(--spacing-2xl) 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, #E8E8E8, transparent);
        }

        .divider span {
            background: white;
            padding: 0 var(--spacing-lg);
            color: #999;
            font-size: var(--font-sm);
            font-weight: 500;
            position: relative;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Premium Social Login */
        .social-login {
            display: flex;
            gap: var(--spacing-md);
            justify-content: center;
            margin-bottom: var(--spacing-xl);
        }

        .btn-social {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            border: 2px solid #F0F0F0;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: var(--font-xl);
            position: relative;
            overflow: hidden;
        }

        .btn-social::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: currentColor;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .btn-social:hover::before {
            opacity: 0.1;
        }

        .btn-google {
            color: #db4437;
        }

        .btn-facebook {
            color: #3b5998;
        }

        .btn-apple {
            color: #000;
        }

        .btn-social:hover {
            border-color: currentColor;
            transform: translateY(-4px) scale(1.05);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
        }

        /* Security Badge */
        .security-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--spacing-sm);
            background: rgba(40, 167, 69, 0.1);
            color: var(--success);
            padding: var(--spacing-md);
            border-radius: 12px;
            font-size: var(--font-xs);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: var(--spacing-lg);
        }

        .security-badge i {
            font-size: var(--font-sm);
        }

        /* Login Footer */
        .login-footer {
            text-align: center;
            margin-top: var(--spacing-xl);
            padding-top: var(--spacing-lg);
            border-top: 1px solid #F0F0F0;
        }

        .login-footer p {
            font-size: var(--font-xs);
            color: #999;
            margin-bottom: var(--spacing-md);
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: var(--spacing-lg);
        }

        .footer-links a {
            font-size: var(--font-xs);
            color: var(--primary-rose);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: var(--deep-plum);
        }

        /* Advanced Animations */
        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-100px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(100px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .beauty-showcase-section {
                flex: 1.2;
            }

            .showcase-content {
                padding: var(--spacing-2xl);
            }

            .services-showcase {
                grid-template-columns: 1fr;
                gap: var(--spacing-md);
            }

            .trust-indicators {
                flex-direction: column;
                gap: var(--spacing-md);
            }
        }

        @media (max-width: 968px) {
            .beautyden-login-container {
                flex-direction: column;
            }

            .beauty-showcase-section {
                flex: none;
                height: 50vh;
                min-height: 400px;
            }

            .login-section {
                flex: 1;
                padding: var(--spacing-lg);
            }

            .showcase-content {
                padding: var(--spacing-xl);
            }

            .brand-name {
                font-size: var(--font-4xl);
            }

            .hero-title {
                font-size: var(--font-3xl);
            }

            .services-showcase {
                display: none;
            }

            .trust-indicators {
                flex-direction: row;
                gap: var(--spacing-sm);
            }

            .trust-item {
                padding: var(--spacing-md);
            }

            .trust-number {
                font-size: var(--font-xl);
            }

            .login-card {
                padding: var(--spacing-xl);
            }
        }

        @media (max-width: 640px) {
            .beauty-showcase-section {
                height: 40vh;
                min-height: 320px;
            }

            .login-section {
                padding: var(--spacing-md);
            }

            .showcase-content {
                padding: var(--spacing-lg);
            }

            .brand-name {
                font-size: var(--font-3xl);
            }

            .hero-title {
                font-size: var(--font-2xl);
            }

            .hero-description {
                font-size: var(--font-base);
            }

            .trust-indicators {
                gap: var(--spacing-xs);
            }

            .trust-item {
                padding: var(--spacing-sm);
            }

            .trust-number {
                font-size: var(--font-lg);
            }

            .trust-label {
                font-size: var(--font-xs);
            }

            .login-card {
                padding: var(--spacing-lg);
                border-radius: 24px;
            }

            .welcome-icon {
                width: 60px;
                height: 60px;
                font-size: var(--font-xl);
            }

            .welcome-title {
                font-size: var(--font-2xl);
            }

            .social-login {
                gap: var(--spacing-sm);
            }

            .btn-social {
                width: 48px;
                height: 48px;
                font-size: var(--font-lg);
            }
        }

        /* Enhanced Focus States for Accessibility */
        .premium-input:focus,
        .btn-premium:focus,
        .btn-social:focus,
        .premium-checkbox:focus-within {
            outline: 3px solid rgba(233, 30, 99, 0.3);
            outline-offset: 2px;
        }

        /* Loading State */
        .loading .beautyden-login-container {
            opacity: 0;
        }

        /* High-end Visual Effects */
        .login-card {
            backdrop-filter: blur(20px);
            background: rgba(255, 255, 255, 0.95);
        }

        .showcase-content>* {
            animation: fadeInUp 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .brand-header {
            animation-delay: 0.2s;
        }

        .hero-content {
            animation-delay: 0.4s;
        }

        .services-showcase {
            animation-delay: 0.6s;
        }

        .trust-indicators {
            animation-delay: 0.8s;
        }

        /* Premium Hover Effects */
        .service-card,
        .trust-item,
        .btn-premium,
        .btn-social,
        .premium-checkbox {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Glass Morphism Effects */
        .service-card,
        .trust-item,
        .admin-badge {
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }

        /* Advanced Typography */
        .brand-name,
        .welcome-title,
        .hero-title {
            text-rendering: optimizeLegibility;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Luxury Details */
        .premium-logo {
            filter: drop-shadow(0 8px 16px rgba(0, 0, 0, 0.2));
        }

        .login-card {
            background-image:
                radial-gradient(circle at 20% 80%, rgba(233, 30, 99, 0.03) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(212, 175, 55, 0.03) 0%, transparent 50%);
        }

        /* Enhanced Visual Hierarchy */
        .form-label span {
            background: linear-gradient(135deg, var(--charcoal), #555);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Micro-interactions */
        .welcome-icon:hover {
            animation: none;
            transform: scale(1.1) rotate(10deg);
            box-shadow: 0 0 80px rgba(212, 175, 55, 0.6);
        }

        .admin-badge:hover {
            transform: scale(1.05);
        }

        /* Professional Polish */
        .login-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="luxury-pattern" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="0.5" fill="rgba(233,30,99,0.02)"/></pattern></defs><rect width="100" height="100" fill="url(%23luxury-pattern)"/></svg>');
            pointer-events: none;
            border-radius: 32px;
        }
    </style>

    <script type="text/javascript">
        let APP_URL = {!! json_encode(url('/admin')) !!};
        let JS_URL = '{{ url('/') }}';
        let is_admin_open = 1;
    </script>

</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="horizontal-layout horizontal-menu blank-page navbar-floating footer-static beautyden-theme"
    data-open="hover" data-menu="horizontal-menu" data-col="blank-page">

    <!-- Animated Background -->
    <div class="animated-background">
        <div class="floating-particles">
            <div class="particle particle-1"></div>
            <div class="particle particle-2"></div>
            <div class="particle particle-3"></div>
            <div class="particle particle-4"></div>
            <div class="particle particle-5"></div>
            <div class="particle particle-6"></div>
        </div>
    </div>

    <!-- BEGIN: Content-->
    <div class="beautyden-login-container">
        <!-- Left Side - Premium Beauty Showcase -->
        <div class="beauty-showcase-section">
            <div class="showcase-overlay">
                <div class="showcase-content">
                    <!-- Brand Header -->
                    <div class="brand-header">
                        <div class="brand-logo-container">
                            <div class="logo-wrapper">
                                {{-- <img src="{{ URL::asset('panel-assets/admin-logo/logo.png') }}" alt="BeautyDen Logo"
                                    class="premium-logo" /> --}}
                                <div class="logo-glow"></div>
                            </div>
                        </div>
                        <div class="brand-identity">
                            <h1 class="brand-name">
                                <span class="beauty-script">Beauty</span><span class="den-text">Den</span>
                            </h1>
                            <p class="brand-tagline">Premium Home Beauty Services</p>
                        </div>
                    </div>

                    <!-- Hero Content -->
                    <div class="hero-content">
                        <h2 class="hero-title">
                            <span class="hero-line-1">Transform Your Beauty</span>
                            <span class="hero-line-2">In the Comfort of Home</span>
                        </h2>
                        <p class="hero-description">
                            Experience luxury beauty treatments with our certified professionals who bring the salon
                            experience directly to your doorstep.
                        </p>
                    </div>

                    <!-- Premium Services Grid -->
                    <div class="services-showcase">
                        <div class="service-card">
                            <div class="service-icon">
                                <i class="fas fa-magic"></i>
                            </div>
                            <div class="service-info">
                                <h4>Professional Makeup</h4>
                                <p>Bridal, party & everyday looks</p>
                            </div>
                        </div>
                        <div class="service-card">
                            <div class="service-icon">
                                <i class="fas fa-spa"></i>
                            </div>
                            <div class="service-info">
                                <h4>Skincare Treatments</h4>
                                <p>Facials, cleansing & rejuvenation</p>
                            </div>
                        </div>
                        <div class="service-card">
                            <div class="service-icon">
                                <i class="fas fa-cut"></i>
                            </div>
                            <div class="service-info">
                                <h4>Hair Styling</h4>
                                <p>Cuts, colors & styling</p>
                            </div>
                        </div>
                        <div class="service-card">
                            <div class="service-icon">
                                <i class="fas fa-hand-sparkles"></i>
                            </div>
                            <div class="service-info">
                                <h4>Nail Artistry</h4>
                                <p>Manicures, pedicures & nail art</p>
                            </div>
                        </div>
                    </div>

                    <!-- Trust Indicators -->
                    <div class="trust-indicators">
                        <div class="trust-item">
                            <div class="trust-number">500+</div>
                            <div class="trust-label">Happy Clients</div>
                        </div>
                        <div class="trust-item">
                            <div class="trust-number">50+</div>
                            <div class="trust-label">Expert Artists</div>
                        </div>
                        <div class="trust-item">
                            <div class="trust-number">5★</div>
                            <div class="trust-label">Average Rating</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Decorative Elements -->
            <div class="decorative-elements">
                <div class="floating-icon floating-icon-1"><i class="fas fa-heart"></i></div>
                <div class="floating-icon floating-icon-2"><i class="fas fa-star"></i></div>
                <div class="floating-icon floating-icon-3"><i class="fas fa-gem"></i></div>
                <div class="floating-icon floating-icon-4"><i class="fas fa-crown"></i></div>
                <div class="floating-icon floating-icon-5"><i class="fas fa-sparkles"></i></div>
            </div>
        </div>

        <!-- Right Side - Premium Login Form -->
        <div class="login-section">
            <div class="login-container">
                <!-- Login Form Card -->
                <div class="login-card">
                    <div class="card-header">
                        <div class="welcome-icon">
                            <i class="fas fa-user-crown"></i>
                        </div>
                        <h3 class="welcome-title">Welcome Back</h3>
                        <p class="welcome-subtitle">Access your BeautyDen dashboard</p>
                    </div>

                    <form class="auth-login-form" method="POST" id="addEditForm">
                        <div class="form-group">
                            <label for="login_email" class="form-label">
                                <i class="fas fa-envelope"></i>
                                <span>Email Address</span>
                            </label>
                            <div class="input-wrapper">
                                <input type="text" class="form-control premium-input" id="login_email"
                                    name="login_email" placeholder="Enter your email address"
                                    aria-describedby="login_email" tabindex="1" autofocus />
                                <div class="input-border"></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="login_password" class="form-label">
                                <i class="fas fa-lock"></i>
                                <span>Password</span>
                            </label>
                            <div class="input-wrapper">
                                <input type="password" class="form-control premium-input" id="login_password"
                                    name="login_password" placeholder="Enter your password"
                                    aria-describedby="login_password" tabindex="1" />
                                <div class="input-border"></div>
                            </div>
                        </div>

                        <button class="btn btn-premium w-100" type="submit" tabindex="4">
                            <span class="btn-text">Access Dashboard</span>
                            <span class="btn-icon"><i class="fas fa-arrow-right"></i></span>
                            <div class="btn-shimmer"></div>
                        </button>
                    </form>

                    <!-- Security Badge -->
                    <div class="security-badge">
                        <i class="fas fa-shield-check"></i>
                        <span>Secured with 256-bit SSL encryption</span>
                    </div>
                </div>

                <!-- Footer -->
                <div class="login-footer">
                    <p>&copy; 2025 BeautyDen. All rights reserved.</p>
                    <div class="footer-links">
                        <a href="#">Privacy Policy</a>
                        <a href="#">Terms of Service</a>
                        <a href="#">Support</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END: Content-->

    <!-- BEGIN: Vendor JS-->
    <script src="{{ URL::asset('panel-assets/vendors/js/vendors.min.js') }}"></script>
    <script src="{{ URL::asset('panel-assets/vendors/js/extensions/toastr.min.js') }}"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <script src="{{ URL::asset('panel-assets/vendors/js/forms/validation/jquery.validate.min.js') }}"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="{{ URL::asset('panel-assets/js/core/app-menu.js') }}"></script>
    <script src="{{ URL::asset('panel-assets/js/scripts/axios.min.js') }}"></script>
    <script src="{{ URL::asset('panel-assets/js/scripts/blockUI.js') }}"></script>
    <script src="{{ URL::asset('panel-assets/js/scripts/parsley.min.js') }}"></script>
    <script src="{{ URL::asset('panel-assets/js/core/app.js') }}"></script>
    <script src="{{ URL::asset('panel-assets/js/core/custom.js') }}"></script>

    <script>
        let form_url = 'login-check';
        let redirect_url = 'dashboard';

        // Enhanced form interactions
        document.querySelectorAll('.premium-input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });

            input.addEventListener('blur', function() {
                if (!this.value) {
                    this.parentElement.classList.remove('focused');
                }
            });
        });

        // Initialize animations on load
        window.addEventListener('load', function() {
            document.body.classList.add('loaded');

            // Stagger animation for service cards
            const serviceCards = document.querySelectorAll('.service-card');
            serviceCards.forEach((card, index) => {
                setTimeout(() => {
                    card.classList.add('animate-in');
                }, index * 150);
            });
        });
    </script>
    <script src="{{ URL::asset('panel-assets/js/core/login-form.js') }}"></script>

    <!-- END: Theme JS-->
    <script>
        $(window).on('load', function() {
            if (feather) {
                feather.replace({
                    width: 14,
                    height: 14
                });
            }
        })
    </script>
</body>
<!-- END: Body-->

</html>
