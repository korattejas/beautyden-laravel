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
