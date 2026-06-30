<!DOCTYPE html>
<html lang="en" data-textdirection="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="description" content="BeautyDen Admin Panel">
    <meta name="author" content="BeautyDen">

    <title>@yield('page_title', 'BeautyDen') | Admin</title>

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

<body class="pa-body vertical-layout vertical-menu-modern navbar-floating footer-static" data-open="click" data-menu="vertical-menu-modern" data-col="" data-page-heading="@yield('page_heading')">

    <div class="pa-shell" id="paShell">
        @include('admin.layouts.sidebar')

        <div class="pa-main" id="paMain">
            @include('admin.layouts.header')

            <main class="pa-content">
                @yield('content')
            </main>

            @include('admin.layouts.footer')
        </div>
    </div>

    <div class="pa-overlay" id="paOverlay"></div>

    @include('admin.layouts.search-overlay')

    {{-- Legacy Vuexy placeholders (hidden via CSS) --}}
    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

    @include('admin.layouts.footer-script')
    @yield('footer_script_content')
</body>
</html>
