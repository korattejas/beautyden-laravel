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

    <title>Test</title>

    @include('admin.layouts.header-css')
    <style>
        /* Modal Base */
        .c-modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.55);
            backdrop-filter: blur(3px);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .c-modal.show {
            display: flex;
        }

        .c-modal-dialog {
            width: 90%;
            max-width: 900px;
            animation: c-fadeIn 0.3s ease;
        }

        .c-modal-content {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        .c-modal-header {
            background: #102365;
            color: #fff;
            padding: 16px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .c-modal-title {
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            color: #fff;
            margin: 0px;
        }

        .c-close-btn {
            background: transparent;
            border: none;
            font-size: 24px;
            color: #fff;
            cursor: pointer;
        }

        /* Body */
        .c-modal-body {
            padding: 20px;
        }

        .c-loader {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            padding: 40px;
        }

        .c-spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #ddd;
            border-top: 4px solid #007bff;
            border-radius: 50%;
            animation: c-spin 1s linear infinite;
        }

        @keyframes c-spin {
            100% {
                transform: rotate(360deg);
            }
        }

        /* Footer */
        .c-modal-footer {
            padding: 12px 20px;
            background: #f8f9fa;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .c-btn {
            background: #102365;
            color: #fff;
            border: none;
            border-radius: 50px;
            padding: 8px 18px;
            cursor: pointer;
            font-weight: 500;
            transition: 0.2s;
        }

        /* Detail Cards */
        .c-detail-card {
            background: #f9fafc;
            border: 1px solid #eee;
            border-radius: 12px;
            padding: 15px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        .c-detail-card label {
            font-weight: 600;
            color: #6c757d;
            margin-bottom: 6px;
            display: block;
        }

        .c-detail-card p {
            margin: 0;
            font-size: 15px;
            color: #000;
            font-weight: 500;
        }

        /* Grid System */
        .c-row {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
        }

        .c-col-6 {
            flex: 0 0 calc(50% - 8px);
        }

        .c-col-12 {
            flex: 0 0 100%;
        }

        /* Animations */
        @keyframes c-fadeIn {
            from {
                transform: translateY(-10px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>
    @yield('header_style_content')

    <script type="text/javascript">
        let APP_URL = {!! json_encode(url('/admin')) !!};
        let JS_URL = '{{ url('/') }}';
        let datatable_url = '/';
        let is_admin_open = 1;
        const status_msg = "Are You Sure?";
        const confirmButtonText = "Yes,change it";
        const cancelButtonText = "No";
        const sweetalert_delete_text = "Are you sure want to delete this record?";
        const cancel_button_text = "Cancel";
        const delete_button_text = "Delete";
        const sweetalert_change_status_text = "Are you sure want to change status of this record?";
        const sweetalert_change_priority_status_text = "Are you sure want to change priority status of this record?";
        const yes_change_it = "Change";
    </script>

</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern  navbar-floating footer-static" data-open="click"
    data-menu="vertical-menu-modern" data-col="">

    <!-- BEGIN: Header-->
    @include('admin.layouts.header')
    <!-- END: Header-->


    <!-- BEGIN: Main Menu-->
    @include('admin.layouts.sidebar')
    <!-- END: Main Menu-->

    <!-- BEGIN: Content-->
    @yield('content')
    <!-- END: Content-->
    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

    <!-- BEGIN: Footer-->
    @include('admin.layouts.footer')
    <!-- END: Footer-->

    @include('admin.layouts.footer-script')

    <!-- BEGIN: Page JS-->
    @yield('footer_script_content')
    <!-- END: Page JS-->
</body>
<!-- END: Body-->

</html>
