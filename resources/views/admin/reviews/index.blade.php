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
                            <h2 class="content-header-title float-start mb-0">Customer Reviews</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.dashboard') }}">Home</a>
                                    </li>
                                    <li class="breadcrumb-item active"><a href="#">Customer Reviews</a></li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">
                    <a href="{{ route('admin.reviews.create') }}" class="btn btn-primary">
                        Add Review
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
                                <label class="form-label">Review Date</label>
                                <input type="date" id="filter-review-date" class="form-control">
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
                                                <th>#</th>
                                                <th>Service</th>
                                                <th>Customer Name</th>
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
    <div id="c-viewReviewModal" class="c-modal">
        <div class="c-modal-dialog">
            <div class="c-modal-content">
                <div class="c-modal-header">
                    <h5 class="c-modal-title"><i class="bi bi-chat-square-dots"></i> Review Details</h5>
                    <button class="c-close-btn" data-c-close>&times;</button>
                </div>
                <div class="c-modal-body" id="c-review-details">
                    <div class="c-loader">
                        <div class="c-spinner"></div>
                        <span>Fetching details...</span>
                    </div>
                </div>
                <div class="c-modal-footer">
                    <small><i class="bi bi-clock"></i> Updated just now</small>
                    <button class="c-btn" data-c-close><i class="bi bi-x-circle"></i> Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer_script_content')
    <script>
        const sweetalert_delete_title = "Delete Customer Review?";
        const sweetalert_change_status = "Change Status of Review";
        const sweetalert_change_priority_status = "Change Popular Status of Review";

        const form_url = '/reviews';
        datatable_url = '/getDataReviews';

        $.extend(true, $.fn.dataTable.defaults, {
            columns: [{
                    data: null,
                    name: 'id',
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    data: 'service_name',
                    name: 'service_name'
                },
                {
                    data: 'customer_name',
                    name: 'customer_name'
                },
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

        // View Modal
        $(document).on('click', '.btn-view', function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            const baseUrlCustomerPhoto = "{{ asset('uploads/review/customer-photos') }}/";
            const baseUrlCustomerPhotos = "{{ asset('uploads/review/photos') }}/";
            const baseUrlCustomerVideo = "{{ asset('uploads/review/videos') }}/";

            $("#c-viewReviewModal").addClass("show");
            $("#c-review-details").html(`
                <div class="c-loader">
                    <div class="c-spinner"></div>
                    <span>Loading...</span>
                </div>
            `);

            $.ajax({
                url: '/admin/reviews-view/' + id,
                type: 'GET',
                success: function(response) {
                    let data = response.data;
                    let html = `
                        <div class="c-row">
                            <div class="c-col-6"><div class="c-detail-card"><label>Service</label><p>${data.service_name ?? '-'}</p></div></div>
                            <div class="c-col-6"><div class="c-detail-card"><label>Customer Name</label><p>${data.customer_name ?? '-'}</p></div></div>
                            <div class="c-col-6"><div class="c-detail-card"><label>Rating</label><p>${data.rating ?? '-'}</p></div></div>
                            <div class="c-col-6"><div class="c-detail-card"><label>Helpful Count</label><p>${data.helpful_count ?? 0}</p></div></div>
                            <div class="c-col-12"><div class="c-detail-card"><label>Review</label><p>${data.review ?? '-'}</p></div></div>
                            <div class="c-col-6"><div class="c-detail-card"><label>Review Date</label><p>${data.review_date ?? '-'}</p></div></div>
                            <div class="c-col-6"><div class="c-detail-card"><label>Status</label>
                                <p>${data.status == 1 
                                    ? '<span class="badge badge-glow bg-success">Active</span>' 
                                    : '<span class="badge badge-glow bg-danger">InActive</span>'}
                                </p>
                            </div></div>
                            <div class="c-col-6"><div class="c-detail-card"><label>Is Popular</label>
                                <p>${data.is_popular == 1 
                                    ? '<span class="badge badge-glow bg-primary">Yes</span>' 
                                    : '<span class="badge badge-glow bg-secondary">No</span>'}
                                </p>
                            </div></div>
                            <div class="c-col-6"><div class="c-detail-card"><label>Created At</label><p>${data.created_at ? new Date(data.created_at).toLocaleString() : '-'}</p></div></div>
                            <div class="c-col-6"><div class="c-detail-card"><label>Updated At</label><p>${data.updated_at ? new Date(data.updated_at).toLocaleString() : '-'}</p></div></div>

                            <!-- Customer Photo -->
                            <div class="c-col-6">
                                <div class="c-detail-card text-center">
                                    <label>Customer Photo</label><br>
                                    ${
                                        data.customer_photo 
                                        ? `<img src="${baseUrlCustomerPhoto + data.customer_photo}" class="img-fluid" style="max-width:150px;cursor:pointer;" onclick="window.open('${baseUrlCustomerPhoto + data.customer_photo}', '_blank')">` 
                                        : '<p>-</p>'
                                    }
                                </div>
                            </div>

                            <!-- Review Photos -->
                            <div class="c-col-12">
                                <div class="c-detail-card">
                                    <label>Review Photos</label><br>
                                    ${
                                        data.photos 
                                        ? JSON.parse(data.photos).map(photo => 
                                            `<img src="${baseUrlCustomerPhotos + photo}" style="max-width:120px; margin:5px; cursor:pointer;" onclick="window.open('${baseUrlCustomerPhotos + photo}', '_blank')">`
                                          ).join("") 
                                        : '<p>-</p>'
                                    }
                                </div>
                            </div>

                            <!-- Review Video -->
                            <div class="c-col-12">
                                <div class="c-detail-card text-center">
                                    <label>Review Video</label><br>
                                   ${
                                        data.video 
                                        ? `<a href="${baseUrlCustomerVideo + data.video}" target="_blank">
                                                    <video controls style="max-width:300px; cursor:pointer;">
                                                        <source src="${baseUrlCustomerVideo + data.video}" type="video/mp4">
                                                        Your browser does not support video.
                                                    </video>
                                            </a>` 
                                        : '<p>-</p>'
                                    }

                                </div>
                            </div>
                        </div>
                    `;
                    $("#c-review-details").html(html);
                },
                error: function() {
                    $("#c-review-details").html(
                        `<div class="c-detail-card" style="color:red">Failed to load details.</div>`
                    );
                }
            });
        });

        $(document).on("click", "[data-c-close]", function() {
            $("#c-viewReviewModal").removeClass("show");
        });
    </script>
    <script src="{{ URL::asset('panel-assets/js/core/datatable.js') }}?v={{ time() }}"></script>
@endsection
