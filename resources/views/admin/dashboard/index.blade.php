@extends('admin.layouts.app')

@section('header_style_content')
<style>
    :root {
        --db-black: #0a0a0a;
        --db-blue: #102365;
        --db-blue-bright: #1e40af;
        --db-blue-light: #3b82f6;
        --db-blue-soft: #eff6ff;
        --db-white: #ffffff;
        --db-surface: #f9fafb;
        --db-border: #e5e7eb;
        --db-text: #111827;
        --db-muted: #6b7280;
        --db-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        --db-radius: 14px;
        --db-transition: 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }

    #beautyden-dashboard {
        padding: 1.75rem 2rem 2.5rem;
        background: var(--db-surface);
        min-height: 100vh;
    }

    /* ── Welcome Header ── */
    .db-welcome {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 1.25rem;
        margin-bottom: 1.75rem;
        padding: 1.5rem 1.75rem;
        background: var(--db-white);
        border: 1px solid var(--db-border);
        border-radius: var(--db-radius);
        box-shadow: var(--db-shadow);
    }

    .db-welcome-text h1 {
        font-size: 1.65rem;
        font-weight: 700;
        color: var(--db-black);
        margin: 0 0 0.25rem;
        letter-spacing: -0.02em;
    }

    .db-welcome-text h1 span { color: var(--db-blue); }

    .db-welcome-text p {
        margin: 0;
        color: var(--db-muted);
        font-size: 0.9rem;
        font-weight: 500;
    }

    .db-date-filter {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: var(--db-surface);
        border: 1px solid var(--db-border);
        border-radius: 50px;
        padding: 0.35rem 0.5rem 0.35rem 1rem;
    }

    .db-date-filter label {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--db-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0;
        white-space: nowrap;
    }

    .db-date-input {
        background: var(--db-white);
        border: 1px solid var(--db-border);
        border-radius: 8px;
        padding: 0.4rem 0.65rem;
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--db-text);
        width: 115px;
        text-align: center;
        outline: none;
        transition: border-color var(--db-transition);
    }

    .db-date-input:focus { border-color: var(--db-blue-bright); }

    .db-date-sep { color: var(--db-muted); font-weight: 600; font-size: 0.85rem; }

    .btn-db-refresh {
        background: var(--db-blue);
        color: #fff;
        border: none;
        border-radius: 50px;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background var(--db-transition), transform var(--db-transition);
        flex-shrink: 0;
    }

    .btn-db-refresh:hover {
        background: var(--db-blue-bright);
        transform: rotate(90deg);
    }

    /* ── Alert Banner ── */
    .db-alert {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 0.875rem 1.25rem;
        background: #fff;
        border: 1px solid #fecaca;
        border-left: 4px solid #dc2626;
        border-radius: var(--db-radius);
        margin-bottom: 1.5rem;
        box-shadow: var(--db-shadow);
    }

    .db-alert-icon {
        width: 42px;
        height: 42px;
        background: #fef2f2;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .db-alert-icon i { color: #dc2626; font-size: 1.2rem; }

    .db-alert-body h6 {
        margin: 0 0 0.15rem;
        font-weight: 700;
        font-size: 0.9rem;
        color: #dc2626;
    }

    .db-alert-body p {
        margin: 0;
        font-size: 0.82rem;
        color: var(--db-muted);
    }

    .btn-db-alert {
        background: #dc2626;
        color: #fff;
        border: none;
        border-radius: 50px;
        padding: 0.45rem 1.25rem;
        font-size: 0.82rem;
        font-weight: 600;
        text-decoration: none;
        white-space: nowrap;
        transition: background var(--db-transition);
    }

    .btn-db-alert:hover { background: #b91c1c; color: #fff; }

    /* ── Stat Cards ── */
    .db-stat-card {
        background: var(--db-white);
        border: 1px solid var(--db-border);
        border-radius: var(--db-radius);
        padding: 1.25rem;
        height: 100%;
        display: flex;
        align-items: center;
        gap: 1rem;
        text-decoration: none !important;
        box-shadow: var(--db-shadow);
        transition: transform var(--db-transition), box-shadow var(--db-transition), border-color var(--db-transition);
        position: relative;
        overflow: hidden;
    }

    .db-stat-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0;
        width: 4px;
        height: 100%;
        border-radius: 4px 0 0 4px;
    }

    .db-stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px rgba(16, 35, 101, 0.12);
        border-color: var(--db-blue-light);
    }

    .db-stat-card.stat-revenue::before { background: var(--db-blue); }
    .db-stat-card.stat-plans::before    { background: var(--db-blue-bright); }
    .db-stat-card.stat-users::before     { background: var(--db-blue-light); }
    .db-stat-card.stat-appts::before     { background: var(--db-black); }

    .db-stat-icon {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1.4rem;
    }

    .stat-revenue .db-stat-icon { background: var(--db-blue-soft); color: var(--db-blue); }
    .stat-plans   .db-stat-icon { background: #dbeafe; color: var(--db-blue-bright); }
    .stat-users   .db-stat-icon { background: #eff6ff; color: var(--db-blue-light); }
    .stat-appts   .db-stat-icon { background: #f3f4f6; color: var(--db-black); }

    .db-stat-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--db-muted);
        margin: 0 0 0.2rem;
    }

    .db-stat-value {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--db-black);
        margin: 0;
        letter-spacing: -0.5px;
        line-height: 1.2;
    }

    /* ── Panel Cards ── */
    .db-panel {
        background: var(--db-white);
        border: 1px solid var(--db-border);
        border-radius: var(--db-radius);
        padding: 1.5rem;
        height: 100%;
        box-shadow: var(--db-shadow);
    }

    .db-panel-header {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        margin-bottom: 1.25rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--db-border);
    }

    .db-panel-header .panel-icon {
        width: 36px;
        height: 36px;
        background: var(--db-blue-soft);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--db-blue);
        font-size: 1rem;
    }

    .db-panel-header h5 {
        margin: 0;
        font-size: 1rem;
        font-weight: 700;
        color: var(--db-black);
    }

    /* ── Leave List ── */
    .db-leave-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.75rem 1rem;
        background: var(--db-surface);
        border: 1px solid var(--db-border);
        border-radius: 10px;
        margin-bottom: 0.6rem;
    }

    .db-leave-item:last-child { margin-bottom: 0; }

    .db-leave-avatar {
        width: 40px;
        height: 40px;
        background: var(--db-white);
        border: 1px solid var(--db-border);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--db-blue);
    }

    .db-leave-name {
        font-weight: 700;
        font-size: 0.88rem;
        color: var(--db-text);
    }

    .db-badge-leave {
        background: #fef2f2;
        color: #dc2626;
        font-size: 0.62rem;
        font-weight: 800;
        letter-spacing: 0.5px;
        padding: 0.2rem 0.5rem;
        border-radius: 50px;
    }

    .db-leave-empty {
        text-align: center;
        padding: 2rem 1rem;
        background: var(--db-blue-soft);
        border: 1px dashed var(--db-blue-light);
        border-radius: 12px;
    }

    .db-leave-empty i { color: var(--db-blue); font-size: 2rem; }
    .db-leave-empty h6 { color: var(--db-blue); font-weight: 700; margin: 0.5rem 0 0.25rem; }
    .db-leave-empty p  { color: var(--db-muted); font-size: 0.82rem; margin: 0; }

    /* ── Management Center ── */
    .db-section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.25rem;
        margin-top: 2rem;
    }

    .db-section-header h4 {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--db-black);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .db-section-header h4::before {
        content: '';
        width: 4px;
        height: 22px;
        background: var(--db-blue);
        border-radius: 2px;
    }

    .db-badge {
        background: var(--db-blue-soft);
        color: var(--db-blue);
        font-size: 0.72rem;
        font-weight: 700;
        padding: 0.35rem 0.85rem;
        border-radius: 50px;
        letter-spacing: 0.3px;
    }

    .db-module-card {
        background: var(--db-white);
        border: 1px solid var(--db-border);
        border-radius: 12px;
        padding: 1.25rem 0.75rem;
        text-decoration: none !important;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.6rem;
        height: 100%;
        position: relative;
        box-shadow: var(--db-shadow);
        transition: all var(--db-transition);
    }

    .db-module-card:hover {
        border-color: var(--db-blue);
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(16, 35, 101, 0.12);
    }

    .db-module-icon {
        width: 48px;
        height: 48px;
        background: var(--db-blue-soft);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--db-blue);
        font-size: 1.3rem;
        transition: all var(--db-transition);
    }

    .db-module-card:hover .db-module-icon {
        background: var(--db-blue);
        color: #fff;
    }

    .db-module-name {
        font-size: 0.78rem;
        font-weight: 700;
        color: var(--db-text);
        text-align: center;
        line-height: 1.3;
    }

    .db-module-count {
        position: absolute;
        top: 8px;
        right: 8px;
        background: var(--db-blue);
        color: #fff;
        font-size: 0.7rem;
        font-weight: 800;
        min-width: 22px;
        height: 22px;
        border-radius: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 5px;
    }

    .db-module-count i { font-size: 0.55rem; }

    /* ── Analytics Section ── */
    .db-analytics {
        margin-top: 2rem;
        background: var(--db-white);
        border: 1px solid var(--db-border);
        border-radius: 16px;
        padding: 2rem;
        box-shadow: var(--db-shadow);
    }

    .db-analytics-title {
        text-align: center;
        margin-bottom: 2rem;
    }

    .db-analytics-title h2 {
        font-size: 1.65rem;
        font-weight: 800;
        color: var(--db-black);
        margin: 0 0 0.4rem;
        letter-spacing: -0.02em;
    }

    .db-analytics-title h2 span { color: var(--db-blue); }

    .db-analytics-title p {
        color: var(--db-muted);
        font-size: 0.9rem;
        font-weight: 500;
        margin: 0;
    }

    .db-report-card {
        border: 1px solid var(--db-border);
        border-radius: 12px;
        overflow: hidden;
        height: 100%;
        background: var(--db-white);
        box-shadow: var(--db-shadow);
    }

    .db-report-header {
        background: var(--db-blue);
        color: #fff;
        padding: 0.75rem 1.25rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: 700;
        font-size: 0.88rem;
        letter-spacing: 0.3px;
    }

    .db-report-header.alt { background: var(--db-black); }
    .db-report-header.alt2 { background: var(--db-blue-bright); }

    .db-report-header .download-icon {
        cursor: pointer;
        opacity: 0.75;
        transition: opacity var(--db-transition);
        font-size: 0.95rem;
    }

    .db-report-header .download-icon:hover { opacity: 1; }

    .db-report-table {
        width: 100%;
        border-collapse: collapse;
    }

    .db-report-table thead th {
        background: var(--db-surface);
        padding: 0.65rem 1.25rem;
        text-align: left;
        font-size: 0.72rem;
        font-weight: 700;
        color: var(--db-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid var(--db-border);
    }

    .db-report-table tbody td {
        padding: 0.7rem 1.25rem;
        font-size: 0.88rem;
        font-weight: 600;
        color: var(--db-text);
        border-bottom: 1px solid var(--db-surface);
    }

    .db-report-table tbody tr:last-child td { border-bottom: none; }

    .db-report-total td {
        background: var(--db-blue-soft);
        padding: 0.85rem 1.25rem !important;
        font-weight: 800 !important;
        font-size: 0.95rem !important;
        color: var(--db-blue) !important;
        border-top: 2px solid var(--db-border) !important;
    }

    /* ── Insight Pills ── */
    .db-insight-pills {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 1.5rem;
        margin-top: 2rem;
        padding-top: 1.75rem;
        border-top: 1px solid var(--db-border);
    }

    .db-insight-pill {
        text-align: center;
    }

    .db-insight-pill .pill-icon {
        width: 46px;
        height: 46px;
        background: var(--db-blue-soft);
        color: var(--db-blue);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 0.5rem;
        font-size: 1.1rem;
    }

    .db-insight-pill p {
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--db-muted);
        margin: 0;
    }

    /* ── Responsive ── */
    @media (max-width: 768px) {
        #beautyden-dashboard { padding: 1.25rem 1rem 2rem; }
        .db-welcome { padding: 1.25rem; }
        .db-welcome-text h1 { font-size: 1.35rem; }
        .db-date-filter { width: 100%; justify-content: center; flex-wrap: wrap; }
        .db-analytics { padding: 1.25rem; }
    }
</style>
@endsection

@section('content')
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-body">
            <section id="beautyden-dashboard">

                <!-- Welcome Header -->
                <div class="db-welcome">
                    <div class="db-welcome-text">
                        <h1>BeautyDen <span>Dashboard</span></h1>
                        <p>Your business overview at a glance</p>
                    </div>
                    <div class="db-date-filter">
                        <label>Range</label>
                        <input type="text" id="global_start_date" class="db-date-input" value="{{ now()->startOfMonth()->format('d-m-Y') }}">
                        <span class="db-date-sep">—</span>
                        <input type="text" id="global_end_date" class="db-date-input" value="{{ now()->endOfMonth()->format('d-m-Y') }}">
                        <button class="btn-db-refresh" id="btn-refresh-dashboard" title="Refresh">
                            <i class="bi bi-arrow-repeat"></i>
                        </button>
                    </div>
                </div>

                <!-- Pending Reviews Alert -->
                @if($pendingReviews > 0)
                <div class="db-alert">
                    <div class="d-flex align-items-center gap-3">
                        <div class="db-alert-icon">
                            <i class="bi bi-exclamation-octagon"></i>
                        </div>
                        <div class="db-alert-body">
                            <h6>Action Required: Pending Reviews</h6>
                            <p>You have <strong>{{ $pendingReviews }}</strong> reviews waiting for approval.</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.reviews.index', ['status' => 0]) }}" class="btn-db-alert">Review Now</a>
                </div>
                @endif

                <!-- Stat Cards -->
                <div class="row g-3 mb-3">
                    <div class="col-sm-6 col-xl-3">
                        <div class="db-stat-card stat-revenue">
                            <div class="db-stat-icon"><i class="bi bi-wallet2"></i></div>
                            <div>
                                <p class="db-stat-label">Total Revenue</p>
                                <h2 class="db-stat-value" id="stat-revenue">₹{{ number_format($totalRevenue, 0) }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <a href="{{ route('admin.membership.index') }}" class="db-stat-card stat-plans">
                            <div class="db-stat-icon"><i class="bi bi-gem"></i></div>
                            <div>
                                <p class="db-stat-label">Active Plans</p>
                                <h2 class="db-stat-value" id="stat-plans">{{ $activeMemberships }}</h2>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <a href="{{ route('admin.user.index') }}" class="db-stat-card stat-users">
                            <div class="db-stat-icon"><i class="bi bi-people"></i></div>
                            <div>
                                <p class="db-stat-label">Total Users</p>
                                <h2 class="db-stat-value" id="stat-users">{{ $totalUsers }}</h2>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <a href="{{ route('admin.appointments.index', ['date' => date('Y-m-d')]) }}" class="db-stat-card stat-appts">
                            <div class="db-stat-icon"><i class="bi bi-calendar-event"></i></div>
                            <div>
                                <p class="db-stat-label">Today's Appts</p>
                                <h2 class="db-stat-value" id="stat-appts">{{ $todayAppointments }}</h2>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Charts & Staff Leave -->
                <div class="row g-3">
                    <div class="col-lg-8">
                        <div class="db-panel">
                            <div class="db-panel-header">
                                <div class="panel-icon"><i class="bi bi-graph-up"></i></div>
                                <h5>Booking Trends</h5>
                            </div>
                            <div id="completed-appointments-chart"></div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="db-panel">
                            <div class="db-panel-header">
                                <div class="panel-icon"><i class="bi bi-calendar2-x"></i></div>
                                <h5>Staff on Leave Today</h5>
                            </div>
                            <div class="leave-list">
                                @forelse($onLeaveToday as $leave)
                                <div class="db-leave-item">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="db-leave-avatar"><i class="bi bi-person-fill"></i></div>
                                        <div>
                                            <div class="db-leave-name">{{ $leave->beautician->name ?? 'N/A' }}</div>
                                            <span class="db-badge-leave">ON LEAVE</span>
                                        </div>
                                    </div>
                                    <span class="text-muted small fw-bold">Full Day</span>
                                </div>
                                @empty
                                <div class="db-leave-empty">
                                    <i class="bi bi-check-circle"></i>
                                    <h6>Perfect Availability!</h6>
                                    <p>All staff members are on duty today.</p>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Management Center -->
                @php
                    $managementModules = [
                        ['name' => 'Appointments', 'icon' => 'calendar', 'route' => 'admin.appointments.index', 'id' => 'appointments'],
                        ['name' => 'Team Members', 'icon' => 'people', 'route' => 'admin.team.index', 'id' => 'team'],
                        ['name' => 'Attendance', 'icon' => 'clock', 'route' => 'admin.attendance.index', 'id' => 'attendance'],
                        ['name' => 'Users', 'icon' => 'person-check', 'route' => 'admin.user.index', 'id' => 'users'],
                        ['name' => 'Advanced Catalog', 'icon' => 'lightning', 'route' => 'admin.service-master.index', 'id' => 'advanced_catalog'],
                        ['name' => 'Master Essentials', 'icon' => 'grid', 'route' => 'admin.service-essential.index', 'id' => 'essentials'],
                        ['name' => 'Categories', 'icon' => 'box', 'route' => 'admin.service-category.index', 'id' => 'categories'],
                        ['name' => 'Sub Categories', 'icon' => 'layers', 'route' => 'admin.service-subcategory.index', 'id' => 'subcategories'],
                        ['name' => 'City List', 'icon' => 'geo-alt', 'route' => 'admin.city.index', 'id' => 'cities'],
                        ['name' => 'Offers (Banners)', 'icon' => 'gift', 'route' => 'admin.offers.index', 'id' => 'offers'],
                        ['name' => 'Coupon Codes', 'icon' => 'tag', 'route' => 'admin.coupon-codes.index', 'id' => 'coupons'],
                        ['name' => 'Memberships', 'icon' => 'award', 'route' => 'admin.membership.index', 'id' => 'memberships'],
                        ['name' => 'Combos', 'icon' => 'box-seam', 'route' => 'admin.combo.index', 'id' => 'combos'],
                        ['name' => 'Reviews', 'icon' => 'star', 'route' => 'admin.reviews.index', 'id' => 'reviews'],
                    ];
                @endphp

                <div class="db-section-header">
                    <h4>Management Center</h4>
                    <span class="db-badge">Full Control Hub</span>
                </div>
                <div class="row g-2">
                    @foreach($managementModules as $mod)
                    <div class="col-6 col-md-3 col-lg-2">
                        <a href="{{ route($mod['route']) }}" class="db-module-card">
                            <span class="db-module-count" id="count-{{ $mod['id'] }}"><i class="bi bi-arrow-repeat spin"></i></span>
                            <div class="db-module-icon"><i class="bi bi-{{ $mod['icon'] }}"></i></div>
                            <span class="db-module-name text-truncate">{{ $mod['name'] }}</span>
                        </a>
                    </div>
                    @endforeach
                </div>

                <!-- Analytics -->
                <div class="db-analytics">
                    <div class="db-analytics-title">
                        <h2>Gain <span>Insights</span> & Grow Your Business</h2>
                        <p>Track your performance with detailed analytics</p>
                    </div>

                    <div class="analytics-filter-row d-none">
                        <input type="text" id="report_start_date" value="{{ now()->startOfMonth()->format('d-m-Y') }}">
                        <input type="text" id="report_end_date" value="{{ now()->endOfMonth()->format('d-m-Y') }}">
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="db-report-card">
                                <div class="db-report-header">
                                    <span>Daily Revenue</span>
                                    <i class="bi bi-download download-icon" onclick="downloadReport('revenue')"></i>
                                </div>
                                <table class="db-report-table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th class="text-end">Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody id="revenue-report-body"></tbody>
                                    <tfoot>
                                        <tr class="db-report-total">
                                            <td>Total</td>
                                            <td class="text-end" id="total-revenue-val">₹0</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="db-report-card">
                                <div class="db-report-header alt2">
                                    <span>Appointments Per Day</span>
                                    <i class="bi bi-download download-icon" onclick="downloadReport('appointments')"></i>
                                </div>
                                <table class="db-report-table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th class="text-end">Appointments</th>
                                        </tr>
                                    </thead>
                                    <tbody id="appt-report-body"></tbody>
                                    <tfoot>
                                        <tr class="db-report-total">
                                            <td>Total</td>
                                            <td class="text-end" id="total-appt-val">0</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="db-report-card">
                                <div class="db-report-header">
                                    <span>Top Staff by Services</span>
                                    <i class="bi bi-download download-icon" onclick="downloadReport('staff_services')"></i>
                                </div>
                                <table class="db-report-table">
                                    <thead>
                                        <tr>
                                            <th>Staff</th>
                                            <th class="text-center">Services</th>
                                            <th class="text-end">Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody id="staff-services-body"></tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="db-report-card">
                                <div class="db-report-header alt">
                                    <span>Top Staff by Revenue</span>
                                    <i class="bi bi-download download-icon" onclick="downloadReport('staff_revenue')"></i>
                                </div>
                                <table class="db-report-table">
                                    <thead>
                                        <tr>
                                            <th>Staff</th>
                                            <th class="text-end">Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody id="staff-revenue-body"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="db-insight-pills">
                        <div class="db-insight-pill">
                            <div class="pill-icon"><i class="bi bi-bar-chart-line"></i></div>
                            <p>Sales Reports</p>
                        </div>
                        <div class="db-insight-pill">
                            <div class="pill-icon"><i class="bi bi-people"></i></div>
                            <p>Top Performers</p>
                        </div>
                        <div class="db-insight-pill">
                            <div class="pill-icon"><i class="bi bi-lightbulb"></i></div>
                            <p>Service Insights</p>
                        </div>
                        <div class="db-insight-pill">
                            <div class="pill-icon"><i class="bi bi-wallet2"></i></div>
                            <p>Revenue Tracking</p>
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
    document.addEventListener('DOMContentLoaded', function () {
        let dailyChart = null;

        if (typeof flatpickr !== 'undefined') {
            flatpickr("#global_start_date", { dateFormat: "d-m-Y" });
            flatpickr("#global_end_date", { dateFormat: "d-m-Y" });

            $('#global_start_date, #global_end_date').on('change', function() {
                $('#report_start_date').val($('#global_start_date').val());
                $('#report_end_date').val($('#global_end_date').val());
                refreshAnalytics();
            });
        }

        function initChart(labels, series) {
            const chartElement = document.querySelector("#completed-appointments-chart");
            if (!chartElement) return;

            const options = {
                series: series,
                chart: {
                    type: 'area',
                    height: 380,
                    stacked: false,
                    toolbar: { show: true },
                    zoom: { enabled: true },
                    fontFamily: 'Inter, sans-serif',
                },
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 2.5 },
                xaxis: {
                    categories: labels,
                    labels: { style: { colors: '#6b7280', fontWeight: 600 } }
                },
                yaxis: {
                    labels: { style: { colors: '#6b7280', fontWeight: 600 } }
                },
                grid: { borderColor: '#e5e7eb', strokeDashArray: 4 },
                fill: {
                    type: 'gradient',
                    gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.05 }
                },
                colors: ['#102365', '#1e40af', '#3b82f6', '#0a0a0a'],
                tooltip: { theme: 'light', x: { show: true } },
                legend: { position: 'top', horizontalAlign: 'left', fontWeight: 700 }
            };

            if (dailyChart) {
                dailyChart.updateOptions(options);
            } else {
                dailyChart = new ApexCharts(chartElement, options);
                dailyChart.render();
            }
        }

        const initialLabels = @json($chartLabels);
        const initialData = @json($chartData);
        initChart(initialLabels, [{ name: 'Completed', data: initialData }]);

        function refreshAnalytics() {
            let start = $('#global_start_date').val();
            let end = $('#global_end_date').val();

            $('#btn-refresh-dashboard i').addClass('fa-spin');

            $.ajax({
                url: "{{ route('admin.dashboard.analytics') }}",
                type: 'GET',
                data: { start_date: start, end_date: end },
                success: function(res) {
                    $('#btn-refresh-dashboard i').removeClass('fa-spin');

                    $('#stat-revenue').text('₹' + res.stats.total_revenue);
                    $('#stat-plans').text(res.stats.active_plans);
                    $('#stat-users').text(res.stats.total_users);
                    $('#stat-appts').text(res.stats.total_appts);

                    initChart(res.chart.labels, res.chart.series);

                    let revHtml = '';
                    res.daily_revenue.forEach(item => {
                        revHtml += `<tr><td>${item.date}</td><td class="text-end">₹${parseFloat(item.revenue).toLocaleString()}</td></tr>`;
                    });
                    if(res.daily_revenue.length === 0) revHtml = '<tr><td colspan="2" class="text-center py-4 text-muted">No data found</td></tr>';
                    $('#revenue-report-body').html(revHtml);
                    $('#total-revenue-val').text('₹' + res.stats.total_revenue);

                    let ssHtml = '';
                    res.top_staff_services.forEach(item => {
                        ssHtml += `<tr><td>${item.staff}</td><td class="text-center">${item.services}</td><td class="text-end">₹${parseFloat(item.revenue).toLocaleString()}</td></tr>`;
                    });
                    if(res.top_staff_services.length === 0) ssHtml = '<tr><td colspan="3" class="text-center py-4 text-muted">No performers found</td></tr>';
                    $('#staff-services-body').html(ssHtml);

                    let srHtml = '';
                    res.top_staff_revenue.forEach(item => {
                        srHtml += `<tr><td>${item.staff}</td><td class="text-end">₹${parseFloat(item.revenue).toLocaleString()}</td></tr>`;
                    });
                    if(res.top_staff_revenue.length === 0) srHtml = '<tr><td colspan="2" class="text-center py-4 text-muted">No performers found</td></tr>';
                    $('#staff-revenue-body').html(srHtml);
                },
                error: function() {
                    $('#btn-refresh-dashboard i').removeClass('fa-spin');
                }
            });
        }

        $('#btn-refresh-dashboard').on('click', function() {
            refreshAnalytics();
        });

        setTimeout(refreshAnalytics, 500);

        function fetchManagementCounts() {
            $.ajax({
                url: "{{ route('admin.dashboard.management-counts') }}",
                type: 'GET',
                success: function(res) {
                    Object.keys(res).forEach(key => {
                        $(`#count-${key}`).text(res[key]);
                    });
                }
            });
        }

        fetchManagementCounts();

        window.downloadReport = function(type) {
            let start = $('#global_start_date').val();
            let end = $('#global_end_date').val();
            let url = "{{ route('admin.dashboard.export-analytics') }}?type=" + type + "&start_date=" + start + "&end_date=" + end;
            window.location.href = url;
        }
    });
</script>
@endsection
