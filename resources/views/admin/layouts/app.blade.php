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

    <title>BeautyDen</title>

    @include('admin.layouts.header-css')
    @yield('header_style_content')

    <script type="text/javascript">
        var APP_URL = {!! json_encode(url('/admin')) !!};
        var JS_URL = '{{ url('/') }}';
        var datatable_url = '/';
        var is_admin_open = 1;
        var status_msg = "Are You Sure?";
        var confirmButtonText = "Yes,change it";
        var cancelButtonText = "No";
        var sweetalert_delete_text = "Are you sure want to delete this record?";
        var cancel_button_text = "Cancel";
        var delete_button_text = "Delete";
        var sweetalert_change_status_text = "Are you sure want to change status of this record?";
        var sweetalert_change_priority_status_text = "Are you sure want to change priority status of this record?";
        var yes_change_it = "Change";
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
