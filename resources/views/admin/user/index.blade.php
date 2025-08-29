@extends('admin.layouts.app')
@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">{{trans('admin_string.user')}}</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}">{{trans('admin_string.home')}}</a>
                                </li>
                                <li class="breadcrumb-item active"><a href="#">{{trans('admin_string.user')}}</a>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <!-- Column Search -->
            <section id="column-search-datatable">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-datatable">
                                <table class="dt-column-search table w-100 dataTable" id="table-1">
                                    <thead>
                                        <tr>
                                            <th>{{trans('admin_string.id')}}</th>
                                            <th>{{trans('admin_string.name')}}</th>
                                            <th>{{trans('admin_string.mobile_number')}}</th>
                                            <th>{{trans('admin_string.email')}}</th>
                                            <th>{{trans('admin_string.role')}}</th>
                                            <th>{{trans('admin_string.mobile_verified_at')}}</th>
                                            <th>{{trans('admin_string.email_verified_at')}}</th>
                                            <th>{{trans('admin_string.ip_address')}}</th>
                                            <th data-stuff="Active,InActive">{{trans('admin_string.status')}}</th>
                                            <th data-search="false">{{trans('admin_string.action')}}</th>

                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!--/ Column Search -->
        </div>
    </div>
</div>
@endsection
@section('footer_script_content')
<script>
    const sweetalert_delete_title = "Delete User?";
    const sweetalert_change_status = "Change Status of User";
    const form_url = '/user';
    datatable_url = '/getDataUser';

    $.extend(true, $.fn.dataTable.defaults, {
        columns: [
            {
                data: null,
                name: 'id',
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            { data: 'name', name: 'name' },
            { data: 'mobile_number', name: 'mobile_number' },
            { data: 'email', name: 'email' },
            {
                data: 'role',
                name: 'role',
                render: function (data) {
                    if (data == 1) {
                        return `<span class="badge badge-glow bg-info">Application</span>`;
                    } else {
                        return `<span class="badge badge-glow bg-info">Website</span>`;
                    }
                }
            },
            { data: 'mobile_verified_at', name: 'mobile_verified_at' },
            { data: 'email_verified_at', name: 'email_verified_at' },
            { data: 'ip_address', name: 'ip_address' },
            { data: 'status', name: 'status' },
            { data: 'action', name: 'action', orderable: false },
        ],
        order: [[0, 'DESC']],
    });


</script>
<script src="{{ URL::asset('panel-assets/js/core/datatable.js') }}?v={{time()}}"></script>
@endsection