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
            <div class="col-md-9 mb-2">
                <h2 class="content-header-title float-start mb-0">Push Notification Center</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Notifications</li>
                    </ol>
                </div>
            </div>
            <div class="col-md-3 text-end">
                <a href="{{ route('admin.notifications.create') }}" class="btn btn-primary header-btn">
                    <i class="bi bi-send-fill"></i> New Notification
                </a>
            </div>
        </div>

        <div class="content-body">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="notif-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Status</th>
                                    <th>Success</th>
                                    <th>Failure</th>
                                    <th>Scheduled At</th>
                                    <th>Created At</th>
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
        $('#notif-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.notifications.getData') }}",
            columns: [
                { data: 'id', name: 'id' },
                { data: 'title', name: 'title' },
                { data: 'status', name: 'status' },
                { data: 'success_count', name: 'success_count' },
                { data: 'failure_count', name: 'failure_count' },
                { data: 'scheduled_at', name: 'scheduled_at' },
                { data: 'created_at', name: 'created_at' }
            ],
            order: [[0, 'desc']],
            dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        });
    });
</script>
@endsection
