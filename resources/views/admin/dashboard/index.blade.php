@extends('admin.layouts.app')

@section('header_style_content')
<style>
    :root {
        --mst-indigo: #102365;
        --mst-indigo-light: #f5f7ff;
        --mst-success: #059669;
        --mst-warning: #d97706;
        --mst-danger: #dc2626;
        --mst-info: #2563eb;
        --mst-text-main: #1e293b;
        --mst-text-muted: #64748b;
        --mst-bg-gray: #f8fafc;
    }

    #beautyden-dashboard {
        padding: 2rem;
        background: #f8fafc;
        min-height: 100vh;
    }

    /* Welcome Header */
    .dashboard-welcome h1 {
        font-weight: 800;
        color: #1e293b;
        font-size: 2.5rem;
        letter-spacing: -1px;
        margin-bottom: 8px;
    }
    .dashboard-welcome p {
        color: #64748b;
        font-size: 1.2rem;
        font-weight: 500;
    }

    /* Premium Stat Cards */
    .dashboard-stat-card {
        border: none;
        border-radius: 24px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.04);
        overflow: hidden;
        position: relative;
        background: #fff;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid #f1f5f9;
    }
    .dashboard-stat-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 50px rgba(0,0,0,0.08);
    }
    .card-accent-bar {
        position: absolute;
        top: 0;
        left: 0;
        width: 6px;
        height: 100%;
        border-radius: 0 4px 4px 0;
    }
    .stat-avatar {
        border-radius: 18px;
        width: 64px;
        height: 64px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .stat-label {
        color: #64748b;
        font-size: 0.95rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        margin-bottom: 4px;
    }
    .stat-value {
        color: #0f172a;
        font-size: 1.8rem;
        font-weight: 900;
        margin-bottom: 0;
        letter-spacing: -0.5px;
    }

    /* Module Section */
    .dashboard-section { margin-top: 3rem; }
    .section-title { margin-bottom: 1.5rem; }
    .section-title h4 { 
        font-weight: 800; 
        color: #1e293b; 
        font-size: 1.4rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .section-title h4::after {
        content: "";
        flex: 1;
        height: 2px;
        background: #f1f5f9;
    }

    .module-card-premium {
        background: #fff;
        padding: 24px;
        border-radius: 20px;
        border: 1px solid #f1f5f9;
        text-decoration: none !important;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        position: relative;
    }
    .module-card-premium:hover {
        background: #fff;
        border-color: #7367f0;
        box-shadow: 0 15px 35px rgba(115, 103, 240, 0.1);
        transform: translateY(-5px);
    }
    .module-icon {
        font-size: 2.2rem;
        color: #7367f0;
        margin-bottom: 14px;
        background: #f5f3ff;
        width: 64px;
        height: 64px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 16px;
        transition: all 0.3s ease;
    }
    .module-card-premium:hover .module-icon {
        background: #7367f0;
        color: #fff;
        transform: scale(1.1);
    }
    .module-name {
        font-weight: 800;
        color: #334155;
        font-size: 1rem;
        text-align: center;
    }
    .module-count {
        position: absolute;
        top: 15px;
        right: 15px;
        background: #7367f0;
        color: #fff;
        padding: 4px 8px;
        border-radius: 50px;
        font-size: 0.9rem;
        font-weight: 800;
        box-shadow: 0 4px 10px rgba(239, 68, 68, 0.2);
        min-width: 25px;
        text-align: center;
    }

    .module-count i { font-size: 0.6rem; }

    /* Analytics Dashboard Styling */
    .analytics-container {
        margin-top: 3rem;
        background: #fff;
        border-radius: 30px;
        padding: 30px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.04);
        border: 1px solid #f1f5f9;
    }

    .analytics-header {
        text-align: center;
        margin-bottom: 2.5rem;
    }

    .analytics-header h2 {
        font-weight: 900;
        font-size: 2.4rem;
        color: #0f172a;
        margin-bottom: 10px;
    }

    .analytics-header h2 span {
        color: #7367f0;
    }

    .analytics-filter-row {
        background: #f8fafc;
        padding: 15px 25px;
        border-radius: 20px;
        margin-bottom: 2.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 20px;
        border: 1px solid #edf2f7;
    }

    .filter-label {
        font-weight: 700;
        color: #475569;
        font-size: 0.95rem;
    }

    .analytics-date-input {
        background: #fff;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        padding: 8px 15px;
        font-weight: 600;
        color: #1e293b;
        width: 150px;
        text-align: center;
    }

    .report-card {
        border: none;
        border-radius: 20px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        height: 100%;
        border: 1px solid #f1f5f9;
    }

    .report-card-header {
        padding: 12px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: #fff;
        font-weight: 800;
        font-size: 1rem;
        letter-spacing: 0.5px;
    }

    .header-green { background: #28c76f; }
    .header-orange { background: #ff9f43; }
    .header-blue { background: #7367f0; }
    .header-purple { background: #9d50bb; }

    .download-icon {
        cursor: pointer;
        opacity: 0.8;
        transition: opacity 0.3s;
    }
    .download-icon:hover { opacity: 1; }

    .report-table-mini {
        width: 100%;
        border-collapse: collapse;
    }

    .report-table-mini thead th {
        background: #f8fafc;
        padding: 10px 20px;
        text-align: left;
        font-size: 0.8rem;
        font-weight: 800;
        color: #64748b;
        text-transform: uppercase;
        border-bottom: 1px solid #f1f5f9;
    }

    .report-table-mini tbody td {
        padding: 12px 20px;
        font-size: 0.95rem;
        font-weight: 700;
        color: #1e293b;
        border-bottom: 1px solid #f8fafc;
    }

    .report-table-mini tbody tr:last-child td { border-bottom: none; }

    .report-total-row {
        background: #fdfdfd;
        border-top: 2px dashed #f1f5f9;
    }

    .report-total-row td {
        padding: 15px 20px !important;
        font-weight: 900 !important;
        font-size: 1.1rem !important;
        color: #0f172a !important;
    }

</style>
@endsection

@section('content')
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-body">
            <section id="beautyden-dashboard">
                
                <!-- Welcome Header -->
                <div class="dashboard-welcome d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                    <div>
                        <h1>BeautyDen Dashboard ✨</h1>
                        <p>Your business overview at a glance.</p>
                    </div>
                    <div class="d-flex align-items-center gap-2 mt-2 mt-md-0 bg-white p-1 rounded-pill shadow-sm border">
                        <span class="ps-3 fw-bold text-muted small">Range:</span>
                        <input type="text" id="global_start_date" class="analytics-date-input border-0" style="width: 120px;" value="{{ now()->startOfMonth()->format('d-m-Y') }}">
                        <span class="fw-bold text-muted">-</span>
                        <input type="text" id="global_end_date" class="analytics-date-input border-0" style="width: 120px;" value="{{ now()->endOfMonth()->format('d-m-Y') }}">
                        <button class="btn btn-primary rounded-pill px-3 py-1 me-1" id="btn-refresh-dashboard">
                            <i class="bi bi-arrow-repeat"></i>
                        </button>
                    </div>
                </div>

                <!-- Priority Action Alerts -->
                @if($pendingReviews > 0)
                <div class="card mb-3 border-0 shadow-sm" style="border-radius: 16px; background: #fff5f5; overflow: hidden;">
                    <div class="card-body p-2 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar p-1" style="background: rgba(220, 38, 38, 0.1); border-radius: 12px; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-exclamation-octagon text-danger fs-3"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-danger">Action Required: Pending Reviews</h6>
                                <p class="mb-0 text-muted small">You have <strong>{{ $pendingReviews }}</strong> reviews waiting for approval.</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.reviews.index', ['status' => 0]) }}" class="btn btn-danger btn-sm rounded-pill px-4">Review Now</a>
                    </div>
                </div>
                @endif

                <!-- Vital Stats Grid -->
                <div class="row g-2 mb-4">
                    <div class="col-md-3">
                        <div class="dashboard-stat-card h-100">
                            <div class="card-accent-bar" style="background: #7367f0;"></div>
                            <div class="card-body p-2 d-flex align-items-center">
                                <div class="stat-avatar me-2" style="background: rgba(115, 103, 240, 0.1);">
                                    <i class="bi bi-wallet2 fs-3" style="color: #7367f0;"></i>
                                </div>
                                <div>
                                    <p class="stat-label">Total Revenue</p>
                                    <h2 class="stat-value" id="stat-revenue">₹{{ number_format($totalRevenue, 0) }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <a href="{{ route('admin.membership.index') }}" class="dashboard-stat-card h-100 d-block text-decoration-none">
                            <div class="card-accent-bar" style="background: #28c76f;"></div>
                            <div class="card-body p-2 d-flex align-items-center">
                                <div class="stat-avatar me-2" style="background: rgba(40, 199, 111, 0.1);">
                                    <i class="bi bi-gem fs-3" style="color: #28c76f;"></i>
                                </div>
                                <div class="w-100">
                                    <p class="stat-label">Active Plans</p>
                                    <h2 class="stat-value" id="stat-plans">{{ $activeMemberships }}</h2>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-3">
                        <a href="{{ route('admin.user.index') }}" class="dashboard-stat-card h-100 d-block text-decoration-none">
                            <div class="card-accent-bar" style="background: #00cfe8;"></div>
                            <div class="card-body p-2 d-flex align-items-center">
                                <div class="stat-avatar me-2" style="background: rgba(0, 207, 232, 0.1);">
                                    <i class="bi bi-people fs-3" style="color: #00cfe8;"></i>
                                </div>
                                <div class="w-100">
                                    <p class="stat-label">Total Users</p>
                                    <h2 class="stat-value" id="stat-users">{{ $totalUsers }}</h2>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-3">
                        <a href="{{ route('admin.appointments.index', ['date' => date('Y-m-d')]) }}" class="dashboard-stat-card h-100 d-block text-decoration-none">
                            <div class="card-accent-bar" style="background: #ff9f43;"></div>
                            <div class="card-body p-2 d-flex align-items-center">
                                <div class="stat-avatar me-2" style="background: rgba(255, 159, 67, 0.1);">
                                    <i class="bi bi-calendar-event fs-3" style="color: #ff9f43;"></i>
                                </div>
                                <div class="w-100">
                                    <p class="stat-label">Total Appt.</p>
                                    <h2 class="stat-value" id="stat-appts">{{ $todayAppointments }}</h2>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="row g-2">
                    <!-- Charts -->
                    <div class="col-lg-8">
                        <div class="dashboard-stat-card p-3 h-100">
                            <div class="card-accent-bar" style="background: #7367f0;"></div>
                            <h5 class="fw-bold mb-3"><i class="bi bi-graph-up text-primary me-2"></i> Booking Trends</h5>
                            <div id="completed-appointments-chart"></div>
                        </div>
                    </div>

                    <!-- Staff Availability -->
                    <div class="col-lg-4">
                        <div class="dashboard-stat-card p-3 h-100">
                            <div class="card-accent-bar" style="background: #ea5455;"></div>
                            <h5 class="fw-bold mb-3"><i class="bi bi-calendar2-x text-danger me-2"></i> Staff on Leave Today</h5>
                            <div class="leave-list mt-2">
                                @forelse($onLeaveToday as $leave)
                                <div class="d-flex align-items-center justify-content-between p-2 mb-2" style="background: #fff5f5; border-radius: 12px; border: 1px solid #fee2e2;">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="stat-avatar shadow-sm" style="background: #fff; width: 48px; height: 48px; border-radius: 12px;">
                                            <i class="bi bi-person-fill text-danger fs-4"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold" style="color: #1e293b; font-size: 0.95rem;">{{ $leave->beautician->name ?? 'N/A' }}</div>
                                            <span class="badge rounded-pill bg-danger shadow-sm" style="font-size: 0.65rem; font-weight: 800; letter-spacing: 0.5px;">ON LEAVE</span>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span class="text-muted small fw-bold d-block">Full Day</span>
                                    </div>
                                </div>
                                @empty
                                <div class="text-center py-4 px-2" style="background: #f0fdf4; border-radius: 16px; border: 1px dashed #28c76f;">
                                    <div class="stat-avatar mx-auto mb-3 shadow-sm" style="background: #fff; width: 64px; height: 64px; border-radius: 20px;">
                                        <i class="bi bi-check-lg text-success fs-1"></i>
                                    </div>
                                    <h6 class="fw-bold text-success mb-1">Perfect Availability!</h6>
                                    <p class="text-muted small mb-0 fw-bold">All staff members are on duty today.</p>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Management Center (Full Modules) -->
                @php
                    $managementModules = [
                        ['name' => 'Appointments', 'icon' => 'calendar', 'route' => 'admin.appointments.index', 'id' => 'appointments'],
                        ['name' => 'Team Members', 'icon' => 'users', 'route' => 'admin.team.index', 'id' => 'team'],
                        ['name' => 'Attendance', 'icon' => 'clock', 'route' => 'admin.attendance.index', 'id' => 'attendance'],
                        ['name' => 'Users', 'icon' => 'user-check', 'route' => 'admin.user.index', 'id' => 'users'],
                        // ['name' => 'Services', 'icon' => 'shopping-bag', 'route' => 'admin.service.index', 'id' => 'services'],
                        ['name' => 'Advanced Catalog', 'icon' => 'zap', 'route' => 'admin.service-master.index', 'id' => 'advanced_catalog'],
                        ['name' => 'Master Essentials', 'icon' => 'grid', 'route' => 'admin.service-essential.index', 'id' => 'essentials'],
                        ['name' => 'Categories', 'icon' => 'box', 'route' => 'admin.service-category.index', 'id' => 'categories'],
                        ['name' => 'Sub Categories', 'icon' => 'layers', 'route' => 'admin.service-subcategory.index', 'id' => 'subcategories'],
                        ['name' => 'City List', 'icon' => 'map-pin', 'route' => 'admin.city.index', 'id' => 'cities'],
                        // ['name' => 'Pricing (Web)', 'icon' => 'dollar-sign', 'route' => 'admin.service-city-price.index', 'id' => 'pricing_web'],
                        // ['name' => 'Pricing (App)', 'icon' => 'monitor', 'route' => 'admin.service-city-master.index', 'id' => 'pricing_app'],
                        ['name' => 'Offers (Banners)', 'icon' => 'gift', 'route' => 'admin.offers.index', 'id' => 'offers'],
                        ['name' => 'Coupon Codes', 'icon' => 'tag', 'route' => 'admin.coupon-codes.index', 'id' => 'coupons'],
                        ['name' => 'Memberships', 'icon' => 'award', 'route' => 'admin.membership.index', 'id' => 'memberships'],
                        ['name' => 'Combos', 'icon' => 'package', 'route' => 'admin.combo.index', 'id' => 'combos'],
                        // ['name' => 'Transactions', 'icon' => 'credit-card', 'route' => 'admin.razorpay.index', 'id' => 'transactions'],
                        // ['name' => 'Inquiries', 'icon' => 'mail', 'route' => 'admin.contact-submissions.index', 'id' => 'inquiries'],
                        // ['name' => 'Notifications', 'icon' => 'bell', 'route' => 'admin.notifications.index', 'id' => 'notifications'],
                        ['name' => 'Reviews', 'icon' => 'star', 'route' => 'admin.reviews.index', 'id' => 'reviews'],
                        // ['name' => 'Portfolio', 'icon' => 'image', 'route' => 'admin.portfolio.index', 'id' => 'portfolio'],
                        // ['name' => 'Blogs', 'icon' => 'edit', 'route' => 'admin.blogs.index', 'id' => 'blogs'],
                    ];
                @endphp

                <div class="dashboard-section mt-5">
                    <div class="section-title d-flex justify-content-between align-items-center">
                        <h4>Management Center</h4>
                        <span class="badge bg-soft-primary text-primary rounded-pill px-3">Full Control Hub</span>
                    </div>
                    <div class="row g-2">
                        @foreach($managementModules as $mod)
                        <div class="col-6 col-md-3 col-lg-2">
                            <a href="{{ route($mod['route']) }}" class="module-card-premium">
                                <span class="module-count" id="count-{{ $mod['id'] }}"><i class="bi bi-arrow-repeat spin"></i></span>
                                <i class="bi bi-{{ $mod['icon'] }} module-icon"></i>
                                <span class="module-name text-truncate">{{ $mod['name'] }}</span>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Gain Insights Section (Analytics) -->
                <section class="analytics-container">
                    <div class="analytics-header">
                        <h2>Gain <span>Insights</span> & Grow Your Business!</h2>
                        <p class="text-muted fw-bold">Track your performance with detailed analytics.</p>
                    </div>

                    <div class="analytics-filter-row d-none">
                        <input type="text" id="report_start_date" value="{{ now()->startOfMonth()->format('d-m-Y') }}">
                        <input type="text" id="report_end_date" value="{{ now()->endOfMonth()->format('d-m-Y') }}">
                    </div>

                    <div class="row g-3">
                        <!-- Daily Revenue -->
                        <div class="col-md-6">
                            <div class="report-card">
                                <div class="report-card-header header-green">
                                    <span>Daily Revenue</span>
                                    <i class="bi bi-download download-icon" onclick="downloadReport('revenue')"></i>
                                </div>
                                <table class="report-table-mini">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th class="text-end">Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody id="revenue-report-body">
                                        <!-- Dynamic Content -->
                                    </tbody>
                                    <tfoot>
                                        <tr class="report-total-row">
                                            <td>Total</td>
                                            <td class="text-end" id="total-revenue-val">₹0</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <!-- Appointments Per Day -->
                        <div class="col-md-6">
                            <div class="report-card">
                                <div class="report-card-header header-orange">
                                    <span>Appointments Per Day</span>
                                    <i class="bi bi-download download-icon" onclick="downloadReport('appointments')"></i>
                                </div>
                                <table class="report-table-mini">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th class="text-end">Appointments</th>
                                        </tr>
                                    </thead>
                                    <tbody id="appt-report-body">
                                        <!-- Dynamic Content -->
                                    </tbody>
                                    <tfoot>
                                        <tr class="report-total-row">
                                            <td>Total</td>
                                            <td class="text-end" id="total-appt-val">0</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <!-- Top Staff by Services -->
                        <div class="col-md-6">
                            <div class="report-card">
                                <div class="report-card-header header-blue">
                                    <span>Top Staff by Services</span>
                                    <i class="bi bi-download download-icon" onclick="downloadReport('staff_services')"></i>
                                </div>
                                <table class="report-table-mini">
                                    <thead>
                                        <tr>
                                            <th>Staff</th>
                                            <th class="text-center">Services</th>
                                            <th class="text-end">Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody id="staff-services-body">
                                        <!-- Dynamic Content -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Top Staff by Revenue -->
                        <div class="col-md-6">
                            <div class="report-card">
                                <div class="report-card-header header-purple">
                                    <span>Top Staff by Revenue</span>
                                    <i class="bi bi-download download-icon" onclick="downloadReport('staff_revenue')"></i>
                                </div>
                                <table class="report-table-mini">
                                    <thead>
                                        <tr>
                                            <th>Staff</th>
                                            <th class="text-end">Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody id="staff-revenue-body">
                                        <!-- Dynamic Content -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4 pt-4 border-top">
                        <div class="col-12 d-flex justify-content-center gap-4">
                            <div class="text-center">
                                <div class="stat-avatar mx-auto mb-2" style="background: #f0f4ff; color: #7367f0; width: 50px; height: 50px;">
                                    <i class="bi bi-bar-chart-line fs-4"></i>
                                </div>
                                <p class="small fw-bold text-muted mb-0">Sales Reports</p>
                            </div>
                            <div class="text-center">
                                <div class="stat-avatar mx-auto mb-2" style="background: #f0fdf4; color: #28c76f; width: 50px; height: 50px;">
                                    <i class="bi bi-people fs-4"></i>
                                </div>
                                <p class="small fw-bold text-muted mb-0">Top Performers</p>
                            </div>
                            <div class="text-center">
                                <div class="stat-avatar mx-auto mb-2" style="background: #fff7ed; color: #ff9f43; width: 50px; height: 50px;">
                                    <i class="bi bi-lightbulb fs-4"></i>
                                </div>
                                <p class="small fw-bold text-muted mb-0">Service Insights</p>
                            </div>
                            <div class="text-center">
                                <div class="stat-avatar mx-auto mb-2" style="background: #fdf2ff; color: #9d50bb; width: 50px; height: 50px;">
                                    <i class="bi bi-wallet2 fs-4"></i>
                                </div>
                                <p class="small fw-bold text-muted mb-0">Revenue Tracking</p>
                            </div>
                        </div>
                    </div>
                </section>

            </section>
        </div>
    </div>
</div>
@endsection

@section('footer_script_content')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let dailyChart = null;

        // Initialize Flatpickr explicitly for analytics dates
        if (typeof flatpickr !== 'undefined') {
            flatpickr("#global_start_date", { dateFormat: "d-m-Y" });
            flatpickr("#global_end_date", { dateFormat: "d-m-Y" });
            
            // Sync with hidden report inputs
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
                series: series, // [ {name: 'Completed', data: [...]}, ... ]
                chart: { 
                    type: 'area', 
                    height: 380, 
                    stacked: false,
                    toolbar: { show: true }, 
                    zoom: { enabled: true } 
                },
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 3 },
                xaxis: { 
                    categories: labels, 
                    labels: { style: { colors: '#64748b', fontWeight: 600 } } 
                },
                fill: { 
                    type: 'gradient', 
                    gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05 } 
                },
                colors: ['#28c76f', '#ff9f43', '#7367f0', '#ea5455'], // Completed, Pending, Assigned, Rejected
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

        // Initial Data from PHP
        const initialLabels = @json($chartLabels);
        const initialData = @json($chartData);
        initChart(initialLabels, [{ name: 'Completed', data: initialData }]);

        // Analytics Fetching Logic
        function refreshAnalytics() {
            let start = $('#global_start_date').val();
            let end = $('#global_end_date').val();

            // Show loader if needed
            $('#btn-refresh-dashboard i').addClass('fa-spin');

            $.ajax({
                url: "{{ route('admin.dashboard.analytics') }}",
                type: 'GET',
                data: { start_date: start, end_date: end },
                success: function(res) {
                    $('#btn-refresh-dashboard i').removeClass('fa-spin');

                    // Update Stat Cards
                    $('#stat-revenue').text('₹' + res.stats.total_revenue);
                    $('#stat-plans').text(res.stats.active_plans);
                    $('#stat-users').text(res.stats.total_users);
                    $('#stat-appts').text(res.stats.total_appts);

                    // Update Chart
                    initChart(res.chart.labels, res.chart.series);

                    // 1. Revenue Table
                    let revHtml = '';
                    res.daily_revenue.forEach(item => {
                        revHtml += `<tr><td>${item.date}</td><td class="text-end">₹${parseFloat(item.revenue).toLocaleString()}</td></tr>`;
                    });
                    if(res.daily_revenue.length === 0) revHtml = '<tr><td colspan="2" class="text-center py-4 text-muted">No data found</td></tr>';
                    $('#revenue-report-body').html(revHtml);
                    $('#total-revenue-val').text('₹' + res.stats.total_revenue);

                    // 2. Appointments (Not used anymore as we have chart series, but keeping body if needed)
                    // ...

                    // 3. Staff Services
                    let ssHtml = '';
                    res.top_staff_services.forEach(item => {
                        ssHtml += `<tr><td>${item.staff}</td><td class="text-center">${item.services}</td><td class="text-end">₹${parseFloat(item.revenue).toLocaleString()}</td></tr>`;
                    });
                    if(res.top_staff_services.length === 0) ssHtml = '<tr><td colspan="3" class="text-center py-4 text-muted">No performers found</td></tr>';
                    $('#staff-services-body').html(ssHtml);

                    // 4. Staff Revenue
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

        // Trigger Refresh
        $('#btn-refresh-dashboard').on('click', function() {
            refreshAnalytics();
        });

        // Initial Load of full analytics
        setTimeout(refreshAnalytics, 500);

        // Fetch Management Counts Asynchronously
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

        // Download functionality
        window.downloadReport = function(type) {
            let start = $('#global_start_date').val();
            let end = $('#global_end_date').val();
            let url = "{{ route('admin.dashboard.export-analytics') }}?type=" + type + "&start_date=" + start + "&end_date=" + end;
            window.location.href = url;
        }
    });
</script>
@endsection
