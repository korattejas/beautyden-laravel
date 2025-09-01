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
                            <h2 class="content-header-title float-start mb-0">Customer Contact Submission Data</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.dashboard') }}">{{ trans('admin_string.home') }}</a>
                                    </li>
                                    <li class="breadcrumb-item active"><a href="#">Customer Contact Submission
                                            Data</a>
                                    </li>
                                </ol>
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
                                                <th>First Name</th>
                                                <th>Last Name</th>
                                                <th>Email</th>
                                                <th>Phone</th>
                                                <th>Service Name</th>
                                                <th>Subject</th>
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
                <!--/ Column Search -->
            </div>
        </div>
    </div>

    <!-- Modal -->
    <!-- Modal -->
    <div id="c-viewContactModal" class="c-modal">
        <div class="c-modal-dialog">
            <div class="c-modal-content">

                <!-- Header -->
                <div class="c-modal-header">
                    <h5 class="c-modal-title">
                        <i class="bi bi-person-lines-fill"></i> Contact Submission Details
                    </h5>
                    <button class="c-close-btn" data-c-close>&times;</button>
                </div>

                <!-- Body -->
                <div class="c-modal-body" id="c-contact-details">
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
        const sweetalert_delete_title = "Delete Contact?";
        const sweetalert_change_status = "Change Status of Contact";
        const form_url = '/contact-submissions';
        datatable_url = '/getDataContactSubmissions';

        $.extend(true, $.fn.dataTable.defaults, {
            columns: [{
                    data: null,
                    name: 'id',
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    data: 'first_name',
                    name: 'first_name'
                },
                {
                    data: 'last_name',
                    name: 'last_name'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'phone',
                    name: 'phone'
                },
                {
                    data: 'service_name',
                    name: 'service_name'
                },
                {
                    data: 'subject',
                    name: 'subject'
                },
                {
                    data: 'status',
                    name: 'status'
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

        $(document).on('click', '.btn-view', function(e) {
            e.preventDefault();
            let id = $(this).data('id');

            $("#c-viewContactModal").addClass("show");
            $("#c-contact-details").html(`
        <div class="c-loader">
            <div class="c-spinner"></div>
            <span>Loading...</span>
        </div>
    `);

            $.ajax({
                url: '/admin/contact-submissions-view/' + id,
                type: 'GET',
                success: function(response) {
                    let data = response.data;
                    let html = `
                    <div class="c-row">
                        <div class="c-col-6">
                        <div class="c-detail-card">
                            <label>First Name</label>
                            <p>${data.first_name ?? '-'}</p>
                        </div>
                        </div>
                        <div class="c-col-6">
                        <div class="c-detail-card">
                            <label>Last Name</label>
                            <p>${data.last_name ?? '-'}</p>
                        </div>
                        </div>
                        <div class="c-col-6">
                        <div class="c-detail-card">
                            <label>Email</label>
                            <p>${data.email ?? '-'}</p>
                        </div>
                        </div>
                        <div class="c-col-6">
                        <div class="c-detail-card">
                            <label>Phone</label>
                            <p>${data.phone ?? '-'}</p>
                        </div>
                        </div>
                        <div class="c-col-6">
                        <div class="c-detail-card">
                            <label>Service</label>
                            <p>${data.service_name ?? '-'}</p>
                        </div>
                        </div>
                        <div class="c-col-6">
                        <div class="c-detail-card">
                            <label>Subject</label>
                            <p>${data.subject ?? '-'}</p>
                        </div>
                        </div>
                        <div class="c-col-12">
                        <div class="c-detail-card">
                            <label>Message</label>
                            <p>${data.message ?? '-'}</p>
                        </div>
                        </div>
                        <div class="c-col-6">
                        <div class="c-detail-card">
                            <label>Status</label>
                            <p>
                            ${data.status == 1 
                                ? '<span class="badge badge-glow bg-success">Active</span>' 
                                : '<span class="badge badge-glow bg-danger">InActive</span>'}
                            </p>
                        </div>
                        </div>
                        <div class="c-col-6">
                        <div class="c-detail-card">
                            <label>Created At</label>
                            <p>${data.created_at ? new Date(data.created_at).toLocaleString() : '-'}</p>
                        </div>
                        </div>
                        <div class="c-col-6">
                        <div class="c-detail-card">
                            <label>Updated At</label>
                            <p>${data.updated_at ? new Date(data.updated_at).toLocaleString() : '-'}</p>
                        </div>
                        </div>
                    </div>`;
                    $("#c-contact-details").html(html);
                },

                error: function() {
                    $("#c-contact-details").html(
                        `<div class="c-detail-card" style="color:red">Failed to load details.</div>`
                    );
                }
            });
        });

        $(document).on("click", "[data-c-close]", function() {
            $("#c-viewContactModal").removeClass("show");
        });
    </script>
    <script src="{{ URL::asset('panel-assets/js/core/datatable.js') }}?v={{ time() }}"></script>
@endsection
