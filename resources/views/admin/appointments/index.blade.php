@extends('admin.layouts.app')

@section('header_style_content')
<style>
    /* Premium Member Grid */
    .member-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 15px;
        max-height: 400px;
        overflow-y: auto;
        padding: 10px;
        margin-top: 15px;
    }

    .member-card {
        border: 2px solid transparent;
        border-radius: 12px;
        padding: 15px 10px;
        text-align: center;
        background: #f8fafc;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }

    .member-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        background: #fff;
    }

    .member-card.selected {
        border-color: #1a4a7a;
        background: #eff6ff;
        box-shadow: 0 4px 12px rgba(26, 74, 122, 0.12);
    }

    .member-avatar {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        margin: 0 auto 10px;
        object-fit: cover;
        border: 3px solid #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        background: #e2e8f0;
        font-weight: bold;
        color: #64748b;
        font-size: 1.2rem;
        background-size: cover;
        background-position: center;
    }

    .member-name {
        font-weight: 700;
        font-size: 1.2rem;
        color: #1e293b;
        margin-bottom: 6px;
        display: block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .member-role {
        font-size: 1rem;
        color: #64748b;
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
    }

    .member-experience {
        font-size: 0.9rem;
        color: #7367f0;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(115, 103, 240, 0.1);
        padding: 5px 15px;
        border-radius: 50px;
        margin-bottom: 8px;
    }

    .member-address {
        font-size: 0.9rem;
        color: #64748b;
        display: block;
        margin-top: 4px;
        line-height: 1.25;
        font-weight: 500;
        padding: 0 5px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .selection-indicator {
        position: absolute;
        top: 8px;
        right: 8px;
        width: 22px;
        height: 22px;
        background: #1a4a7a;
        color: #fff;
        border-radius: 50%;
        display: none;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .member-card.selected .selection-indicator {
        display: flex;
    }

    /* Modal Styling */
    #c-assignModal .c-modal-dialog {
        max-width: 650px;
    }

    .member-search-wrap {
        position: relative;
        margin-bottom: 10px;
    }

    .member-search-wrap i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
    }

    .member-search-wrap input#memberSearch {
        padding-left: 35px;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        padding-top: 10px;
        padding-bottom: 10px;
        background: #f8fafc;
    }

    .member-search-wrap input#memberSearch:focus {
        background: #fff;
        border-color: #1a4a7a;
        box-shadow: 0 0 0 3px rgba(26, 74, 122, 0.1);
    }

    /* Premium Detail Modal Enhancements */
    #c-viewAppointmentModal .c-modal-content {
        border: none;
        box-shadow: 0 20px 50px rgba(0,0,0,0.15);
    }

    #c-viewAppointmentModal .c-modal-header {
        background: linear-gradient(135deg, #102365 0%, #1a4a7a 100%);
        padding: 20px 24px;
    }

    .detail-section-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #7367f0;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .detail-info-card {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 16px;
        height: 100%;
        border: 1px solid #edf2f7;
        transition: all 0.3s ease;
    }

    .detail-info-card:hover {
        background: #fff;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        border-color: #7367f0;
    }

    .info-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 12px;
    }

    .info-item:last-child {
        margin-bottom: 0;
    }

    .info-icon {
        width: 32px;
        height: 32px;
        background: rgba(115, 103, 240, 0.1);
        color: #7367f0;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    .info-content label {
        display: block;
        font-size: 0.72rem;
        color: #82868b;
        font-weight: 700;
        margin-bottom: 2px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-content p {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 700;
        color: #1e293b;
        line-height: 1.3;
    }

    .premium-table-container {
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #edf2f7;
        margin-top: 20px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
    }

    .premium-table {
        width: 100%;
        border-collapse: collapse;
    }

    .premium-table thead th {
        background: #f8f9fa;
        color: #475569;
        font-weight: 800;
        font-size: 0.8rem;
        padding: 14px 16px;
        text-transform: uppercase;
        text-align: left;
        letter-spacing: 0.8px;
    }

    .premium-table tbody td {
        padding: 14px 16px;
        border-bottom: 1px solid #edf2f7;
        font-size: 0.9rem;
        color: #1e293b;
    }

    .premium-table tbody tr:last-child td {
        border-bottom: none;
    }

    .summary-box {
        background: #fdfdfd;
        border: 1px solid #edf2f7;
        border-radius: 12px;
        padding: 20px;
        margin-top: 20px;
        width: 100%;
        max-width: 350px;
        margin-left: auto;
    }

    .summary-line {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;
        font-size: 0.95rem;
        font-weight: 500;
        color: #475569;
    }

    .summary-line:last-child {
        margin-bottom: 0;
    }

    .summary-total {
        border-top: 2px dashed #dbdade;
        margin-top: 15px;
        padding-top: 15px;
        font-weight: 800;
        font-size: 1.4rem;
        color: #7367f0;
    }

    /* Premium Table Layout Optimization */
    .card-datatable {
        padding: 0.5rem;
        overflow-x: auto;
    }
    #table-appointments {
        width: 100% !important;
        border-spacing: 0 10px !important;
        border-collapse: separate !important;
    }
    #table-appointments thead th {
        border-bottom: 2px solid #ebe9f1 !important;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.82rem;
        padding: 12px 10px !important;
        color: #5e5873;
        background-color: #f8f8f8;
    }
    #table-appointments tbody tr {
        transition: all 0.25s ease;
        cursor: pointer;
    }
    #table-appointments tbody tr:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(115,103,240,0.1);
        background-color: #fcfaff !important;
    }
    #table-appointments td {
        padding: 10px !important;
        vertical-align: middle !important;
        border-top: none !important;
    }
    /* Premium Table Layout Optimization */
    .card-datatable {
        padding: 0.5rem;
    }
    #table-appointments {
        width: 100% !important;
        border-collapse: separate !important;
        border-spacing: 0 5px !important;
    }
    #table-appointments thead th {
        border-bottom: 2px solid #ebe9f1 !important;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.82rem;
        padding: 12px 10px !important;
        color: #5e5873;
        background-color: #f8f8f8;
    }
    #table-appointments tbody tr {
        transition: all 0.25s ease;
    }
    #table-appointments tbody tr:hover {
        background-color: #fcfaff !important;
    }
    #table-appointments td {
        padding: 15px 12px !important;
        vertical-align: middle !important;
        border-top: 1px solid #ebe9f1 !important;
        /* font-size: 1.15rem !important; */
    }
    
    /* Ensure dropdowns are not cut off */
    .dataTables_wrapper .table-responsive {
        overflow: visible !important;
    }
    .card-datatable {
        overflow: visible !important;
    }
    
    /* Z-index Fix for Dropdowns vs Badges */
    .dropdown-menu.show {
        z-index: 9999 !important;
        position: fixed !important; /* Force to top-level stacking context if possible */
    }
    
    .badge {
        z-index: 1 !important;
        position: relative;
    }
    
    .badge-glow {
        box-shadow: none !important; /* Remove glow if it causes rendering issues */
        z-index: 1 !important;
    }

    #table-appointments tbody tr {
        position: relative;
    }
    
    /* Make sure the row with the open dropdown is on top */
    #table-appointments tbody tr:has(.dropdown-menu.show) {
        z-index: 1000 !important;
    }
    /* Stat Filter Cards */
    .stat-filter-card {
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        border: 2px solid transparent !important;
    }
    .stat-filter-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
        border-color: rgba(115,103,240,0.3) !important;
    }
    .stat-filter-card.active-stat {
        background-color: #f0edff !important;
        border-color: #7367f0 !important;
        box-shadow: 0 4px 15px rgba(115,103,240,0.15) !important;
    }
    .stat-filter-card.active-stat h4 {
        color: #7367f0 !important;
    }

    /* Premium Period Filter Styling */
    .period-filter-card {
        background: #ffffff !important;
        border: none !important;
        border-radius: 20px !important;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05) !important;
        overflow: hidden;
    }
    .period-filter-header {
        background: #f8f9fa;
        padding: 8px 15px;
        border-bottom: 1px solid #f1f1f1;
    }
    .custom-pill-select {
        appearance: none;
        background-color: #f3f4f6 !important;
        border: 2px solid transparent !important;
        border-radius: 50px !important;
        padding: 8px 40px 8px 20px !important;
        font-weight: 600 !important;
        color: #4b5563 !important;
        cursor: pointer;
        transition: all 0.3s ease !important;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E") !important;
        background-repeat: no-repeat !important;
        background-position: right 15px center !important;
        background-size: 18px !important;
    }
    .custom-pill-select:hover {
        background-color: #e5e7eb !important;
        transform: translateY(-1px);
    }
    .custom-pill-select:focus {
        border-color: #7367f0 !important;
        background-color: #ffffff !important;
        box-shadow: 0 0 0 4px rgba(115,103,240,0.1) !important;
        outline: none;
    }
</style>
@endsection

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">Appointments</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}">{{ trans('admin_string.home') }}</a>
                                </li>
                                <li class="breadcrumb-item active"><a href="#">Appointments</a></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">
                <a href="{{ route('admin.appointments.create') }}" class="btn btn-primary">
                    Add Appointments
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
                                <option value="1">Pending</option>
                                <option value="2">Assigned</option>
                                <option value="3">Completed</option>
                                <option value="4">Rejected</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Appointment Date</label>
                            <input type="date" id="filter-appointment-date" class="form-control">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Appointment Time</label>
                            <input type="time" id="filter-appointment-time" class="form-control">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Created Date</label>
                            <input type="date" id="filter-created-date" class="form-control">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">City</label>
                            <select id="filter-city" class="form-select">
                                <option value="">All Cities</option>
                                @foreach ($cities as $city)
                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                                @endforeach
                            </select>
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
            <!-- Summary Boxes -->
            <!-- Premium Revenue Stats & Time Filter -->
            <div class="row g-2 mb-2">
                <div class="col-md-4">
                    <div class="card h-100 mb-0" style="background: linear-gradient(135deg, #7367f0 0%, #ce9ffc 100%); border: none; border-radius: 16px; box-shadow: 0 4px 20px rgba(115,103,240,0.25);">
                        <div class="card-body p-2 d-flex align-items-center">
                            <div class="avatar p-1 m-0 me-2" style="background: rgba(255,255,255,0.2); border-radius: 14px; width: 56px; height: 56px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-wallet2 text-white" style="font-size: 1.6rem;"></i>
                            </div>
                            <div>
                                <h2 class="text-white fw-bolder mb-0" style="font-size: 1.6rem;">₹{{ number_format($totalRevenue, 2) }}</h2>
                                <p class="text-white mb-0" style="opacity: 0.9; font-weight: 600; font-size: 0.9rem; letter-spacing: 0.5px;">TOTAL REVENUE</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 mb-0" style="background: linear-gradient(135deg, #28c76f 0%, #81fbb8 100%); border: none; border-radius: 16px; box-shadow: 0 4px 20px rgba(40,199,111,0.25);">
                        <div class="card-body p-2 d-flex align-items-center">
                            <div class="avatar p-1 m-0 me-2" style="background: rgba(255,255,255,0.2); border-radius: 14px; width: 56px; height: 56px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-bank2 text-white" style="font-size: 1.6rem;"></i>
                            </div>
                            <div>
                                <h2 class="text-white fw-bolder mb-0" style="font-size: 1.6rem;">₹{{ number_format($companyRevenue, 2) }}</h2>
                                <p class="text-white mb-0" style="opacity: 0.9; font-weight: 600; font-size: 0.9rem; letter-spacing: 0.5px;">COMPANY REVENUE</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 mb-0 period-filter-card">
                        <div class="period-filter-header d-flex align-items-center justify-content-center">
                            <i class="bi bi-calendar3 me-1 text-primary"></i>
                            <span class="fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 1px; color: #6e6b7b;">Select Period</span>
                        </div>
                        <div class="card-body p-2 d-flex align-items-center justify-content-center">
                            <div class="row g-1 w-100">
                                <div class="col-6">
                                    <select id="global-month-filter" class="form-select custom-pill-select w-100">
                                        <option value="all" {{ $month == 'all' ? 'selected' : '' }}>All Months</option>
                                        @for ($m = 1; $m <= 12; $m++)
                                            <option value="{{ sprintf('%02d', $m) }}" {{ $month == sprintf('%02d', $m) ? 'selected' : '' }}>
                                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-6">
                                    <select id="global-year-filter" class="form-select custom-pill-select w-100">
                                        <option value="all" {{ $year == 'all' ? 'selected' : '' }}>All Years</option>
                                        @php
                                            $currentYear = date('Y');
                                        @endphp
                                        @for ($y = $currentYear - 2; $y <= $currentYear + 1; $y++)
                                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Stats Row - Quick Filters -->
            <div class="row g-1 mb-2">
                <div class="col-md-2 col-sm-4 col-6">
                    <div class="card h-100 mb-0 stat-filter-card active-stat" data-type="total">
                        <div class="card-body d-flex align-items-center p-1">
                            <div class="avatar p-50 m-0" style="border-radius: 12px; background: #f3e8ff !important; width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-calendar-check" style="font-size: 1.2rem; color: #7c3aed;"></i>
                            </div>
                            <div class="ms-1">
                                <h4 class="fw-bolder mb-0" style="color: #1e293b;">{{ $totalAppointments }}</h4>
                                <p class="card-text mb-0" style="color: #64748b; font-weight: 500; font-size: 0.85rem;">Total</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 col-6">
                    <div class="card h-100 mb-0 stat-filter-card" data-type="today">
                        <div class="card-body d-flex align-items-center p-1">
                            <div class="avatar p-50 m-0" style="border-radius: 12px; background: #fff1f2 !important; width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-calendar-day" style="font-size: 1.2rem; color: #e11d48;"></i>
                            </div>
                            <div class="ms-1">
                                <h4 class="fw-bolder mb-0" style="color: #1e293b;">{{ $todayAppointments }}</h4>
                                <p class="card-text mb-0" style="color: #64748b; font-weight: 500; font-size: 0.85rem;">Today</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 col-6">
                    <div class="card h-100 mb-0 stat-filter-card" data-type="pending">
                        <div class="card-body d-flex align-items-center p-1">
                            <div class="avatar p-50 m-0" style="border-radius: 12px; background: #fff7ed !important; width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-clock-history" style="font-size: 1.2rem; color: #ea580c;"></i>
                            </div>
                            <div class="ms-1">
                                <h4 class="fw-bolder mb-0" style="color: #1e293b;">{{ $pendingAppointments }}</h4>
                                <p class="card-text mb-0" style="color: #64748b; font-weight: 500; font-size: 0.85rem;">Pending</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 col-6">
                    <div class="card h-100 mb-0 stat-filter-card" data-type="2" data-is-status="true">
                        <div class="card-body d-flex align-items-center p-1">
                            <div class="avatar p-50 m-0" style="border-radius: 12px; background: #e0f2fe !important; width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-person-check" style="font-size: 1.2rem; color: #0284c7;"></i>
                            </div>
                            <div class="ms-1">
                                <h4 class="fw-bolder mb-0" style="color: #1e293b;">{{ $assignedAppointments }}</h4>
                                <p class="card-text mb-0" style="color: #64748b; font-weight: 500; font-size: 0.85rem;">Assigned</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 col-6">
                    <div class="card h-100 mb-0 stat-filter-card" data-type="3" data-is-status="true">
                        <div class="card-body d-flex align-items-center p-1">
                            <div class="avatar p-50 m-0" style="border-radius: 12px; background: #dcfce7 !important; width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-check2-circle" style="font-size: 1.2rem; color: #16a34a;"></i>
                            </div>
                            <div class="ms-1">
                                <h4 class="fw-bolder mb-0" style="color: #1e293b;">{{ $completedAppointments }}</h4>
                                <p class="card-text mb-0" style="color: #64748b; font-weight: 500; font-size: 0.85rem;">Completed</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 col-6">
                    <div class="card h-100 mb-0 stat-filter-card" data-type="4" data-is-status="true">
                        <div class="card-body d-flex align-items-center p-1">
                            <div class="avatar p-50 m-0" style="border-radius: 12px; background: #fee2e2 !important; width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-x-circle" style="font-size: 1.2rem; color: #dc2626;"></i>
                            </div>
                            <div class="ms-1">
                                <h4 class="fw-bolder mb-0" style="color: #1e293b;">{{ $rejectedAppointments }}</h4>
                                <p class="card-text mb-0" style="color: #64748b; font-weight: 500; font-size: 0.85rem;">Rejected</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Column Search -->
            <section id="column-search-datatable">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-datatable">
                                <table class="dt-column-search table w-100 dataTable" id="table-appointments">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            {{-- <th>Service Category</th> --}}
                                            {{-- <th>Service</th> --}}
                                            <th>Order Number</th>
                                            <th>Client</th>
                                            <th>Phone</th>
                                            <th>Schedule</th>
                                            <th>Assigned To</th>
                                            <th>Grand Total</th>
                                            <th>Company Amount</th>
                                            <th data-search="false">Status</th>
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
<!-- Assign Team Members Modal -->
<div id="c-assignModal" class="c-modal">
    <div class="c-modal-dialog">
        <div class="c-modal-content">

            <!-- Header -->
            <div class="c-modal-header">
                <h5 class="c-modal-title"><i class="bi bi-people-fill"></i> Assign Team Members</h5>
                <button class="c-close-btn" data-c-close>&times;</button>
            </div>

            <!-- Body -->
            <div class="c-modal-body">
                <form id="assignForm">
                    <input type="hidden" id="value_id" name="value_id">
                    
                    <div class="member-search-wrap">
                        <i class="bi bi-search"></i>
                        <input type="text" id="memberSearch" class="form-control shadow-none" placeholder="Search team members...">
                    </div>

                    <div class="member-grid" id="memberGrid">
                        @foreach ($teamMembers as $member)
                        <div class="member-card" data-id="{{ $member->id }}" data-name="{{ strtolower($member->name) }}">
                            <div class="selection-indicator"><i class="bi bi-check"></i></div>
                            <div class="member-avatar" 
                                style="{{ $member->icon && file_exists(public_path('uploads/team-member/' . $member->icon)) 
                                    ? 'background-image: url(' . asset('uploads/team-member/' . $member->icon) . ')' 
                                    : '' }}">
                                @if(!($member->icon && file_exists(public_path('uploads/team-member/' . $member->icon))))
                                    {{ strtoupper(substr($member->name, 0, 1)) }}
                                @endif
                            </div>
                            <span class="member-name">{{ $member->name }}</span>
                            <span class="member-role">{{ $member->role ?? 'Professional' }}</span>
                            <div class="member-experience">
                                <i class="bi bi-briefcase" style="font-size: 0.65rem;"></i> {{ $member->experience_years ?? 0 }} Years Exp.
                            </div>
                            <div class="member-address">
                                <i class="bi bi-geo-alt-fill" style="font-size: 0.75rem; color: #ef4444;"></i> 
                                {{ $member->address ? $member->address : ($member->city ? $member->city : 'Location N/A') }}
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <select id="team_members" name="team_members[]" class="d-none" multiple>
                        @foreach ($teamMembers as $member)
                        <option value="{{ $member->id }}">{{ $member->name }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            <!-- Footer -->
            <div class="c-modal-footer">
                <button class="c-btn c-btn-secondary" data-c-close>
                    <i class="bi bi-x-circle"></i> Close
                </button>
                <button type="button" id="saveMembers" class="c-btn c-btn-primary">
                    <i class="bi bi-check-circle"></i> Save
                </button>
            </div>

        </div>
    </div>
</div>

<div id="c-viewAppointmentModal" class="c-modal">
    <div class="c-modal-dialog" style="max-width: 850px;">
        <div class="c-modal-content">

            <!-- Header -->
            <div class="c-modal-header">
                <h5 class="c-modal-title" style="margin:0;">
                    <i class="bi bi-stars"></i> Appointment Insights
                </h5>

                <div style="display:flex; align-items:center; gap:12px;">

                    <!-- 🔥 Copy Button -->
                    <button id="copyAppointmentData"
                        style="
                            background: rgba(255,255,255,0.15);
                            color:#fff;
                            border: 1px solid rgba(255,255,255,0.3);
                            padding:8px 16px;
                            border-radius:8px;
                            font-size:13px;
                            font-weight: 600;
                            cursor:pointer;
                            display:flex;
                            align-items:center;
                            gap:8px;
                            transition:all 0.3s ease;
                            backdrop-filter: blur(5px);
                        "
                        onmouseover="this.style.background='rgba(255,255,255,0.25)'"
                        onmouseout="this.style.background='rgba(255,255,255,0.15)'">

                        <i class="bi bi-clipboard2-check"></i> <span>Copy Details</span>
                    </button>

                    <!-- Close Button -->
                    <button class="c-close-btn" data-c-close 
                        style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.2); border-radius: 50%; font-size: 20px; transition: all 0.3s;">
                        &times;
                    </button>

                </div>
            </div>

            <!-- Body -->
            <div class="c-modal-body" id="c-appointment-details" style="background: #fff; padding: 24px;">
                <div class="c-loader">
                    <div class="c-spinner"></div>
                    <span>Revealing information...</span>
                </div>
            </div>

            <!-- Footer -->
            <div class="c-modal-footer" style="background: #f8f9fa; border-top: 1px solid #edf2f7; padding: 16px 24px;">
                <div style="display: flex; align-items: center; gap: 8px; color: #82868b; font-size: 0.85rem;">
                    <i class="bi bi-shield-check text-success"></i>
                    <span>Verified Appointment Record</span>
                </div>
                <button class="c-btn" data-c-close style="background: #444050; border-radius: 8px; padding: 10px 20px;">
                    <i class="bi bi-x-lg me-1"></i> Close View
                </button>
            </div>

        </div>
    </div>
</div>

@endsection

@section('footer_script_content')
<script>
    const sweetalert_delete_title = "Delete Appointment?";
    const sweetalert_change_status = "Change Status of Appointment";
    const form_url = '/appointments';
    $(document).on('change', '#global-month-filter, #global-year-filter', function() {
        let month = $('#global-month-filter').val();
        let year = $('#global-year-filter').val();
        let url = new URL(window.location.href);
        url.searchParams.set('month', month);
        url.searchParams.set('year', year);
        window.location.href = url.toString();
    });

    window.currentFilterType = 'total';
    $(document).on('click', '.stat-filter-card', function() {
        $('.stat-filter-card').removeClass('active-stat');
        $(this).addClass('active-stat');
        window.currentFilterType = $(this).data('type');
        
        // Show/hide status filter based on whether we are using status-specific filter
        if ($(this).data('is-status')) {
            $('#filter-status').val($(this).data('type'));
        } else if (window.currentFilterType !== 'total') {
            $('#filter-status').val(''); // Reset status if using other filter
        }
        
        $('.dt-column-search').DataTable().ajax.reload();
    });

    datatable_url = '/getDataAppointments';
    
    // Member Selection Logic
    $(document).on('click', '.member-card', function() {
        let card = $(this);
        let id = card.data('id');
        let select = $('#team_members');
        
        card.toggleClass('selected');
        
        // Sync with hidden select
        let option = select.find(`option[value="${id}"]`);
        if (card.hasClass('selected')) {
            option.prop('selected', true);
        } else {
            option.prop('selected', false);
        }
    });

    // Search Filtering
    $(document).on('keyup', '#memberSearch', function() {
        let value = $(this).val().toLowerCase();
        $('.member-card').each(function() {
            let name = $(this).data('name');
            if (name.includes(value)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    $(document).on('click', '.assign-member', function() {
        // Reset selections when opening modal
        $('.member-card').removeClass('selected');
        $('#team_members').val([]);
        $('#memberSearch').val('');
        $('.member-card').show();
        
        const value_id = $(this).data('id');
        const currentMembers = $(this).data('members'); // Comma-separated IDs

        if (currentMembers) {
            const memberIds = currentMembers.toString().split(',');
            memberIds.forEach(id => {
                $(`.member-card[data-id="${id}"]`).addClass('selected');
                $(`#team_members option[value="${id}"]`).prop('selected', true);
            });
        }

        $('#value_id').val(value_id);
        $("#c-assignModal").addClass("show");
    });

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
            // {
            //     data: 'service_category_name',
            //     name: 'service_category_name'
            // },
            // {
            //     data: 'service_name',
            //     name: 'service_name'
            // },
            {
                data: 'order_number',
                name: 'order_number'
            },
            {
                data: 'first_name',
                name: 'first_name'
            },
            // {
            //     data: 'last_name',
            //     name: 'last_name'
            // },
            {
                data: 'phone',
                name: 'phone'
            },
            {
                data: 'schedule',
                name: 'appointment_date'
            },
            {
                data: 'assigned_to_name',
                name: 'assigned_to_name'
            },
            {
                data: 'grand_total',
                name: 'grand_total'
            },
            {
                data: 'company_amount',
                name: 'company_amount'
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
    });

    $(document).on('click', '#saveMembers', function() {
        let selected = $('#team_members').val();
        let value_id = $('#value_id').val();

        $.ajax({
            url: 'appointments/assign_member',
            method: 'POST',
            data: {
                value_id: value_id,
                members: selected
            },
            success: function(res) {
                location.reload();
                $('#c-assignModal').removeClass("show");
            }
        });
    });

    $(document).on("click", "[data-c-close]", function() {
        $("#c-assignModal").removeClass("show");
    });

    let currentAppointmentData = null

    $(document).on('click', '.btn-view', function(e) {
        e.preventDefault();
        let id = $(this).data('id');

        $("#c-viewAppointmentModal").addClass("show");
        $("#c-appointment-details").html(
            `<div class="c-loader"><div class="c-spinner"></div><span>Loading...</span></div>`
        );

        $.ajax({
            url: '/admin/appointments-view/' + id,
            type: 'GET',
            success: function(response) {

                let data = response.data;
                currentAppointmentData = data;

                let client = data.client || {};
                let appointment = data.appointment || {};
                let services = data.services || [];
                let summary = data.summary || {};

                let servicesHtml = '';

                if (services.length > 0) {
                    servicesHtml = `
                        <div class="detail-section-label mt-4">
                            <i class="bi bi-layers-half"></i> Service Inventory
                        </div>
                        <div class="premium-table-container">
                            <table class="premium-table">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th>Name</th>
                                        <th style="text-align: right;">Price</th>
                                        <th style="text-align: center;">Qty</th>
                                        <th style="text-align: right;">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;

                    services.forEach((s, index) => {
                        servicesHtml += `
                            <tr>
                                <td style="text-align: center; color: #82868b;">${index + 1}</td>
                                <td style="font-weight: 700; color: #1e293b;">
                                    ${s.name ?? '-'}
                                    <div style="font-size: 0.75rem; color: #82868b; text-transform: capitalize; font-weight: 500;">${s.type ?? 'Standard'}</div>
                                </td>
                                <td style="text-align: right; font-weight: 600;">₹${parseFloat(s.price).toFixed(2)}</td>
                                <td style="text-align: center; font-weight: 600;">${s.qty}</td>
                                <td style="text-align: right; font-weight: 800; color: #7367f0; font-size: 1rem;">
                                    ₹${parseFloat(s.total).toFixed(2)}
                                </td>
                            </tr>
                        `;
                    });
                    servicesHtml += `</tbody></table></div>`;
                }

                $("#c-appointment-details").html(`
                    <div class="row">
                        <!-- Client Contact Card -->
                        <div class="col-md-6 mb-3">
                            <div class="detail-section-label">
                                <i class="bi bi-person-circle"></i> Client Information
                            </div>
                            <div class="detail-info-card">
                                <div class="info-item">
                                    <div class="info-icon"><i class="bi bi-person"></i></div>
                                    <div class="info-content">
                                        <label>Full Name</label>
                                        <p>${client.first_name ?? '-'} ${client.last_name ?? ''}</p>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-icon"><i class="bi bi-envelope"></i></div>
                                    <div class="info-content">
                                        <label>Email Address</label>
                                        <p>${client.email ?? 'Not provided'}</p>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-icon"><i class="bi bi-telephone"></i></div>
                                    <div class="info-content">
                                        <label>Phone Number</label>
                                        <p>${client.phone ?? 'Not provided'}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Schedule & Location Card -->
                        <div class="col-md-6 mb-3">
                            <div class="detail-section-label">
                                <i class="bi bi-geo-alt-fill"></i> Schedule & Logistics
                            </div>
                            <div class="detail-info-card" style="border-left: 4px solid #7367f0;">
                                <div class="info-item">
                                    <div class="info-icon" style="background: rgba(115, 103, 240, 0.2);"><i class="bi bi-calendar-check"></i></div>
                                    <div class="info-content">
                                        <label>Appointment Date</label>
                                        <p>${appointment.date ?? '-'}</p>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-icon" style="background: rgba(115, 103, 240, 0.2);"><i class="bi bi-clock-history"></i></div>
                                    <div class="info-content">
                                        <label>Reserved Time</label>
                                        <p>${appointment.time ?? '-'}</p>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-icon" style="background: rgba(115, 103, 240, 0.2);"><i class="bi bi-geo"></i></div>
                                    <div class="info-content">
                                        <label>Service Location</label>
                                        <p>${appointment.address ?? 'On-site'}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    ${servicesHtml}

                    <div class="summary-box">
                        <div class="summary-line">
                            <span>Subtotal</span>
                            <span style="font-weight: 700; color: #1e293b;">₹${parseFloat(summary.sub_total || 0).toFixed(2)}</span>
                        </div>
                        <div class="summary-line">
                            <span>Traveling Charges</span>
                            <span style="font-weight: 700; color: #1e293b;">+ ₹${parseFloat(summary.travel_charges || 0).toFixed(2)}</span>
                        </div>
                        <div class="summary-line" style="background: #f0edff; margin: 0 -10px 10px; padding: 10px; border-radius: 8px;">
                            <span style="font-weight: 700;">Company Amount</span>
                            <span style="font-weight: 800; color: #7367f0;">₹${parseFloat(data.company_amount || 0).toFixed(2)}</span>
                        </div>
                        <div class="summary-line text-danger">
                            <span style="font-weight: 600;">Discount (${summary.discount_percent || 0}%)</span>
                            <span style="font-weight: 700;">- ₹${parseFloat(summary.discount_amount || 0).toFixed(2)}</span>
                        </div>
                        <div class="summary-line summary-total">
                            <span>Grand Total</span>
                            <span>₹${parseFloat(summary.grand_total || 0).toFixed(2)}</span>
                        </div>
                    </div>

                    <div class="mt-4 p-3" style="background: #fff8eb; border-radius: 12px; border: 1px solid #ffe5b4; box-shadow: 0 4px 12px rgba(255, 159, 67, 0.08);">
                        <div class="detail-section-label" style="color: #ff9f43; margin-bottom: 8px;">
                            <i class="bi bi-sticky"></i> Special Instructions
                        </div>
                        <p style="margin:0; font-size: 0.95rem; color: #1e293b; font-weight: 600; font-style: italic; line-height: 1.5;">
                            "${appointment.special_notes ?? 'No special instructions provided for this appointment.'}"
                        </p>
                    </div>
                `);

                // Update copy button text
                $("#copyAppointmentData").html('<i class="bi bi-clipboard2-check"></i> <span>Copy Details</span>');
            },
            error: function() {
                $("#c-appointment-details").html(
                    `<div class="text-center py-5 text-danger"><i class="bi bi-exclamation-triangle fs-1"></i><p>Failed to load data</p></div>`
                );
            }
        });
    });

    $(document).on('click', '#copyAppointmentData', function() {
        if (!currentAppointmentData) return;

        let d = currentAppointmentData;
        let client = d.client || {};
        let appointment = d.appointment || {};
        let summary = d.summary || {};
        let services = d.services || [];

        let text = `Hello ${client.first_name}! 👋\n\n`;
        text += `Your appointment with BeautyDen has been successfully booked. 💖\n\n`;
        
        text += `📋 Appointment Details\n`;
        text += `---------------------------------\n`;
        text += `Order: ${d.order_number}\n`;
        text += `Customer: ${client.first_name} ${client.last_name || ''}\n`;
        text += `Phone: ${client.phone}\n`;
        text += `City: ${d.city_name || 'Ahmedabad'}\n`;
        text += `Date: ${appointment.date}\n`;
        text += `Time: ${appointment.time}\n`;
        text += `Address: ${appointment.address}\n\n`;

        text += `🛍 Services:\n`;
        text += `---------------------------------\n`;
        services.forEach((s) => {
            text += `${s.name} (${s.qty} x ₹${parseFloat(s.price).toFixed(0)}) = ₹${parseFloat(s.total).toFixed(0)}\n`;
        });
        text += `---------------------------------\n`;
        
        text += `Subtotal: ₹${parseFloat(summary.sub_total || 0).toFixed(2)}\n`;
        text += `Discount: - ₹${parseFloat(summary.discount_amount || 0).toFixed(2)}\n`;
        text += `Travel Charges: + ₹${parseFloat(summary.travel_charges || 0).toFixed(2)}\n`;
        text += `Grand Total: ₹${parseFloat(summary.grand_total || 0).toFixed(2)}\n\n`;

        text += `We’ll review your booking and confirm shortly.\n`;
        text += `Thank you for choosing BeautyDen 💖\n\n`;
        text += `📞 Support: +91 95747 58282`;

        navigator.clipboard.writeText(text).then(() => {
            let btn = $('#copyAppointmentData');
            btn.html('<i class="bi bi-check-circle"></i> Copied');
            btn.css('background', '#28a745');

            setTimeout(() => {
                btn.html('<i class="bi bi-clipboard2-check"></i> <span>Copy Details</span>');
                btn.css('background', 'rgba(255,255,255,0.15)');
            }, 2000);
        });
    });




    $(document).on("click", "[data-c-close]", function() {
        $("#c-viewAppointmentModal").removeClass("show");
    });

    // Inline edit for Company Amount
    $(document).on('click', '.amount-display', function() {
        let wrapper = $(this).closest('.editable-amount-wrapper');
        $(this).addClass('d-none');
        wrapper.find('.amount-input').removeClass('d-none').focus();
    });

    $(document).on('blur', '.amount-input', function() {
        saveInlineAmount($(this));
    });

    $(document).on('keypress', '.amount-input', function(e) {
        if (e.which == 13) {
            $(this).blur();
        }
    });

    function saveInlineAmount(input) {
        let wrapper = input.closest('.editable-amount-wrapper');
        let id = wrapper.data('id');
        let amount = input.val();
        let display = wrapper.find('.amount-display');

        if (input.hasClass('updating')) return;
        input.addClass('updating');

        $.ajax({
            url: '{{ route("admin.appointments.updateAmount") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                id: id,
                amount: amount
            },
            success: function(response) {
                if (response.success) {
                    display.text(response.formatted_amount);
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
                input.addClass('d-none').removeClass('updating');
                display.removeClass('d-none');
            },
            error: function(xhr) {
                toastr.error('Failed to update amount');
                input.addClass('d-none').removeClass('updating');
                display.removeClass('d-none');
            }
        });
    }

    // Row click to open view modal
    $('#table-appointments tbody').on('click', 'tr', function (e) {
        // Don't trigger if clicking on action items or inputs
        if ($(e.target).closest('.dropdown, .amount-input, .amount-display, button, a').length) {
            return;
        }
        
        // Find the view button in this row and trigger it
        let viewBtn = $(this).find('.btn-view');
        if (viewBtn.length) {
            viewBtn.click();
        } else {
            // Fallback for DataTables data-driven access
            let data = $('#table-appointments').DataTable().row(this).data();
            if (data && data.id) {
                // If it's a manual click without the btn-view in DOM (rare in DT)
                viewAppointment(data.id);
            }
        }
    });

    function viewAppointment(id) {
        $.ajax({
            url: "{{ url('admin/appointments-view') }}/" + id,
            method: 'GET',
            success: function(response) {
                renderAppointmentDetail(response.data);
                $("#c-viewAppointmentModal").addClass("show");
            }
        });
    }
</script>
<script src="{{ URL::asset('panel-assets/js/core/datatable.js') }}?v={{ time() }}"></script>
@endsection