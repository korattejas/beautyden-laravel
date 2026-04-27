@extends('admin.layouts.app')

@section('header_style_content')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    :root { --mst-primary: #1a237e; --mst-bg: #f8fafc; }
    body { background-color: var(--mst-bg); font-family: 'Poppins', sans-serif; }
    .card { border-radius: 12px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.04); }
    pre { background: #f1f5f9; padding: 15px; border-radius: 8px; font-size: 0.85rem; max-height: 400px; overflow-y: auto; }
</style>
@endsection

@section('content')
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="col-12 mb-2">
                <h2 class="content-header-title float-start mb-0">Razorpay Transactions</h2>
            </div>
        </div>

        <div class="content-body">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="transaction-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Order ID</th>
                                    <th>Payment ID</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Method</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
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
                <pre id="json-payload"></pre>
                <hr>
                <h6>Metadata (Services/Appointments):</h6>
                <pre id="metadata-payload"></pre>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer_script_content')
<script>
    $(document).ready(function() {
        $('#transaction-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.razorpay.getData') }}",
            columns: [
                { data: 'id', name: 'id' },
                { data: 'user_name', name: 'user.name' },
                { data: 'razorpay_order_id', name: 'razorpay_order_id' },
                { data: 'razorpay_payment_id', name: 'razorpay_payment_id' },
                { data: 'amount', name: 'amount' },
                { data: 'status', name: 'status' },
                { data: 'method', name: 'method' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[0, 'desc']],
            dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        });
    });

    function viewDetails(id) {
        $.get("{{ url('admin/razorpay') }}/" + id, function(res) {
            $('#json-payload').text(JSON.stringify(res.payment_details, null, 4));
            $('#metadata-payload').text(JSON.stringify(res.meta_data, null, 4));
            $('#detailsModal').modal('show');
        });
    }
</script>
@endsection
