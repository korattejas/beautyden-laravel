@extends('admin.layouts.app')

@section('header_style_content')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
    :root { --mst-primary: #1a237e; --mst-bg: #f8fafc; --mst-radius: 12px; --mst-shadow: 0 4px 15px rgba(0,0,0,0.04); }
    body { background-color: var(--mst-bg); font-family: 'Poppins', sans-serif; }
    .card { border-radius: var(--mst-radius); border: none; box-shadow: var(--mst-shadow); }
</style>
@endsection

@section('content')
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-12 col-12 mb-2">
                <h2 class="content-header-title float-start mb-0">Registered Users Management</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Users</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="content-body">
            <div class="card mb-2">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select id="filter-status" class="form-select">
                                <option value="">All Status</option>
                                <option value="1">Active</option>
                                <option value="0">Suspended</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button id="btn-apply-filters" class="btn btn-primary w-100">Apply Filters</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="user-table text-nowrap">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>User Info</th>
                                    <th>Contact</th>
                                    <th>Joined On</th>
                                    <th>Role</th>
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
    const sweetalert_delete_title = "Delete User?";
    const sweetalert_change_status = "Change Status of User";
    const form_url = '/user'; // For base actions
    
    $(document).ready(function() {
        var table = $('#user-table text-nowrap').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.user.getDataUser') }}",
                data: function(d) {
                    d.status = $('#filter-status').val();
                }
            },
            columns: [
                {
                    data: null,
                    name: 'id',
                    render: function (data, type, row, meta) { return meta.row + 1; }
                },
                { 
                    data: 'name', 
                    name: 'name',
                    render: function(data, type, row) {
                        return `<div class="d-flex align-items-center">
                                    <div class="avatar bg-light-primary me-1"><span class="avatar-content">${data.charAt(0)}</span></div>
                                    <div class="d-flex flex-column">
                                        <span class="user_name text-truncate fw-bold">${data}</span>
                                        <small class="emp_post text-muted">ID: #${row.id}</small>
                                    </div>
                                </div>`;
                    }
                },
                { 
                    data: 'mobile_number', 
                    name: 'mobile_number',
                    render: function(data, type, row) {
                        return `<div class="d-flex flex-column">
                                    <span class="fw-bold">${data}</span>
                                    <small class="text-muted">${row.email || '-'}</small>
                                </div>`;
                    }
                },
                { data: 'created_at', name: 'created_at' },
                {
                    data: 'role',
                    name: 'role',
                    render: function (data) {
                        let color = data == 1 ? 'bg-light-info' : 'bg-light-secondary';
                        let label = data == 1 ? 'App User' : 'Web User';
                        return `<span class="badge ${color} text-dark">${label}</span>`;
                    }
                },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            order: [[0, 'DESC']],
            dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        });

        $('#btn-apply-filters').click(function() { table.draw(); });
    });
</script>
<script src="{{ URL::asset('panel-assets/js/core/datatable.js') }}?v={{time()}}"></script>
@endsection