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
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                    <li class="breadcrumb-item active">Coupon Codes</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">
                    <a href="{{ route('admin.coupon-codes.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Add Coupon Code
                    </a>
                </div>
            </div>

            <div class="content-body">
                <section id="column-search-datatable">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-datatable table-responsive p-2">
                                    <table class="dt-column-search table" id="table-1">
                                        <thead>
                                            <tr>
                                                <th>Id</th>
                                                <th>Code</th>
                                                <th>Discount</th>
                                                <th>Min Purchase</th>
                                                <th>Validity</th>
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
            </div>
        </div>
    </div>

    <!-- Quick View Modal -->
    <div id="c-viewCouponModal" class="c-modal">
        <div class="c-modal-dialog">
            <div class="c-modal-content">
                <div class="c-modal-header">
                    <h5 class="c-modal-title"><i class="bi bi-ticket-perforated"></i> Coupon Details</h5>
                    <button class="c-close-btn" data-c-close>&times;</button>
                </div>
                <div class="c-modal-body" id="c-coupon-details">
                    <div class="c-loader">
                        <div class="c-spinner"></div>
                        <span>Fetching details...</span>
                    </div>
                </div>
                <div class="c-modal-footer">
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
        var sweetalert_delete_title = "Delete coupon code?";
        var sweetalert_change_status = "Change Status of coupon code";
        var form_url = '/coupon-codes';
        var datatable_url = '/getDataCouponCodes';

        $.extend(true, $.fn.dataTable.defaults, {
            pageLength: 25,
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],
            columns: [
                {
                    data: null,
                    name: 'id',
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    data: 'code',
                    name: 'code',
                    render: function(data) {
                        return `<span class="badge bg-light-primary text-uppercase fw-bolder" style="letter-spacing: 1px;">${data}</span>`;
                    }
                },
                {
                    data: 'discount',
                    name: 'discount'
                },
                {
                    data: 'min_purchase_amount',
                    name: 'min_purchase_amount',
                    render: function(data) {
                        return '₹' + parseFloat(data).toFixed(2);
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
                }
            ],
            order: [[0, 'DESC']]
        });

        // Quick View Functionality
        $(document).on('click', '.btn-view', function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            $("#c-viewCouponModal").addClass("show");
            $("#c-coupon-details").html(`
                <div class="c-loader">
                    <div class="c-spinner"></div>
                    <span>Loading details...</span>
                </div>
            `);

            $.ajax({
                url: '/admin/coupon-codes-view/' + id,
                type: 'GET',
                success: function(response) {
                    $("#c-coupon-details").html(response);
                },
                error: function() {
                    $("#c-coupon-details").html('<div class="alert alert-danger">Failed to load coupon details.</div>');
                }
            });
        });

        $(document).on("click", "[data-c-close]", function() {
            $("#c-viewCouponModal").removeClass("show");
        });
    </script>
    <script src="{{ URL::asset('panel-assets/js/core/datatable.js') }}?v={{ time() }}"></script>
@endsection

