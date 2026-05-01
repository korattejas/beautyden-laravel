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
        background: #f8fafc;
        min-height: 100vh;
    }

    /* Welcome Header */
    .dashboard-welcome h1 {
        font-weight: 800;
        color: #1e293b;
        font-size: 2.2rem;
        letter-spacing: -0.5px;
    }
    .dashboard-welcome p {
        color: #64748b;
        font-size: 1.1rem;
    }

    /* Stat Cards */
    .dashboard-stat-card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        overflow: hidden;
        position: relative;
        background: #fff;
        transition: transform 0.3s ease;
    }
    .dashboard-stat-card:hover {
        transform: translateY(-5px);
    }
    .card-accent-bar {
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
    }
    .stat-avatar {
        border-radius: 12px;
        width: 56px;
        height: 56px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .stat-label {
        color: #82868b;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 2px;
    }
    .stat-value {
        color: #1e293b;
        font-size: 1.6rem;
        font-weight: 800;
        margin-bottom: 0;
    }

    /* Module Section */
    .dashboard-section { margin-top: 2rem; }
    .section-title { margin-bottom: 1.5rem; }
    .section-title h4 { font-weight: 800; color: #1e293b; }

    .module-card-premium {
        background: #fff;
        padding: 20px;
        border-radius: 16px;
        border: 1px solid #edf2f7;
        text-decoration: none !important;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 6px rgba(0,0,0,0.02);
    }
    .module-card-premium:hover {
        background: #fff;
        border-color: #7367f0;
        box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        transform: translateY(-3px);
    }
    .module-icon {
        font-size: 1.8rem;
        color: #7367f0;
        margin-bottom: 12px;
    }
    .module-name {
        font-weight: 700;
        color: #475569;
        font-size: 0.9rem;
    }
    .module-count {
        background: rgba(115, 103, 240, 0.1);
        color: #7367f0;
        padding: 2px 10px;
        border-radius: 50px;
        font-size: 0.7rem;
        font-weight: 800;
        margin-bottom: 5px;
    }

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
                                    <h2 class="stat-value">₹{{ number_format($totalRevenue, 0) }}</h2>
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
                                <div>
                                    <p class="stat-label">Active Plans</p>
                                    <h2 class="stat-value">{{ $activeMemberships }}</h2>
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
                                <div>
                                    <p class="stat-label">Total Users</p>
                                    <h2 class="stat-value">{{ $totalUsers }}</h2>
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
                                <div>
                                    <p class="stat-label">Today's Appt.</p>
                                    <h2 class="stat-value">{{ $todayAppointments }}</h2>
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
                                <div class="d-flex align-items-center justify-content-between py-1 border-bottom border-light">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="stat-avatar" style="background: rgba(234, 84, 85, 0.1); width: 40px; height: 40px;">
                                            <i class="bi bi-person text-danger"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold small" style="color: #1e293b;">{{ $leave->beautician->name ?? 'N/A' }}</div>
                                            <span class="badge bg-light-danger text-danger" style="font-size: 0.6rem; font-weight: 700;">ON LEAVE</span>
                                        </div>
                                    </div>
                                    <span class="text-muted small fw-bold">Full Day</span>
                                </div>
                                @empty
                                <div class="text-center py-4">
                                    <div class="stat-avatar mx-auto mb-2" style="background: rgba(40, 199, 111, 0.1); width: 64px; height: 64px;">
                                        <i class="bi bi-check2-circle text-success fs-1"></i>
                                    </div>
                                    <p class="text-muted small mb-0 fw-bold">All staff available today</p>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Admin Modules Breakdown -->
                <div class="dashboard-section">
                    <div class="section-title">
                        <h4>Management Center</h4>
                    </div>
                    <div class="row g-2">
                        <div class="col-6 col-md-3">
                            <a href="{{ route('admin.razorpay.index') }}" class="module-card-premium">
                                <i class="bi bi-credit-card module-icon"></i>
                                <span class="module-name">Transactions</span>
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="{{ route('admin.combo.index') }}" class="module-card-premium">
                                <span class="module-count">{{ $activeCombos }}</span>
                                <i class="bi bi-box-seam module-icon"></i>
                                <span class="module-name">Combos</span>
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="{{ route('admin.notifications.index') }}" class="module-card-premium">
                                <i class="bi bi-megaphone module-icon"></i>
                                <span class="module-name">Push Center</span>
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="{{ route('admin.service-master.index') }}" class="module-card-premium">
                                <i class="bi bi-scissors module-icon"></i>
                                <span class="module-name">App Pricing</span>
                            </a>
                        </div>
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
