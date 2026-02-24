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
                            <h2 class="content-header-title float-start mb-0">Team Members</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.dashboard') }}">Home</a>
                                    </li>
                                    <li class="breadcrumb-item active"><a href="#">Team Members</a></li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">
                    <a href="{{ route('admin.team.create') }}" class="btn btn-primary">
                        Add Team Member
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
                                <label class="form-label">Experience (Years)</label>
                                <select id="filter-year-of-experience" class="form-select">
                                    <option value="">All</option>
                                    @for ($i = 0; $i <= 10; $i++)
                                        @if ($i < 10)
                                            <option value="{{ $i }}">{{ $i }}
                                                year{{ $i > 1 ? 's' : '' }}</option>
                                        @else
                                            <option value="10+">10+ years</option>
                                        @endif
                                    @endfor
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
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Role</th>
                                                <th>Experience (Years)</th>
                                                <th>Phone</th>
                                                <th>Address</th>
                                                <th>Bio</th>
                                                <th data-search="false">Photo</th>
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
    <div id="c-viewTeamModal" class="c-modal">
        <div class="c-modal-dialog">
            <div class="c-modal-content">
                <!-- Header -->
                <div class="c-modal-header">
                    <h5 class="c-modal-title"><i class="bi bi-person-badge"></i> Team Member Details</h5>
                    <button class="c-close-btn" data-c-close>&times;</button>
                </div>
                <!-- Body -->
                <div class="c-modal-body" id="c-team-details">
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
        const sweetalert_delete_title = "Delete Team Member?";
        const sweetalert_change_status = "Change Status of Team Member";
        const sweetalert_change_priority_status = "Change Popularity Status of Team Member";

        // base form and data URLs
        const form_url = '/team';
        datatable_url = '/getDataTeamMembers';

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
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'role',
                    name: 'role'
                },
                {
                    data: 'experience_years',
                    name: 'experience_years'
                },
                {
                    data: 'phone',
                    name: 'phone'
                },
                {
                    data: 'address',
                    name: 'address'
                },
                {
                    data: 'bio',
                    name: 'bio'
                },
                {
                    data: 'icon',
                    name: 'icon',
                    orderable: false
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

        // Modal View
        $(document).on('click', '.btn-view', function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            const baseUrl = "{{ asset('uploads/team-member') }}/";

            $("#c-viewTeamModal").addClass("show");
            $("#c-team-details").html(`
            <div class="c-loader">
                <div class="c-spinner"></div>
                <span>Loading...</span>
            </div>
        `);

            $.ajax({
                url: '/admin/team-view/' + id,
                type: 'GET',
                success: function(response) {
                    let data = response.data;
                    let html = `
                    <div class="c-row">
                        <div class="c-col-6"><div class="c-detail-card"><label>Name</label><p>${data.name ?? '-'}</p></div></div>
                        <div class="c-col-6"><div class="c-detail-card"><label>Role</label><p>${data.role ?? '-'}</p></div></div>
                        <div class="c-col-6"><div class="c-detail-card"><label>Experience (Years)</label><p>${data.experience_years ?? '-'}</p></div></div>
                        <div class="c-col-12"><div class="c-detail-card"><label>Bio</label><p>${data.bio ?? '-'}</p></div></div>
                        <div class="c-col-12"><div class="c-detail-card"><label>Specialties</label><p>${
                            data.specialties 
                            ? JSON.parse(data.specialties).map(item => `<span class="c-include-badge">${item}</span>`).join(" ") 
                            : '-'
                        }</p></div></div>
                        <div class="c-col-12"><div class="c-detail-card"><label>Certifications</label><p>${
                            data.certifications 
                            ? JSON.parse(data.certifications).map(item => `<span class="c-include-badge">${item}</span>`).join(" ") 
                            : '-'
                        }</p></div></div>
                        <div class="c-col-6"><div class="c-detail-card"><label>Address</label><p>${data.address ?? '-'}</p></div></div>
                        <div class="c-col-6"><div class="c-detail-card"><label>City</label><p>${data.city ?? '-'}</p></div></div>
                        <div class="c-col-6"><div class="c-detail-card"><label>Taluko</label><p>${data.taluko ?? '-'}</p></div></div>
                        <div class="c-col-6"><div class="c-detail-card"><label>Village</label><p>${data.village ?? '-'}</p></div></div>
                        <div class="c-col-6"><div class="c-detail-card"><label>State</label><p>${data.state ?? '-'}</p></div></div>
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

                        <!-- Icon (Image Preview) -->
                        <div class="c-col-12">
                            <div class="c-detail-card text-center">
                                <label>Icon</label><br>
                                ${
                                    data.icon 
                                    ? `<img 
                                                                    src="${baseUrl + data.icon}" 
                                                                    alt="Team Icon" 
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
                    $("#c-team-details").html(html);
                },
                error: function() {
                    $("#c-team-details").html(
                        `<div class="c-detail-card" style="color:red">Failed to load details.</div>`
                    );
                }
            });
        });

        $(document).on("click", "[data-c-close]", function() {
            $("#c-viewTeamModal").removeClass("show");
        });
    </script>
    <script src="{{ URL::asset('panel-assets/js/core/datatable.js') }}?v={{ time() }}"></script>
@endsection
