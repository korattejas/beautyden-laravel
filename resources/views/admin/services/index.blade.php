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
                            <h2 class="content-header-title float-start mb-0">Services</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.dashboard') }}">Home</a>
                                    </li>
                                    <li class="breadcrumb-item active"><a href="#">Services</a></li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-end d-md-block d-none"
                    style="display: flex !important;gap: 12px;width: 100%;justify-content: end;padding-bottom: 10px;">
                    <a href="{{ route('admin.service.export.pdf') }}" class="btn btn-primary">
                        Export PDF Format
                    </a>
                    <a href="{{ route('admin.service.export.excel') }}" class="btn btn-primary">
                        Export Excel Format
                    </a>
                    <a href="{{ route('admin.service.create') }}" class="btn btn-primary">
                        Add Service
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
                                                <th>Id</th>
                                                <th>Category</th>
                                                <th>Sub Category</th>
                                                <th>Name</th>
                                                <th>Price</th>
                                                <th>Discount Price</th>
                                                {{-- <th>TP</th> --}}
                                                {{-- <th>PP</th> --}}
                                                <th data-stuff="Active,InActive">Status</th>
                                                <th data-stuff="High Priority,Low Priority">Is Popular</th>
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

    <div id="c-viewServiceModal" class="c-modal">
        <div class="c-modal-dialog">
            <div class="c-modal-content">
                <!-- Header -->
                <div class="c-modal-header">
                    <h5 class="c-modal-title"><i class="bi bi-briefcase"></i> Service Details</h5>
                    <button class="c-close-btn" data-c-close>&times;</button>
                </div>
                <!-- Body -->
                <div class="c-modal-body" id="c-service-details">
                    <div class="c-loader">
                        <div class="c-spinner"></div>
                        <span>Fetching details...</span>
                    </div>
                </div>
                <!-- Footer -->
                <div class="c-modal-footer">
                    <small><i class="bi bi-clock"></i> Updated just now</small>
                    <button class="c-btn" data-c-close>
                        <i class="bi bi-x-circle"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer_script_content')
    <script>
        const sweetalert_delete_title = "Delete Service?";
        const sweetalert_change_status = "Change Status of Service";
        const sweetalert_change_priority_status = "Change Popularity Status of Service";

        // base form and data URLs
        const form_url = '/service';
        datatable_url = '/getDataService';

        $.extend(true, $.fn.dataTable.defaults, {
            pageLength: 100,
            lengthMenu: [
                [10, 25, 50, 100, 200, -1],
                [10, 25, 50, 100, 200, "All"]
            ],
            columns: [{
                    data: null,
                    name: 'id',
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    data: 'category_name',
                    name: 'category_name'
                },
                {
                    data: 'sub_category_name',
                    name: 'sub_category_name'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'price',
                    name: 'price'
                },
                {
                    data: 'discount_price',
                    name: 'discount_price'
                },
                // {
                //     data: null,
                //     name: 'total_price',
                //     render: function(data, type, row) {
                //         let price = parseFloat(row.price) || 0;
                //         let discount = parseFloat(row.discount_price) || 0;
                //         let total = price - discount;

                //         // Color: green if discount applied, else black
                //         let color = discount > 0 ? 'green' : 'black';
                //         return `<span style="color:${color}; font-weight:bold;">${total.toFixed(2)}</span>`;
                //     }
                // },
                // {
                //     data: null,
                //     name: 'discount_percent',
                //     render: function(data, type, row) {
                //         let price = parseFloat(row.price) || 0;
                //         let discount = parseFloat(row.discount_price) || 0;
                //         let percent = 0;

                //         if (price > 0 && discount > 0) {
                //             percent = (discount / price) * 100;
                //         }

                //         // Color: green if discount applied, else black
                //         let color = discount > 0 ? 'green' : 'black';
                //         return `<span style="color:${color}; font-weight:bold;">${percent.toFixed(2)}%</span>`;
                //     }
                // },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'is_popular',
                    name: 'is_popular'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false
                },
            ],
            order: [
                [0, 'DESC']
            ],
        });

        // Modal View
        $(document).on('click', '.btn-view', function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            const baseUrl = "{{ asset('uploads/service') }}/";

            $("#c-viewServiceModal").addClass("show");
            $("#c-service-details").html(`
            <div class="c-loader">
                <div class="c-spinner"></div>
                <span>Loading...</span>
            </div>
        `);

            $.ajax({
                url: '/admin/service-view/' + id,
                type: 'GET',
                success: function(response) {
                    let data = response.data;
                    let html = `
                        <div class="c-row">
                            <div class="c-col-6"><div class="c-detail-card"><label>Category</label><p>${data.category_name ?? '-'}</p></div></div>
                            <div class="c-col-6"><div class="c-detail-card"><label>Sub Category</label><p>${data.sub_category_name ?? '-'}</p></div></div>
                            <div class="c-col-6"><div class="c-detail-card"><label>Name</label><p>${data.name ?? '-'}</p></div></div>
                            <div class="c-col-6"><div class="c-detail-card"><label>Price</label><p>${data.price ?? '-'}</p></div></div>
                            <div class="c-col-6"><div class="c-detail-card"><label>Discount Price</label><p>${data.discount_price ?? '-'}</p></div></div>
                            <div class="c-col-6"><div class="c-detail-card"><label>Duration</label><p>${data.duration ?? '-'}</p></div></div>
                            <div class="c-col-6"><div class="c-detail-card"><label>Rating</label><p>${data.rating ?? '-'}</p></div></div>
                            <div class="c-col-6"><div class="c-detail-card"><label>Reviews</label><p>${data.reviews ?? '-'}</p></div></div>
                            <div class="c-col-12"><div class="c-detail-card"><label>Description</label><p>${data.description ?? '-'}</p></div></div>
                            <div class="c-col-12"><div class="c-detail-card"><label>Includes</label><p>${
                                data.includes 
                                ? JSON.parse(data.includes).map(item => `<span class="c-include-badge">${item}</span>`).join(" ") 
                                : '-'
                            }</p></div></div>
                            <div class="c-col-6"><div class="c-detail-card"><label>Status</label>
                                <p>${data.status == 1 
                                    ? '<span class="badge badge-glow bg-success">Active</span>' 
                                    : '<span class="badge badge-glow bg-danger">InActive</span>'}
                                </p>
                            </div></div>
                            <div class="c-col-6"><div class="c-detail-card"><label>Is Popular</label>
                                <p>${data.is_popular == 1 
                                    ? '<span class="badge badge-glow bg-primary">High Priority</span>' 
                                    : '<span class="badge badge-glow bg-secondary">Low Priority</span>'}
                                </p>
                            </div></div>
                            <div class="c-col-6"><div class="c-detail-card"><label>Created At</label><p>${data.created_at ? new Date(data.created_at).toLocaleString() : '-'}</p></div></div>
                            <div class="c-col-6"><div class="c-detail-card"><label>Updated At</label><p>${data.updated_at ? new Date(data.updated_at).toLocaleString() : '-'}</p></div></div>

                            <!-- Icon (Image Preview) -->
                            <div class="c-col-12">
                                <div class="c-detail-card text-center">
                                    <label>Icon</label><br>
                                    ${
                                        data.icon 
                                        ? `<img 
                                                                                    src="${baseUrl + data.icon}" 
                                                                                    alt="Service Icon" 
                                                                                    class="img-fluid service-icon" 
                                                                                    style="max-width:250px; cursor:pointer;" 
                                                                                    onclick="window.open('${baseUrl + data.icon}', '_blank')" 
                                                                                >`
                                        : '<p>-</p>'
                                    }
                                </div>
                            </div>
                        </div>
                    `;
                    $("#c-service-details").html(html);

                },
                error: function() {
                    $("#c-service-details").html(
                        `<div class="c-detail-card" style="color:red">Failed to load details.</div>`
                    );
                }
            });
        });

        $(document).on("click", "[data-c-close]", function() {
            $("#c-viewServiceModal").removeClass("show");
        });
    </script>
    <script src="{{ URL::asset('panel-assets/js/core/datatable.js') }}?v={{ time() }}"></script>
@endsection
