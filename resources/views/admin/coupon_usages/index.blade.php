@extends('admin.layouts.app')

@section('header_style_content')
<style>
    .fw-number { font-family: 'JetBrains Mono', 'Courier New', monospace; font-weight: 600; color: #1a237e; }
    .badge-id { background: #f1f5f9; color: #475569; padding: 4px 10px; border-radius: 8px; font-weight: 700; font-size: 0.8rem; }
    .btn-delete-icon { width: 35px; height: 35px; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: #fff5f5; color: #e53e3e; border: 1px solid #feb2b2; transition: all 0.2s; }
    .btn-delete-icon:hover { background: #e53e3e; color: #fff; transform: scale(1.1); box-shadow: 0 4px 12px rgba(229, 62, 62, 0.2); }
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
                            <h2 class="content-header-title float-start mb-0">Coupon Usage Logs</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.dashboard') }}">{{ trans('admin_string.home') }}</a>
                                    </li>
                                    <li class="breadcrumb-item active">Coupon Usage Logs</li>
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
                                <div class="card-datatable table-responsive p-2">
                                    <table class="dt-column-search table w-100 dataTable" id="table-1">
                                        <thead>
                                            <tr>
                                                <th>{{ trans('admin_string.id') }}</th>
                                                <th>Coupon Code</th>
                                                <th>User Details</th>
                                                <th>Appointment #</th>
                                                <th>Discount Amt</th>
                                                <th>Used At</th>

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
        var sweetalert_delete_title = "Delete usage record?";
        var sweetalert_delete_text = "This will allow the user to reuse the coupon code!";
        var form_url = '/coupon-usage';
        var datatable_url = '/getDataCouponUsages';

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
                        return '<span class="badge-id">#' + (meta.row + 1) + '</span>';
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

            ],
            order: [
                [0, 'DESC']
            ],
        });
    </script>
    <script src="{{ URL::asset('panel-assets/js/core/datatable.js') }}?v={{ time() }}"></script>
@endsection
