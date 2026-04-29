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
                        <h2 class="content-header-title float-start mb-0">Notifications</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}">Home</a>
                                </li>
                                <li class="breadcrumb-item active"><a href="#">Notifications</a></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">
                <a href="{{ route('admin.notifications.create') }}" class="btn btn-primary">
                    New Notification
                </a>
            </div>
        </div>
        <div class="content-body">
            <section id="column-search-datatable">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-datatable">
                                <table class="dt-column-search table w-100 dataTable" id="table-1">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Title</th>
                                            <th data-stuff="Draft,Sent,Scheduled">Status</th>
                                            <th>Success</th>
                                            <th>Failure</th>
                                            <th>Scheduled At</th>
                                            <th>Created At</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection

@section('footer_script_content')
    <script>
        const sweetalert_delete_title = "Delete Notification?";
        const sweetalert_change_status = "Change Status of Notification";
        const form_url = '/admin/notifications';
        datatable_url = '/admin/getDataNotifications';

        $.extend(true, $.fn.dataTable.defaults, {
            pageLength: 100,
            lengthMenu: [
                [10, 25, 50, 100, 200, -1],
                [10, 25, 50, 100, 200, "All"]
            ],
            columns: [{
                    data: null,
                    name: 'id',
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    data: 'title',
                    name: 'title'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'success_count',
                    name: 'success_count'
                },
                {
                    data: 'failure_count',
                    name: 'failure_count'
                },
                {
                    data: 'scheduled_at',
                    name: 'scheduled_at'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
            ],
            order: [
                [0, 'DESC']
            ],
        });
    </script>
    <script src="{{ URL::asset('panel-assets/js/core/datatable.js') }}?v={{ time() }}"></script>
@endsection
