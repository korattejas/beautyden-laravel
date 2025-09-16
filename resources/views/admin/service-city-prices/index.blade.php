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
                            <h2 class="content-header-title float-start mb-0">Service City Prices</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.dashboard') }}">Home</a>
                                    </li>
                                    <li class="breadcrumb-item active"><a href="#">Service City Prices</a></li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-end d-md-block d-none"
                    style="display: flex !important;gap: 12px;width: 100%;justify-content: end;padding-bottom: 10px;">
                    <a href="{{ route('admin.service-city-price.export.pdf') }}" class="btn btn-primary">
                        Export PDF Format
                    </a>
                    <a href="{{ route('admin.service-city-price.export.excel') }}" class="btn btn-primary">
                        Export Excel Format
                    </a>
                    <a href="{{ route('admin.service-city-price.create') }}" class="btn btn-primary">
                        Add Service City Price
                    </a>
                    <div class="btn-group">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        <div class="dropdown-menu dropdown-menu-end p-2" style="min-width: 300px;">
                            <div class="mb-2">
                                <label class="form-label">Status</label>
                                <select id="filter-status" class="form-select">
                                    <option value="">All</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Created Date</label>
                                <input type="date" id="filter-created-date" class="form-control">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">City</label>
                                <select id="filter-city" class="form-select">
                                    <option value="">All Cities</option>
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                                    @endforeach
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
                                                <th>#</th>
                                                <th>City</th>
                                                <th>Category</th>
                                                <th>Sub Category</th>
                                                <th>Service</th>
                                                <th>Price</th>
                                                <th>Discount Price</th>
                                                {{-- <th>Total Price</th>
                                                <th>Discount %</th> --}}
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

    <div id="c-viewPriceModal" class="c-modal">
        <div class="c-modal-dialog">
            <div class="c-modal-content">
                <!-- Header -->
                <div class="c-modal-header">
                    <h5 class="c-modal-title"><i class="bi bi-cash-coin"></i> Service City Price Details</h5>
                    <button class="c-close-btn" data-c-close>&times;</button>
                </div>
                <!-- Body -->
                <div class="c-modal-body" id="c-price-details">
                    <div class="c-loader">
                        <div class="c-spinner"></div>
                        <span>Fetching details...</span>
                    </div>
                </div>
                <!-- Footer -->
                <div class="c-modal-footer">
                    <small><i class="bi bi-clock"></i> Updated just now</small>
                    <button class="c-btn" data-c-close>
                        <i class="bi bi-x-circle"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer_script_content')
    <script>
        const sweetalert_delete_title = "Delete Service City Price?";
        const sweetalert_change_status = "Change Status of Service City Price";
        const form_url = '/service-city-price';
        datatable_url = '/getDataServiceCityPrice';

        $.extend(true, $.fn.dataTable.defaults, {
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
                    data: 'service_category_name',
                    name: 'service_category_name'
                },
                {
                    data: 'service_sub_category_name',
                    name: 'service_sub_category_name'
                },
                {
                    data: 'service_name',
                    name: 'service_name'
                },
                {
                    data: 'price',
                    name: 'price'
                },
                {
                    data: 'discount_price',
                    name: 'discount_price'
                },
                // {
                //     data: null,
                //     name: 'total_price',
                //     render: function(data, type, row) {
                //         let price = parseFloat(row.price) || 0;
                //         let discount = parseFloat(row.discount_price) || 0;
                //         let total = price - discount;

                //         let color = discount > 0 ? 'green' : 'black';
                //         return `<span style="color:${color}; font-weight:bold;">${total.toFixed(2)}</span>`;
                //     }
                // },
                // {
                //     data: null,
                //     name: 'discount_percent',
                //     render: function(data, type, row) {
                //         let price = parseFloat(row.price) || 0;
                //         let discount = parseFloat(row.discount_price) || 0;
                //         let percent = 0;

                //         if (price > 0 && discount > 0) {
                //             percent = (discount / price) * 100;
                //         }

                //         let color = discount > 0 ? 'green' : 'black';
                //         return `<span style="color:${color}; font-weight:bold;">${percent.toFixed(2)}%</span>`;
                //     }
                // },
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
