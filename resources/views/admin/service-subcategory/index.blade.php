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
                        <h2 class="content-header-title float-start mb-0">Service Subcategory</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}">Home</a>
                                </li>
                                <li class="breadcrumb-item active">
                                    <a href="#">Service Subcategory</a>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">
                <a href="{{ route('admin.service-subcategory.create') }}" class="btn btn-primary">
                    Add Service Subcategory
                </a>
                <div class="btn-group">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                    <div class="dropdown-menu dropdown-menu-end p-2" style="min-width: 300px;">
                        <div class="mb-2">
                            <label class="form-label">Status</label>
                            <select id="filter-status" class="form-select">
                                <option value="">All</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Is Popular</label>
                            <select id="filter-popular" class="form-select">
                                <option value="">All</option>
                                <option value="1">High Priority</option>
                                <option value="0">Low Priority</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Created Date</label>
                            <input type="date" id="filter-created-date" class="form-control">
                        </div>
                        <div class="d-flex justify-content-between">
                            <button id="btn-apply-filters" class="btn btn-sm btn-primary">
                                Apply
                            </button>
                            <button id="btn-reset-filters" class="btn btn-sm btn-secondary">
                                Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Body -->
        <div class="content-body">
            <section id="column-search-datatable">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-datatable">
                                <table class="dt-column-search table w-100 dataTable" id="table-1">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Category</th>
                                            <th>Subcategory Name</th>
                                            <th data-search="false">Icon</th>
                                            <th>Status</th>
                                            <th>Is Popular</th>
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
        <!--/ Content Body -->

    </div>
</div>
@endsection

@section('footer_script_content')
<script>
    const sweetalert_delete_title = "Delete Subcategory?";
    const sweetalert_change_status = "Change Status of Subcategory";
    const sweetalert_change_priority_status = "Change Priority Status of Subcategory";
    const form_url = '/service-subcategory';
    datatable_url = '/getDataServiceSubcategory';

    $.extend(true, $.fn.dataTable.defaults, {
        columns: [
            {
                data: null,
                name: 'id',
                render: function(data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            { data: 'category_name', name: 'category_name' },
            { data: 'name', name: 'name' },
            { data: 'icon', name: 'icon', orderable: false },
            { data: 'status', name: 'status' },
            { data: 'is_popular', name: 'is_popular' },
            { data: 'action', name: 'action', orderable: false },
        ],
        order: [[0, 'DESC']],
    });
</script>
<script src="{{ URL::asset('panel-assets/js/core/datatable.js') }}?v={{ time() }}"></script>
@endsection
