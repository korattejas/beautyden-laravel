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
                        <h2 class="content-header-title float-start mb-0">Razorpay Transactions</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}">{{ trans('admin_string.home') }}</a>
                                </li>
                                <li class="breadcrumb-item active">Razorpay Transactions</li>
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
                                <table class="dt-column-search table w-100 dataTable" id="transaction-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>User</th>
                                            <th>Order ID</th>
                                            <th>Appt ID</th>
                                            <th>Payment ID</th>
                                            <th>Amount</th>
                                            <th data-stuff="captured,success,failed,pending">Status</th>
                                            <th>Method</th>
                                            <th>Date</th>
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

<!-- Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Transaction Details Payload</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>Payment Raw JSON:</h6>
                <pre id="json-payload" style="background: #f8f9fa; padding: 15px; border-radius: 8px; font-size: 0.85rem; max-height: 300px; overflow-y: auto;"></pre>
                <hr>
                <h6>Metadata (Services/Appointments):</h6>
                <pre id="metadata-payload" style="background: #f8f9fa; padding: 15px; border-radius: 8px; font-size: 0.85rem; max-height: 200px; overflow-y: auto;"></pre>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer_script_content')
<script>
    var datatable_url = '/getDataRazorpay';

    $.extend(true, $.fn.dataTable.defaults, {
        pageLength: 25,
        lengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "All"]
        ],
        columns: [
            { data: 'id', name: 'id' },
            { data: 'user_name', name: 'user.name' },
            { data: 'razorpay_order_id', name: 'razorpay_order_id' },
            { data: 'appointment_link', name: 'appointment_link', orderable: false, searchable: false },
            { data: 'razorpay_payment_id', name: 'razorpay_payment_id' },
            { 
                data: 'amount', 
                name: 'amount',
                render: function(data) {
                    return '₹' + data;
                }
            },
            { data: 'status', name: 'status' },
            { data: 'method', name: 'method' },
            { data: 'created_at', name: 'created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
    });

    function viewDetails(id) {
        loaderView();
        $.get("{{ url('admin/razorpay') }}/" + id, function(res) {
            loaderHide();
            $('#json-payload').text(JSON.stringify(res.payment_details, null, 4));
            $('#metadata-payload').text(JSON.stringify(res.meta_data, null, 4));
            $('#detailsModal').modal('show');
        });
    }

    function refundTransaction(id, maxAmount) {
        Swal.fire({
            title: 'Refund Amount',
            text: "Enter the amount to refund. Leave unchanged for full refund (Max: ₹" + maxAmount + ")",
            input: 'number',
            inputValue: maxAmount,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, refund it!',
            inputAttributes: {
                min: 1,
                max: maxAmount,
                step: 0.01
            },
            inputValidator: (value) => {
                if (!value) {
                    return 'You need to enter an amount!'
                }
                if (value > maxAmount) {
                    return 'Amount cannot exceed the total transaction amount (₹' + maxAmount + ')!'
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                loaderView();
                $.ajax({
                    url: "{{ url('admin/razorpay/refund') }}/" + id,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        amount: result.value
                    },
                    success: function(res) {
                        loaderHide();
                        if (res.success) {
                            Swal.fire('Refunded!', res.message, 'success');
                            $('.dataTable').DataTable().ajax.reload();
                        } else {
                            Swal.fire('Error!', res.message, 'error');
                        }
                    },
                    error: function(err) {
                        loaderHide();
                        Swal.fire('Error!', 'Something went wrong.', 'error');
                    }
                });
            }
        })
    }
</script>
<script src="{{ URL::asset('panel-assets/js/core/datatable.js') }}?v={{ time() }}"></script>
@endsection
