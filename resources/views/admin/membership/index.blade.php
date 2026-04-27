@extends('admin.layouts.app')

@section('header_style_content')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
    :root {
        --mst-primary: #1a237e;
        --mst-bg: #f8fafc;
        --mst-card-bg: #ffffff;
        --mst-radius: 12px;
        --mst-shadow: 0 4px 15px rgba(0,0,0,0.04);
    }

    body { background-color: var(--mst-bg); font-family: 'Poppins', sans-serif; }

    .header-btn {
        padding: 0 24px !important;
        font-weight: 700 !important;
        border-radius: 12px !important;
        display: flex; align-items: center; gap: 10px; height: 48px;
        background: linear-gradient(135deg, #1a237e 0%, #311b92 100%) !important;
        border: none !important; color: #fff !important;
        box-shadow: 0 4px 15px rgba(26, 35, 126, 0.25) !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }

    .header-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(26, 35, 126, 0.4) !important;
    }

    .card { border-radius: var(--mst-radius); border: none; box-shadow: var(--mst-shadow); }
    .table thead th { background: #f8fafc; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.5px; font-weight: 700; border-bottom: none; }
</style>
@endsection

@section('content')
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <h2 class="content-header-title float-start mb-0">Membership Plans</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Membership Plans</li>
                    </ol>
                </div>
            </div>
            <div class="content-header-right text-md-end col-md-3 col-12 d-md-flex align-items-center justify-content-end d-none">
                <a href="{{ route('admin.membership.create') }}" class="btn btn-primary header-btn">
                    <i class="bi bi-plus-lg"></i> Create Plan
                </a>
            </div>
        </div>

        <div class="content-body">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="membership-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>Discount</th>
                                    <th>Duration</th>
                                    <th>Status</th>
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
@endsection

@section('footer_script_content')
<script>
    $(document).ready(function() {
        $('#membership-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.membership.getData') }}",
            columns: [
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'price', name: 'price' },
                { data: 'discount_percentage', name: 'discount_percentage', render: function(data){ return data+'%'; } },
                { data: 'duration_months', name: 'duration_months' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[0, 'desc']],
            dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        });
    });

    function changeStatus(id, status) {
        $.ajax({
            url: "{{ url('admin/membership/status') }}/" + id + "/" + status,
            type: "GET",
            success: function(res) {
                toastr.success(res.message);
                $('#membership-table').DataTable().ajax.reload();
            }
        });
    }

    function deleteRecord(id) {
        if(confirm("Are you sure you want to delete this plan?")) {
            $.ajax({
                url: "{{ url('admin/membership') }}/" + id,
                type: "DELETE",
                data: { _token: "{{ csrf_token() }}" },
                success: function(res) {
                    toastr.success(res.message);
                    $('#membership-table').DataTable().ajax.reload();
                }
            });
        }
    }
</script>
@endsection
