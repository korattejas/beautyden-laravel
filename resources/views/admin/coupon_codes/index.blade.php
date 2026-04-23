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
                            <h2 class="content-header-title float-start mb-0">Coupon Codes</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.dashboard') }}">{{ trans('admin_string.home') }}</a>
                                    </li>
                                    <li class="breadcrumb-item active"><a
                                            href="#">Coupon Codes</a>
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">
                    <a href="{{ route('admin.coupon-codes.create') }}" class="btn btn-primary">
                        Add Coupon Code
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
                                                <th>{{ trans('admin_string.id') }}</th>
                                                <th>Code</th>
                                                <th>Discount</th>
                                                <th>Min Purchase</th>
                                                <th>Validity</th>
                                                <th data-stuff="Active,InActive">{{ trans('admin_string.status') }}</th>
                                                <th data-search="false">{{ trans('admin_string.action') }}</th>
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
        const sweetalert_delete_title = "Delete coupon code?";
        const sweetalert_change_status = "Change Status of coupon code";
        const form_url = '/coupon-codes';
        datatable_url = '/getDataCouponCodes';

        $.extend(true, $.fn.dataTable.defaults, {
            pageLength: 10,
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
                    data: 'code',
                    name: 'code'
                },
                {
                    data: 'discount',
                    name: 'discount'
                },
                {
                    data: 'min_purchase_amount',
                    name: 'min_purchase_amount',
                    render: function(data) {
                        return '₹' + data;
                    }
                },
                {
                    data: 'validity',
                    name: 'validity'
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
