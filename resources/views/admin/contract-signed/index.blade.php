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
                            <h2 class="content-header-title float-start mb-0">Contracts Signed</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.dashboard') }}">Home</a>
                                    </li>
                                    <li class="breadcrumb-item active"><a href="#">Contracts Signed</a></li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">
                    <div class="btn-group">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        <div class="dropdown-menu dropdown-menu-end p-2" style="min-width: 300px;">
                            <div class="mb-2">
                                <label class="form-label">Status</label>
                                <select id="filter-status" class="form-select">
                                    <option value="">All</option>
                                    <option value="1">Signed</option>
                                    <option value="0">Pending</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Signed Date</label>
                                <input type="date" id="filter-signed-date" class="form-control">
                            </div>
                            <div class="d-flex justify-content-between">
                                <button id="btn-apply-filters" class="btn btn-sm btn-primary">Apply</button>
                                <button id="btn-reset-filters" class="btn btn-sm btn-secondary">Reset</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- Column Search -->
                <section id="column-search-datatable">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-datatable">
                                    <table class="dt-column-search table w-100 dataTable" id="table-1">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Provider Name</th>
                                                <th>Mobile</th>
                                                <th>Address</th>
                                                <th data-search="false">Signed PDF</th>
                                                <th data-search="false">Signature</th>
                                                <th data-search="false">Status</th>
                                                <th>Signed At</th>
                                                <th data-search="false">Action</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!--/ Column Search -->
            </div>
        </div>
    </div>
@endsection

@section('footer_script_content')
    <script>
        const sweetalert_delete_title = "Delete Contract?";
        const sweetalert_change_status = "Change Status of Contract";
        const form_url = '/contract-signed';
        datatable_url = '/getDataContracts';

        $.extend(true, $.fn.dataTable.defaults, {
            pageLength: 100,
            lengthMenu: [
                [10, 25, 50, 100, 200, -1],
                [10, 25, 50, 100, 200, "All"]
            ],
            columns: [
                {
                    data: null,
                    name: 'id',
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                { data: 'provider_name', name: 'provider_name' },
                { data: 'provider_mobile', name: 'provider_mobile' },
                { data: 'provider_address', name: 'provider_address' },
                { data: 'signed_pdf', name: 'signed_pdf', orderable: false },
                { data: 'signature_image', name: 'signature_image', orderable: false },
                { data: 'status', name: 'status' },
                { data: 'signed_at', name: 'signed_at' },
                { data: 'action', name: 'action', orderable: false },
            ],
            order: [[0, 'DESC']],
        });
    </script>
    <script src="{{ URL::asset('panel-assets/js/core/datatable.js') }}?v={{ time() }}"></script>
@endsection
