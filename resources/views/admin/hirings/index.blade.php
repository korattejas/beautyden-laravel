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
                            <h2 class="content-header-title float-start mb-0">Hirings</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.dashboard') }}">Home</a>
                                    </li>
                                    <li class="breadcrumb-item active"><a href="#">Hirings</a></li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">
                    <a href="{{ route('admin.hirings.create') }}" class="btn btn-primary">
                        Add Hiring
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
                                <label class="form-label">Is Featured</label>
                                <select id="filter-popular" class="form-select">
                                    <option value="">All</option>
                                    <option value="1">Featured</option>
                                    <option value="0">Normal</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Min Experience (Years)</label>
                                <input type="number" id="filter-min-exp" class="form-control" placeholder="e.g. 1">
                            </div>

                            <div class="mb-2">
                                <label class="form-label">Max Experience (Years)</label>
                                <input type="number" id="filter-max-exp" class="form-control" placeholder="e.g. 5">
                            </div>

                            <div class="mb-2">
                                <label class="form-label">Salary Range</label>
                                <input type="text" id="filter-salary" class="form-control"
                                    placeholder="e.g. ₹20000-₹40000">
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
                                                <th>Title</th>
                                                <th>City</th>
                                                <th data-stuff="Fresher,Experienced,Expert">Experience Level</th>
                                                <th data-stuff="Active,Inactive">Status</th>
                                                <th data-stuff="Yes,No">Is Popular</th>
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
    <div id="c-viewHiringModal" class="c-modal">
        <div class="c-modal-dialog">
            <div class="c-modal-content">
                <div class="c-modal-header">
                    <h5 class="c-modal-title"><i class="bi bi-briefcase"></i> Hiring Details</h5>
                    <button class="c-close-btn" data-c-close>&times;</button>
                </div>
                <div class="c-modal-body" id="c-hiring-details">
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
        const sweetalert_delete_title = "Delete Hiring?";
        const sweetalert_change_status = "Change Status of Hiring";
        const sweetalert_change_priority_status = "Change Popular Status of Hiring";

        const form_url = '/hirings';
        datatable_url = '/getDataHirings';

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
                    data: 'title',
                    name: 'title'
                },
                {
                    data: 'city',
                    name: 'city'
                },
                {
                    data: 'experience_level',
                    name: 'experience_level',
                    render: function(data) {
                        switch (data) {
                            case 1:
                                return "Fresher";
                            case 2:
                                return "Experienced";
                            case 3:
                                return "Expert";
                            default:
                                return "-";
                        }
                    }
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

        // Hiring View Modal
        $(document).on('click', '.btn-view', function(e) {
            e.preventDefault();
            let id = $(this).data('id');

            $("#c-viewHiringModal").addClass("show");
            $("#c-hiring-details").html(
                `<div class="c-loader"><div class="c-spinner"></div><span>Loading...</span></div>`);

            $.ajax({
                url: '/admin/hirings-view/' + id,
                type: 'GET',
                success: function(response) {
                    let data = response.data;

                    let experience_level = {
                        1: "Fresher",
                        2: "Experienced",
                        3: "Expert"
                    } [data.experience_level] ?? "-";

                    let hiring_type = {
                        1: "Full-time",
                        2: "Part-time",
                        3: "Internship",
                        4: "Work from home"
                    } [data.hiring_type] ?? "-";

                    let gender_pref = {
                        1: "Female",
                        2: "Male",
                        3: "Any"
                    } [data.gender_preference] ?? "-";

                    let html = `
                    <div class="c-row">
                        <div class="c-col-6"><div class="c-detail-card"><label>Title</label><p>${data.title ?? '-'}</p></div></div>
                        <div class="c-col-6"><div class="c-detail-card"><label>City</label><p>${data.city ?? '-'}</p></div></div>
                        <div class="c-col-12"><div class="c-detail-card"><label>Description</label><p>${data.description ?? '-'}</p></div></div>
                        
                        <div class="c-col-6"><div class="c-detail-card"><label>Min Experience</label><p>${data.min_experience ?? '-'}</p></div></div>
                        <div class="c-col-6"><div class="c-detail-card"><label>Max Experience</label><p>${data.max_experience ?? '-'}</p></div></div>
                        
                        <div class="c-col-6"><div class="c-detail-card"><label>Experience Level</label><p>${experience_level}</p></div></div>
                        <div class="c-col-6"><div class="c-detail-card"><label>Hiring Type</label><p>${hiring_type}</p></div></div>
                        
                        <div class="c-col-6"><div class="c-detail-card"><label>Gender Preference</label><p>${gender_pref}</p></div></div>
                        <div class="c-col-6"><div class="c-detail-card"><label>Salary Range</label><p>${data.salary_range ?? '-'}</p></div></div>
                        
                        <div class="c-col-12"><div class="c-detail-card"><label>Required Skills</label><p>${
                            data.required_skills ? JSON.parse(data.required_skills).map(skill => `<span class="c-include-badge">${skill}</span>`).join(" ") : '-'
                        }</p></div></div>
                        
                        <div class="c-col-6"><div class="c-detail-card"><label>Popular</label>
                            <p>${data.is_popular == 1 ? '<span class="badge badge-glow bg-primary">Yes</span>' : '<span class="badge badge-glow bg-secondary">No</span>'}</p>
                        </div></div>
                        
                        <div class="c-col-6"><div class="c-detail-card"><label>Status</label>
                            <p>${data.status == 1 ? '<span class="badge badge-glow bg-success">Active</span>' : '<span class="badge badge-glow bg-danger">InActive</span>'}</p>
                        </div></div>
                        
                        <div class="c-col-6"><div class="c-detail-card"><label>Created At</label><p>${data.created_at ? new Date(data.created_at).toLocaleString() : '-'}</p></div></div>
                        <div class="c-col-6"><div class="c-detail-card"><label>Updated At</label><p>${data.updated_at ? new Date(data.updated_at).toLocaleString() : '-'}</p></div></div>
                    </div>
                `;

                    $("#c-hiring-details").html(html);
                },
                error: function() {
                    $("#c-hiring-details").html(
                        `<div class="c-detail-card" style="color:red">Failed to load details.</div>`
                    );
                }
            });
        });

        $(document).on("click", "[data-c-close]", function() {
            $("#c-viewHiringModal").removeClass("show");
        });
    </script>
    <script src="{{ URL::asset('panel-assets/js/core/datatable.js') }}?v={{ time() }}"></script>
@endsection
