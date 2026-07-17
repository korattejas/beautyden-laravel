@extends('admin.layouts.app')
@section('header_style_content')
<style>
   .badge-light-success{
        color: #000 !important;
   }
</style>
@endsection
@section('content')
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-start mb-0">Notification Templates</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                    <li class="breadcrumb-item active"><a href="#">Notification Templates</a></li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">
                    <a href="{{ route('admin.notification-templates.create') }}" class="btn btn-primary">
                        Add New Template
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
                                                <th>ID</th>
                                                <th>EVENT NAME</th>
                                                <th>TITLE</th>
                                                <th>REDIRECT TYPE</th>
                                                <th data-search="false">STATUS</th>
                                                <th data-search="false">ACTION</th>
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
        const sweetalert_delete_title = "Delete Template?";
        const sweetalert_change_status = "Change Status of Template";
        const form_url = '/notification-templates';
        datatable_url = '/getDataNotificationTemplates';

        $.extend(true, $.fn.dataTable.defaults, {
            pageLength: 100,
            lengthMenu: [[10, 25, 50, 100, 200, -1], [10, 25, 50, 100, 200, "All"]],
            columns: [
                { data: null, name: 'id', render: (data, type, row, meta) => meta.row + 1 },
                { data: 'event_name', name: 'event_name', render: (data) => `<span class="badge bg-light-primary text-primary" style="text-transform: uppercase; font-size: 13px;">${data.replace(/_/g, ' ')}</span>` },
                { data: 'title', name: 'title', render: (data, type, row) => `<strong style="font-size: 15px;">${data}</strong><br><span class="text-muted" style="white-space: pre-wrap; font-size: 14px;">${row.message.substring(0, 70)}${row.message.length > 70 ? '...' : ''}</span>` },
                { data: 'type', name: 'type', render: (data) => `<span class="badge bg-light-info text-info" style="font-size: 13px;">${data}</span>` },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            order: [[0, 'DESC']],
        });
    </script>
    <script src="{{ URL::asset('panel-assets/js/core/datatable.js') }}?v={{ time() }}"></script>
@endsection
