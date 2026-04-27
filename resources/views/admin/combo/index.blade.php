@extends('admin.layouts.app')

@section('header_style_content')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
    :root { --mst-primary: #1a237e; --mst-bg: #f8fafc; --mst-radius: 12px; --mst-shadow: 0 4px 15px rgba(0,0,0,0.04); }
    body { background-color: var(--mst-bg); font-family: 'Poppins', sans-serif; }
    .header-btn {
        padding: 0 24px !important; font-weight: 700 !important; border-radius: 12px !important;
        display: flex; align-items: center; gap: 10px; height: 48px;
        background: linear-gradient(135deg, #1a237e 0%, #311b92 100%) !important;
        border: none !important; color: #fff !important;
        box-shadow: 0 4px 15px rgba(26, 35, 126, 0.25) !important;
    }
    .card { border-radius: var(--mst-radius); border: none; box-shadow: var(--mst-shadow); }
</style>
@endsection

@section('content')
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <h2 class="content-header-title float-start mb-0">Service Combos</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Service Combos</li>
                    </ol>
                </div>
            </div>
            <div class="content-header-right text-md-end col-md-3 col-12 d-md-flex align-items-center justify-content-end d-none">
                <a href="{{ route('admin.combo.create') }}" class="btn btn-primary header-btn">
                    <i class="bi bi-plus-lg"></i> Create Combo
                </a>
            </div>
        </div>

        <div class="content-body">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="combo-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Min. Price</th>
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
        $('#combo-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.combo.getData') }}",
            columns: [
                { data: 'id', name: 'id' },
                { data: 'image', name: 'image', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'min_price', name: 'min_price' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[0, 'desc']],
            dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        });
    });

    function deleteRecord(id) {
        if(confirm("Are you sure you want to delete this combo?")) {
            $.ajax({
                url: "{{ url('admin/combo') }}/" + id,
                type: "DELETE",
                data: { _token: "{{ csrf_token() }}" },
                success: function(res) {
                    toastr.success(res.message);
                    $('#combo-table').DataTable().ajax.reload();
                }
            });
        }
    }
</script>
@endsection
