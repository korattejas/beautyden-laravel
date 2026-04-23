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
                            <h2 class="content-header-title float-start mb-0">Coupon Usage Logs</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.dashboard') }}">{{ trans('admin_string.home') }}</a>
                                    </li>
                                    <li class="breadcrumb-item active"><a
                                            href="#">Coupon Usage Logs</a>
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
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
                                                <th>Coupon Code</th>
                                                <th>User Details</th>
                                                <th>Appointment #</th>
                                                <th>Discount Amt</th>
                                                <th>Used At</th>
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
        const sweetalert_delete_title = "Delete usage record?";
        const sweetalert_delete_text = "This will allow the user to reuse the coupon code!";
        const form_url = '/coupon-usage';
        datatable_url = '/getDataCouponUsages';

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
                    data: 'coupon_code',
                    name: 'coupon_code'
                },
                {
                    data: 'user_details',
                    name: 'user_details'
                },
                {
                    data: 'appointment_number',
                    name: 'appointment_number'
                },
                {
                    data: 'discount',
                    name: 'discount'
                },
                {
                    data: 'used_at',
                    name: 'used_at'
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
