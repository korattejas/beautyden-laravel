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
                            <h2 class="content-header-title float-start mb-0">Service Management</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                    <li class="breadcrumb-item active"><a href="#">Service Masters</a></li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">
                    <a href="{{ route('admin.service-master.create') }}" class="btn btn-primary">
                        Add New Service
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
                                                <th>ICON</th>
                                                <th>CATEGORY</th>
                                                <th>SUB CATEGORY</th>
                                                <th>NAME</th>
                                                <th>PRICE</th>
                                                <th>DISCOUNT PRICE</th>
                                                <th data-stuff="Active,InActive">STATUS</th>
                                                <th>IS POPULAR</th>
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
        const sweetalert_delete_title = "Delete Service?";
        const sweetalert_change_status = "Change Status of Service";

        const form_url = '/service-master';
        datatable_url = '/getDataServiceMaster';

        $.extend(true, $.fn.dataTable.defaults, {
            pageLength: 100,
            lengthMenu: [[10, 25, 50, 100, 200, -1], [10, 25, 50, 100, 200, "All"]],
            columns: [
                { data: null, name: 'id', render: (data, type, row, meta) => meta.row + 1 },
                { 
                    data: 'icon', 
                    name: 'icon',
                    render: function(data, type, row) {
                        var img = data ? JS_URL + '/uploads/service/' + data : JS_URL + '/panel-assets/images/no-image.png';
                        return '<div class="avatar avatar-lg shadow-sm border"><img src="'+img+'" alt="icon" style="object-fit: cover;"></div>';
                    },
                    orderable: false,
                    searchable: false
                },
                { data: 'category_name', name: 'sc.name' },
                { data: 'subcategory_name', name: 'ssc.name' },
                { 
                    data: 'name', 
                    name: 'name',
                    render: function(data, type, row) {
                        return '<a href="'+row.view_url+'" class="fw-bold text-primary">'+data+'</a>';
                    }
                },
                { data: 'price', name: 'price' },
                { data: 'discount_price', name: 'discount_price' },
                { data: 'status', name: 'status' },
                { data: 'is_popular', name: 'is_popular' },
                { data: 'action', name: 'action', orderable: false },
            ],
            order: [[0, 'DESC']],
        });
    </script>
    <script src="{{ URL::asset('panel-assets/js/core/datatable.js') }}?v={{ time() }}"></script>
@endsection
