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
        padding: 1.5rem;
        background: var(--mst-bg-gray);
        min-height: 100vh;
    }

    /* Welcome Header */
    .dashboard-welcome {
        margin-bottom: 2.5rem;
    }
    .dashboard-welcome h1 {
        font-weight: 800;
        color: var(--mst-indigo);
        margin-bottom: 0.5rem;
        font-size: 2rem;
    }
    .dashboard-welcome p {
        color: var(--mst-text-muted);
        font-size: 1.1rem;
    }

    /* Primary Stats Grid */
    .primary-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.5rem;
        margin-bottom: 3rem;
    }

    .stat-card-luxury {
        background: #fff;
        border-radius: 24px;
        padding: 24px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(226, 232, 240, 0.8);
        text-decoration: none !important;
        display: block;
        position: relative;
        overflow: hidden;
    }

    .stat-card-luxury:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.08), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        border-color: var(--mst-indigo);
    }

    .stat-card-luxury.card-revenue {
        background: linear-gradient(135deg, #102365 0%, #1e3a8a 100%);
        color: #fff;
        border: none;
    }

    .stat-icon-wrapper {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
        font-size: 1.5rem;
    }

    .card-revenue .stat-icon-wrapper { background: rgba(255, 255, 255, 0.15); color: #fff; }
    .card-membership .stat-icon-wrapper { background: #f0fdf4; color: var(--mst-success); }
    .card-users .stat-icon-wrapper { background: #eff6ff; color: var(--mst-info); }
    .card-reviews .stat-icon-wrapper { background: #fff1f2; color: var(--mst-danger); }

    .stat-value {
        font-size: 1.8rem;
        font-weight: 800;
        margin-bottom: 4px;
        color: var(--mst-text-main);
    }
    .card-revenue .stat-value { color: #fff; }

    .stat-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--mst-text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .card-revenue .stat-label { color: rgba(255, 255, 255, 0.8); }

    /* Action Required Section */
    .action-required-banner {
        background: #fdf2f2;
        border: 1px solid #fee2e2;
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 2.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .action-text h5 { color: #991b1b; font-weight: 700; margin-bottom: 0.25rem; }
    .action-text p { color: #b91c1c; margin: 0; font-size: 0.9rem; }

    /* Module Sections */
    .dashboard-section { margin-bottom: 3.5rem; }
    .section-title { display: flex; align-items: center; gap: 12px; margin-bottom: 1.5rem; padding-left: 8px; }
    .section-title i { font-size: 1.4rem; color: var(--mst-indigo); }
    .section-title h4 { margin: 0; font-weight: 700; color: var(--mst-text-main); font-size: 1.25rem; }

    .module-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 1.5rem; }
    .module-card-mini {
        background: #fff; padding: 24px; border-radius: 20px; border: 1px solid #f1f5f9;
        text-decoration: none !important; transition: 0.3s; text-align: center;
    }
    .module-card-mini:hover { background: var(--mst-indigo-light); border-color: var(--mst-indigo); transform: translateY(-3px); }
    .module-icon-mini { font-size: 1.5rem; color: var(--mst-indigo); margin-bottom: 10px; }

    .leave-card {
        background: #fff; border-radius: 20px; border: 1px solid #f1f5f9; padding: 20px;
    }
    .leave-item {
        display: flex; align-items: center; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f8fafc;
    }
    .leave-item:last-child { border: none; }

</style>
@endsection

@section('content')
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-body">
            <section id="beautyden-dashboard">
                
                <!-- Welcome Header -->
                <div class="dashboard-welcome d-flex justify-content-between align-items-end">
                    <div>
                        <h1>BeautyDen Dashboard ✨</h1>
                        <p>Real-time insights for your beauty empire.</p>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-light-primary text-primary px-3 py-1 fs-6 rounded-pill">
                            <i class="bi bi-clock"></i> Last updated: {{ now()->format('h:i A') }}
                        </span>
                    </div>
                </div>

                <!-- Priority Action Alerts -->
                @if($pendingReviews > 0)
                <div class="action-required-banner">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon-wrapper bg-danger text-white mb-0" style="width: 48px; height: 48px; border-radius: 12px;">
                            <i class="bi bi-exclamation-octagon"></i>
                        </div>
                        <div class="action-text">
                            <h5>Pending Reviews Approval</h5>
                            <p>You have <strong>{{ $pendingReviews }}</strong> new customer reviews waiting for your approval.</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.reviews.index', ['status' => 0]) }}" class="btn btn-danger btn-sm px-4 rounded-pill">Take Action</a>
                </div>
                @endif

                <!-- Critical Vital Signs -->
                <div class="primary-stats-grid">
                    <div class="stat-card-luxury card-revenue">
                        <div class="stat-icon-wrapper"><i class="bi bi-wallet2"></i></div>
                        <div class="stat-value">₹{{ number_format($totalRevenue, 0) }}</div>
                        <div class="stat-label">Total Revenue (App + Membership)</div>
                        <small class="text-white-50 mt-2 d-block">Razorpay Online: ₹{{ number_format($totalRazorpayRevenue, 0) }}</small>
                    </div>

                    <a href="{{ route('admin.membership.index') }}" class="stat-card-luxury card-membership">
                        <div class="stat-icon-wrapper"><i class="bi bi-gem"></i></div>
                        <div class="stat-value">{{ $activeMemberships }}</div>
                        <div class="stat-label">Active Memberships</div>
                        <small class="text-muted mt-2 d-block">Premium Subscription Base</small>
                    </a>

                    <a href="{{ route('admin.user.index') }}" class="stat-card-luxury card-users">
                        <div class="stat-icon-wrapper"><i class="bi bi-people"></i></div>
                        <div class="stat-value">{{ $totalUsers }}</div>
                        <div class="stat-label">Total Registered Users</div>
                        <small class="text-success mt-2 d-block">+{{ $newUsersToday }} Joined Today</small>
                    </a>

                    <a href="{{ route('admin.appointments.index', ['date' => date('Y-m-d')]) }}" class="stat-card-luxury card-assigned">
                        <div class="stat-icon-wrapper"><i class="bi bi-calendar-event"></i></div>
                        <div class="stat-value">{{ $todayAppointments }}</div>
                        <div class="stat-label">Today's Appointments</div>
                        <small class="text-info mt-2 d-block">{{ $totalAppointmentsPending }} Approval Required</small>
                    </a>
                </div>

                <div class="row">
                    <!-- Charts -->
                    <div class="col-lg-8">
                        <div class="chart-container bg-white p-3 rounded-4 shadow-sm mb-4">
                            <h5 class="fw-bold mb-3"><i class="bi bi-graph-up text-primary me-2"></i> Booking Trends</h5>
                            <div id="completed-appointments-chart"></div>
                        </div>
                    </div>

                    <!-- Staff Availability -->
                    <div class="col-lg-4">
                        <div class="leave-card shadow-sm mb-4">
                            <h5 class="fw-bold mb-3"><i class="bi bi-calendar2-x text-danger me-2"></i> Staff on Leave Today</h5>
                            <div class="leave-list">
                                @forelse($onLeaveToday as $leave)
                                <div class="leave-item">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar bg-light-danger p-1"><i class="bi bi-person"></i></div>
                                        <div>
                                            <div class="fw-bold small">{{ $leave->beautician->name }}</div>
                                            <span class="badge bg-light-danger text-danger" style="font-size: 0.6rem;">Status: Leave</span>
                                        </div>
                                    </div>
                                    <span class="text-muted small">Full Day</span>
                                </div>
                                @empty
                                <div class="text-center py-4">
                                    <i class="bi bi-check2-circle text-success fs-2"></i>
                                    <p class="text-muted small mb-0">All staff members are available today.</p>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Admin Modules Breakdown -->
                <div class="dashboard-section">
                    <div class="section-title">
                        <i class="bi bi-grid-fill"></i>
                        <h4>Operations & Intelligence</h4>
                    </div>
                    <div class="module-grid">
                        <a href="{{ route('admin.razorpay.index') }}" class="module-card-mini">
                            <div class="module-icon-mini"><i class="bi bi-credit-card"></i></div>
                            <span class="module-name">Transactions</span>
                        </a>
                        <a href="{{ route('admin.combo.index') }}" class="module-card-mini">
                            <div class="module-icon-mini"><i class="bi bi-box-seam"></i></div>
                            <span class="module-count">{{ $activeCombos }}</span>
                            <span class="module-name">Combos</span>
                        </a>
                        <a href="{{ route('admin.notifications.index') }}" class="module-card-mini">
                            <div class="module-icon-mini"><i class="bi bi-megaphone"></i></div>
                            <span class="module-name">Push Center</span>
                        </a>
                        <a href="{{ route('admin.service-master.index') }}" class="module-card-mini">
                            <div class="module-icon-mini"><i class="bi bi-scissors"></i></div>
                            <span class="module-name">App Pricing</span>
                        </a>
                    </div>
                </div>

            </section>
        </div>
    </div>
</div>
@endsection

@section('footer_script_content')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const chartLabels = @json($chartLabels);
        const chartData = @json($chartData);

        const dailyOptions = {
            series: [{ name: 'Bookings', data: chartData }],
            chart: { type: 'area', height: 350, toolbar: { show: false }, zoom: { enabled: false } },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 4 },
            xaxis: { categories: chartLabels, labels: { style: { colors: '#64748b' } } },
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.1 } },
            colors: ['#102365'],
            tooltip: { theme: 'light' }
        };

        const dailyChart = new ApexCharts(document.querySelector("#completed-appointments-chart"), dailyOptions);
        dailyChart.render();
    });
</script>
@endsection
