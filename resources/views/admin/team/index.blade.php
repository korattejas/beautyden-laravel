@extends('admin.layouts.app')

@section('header_style_content')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
    :root {
        --mst-primary: #1a237e;
        --mst-primary-soft: rgba(26, 35, 126, 0.08);
        --mst-bg: #f8fafc;
        --mst-card-bg: #ffffff;
        --mst-text-main: #1e293b;
        --mst-text-muted: #64748b;
        --mst-radius: 12px;
        --mst-shadow: 0 4px 15px rgba(0,0,0,0.04);
        --mst-shadow-hover: 0 10px 25px rgba(0,0,0,0.08);
    }

    body {
        background-color: var(--mst-bg);
        font-family: 'Poppins', sans-serif;
    }

    .team-card-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
        padding: 1.5rem 0;
    }

    .team-member-card {
        background: var(--mst-card-bg);
        border-radius: var(--mst-radius);
        padding: 1.5rem;
        box-shadow: var(--mst-shadow);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        border: 1px solid #eef2f7;
    }

    .team-member-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--mst-shadow-hover);
        border-color: var(--mst-primary-soft);
    }

    .card-profile-header {
        text-align: center;
        margin-bottom: 1.25rem;
    }

    .avatar-wrapper {
        position: relative;
        display: inline-block;
        margin-bottom: 12px;
    }

    .circle-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #fff;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .initials-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: var(--mst-primary-soft);
        color: var(--mst-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.85rem;
        font-weight: 700;
        border: 4px solid #fff;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .status-badge-absolute {
        position: absolute;
        top: 0;
        right: 0;
        transform: translate(25%, -25%);
    }

    .card-info h5 {
        margin: 0;
        font-weight: 700;
        color: var(--mst-text-main);
        font-size: 1.3rem; /* Increased font size */
    }

    .card-info .role-label {
        font-size: 0.85rem; /* Increased font size */
        font-weight: 600;
        color: #7367f0;
        background: rgba(115, 103, 240, 0.08);
        padding: 4px 14px;
        border-radius: 50px;
        margin-top: 6px;
        display: inline-block;
    }

    .member-address {
        font-size: 0.95rem;
        color: #5e6d82;
        margin-top: 10px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 2.8em;
        line-height: 1.4;
        font-weight: 500;
    }

    .all-time-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
        margin: 1.5rem 0;
        padding: 12px 0;
        border-top: 1px solid #f1f5f9;
        border-bottom: 1px solid #f1f5f9;
    }

    .stat-box-mini {
        text-align: center;
    }

    .stat-box-mini label {
        display: block;
        font-size: 0.75rem; /* Increased font size */
        font-weight: 700;
        color: var(--mst-text-muted);
        text-transform: uppercase;
        margin-bottom: 3px;
    }

    .stat-box-mini span {
        font-size: 1.15rem; /* Increased font size */
        font-weight: 700;
        color: var(--mst-text-main);
    }

    .revenue-text { color: #059669 !important; } /* Deeper emerald for better contrast */

    .card-actions-strip {
        display: flex;
        gap: 8px;
        justify-content: center;
        margin-top: auto;
    }

    .btn-action-pill {
        width: 42px; /* Slightly larger */
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        transition: all 0.2s;
        border: 1px solid transparent;
        background: #f8fafc;
        color: #64748b;
    }

    .btn-action-pill:hover {
        transform: scale(1.1);
        background: #fff;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }

    .btn-edit:hover { color: #1a237e; border-color: #1a237e; }
    .btn-delete:hover { color: #ea5455; border-color: #ea5455; }
    .btn-status:hover { color: #28c76f; border-color: #28c76f; }
    .btn-priority:hover { color: #7367f0; border-color: #7367f0; }
    .btn-view-card:hover { color: #1e293b; border-color: #1e293b; }

    .search-input-group {
        width: 350px !important; /* Increased width */
        max-width: 350px !important;
        margin-right: 12px;
    }

    .search-input-group .input-group-text {
        background: #fff;
        border-right: none;
        color: var(--mst-text-muted);
        padding-left: 15px;
        border-radius: 10px 0 0 10px !important;
    }

    .search-input-group .form-control {
        border-left: none;
        padding: 12px 15px; /* Taller input */
        font-size: 0.95rem;
        border-radius: 0 10px 10px 0 !important;
        box-shadow: none !important;
    }

    .header-btn {
        padding: 12px 24px !important; /* Larger buttons */
        font-weight: 600 !important;
        border-radius: 10px !important;
        display: flex;
        align-items: center;
        gap: 8px;
        height: 48px; /* Fixed height for alignment */
    }

    /* Pagination Styling */
    .pagination-wrapper {
        margin-top: 2.5rem;
        display: flex;
        justify-content: center;
    }

    .pagination-wrapper .pagination {
        gap: 5px;
    }

    .pagination-wrapper .page-link {
        border-radius: 8px !important;
        border: none;
        padding: 8px 16px;
        font-weight: 600;
        color: var(--mst-text-main);
        transition: all 0.2s;
        box-shadow: var(--mst-shadow);
    }

    .pagination-wrapper .page-item.active .page-link {
        background-color: var(--mst-primary);
        color: #fff;
    }

    .pagination-wrapper .page-link:hover {
        background-color: var(--mst-primary-soft);
        color: var(--mst-primary);
    }

    /* Summary Boxes */
    .summary-stats-row {
        margin-bottom: 2rem;
    }

    .summary-box {
        background: #fff;
        border-radius: var(--mst-radius);
        padding: 1.25rem 1.5rem;
        box-shadow: var(--mst-shadow);
        display: flex;
        align-items: center;
        gap: 15px;
        border: 1px solid #eef2f7;
        transition: transform 0.3s;
    }

    .summary-box:hover {
        transform: translateY(-5px);
    }

    .summary-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .icon-active {
        background: rgba(40, 199, 111, 0.1);
        color: #28c76f;
    }

    .icon-inactive {
        background: rgba(234, 84, 85, 0.1);
        color: #ea5455;
    }

    .summary-info h3 {
        margin: 0;
        font-weight: 800;
        font-size: 1.5rem;
        color: var(--mst-text-main);
        line-height: 1.2;
    }

    .summary-info span {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--mst-text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Report Table */
    .report-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 8px;
    }
    .report-table th {
        background: var(--mst-bg);
        padding: 15px 18px;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.8rem;
        color: var(--mst-text-muted);
        text-align: left;
        border-radius: 8px;
    }
    .report-table td {
        padding: 18px;
        font-size: 1rem;
        font-weight: 600;
        color: var(--mst-text-main);
        background: #fff;
        border-top: 1px solid #f1f5f9;
        border-bottom: 1px solid #f1f5f9;
    }
    .report-table tr td:first-child { border-left: 1px solid #f1f5f9; border-radius: 10px 0 0 10px; }
    .report-table tr td:last-child { border-right: 1px solid #f1f5f9; border-radius: 0 10px 10px 0; }
    .report-table tr:nth-child(even) td { background: #fcfdfe; }
    .report-table tr:hover td { background: #f8faff; }
    
    .report-total-text {
        font-size: 1.15rem;
        font-weight: 800;
        color: #059669;
    }

    .report-modal-header-title {
        font-size: 1.25rem;
        font-weight: 700;
    }

</style>
@endsection

@section('content')
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-4 col-12 mb-2">
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
                <div class="content-header-right text-md-end col-md-8 col-12 d-md-flex align-items-center justify-content-end d-none">
                    <div class="input-group search-input-group shadow-sm">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" id="search-member" class="form-control" placeholder="Search team members..." value="{{ request('search') }}">
                    </div>
                    <a href="{{ route('admin.team.create') }}" class="btn btn-primary header-btn me-1">
                        <i class="bi bi-plus-lg"></i> Add Team Member
                    </a>
                    <div class="btn-group">
                        <button class="btn btn-outline-secondary dropdown-toggle header-btn" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        <div class="dropdown-menu dropdown-menu-end p-3" style="min-width: 320px;">
                            <div class="mb-2">
                                <label class="form-label">Status</label>
                                <select id="filter-status" class="form-select">
                                    <option value="">All</option>
                                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Is Popular</label>
                                <select id="filter-popular" class="form-select">
                                    <option value="">All</option>
                                    <option value="1" {{ request('popular') == '1' ? 'selected' : '' }}>High Priority</option>
                                    <option value="0" {{ request('popular') == '0' ? 'selected' : '' }}>Low Priority</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Experience (Years)</label>
                                <select id="filter-year-of-experience" class="form-select">
                                    <option value="">All</option>
                                    @for ($i = 0; $i <= 10; $i++)
                                        @php $val = ($i < 10) ? $i : '10+'; @endphp
                                        <option value="{{ $val }}" {{ request('year_of_experience') == (string)$val ? 'selected' : '' }}>
                                            {{ $i < 10 ? $i . ' year' . ($i > 1 ? 's' : '') : '10+ years' }}
                                        </option>
                                    @endfor
                                </select>
                            </div>

                            <div class="row mb-2">
                                <div class="col-6">
                                    <label class="form-label">Month</label>
                                    <select id="filter-month" class="form-select">
                                        <option value="">All Months</option>
                                        @foreach(range(1, 12) as $m)
                                            <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m, 1)) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Year</label>
                                    <select id="filter-year" class="form-select">
                                        <option value="">All Years</option>
                                        @foreach(range(date('Y'), 2020) as $y)
                                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Created Date</label>
                                <input type="date" id="filter-created-date" class="form-control" value="{{ request('created_date') }}">
                            </div>
                            <div class="d-flex justify-content-between pt-1">
                                <button id="btn-apply-card-filters" class="btn btn-primary w-100 me-1">
                                    Apply
                                </button>
                                <button id="btn-reset-card-filters" class="btn btn-outline-secondary w-100">
                                    Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-body">
                <!-- Summary Stats -->
                <div class="row summary-stats-row">
                    <div class="col-md-3">
                        <div class="summary-box">
                            <div class="summary-icon icon-active">
                                <i class="bi bi-person-check-fill"></i>
                            </div>
                            <div class="summary-info">
                                <h3>{{ $active_count }}</h3>
                                <span>Active Members</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="summary-box">
                            <div class="summary-icon icon-inactive">
                                <i class="bi bi-person-x-fill"></i>
                            </div>
                            <div class="summary-info">
                                <h3>{{ $inactive_count }}</h3>
                                <span>Inactive Members</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="team-card-grid">
                    @forelse($stats as $id => $stat)
                        <div class="team-member-card">
                            <div class="card-profile-header">
                                <div class="avatar-wrapper">
                                    @if($stat['member']->icon && file_exists(public_path('uploads/team-member/' . $stat['member']->icon)))
                                        <img src="{{ asset('uploads/team-member/' . $stat['member']->icon) }}" class="circle-avatar" alt="{{ $stat['member']->name }}">
                                    @else
                                        <div class="initials-avatar">
                                            {{ strtoupper(substr($stat['member']->name, 0, 2)) }}
                                        </div>
                                    @endif
                                    
                                    @if($stat['member']->status == 1)
                                        <div class="status-badge-absolute badge bg-success rounded-pill">Active</div>
                                    @else
                                        <div class="status-badge-absolute badge bg-danger rounded-pill">InActive</div>
                                    @endif
                                </div>
                                
                                <div class="card-info">
                                    <h5>{{ $stat['member']->name }}</h5>
                                    <span class="role-label">{{ $stat['member']->role ?? 'Team Member' }}</span>
                                    <p class="member-address" title="{{ $stat['member']->address }}">
                                        <i class="bi bi-geo-alt"></i> {{ $stat['member']->address ?: 'No address provided' }}
                                    </p>
                                </div>
                            </div>

                            <div class="all-time-stats">
                                <div class="stat-box-mini">
                                    <label>Bookings</label>
                                    <span>{{ $stat['total_appointments'] }}</span>
                                </div>
                                <div class="stat-box-mini">
                                    <label>Revenue</label>
                                    <span class="revenue-text">₹{{ number_format($stat['total_revenue'], 0) }}</span>
                                </div>
                                <div class="stat-box-mini">
                                    <label>Exp.</label>
                                    <span>{{ $stat['member']->experience_years ?? 0 }}y</span>
                                </div>
                            </div>

                            <div class="card-actions-strip">
                                <!-- View -->
                                <button type="button" class="btn-action-pill btn-view-card btn-view" data-id="{{ $stat['member']->id }}" title="View Details">
                                    <i class="bi bi-eye"></i>
                                </button>
                                
                                <!-- Edit -->
                                <a href="{{ route('admin.team.edit', encryptId($stat['member']->id)) }}" class="btn-action-pill btn-edit" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                <!-- Status Toggle -->
                                <button data-id="{{ $stat['member']->id }}" data-change-status="{{ $stat['member']->status == 1 ? 0 : 1 }}" 
                                    class="btn-action-pill btn-status status-change" title="{{ $stat['member']->status == 1 ? 'Make InActive' : 'Make Active' }}">
                                    <i class="bi bi-arrow-repeat"></i>
                                </button>

                                <!-- Report -->
                                <button type="button" class="btn-action-pill btn-report btn-report-view" data-id="{{ $stat['member']->id }}" title="Appointments Report">
                                    <i class="bi bi-file-earmark-text"></i>
                                </button>

                                <!-- Popularity Toggle -->
                                <button data-id="{{ $stat['member']->id }}" data-priority-change-status="{{ $stat['member']->is_popular == 1 ? 0 : 1 }}" 
                                    class="btn-action-pill btn-priority priority-status-change" title="{{ $stat['member']->is_popular == 1 ? 'Remove from Popular' : 'Mark as Popular' }}">
                                    <i class="bi {{ $stat['member']->is_popular == 1 ? 'bi-star-fill text-warning' : 'bi-star' }}"></i>
                                </button>

                                <!-- Delete -->
                                <button data-id="{{ $stat['member']->id }}" class="btn-action-pill btn-delete delete-single" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <div class="text-muted">No team members found.</div>
                        </div>
                    @endforelse
                </div>

                @if(isset($members) && $members->hasPages())
                    <div class="pagination-wrapper">
                        {{ $members->links('pagination::bootstrap-5') }}
                    </div>
                @endif
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

    <!-- Appointments Report Modal -->
    <div id="c-reportModal" class="c-modal">
        <div class="c-modal-dialog" style="max-width: 850px;">
            <div class="c-modal-content">
                <div class="c-modal-header">
                    <h5 class="c-modal-title report-modal-header-title"><i class="bi bi-file-earmark-bar-graph"></i> Appointments Report</h5>
                    <button class="c-close-btn" data-c-close-report>&times;</button>
                </div>
                <div class="c-modal-body" id="report-modal-body">
                    <div id="report-table-container">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-2 text-muted">Fetching appointments...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer_script_content')
    <script>
        // Card Filtering Logic
        $(document).on('click', '#btn-apply-card-filters', function() {
            let search = $('#search-member').val();
            let status = $('#filter-status').val();
            let popular = $('#filter-popular').val();
            let exp = $('#filter-year-of-experience').val();
            let date = $('#filter-created-date').val();
            let month = $('#filter-month').val();
            let year = $('#filter-year').val();

            let url = new URL(window.location.href);
            if (search) url.searchParams.set('search', search); else url.searchParams.delete('search');
            if (status !== "") url.searchParams.set('status', status); else url.searchParams.delete('status');
            if (popular !== "") url.searchParams.set('popular', popular); else url.searchParams.delete('popular');
            if (exp) url.searchParams.set('year_of_experience', exp); else url.searchParams.delete('year_of_experience');
            if (date) url.searchParams.set('created_date', date); else url.searchParams.delete('created_date');
            if (month) url.searchParams.set('month', month); else url.searchParams.delete('month');
            if (year) url.searchParams.set('year', year); else url.searchParams.delete('year');

            window.location.href = url.toString();
        });

        $(document).on('click', '#btn-reset-card-filters', function() {
            window.location.href = window.location.pathname;
        });

        // Search on Enter
        $('#search-member').on('keypress', function(e) {
            if(e.which == 13) {
                $('#btn-apply-card-filters').click();
            }
        });

        const sweetalert_delete_title = "Delete Team Member?";
        const sweetalert_change_status = "Change Status of Team Member";
        const sweetalert_change_priority_status = "Change Popularity Status of Team Member";

        // Override datatable.js actions for Card View
        $(document).off('click', '.delete-single');
        $(document).on('click', '.delete-single', function (e) {
            e.preventDefault();
            const value_id = $(this).data('id');
            Swal.fire({
                title: sweetalert_delete_title,
                text: sweetalert_delete_text,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: delete_button_text,
                cancelButtonText: cancel_button_text,
                customClass: { confirmButton: 'btn btn-primary', cancelButton: 'btn btn-outline-danger ms-1' },
                buttonsStyling: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    loaderView();
                    axios.delete(APP_URL + form_url + '/' + value_id).then(function (response) {
                        loaderHide();
                        Swal.fire({
                            title: 'Deleted!',
                            text: response.data.message,
                            icon: 'success',
                            confirmButtonText: 'OK',
                            customClass: { confirmButton: 'btn btn-primary' },
                            buttonsStyling: false
                        }).then(() => {
                            window.location.reload();
                        });
                    }).catch(function (error) {
                        notificationToast(error.response.data.message, 'warning');
                        loaderHide();
                    });
                }
            });
        });

        $(document).off('click', '.status-change');
        $(document).on('click', '.status-change', function (e) {
            e.preventDefault();
            const value_id = $(this).data('id');
            const status = $(this).data('change-status');
            Swal.fire({
                title: sweetalert_change_status,
                text: sweetalert_change_status_text,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: yes_change_it,
                cancelButtonText: cancel_button_text,
                customClass: { confirmButton: 'btn btn-primary', cancelButton: 'btn btn-outline-danger ms-1' },
                buttonsStyling: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    loaderView();
                    axios.get(APP_URL + form_url + '/status/' + value_id + '/' + status).then(function (response) {
                        loaderHide();
                        Swal.fire({
                            title: 'Updated!',
                            text: response.data.message,
                            icon: 'success',
                            confirmButtonText: 'OK',
                            customClass: { confirmButton: 'btn btn-primary' },
                            buttonsStyling: false
                        }).then(() => {
                            window.location.reload();
                        });
                    }).catch(function (error) {
                        notificationToast(error.response.data.message, 'warning');
                        loaderHide();
                    });
                }
            });
        });

        $(document).off('click', '.priority-status-change');
        $(document).on('click', '.priority-status-change', function (e) {
            e.preventDefault();
            const value_id = $(this).data('id');
            const status = $(this).data('priority-change-status');
            Swal.fire({
                title: sweetalert_change_priority_status,
                text: sweetalert_change_priority_status_text,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: yes_change_it,
                cancelButtonText: cancel_button_text,
                customClass: { confirmButton: 'btn btn-primary', cancelButton: 'btn btn-outline-danger ms-1' },
                buttonsStyling: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    loaderView();
                    axios.get(APP_URL + form_url + '/priority-status/' + value_id + '/' + status).then(function (response) {
                        loaderHide();
                        Swal.fire({
                            title: 'Updated!',
                            text: response.data.message,
                            icon: 'success',
                            confirmButtonText: 'OK',
                            customClass: { confirmButton: 'btn btn-primary' },
                            buttonsStyling: false
                        }).then(() => {
                            window.location.reload();
                        });
                    }).catch(function (error) {
                        notificationToast(error.response.data.message, 'warning');
                        loaderHide();
                    });
                }
            });
        });

        // base form and data URLs
        const form_url = '/team';

        // CSRF Setup for AJAX/Axios
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
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

        // Appointment Report Logic
        let currentReportId = null;

        $(document).on('click', '.btn-report-view', function() {
            currentReportId = $(this).data('id');
            $("#c-reportModal").addClass("show");
            loadReport(currentReportId, 1);
        });

        $(document).on("click", "[data-c-close-report]", function() {
            $("#c-reportModal").removeClass("show");
            $("#report-table-container").html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted">Fetching appointments...</p>
                </div>
            `);
        });

        function loadReport(id, page) {
            $.ajax({
                url: `/admin/team/appointments-report/${id}?page=${page}`,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        let html = `
                            <table class="report-table">
                                <thead>
                                    <tr>
                                        <th>Order No.</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Grand Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                        `;

                        if (response.data.length > 0) {
                            response.data.forEach(app => {
                                html += `
                                    <tr>
                                        <td><strong>${app.order_number}</strong></td>
                                        <td>${app.date}</td>
                                        <td>${app.time}</td>
                                        <td><span class="report-total-text">${app.total}</span></td>
                                    </tr>
                                `;
                            });
                        } else {
                            html += `<tr><td colspan="4" class="text-center py-4 text-muted">No completed appointments found.</td></tr>`;
                        }

                        html += `</tbody></table>`;
                        
                        // Add Pagination
                        if (response.pagination) {
                            html += `<div class="pagination-wrapper mt-3">${response.pagination}</div>`;
                        }

                        $("#report-table-container").html(html);
                    }
                },
                error: function() {
                    $("#report-table-container").html('<div class="alert alert-danger">Failed to load report.</div>');
                }
            });
        }

        // Handle report modal pagination clicks
        $(document).on('click', '#c-reportModal .pagination a', function(e) {
            e.preventDefault();
            let page = $(this).attr('href').split('page=')[1];
            loadReport(currentReportId, page);
        });
    </script>
@endsection
