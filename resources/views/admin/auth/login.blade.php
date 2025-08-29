<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="description" content="POSE GALLERY">
    <meta name="keywords" content="POSE GALLERY">
    <meta name="author" content="POSE GALLERY">
    <title>Login - Poses Images</title>

    <link rel="apple-touch-icon" href="{{ URL::asset('panel-assets/admin-logo/logo.png') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ URL::asset('panel-assets/admin-logo/logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600"
        rel="stylesheet">


    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css"
        href="{{ URL::asset('panel-assets/vendors/css/extensions/toastr.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('panel-assets/vendors/css/vendors.min.css') }}">
    <!-- END: Vendor CSS-->
    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('panel-assets/css/bootstrap.css') }}">
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
    <!-- END: Custom CSS-->
    <script type="text/javascript">
        let APP_URL = {!! json_encode(url('/admin')) !!};
        let JS_URL = '{{url('/')}}';
        let is_admin_open = 1;
    </script>

</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="horizontal-layout horizontal-menu blank-page navbar-floating footer-static" data-open="hover"
    data-menu="horizontal-menu" data-col="blank-page">
    <!-- BEGIN: Content-->
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <div class="auth-wrapper auth-basic px-2">
                    <div class="auth-inner my-2">
                        <!-- Login basic -->
                        <div class="card mb-0">
                            <div class="card-body">
                                <a href="#" class="brand-logo">
                                    <img src="{{ URL::asset('panel-assets/admin-logo/logo.png') }}" class="w-50" />
                                </a>

                                <h4 class="card-title mb-1">Welcome to POSE GALLERY DASHBOARD! 👋</h4>
                                <p class="card-text mb-2">Please sign-in to your account</p>

                                <form class="auth-login-form mt-2" method="POST" id="addEditForm">
                                    <div class="mb-1">
                                        <label for="login_email" class="form-label">Email</label>
                                        <input type="text" class="form-control" id="login_email" name="login_email"
                                            placeholder="Enter Your Email" aria-describedby="login_email" tabindex="1"
                                            autofocus />
                                    </div>

                                    <div class="mb-1">
                                        <div class="input-group input-group-merge form-password-toggle">
                                            <input type="password" class="form-control form-control-merge"
                                                id="login_password" name="login_password" tabindex="2"
                                                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                                aria-describedby="login_password" />
                                            <span class="input-group-text cursor-pointer"><i
                                                    data-feather="eye"></i></span>
                                        </div>
                                    </div>
                                    <div class="mb-1">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="remember-me"
                                                tabindex="3" />
                                            <label class="form-check-label" for="remember-me"> Remember Me </label>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary w-100" type="submit" tabindex="4">Sign in</button>
                                </form>

                            </div>
                        </div>
                        <!-- /Login basic -->
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
    <!-- <script src="{{ URL::asset('panel-assets/vendors/js/ui/jquery.sticky.js') }}"></script> -->
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
    </script>
    <script src="{{ URL::asset('panel-assets/js/core/login-form.js') }}"></script>

    <!-- END: Theme JS-->
    <script>
        $(window).on('load', function () {
            if (feather) {
                feather.replace({
                    width: 14,
                    height: 14
                });
            }
        })
    </script>
</body>
<!-- END: Body--
</html>