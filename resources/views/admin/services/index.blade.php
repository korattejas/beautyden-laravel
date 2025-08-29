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
                        <h2 class="content-header-title float-start mb-0">Services</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}">Home</a>
                                </li>
                                <li class="breadcrumb-item active"><a href="#">Services</a></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">
                <a href="{{ route('admin.service.create') }}" class="btn btn-primary">
                    Add Service
                </a>
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
                                            <th>Id</th>
                                            <th>Category</th>
                                            <th>Name</th>
                                            <th>Price</th>
                                            <th>Discount Price</th>
                                            <th>Duration</th>
                                            <th>Rating</th>
                                            <th>Reviews</th>
                                            <th data-stuff="Active,InActive">Status</th>
                                            <th data-stuff="High Priority,Low Priority">Is Popular</th>
                                            <th data-search="false">Action</th>
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
    const sweetalert_delete_title = "Delete Service?";
    const sweetalert_change_status = "Change Status of Service";
    const sweetalert_change_priority_status = "Change Popularity Status of Service";

    // base form and data URLs
    const form_url = '/service';
    datatable_url = '/getDataService';

    $.extend(true, $.fn.dataTable.defaults, {
        columns: [
            {
                data: null,
                name: 'id',
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            { data: 'category_name', name: 'category_name' },
            { data: 'name', name: 'name' },
            { data: 'price', name: 'price' },
            { data: 'discount_price', name: 'discount_price' },
            { data: 'duration', name: 'duration' },
            { data: 'rating', name: 'rating' },
            { data: 'reviews', name: 'reviews' },
            { data: 'status', name: 'status' },
            { data: 'is_popular', name: 'is_popular' },
            { data: 'action', name: 'action', orderable: false },
        ],
        order: [[0, 'DESC']],
    });
</script>
<script src="{{ URL::asset('panel-assets/js/core/datatable.js') }}?v={{time()}}"></script>
@endsection
