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
                        <h2 class="content-header-title float-start mb-0">Product Orders</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <section id="basic-datatable">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table" id="orderTable">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Order No.</th>
                                                <th>User</th>
                                                <th>Mobile</th>
                                                <th>Total Amount</th>
                                                <th>Order Status</th>
                                                <!-- <th>Active/Inactive</th> -->
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<!-- View Order Modal -->
<div id="c-viewOrderModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
            <div class="modal-header" style="background: #f8f9fa; border-bottom: 1px solid #edf2f7; border-radius: 12px 12px 0 0; padding: 20px;">
                <h5 class="modal-title" style="margin: 0; font-weight: 700; color: #1e293b;">
                    <i class="bi bi-box-seam me-1"></i> Order Details
                </h5>
                <div style="display:flex; align-items:center; gap:12px;">
                    <div style="display:flex; gap:8px;">
                        <button id="copyOrderData" class="btn btn-sm btn-outline-primary" style="font-weight: 600;">
                            <i class="bi bi-clipboard2-check"></i> Copy Data
                        </button>
                        <a id="downloadInvoiceBtn" href="#" class="btn btn-sm btn-danger" style="font-weight: 600;">
                            <i class="bi bi-file-pdf"></i> Download PDF
                        </a>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body" id="c-order-details" style="background: #fff; padding: 24px;">
                <div class="text-center p-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer_script_content')
<script>
    $(function() {
        var table = $('#orderTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.product-order.data') }}",
            columns: [
                {data: 'id', name: 'id'},
                {data: 'order_number', name: 'order_number', orderable: false, searchable: false},
                {data: 'user_name', name: 'users.name'},
                {data: 'mobile_number', name: 'users.mobile_number'},
                {data: 'total_amount', name: 'total_amount'},
                {data: 'order_status', name: 'order_status', orderable: false, searchable: false},
                // {data: 'status', name: 'status'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
            order: [[0, 'desc']]
        });

        // Handle Order Status change
        $(document).on('click', '.order-status-change', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var status = $(this).data('status');
            
            $.ajax({
                url: "{{ url('admin/product-order/status') }}/" + id + "/" + status,
                type: 'GET',
                success: function(response) {
                    if(response.success) {
                        if(typeof toastr !== 'undefined') {
                            toastr.success(response.success);
                        } else {
                            alert(response.success);
                        }
                        $('#orderTable').DataTable().ajax.reload(null, false);
                    } else if(response.error) {
                        if(typeof toastr !== 'undefined') {
                            toastr.error(response.error);
                        } else {
                            alert(response.error);
                        }
                    }
                },
                error: function() {
                    if(typeof toastr !== 'undefined') {
                        toastr.error('Something went wrong');
                    } else {
                        alert('Something went wrong');
                    }
                }
            });
        });

        // Handle Row Action View
        $(document).on('click', '.btn-view', function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            
            // Set PDF button route
            $('#downloadInvoiceBtn').attr('href', "{{ url('admin/product-order') }}/" + id + "/pdf");

            var modal = new bootstrap.Modal(document.getElementById('c-viewOrderModal'));
            modal.show();
            
            $("#c-order-details").html(`
                <div class="text-center p-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `);

            $.ajax({
                url: "{{ url('admin/product-order-view') }}/" + id,
                type: 'GET',
                success: function(response) {
                    if(response.data) {
                        let order = response.data;
                        let html = `
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <h6 class="text-muted fw-bolder mb-1">Customer Info</h6>
                                    <p class="mb-0"><strong>Name:</strong> ${order.user_name || 'N/A'}</p>
                                    <p class="mb-0"><strong>Phone:</strong> ${order.phone || 'N/A'}</p>
                                    <p class="mb-0"><strong>Email:</strong> ${order.email || 'N/A'}</p>
                                    <p class="mb-0 mt-1"><strong>Address:</strong><br>${order.address || 'N/A'}</p>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <h6 class="text-muted fw-bolder mb-1">Order Summary</h6>
                                    <p class="mb-0"><strong>Order #:</strong> ${order.order_number}</p>
                                    <p class="mb-0"><strong>Date:</strong> ${new Date(order.created_at).toLocaleString()}</p>
                                    <p class="mb-0"><strong>Total Amount:</strong> ₹${order.total_amount}</p>
                                    <p class="mb-0"><strong>Payment:</strong> <span class="badge bg-light-primary">${order.payment_status}</span></p>
                                    <p class="mb-0 mt-1"><strong>Status:</strong> <span class="badge bg-light-info">${order.order_status}</span></p>
                                </div>
                            </div>
                            <hr>
                            <h6 class="text-muted fw-bolder mb-2">Items Ordered</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Product</th>
                                            <th>Price</th>
                                            <th>Qty</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                        `;
                        
                        let itemsText = `Order Number: ${order.order_number}\nCustomer: ${order.user_name} (${order.phone})\nTotal: Rs. ${order.total_amount}\n\nItems:\n`;

                        if(order.order_data && order.order_data.length > 0) {
                            order.order_data.forEach(item => {
                                let variant = item.variant_name ? ` <small class="text-muted">(${item.variant_name})</small>` : '';
                                let variantText = item.variant_name ? ` (${item.variant_name})` : '';
                                html += `
                                    <tr>
                                        <td>${item.name}${variant}</td>
                                        <td>₹${item.price}</td>
                                        <td>${item.qty}</td>
                                        <td>₹${item.total}</td>
                                    </tr>
                                `;
                                itemsText += `- ${item.name}${variantText} x ${item.qty} = Rs. ${item.total}\n`;
                            });
                        } else {
                            html += `<tr><td colspan="4" class="text-center text-muted">No items found.</td></tr>`;
                        }

                        html += `
                                    </tbody>
                                </table>
                            </div>
                        `;

                        // Store for copying
                        window.currentOrderTextForCopy = itemsText + `\nShipping Address:\n${order.address || 'N/A'}`;

                        $("#c-order-details").html(html);
                    }
                },
                error: function() {
                    $("#c-order-details").html(`<div class="alert alert-danger">Failed to load order details.</div>`);
                }
            });
        });

        // Copy Order Data
        $(document).on('click', '#copyOrderData', function() {
            if(window.currentOrderTextForCopy) {
                navigator.clipboard.writeText(window.currentOrderTextForCopy).then(() => {
                    if(typeof toastr !== 'undefined') toastr.success('Order details copied to clipboard!');
                    else alert('Order details copied to clipboard!');
                }).catch(err => {
                    console.error('Failed to copy: ', err);
                });
            }
        });
    });
</script>
@endsection
