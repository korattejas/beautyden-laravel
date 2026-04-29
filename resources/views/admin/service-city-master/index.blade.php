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
                            <h2 class="content-header-title float-start mb-0">App Service City Master</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.dashboard') }}">Home</a>
                                    </li>
                                    <li class="breadcrumb-item active"><a href="#">App Service City Master</a>
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">
                    <a href="{{ route('admin.service-city-master.create') }}" class="btn btn-primary">
                        Add App Pricing
                    </a>
                    <div class="btn-group">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        <div class="dropdown-menu dropdown-menu-end p-2" style="min-width: 300px;">
                            <div class="mb-2">
                                <label class="form-label">City</label>
                                <select id="filter-city" class="form-select select2">
                                    <option value="">All Cities</option>
                                    @foreach($cities as $city)
                                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Status</label>
                                <select id="filter-status" class="form-select">
                                    <option value="">All</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                            <div class="d-flex justify-content-between">
                                <button id="btn-apply-filters" class="btn btn-sm btn-primary">
                                    Apply
                                </button>
                                <button id="btn-reset-filters" class="btn btn-sm btn-secondary">
                                    Reset
                                </button>
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
                                                <th>Id</th>
                                                <th>City</th>
                                                <th>Service</th>
                                                <th>Category</th>
                                                <th>Price</th>
                                                <th>App Disc. (%)</th>
                                                <th data-stuff="Available,Disabled">Avail.</th>
                                                <th data-stuff="Active,InActive">Status</th>
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
        const sweetalert_delete_title = "Delete App Pricing?";
        const sweetalert_change_status = "Change Status of App Pricing";
        const form_url = '/admin/service-city-master';
        datatable_url = '/admin/getDataServiceCityMaster';

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
                    data: 'city_name',
                    name: 'city_name'
                },
                {
                    data: 'service_name',
                    name: 'service_name'
                },
                {
                    data: 'category_name',
                    name: 'category_name'
                },
                {
                    data: 'price',
                    name: 'price'
                },
                {
                    data: 'app_discount_percentage',
                    name: 'app_discount_percentage'
                },
                {
                    data: 'is_available',
                    name: 'is_available',
                    orderable: false
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false
                },
            ],
            order: [
                [0, 'DESC']
            ],
        });
    </script>
    <script src="{{ URL::asset('panel-assets/js/core/datatable.js') }}?v={{ time() }}"></script>
@endsection

