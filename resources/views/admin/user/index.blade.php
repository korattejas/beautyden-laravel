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
                        <h2 class="content-header-title float-start mb-0">Registered Users</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}">{{ trans('admin_string.home') }}</a>
                                </li>
                                <li class="breadcrumb-item active">Users</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label class="form-label">Filter by Status</label>
                                <select id="filter-status" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="1">Active</option>
                                    <option value="0">Suspended</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 mt-2 mt-md-0">
                            <button id="btn-apply-filters" class="btn btn-primary mt-md-2 w-100">Apply Filters</button>
                        </div>
                    </div>
                </div>
            </div>

            <section id="column-search-datatable">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-datatable table-responsive p-2">
                                <table class="dt-column-search table w-100 dataTable" id="user-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>User Info</th>
                                            <th>Contact</th>
                                            <th>Joined On</th>
                                            <th data-stuff="App User,Web User">Role</th>
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
@endsection

@section('footer_script_content')
<script>
    var datatable_url = '/getDataUser';
    var sweetalert_delete_title = "Delete User?";
    var sweetalert_change_status = "Change Status of User";
    var form_url = '/user';

    $.extend(true, $.fn.dataTable.defaults, {
        pageLength: 25,
        lengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "All"]
        ],
        ajax: {
            url: APP_URL + datatable_url,
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
                                    <span class="user_name text-truncate fw-bold text-dark">${data}</span>
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
    });

    $(document).ready(function() {
        $('#btn-apply-filters').click(function() {
            var table = $('#user-table').DataTable();
            table.draw();
        });
    });
</script>
<script src="{{ URL::asset('panel-assets/js/core/datatable.js') }}?v={{time()}}"></script>
@endsection