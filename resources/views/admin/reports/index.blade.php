@extends('admin.layouts.app')

@section('page_title', 'Reports')
@section('page_heading', 'Reports & Analytics')

@section('content')
<div class="pa-dashboard">

    {{-- Header --}}
    <div class="pa-dashboard-header">
        <div>
            <h1>Reports & Analytics</h1>
            <p>All-time business performance — complete data up to today</p>
        </div>
        <div class="pa-page-actions">
            <a href="{{ route('admin.dashboard.export-analytics') }}?type=revenue" class="pa-btn pa-btn-outline">
                <i class="bi bi-download"></i> Export CSV
            </a>
            <a href="{{ route('admin.dashboard') }}" class="pa-btn pa-btn-primary">
                <i class="bi bi-grid"></i> Dashboard
            </a>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-4 col-xl">
            <div class="pa-stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="pa-stat-label">Total Revenue</div>
                        <div class="pa-stat-value">₹{{ number_format($totalRevenue, 0) }}</div>
                        <div class="pa-stat-meta up"><i class="bi bi-arrow-up-short"></i> All time</div>
                    </div>
                    <div class="pa-stat-icon primary"><i class="bi bi-wallet2"></i></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4 col-xl">
            <a href="{{ route('admin.appointments.index') }}" class="text-decoration-none">
                <div class="pa-stat-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="pa-stat-label">Total Appointments</div>
                            <div class="pa-stat-value">{{ number_format($totalAppointments) }}</div>
                            <div class="pa-stat-meta"><i class="bi bi-calendar-event"></i> All statuses</div>
                        </div>
                        <div class="pa-stat-icon info"><i class="bi bi-calendar3"></i></div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-lg-4 col-xl">
            <div class="pa-stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="pa-stat-label">Completed</div>
                        <div class="pa-stat-value">{{ number_format($totalCompletedAppointments) }}</div>
                        <div class="pa-stat-meta up"><i class="bi bi-check-circle"></i> All time</div>
                    </div>
                    <div class="pa-stat-icon success"><i class="bi bi-calendar-check"></i></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4 col-xl">
            <a href="{{ route('admin.user.index') }}" class="text-decoration-none">
                <div class="pa-stat-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="pa-stat-label">Registered Clients</div>
                            <div class="pa-stat-value">{{ number_format($totalClients) }}</div>
                            <div class="pa-stat-meta"><i class="bi bi-person"></i> All time</div>
                        </div>
                        <div class="pa-stat-icon info"><i class="bi bi-people"></i></div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-lg-4 col-xl">
            <a href="{{ route('admin.user.index') }}" class="text-decoration-none">
                <div class="pa-stat-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="pa-stat-label">Registered Beauticians</div>
                            <div class="pa-stat-value">{{ number_format($totalBeauticians) }}</div>
                            <div class="pa-stat-meta"><i class="bi bi-scissors"></i> All time</div>
                        </div>
                        <div class="pa-stat-icon warning"><i class="bi bi-person-badge"></i></div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- Revenue Overview --}}
    <section class="pa-detailed-analytics mb-4">
        <div class="pa-da-header">
            <div class="pa-da-header-left">
                <div class="pa-da-header-icon"><i class="bi bi-graph-up-arrow"></i></div>
                <div>
                    <h5>Revenue Overview</h5>
                    <p>All-time month-wise revenue — appointments & subscriptions</p>
                </div>
            </div>
            <span class="pa-badge pa-badge-primary">₹{{ number_format($totalRevenue, 0) }} total</span>
        </div>
        <div class="pa-da-body">
            <div id="reports-line-chart"></div>
        </div>
    </section>

    {{-- Bottom Charts --}}
    <section class="pa-detailed-analytics">
        <div class="pa-da-header">
            <div class="pa-da-header-left">
                <div class="pa-da-header-icon"><i class="bi bi-bar-chart-line"></i></div>
                <div>
                    <h5>Detailed Breakdown</h5>
                    <p>All appointments & user registrations — complete history till today</p>
                </div>
            </div>
        </div>
        <div class="pa-da-body">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="pa-da-report pa-da-report--revenue">
                        <div class="pa-da-report-head">
                            <div class="pa-da-report-title">
                                <span class="pa-da-report-icon"><i class="bi bi-calendar-check"></i></span>
                                <div>
                                    <h6>All Appointments</h6>
                                    <span>Pending · Assigned · Completed · Rejected</span>
                                </div>
                            </div>
                            <span class="pa-badge pa-badge-primary">{{ number_format($totalAppointments) }} total</span>
                        </div>
                        <div class="pa-da-report-body" style="max-height:none;padding:0.5rem 0;">
                            <div id="reports-bar-chart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="pa-da-report pa-da-report--staff-svc">
                        <div class="pa-da-report-head">
                            <div class="pa-da-report-title">
                                <span class="pa-da-report-icon"><i class="bi bi-people"></i></span>
                                <div>
                                    <h6>User Registrations</h6>
                                    <span>Clients vs Beauticians — all time</span>
                                </div>
                            </div>
                            <div class="d-flex gap-2 flex-wrap">
                                <span class="pa-badge pa-badge-info">{{ number_format($totalClients) }} Clients</span>
                                <span class="pa-badge pa-badge-primary">{{ number_format($totalBeauticians) }} Beauticians</span>
                            </div>
                        </div>
                        <div class="pa-da-report-body" style="max-height:none;padding:0.5rem 0;">
                            <div id="reports-users-chart"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>
@endsection

@section('footer_script_content')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartDefaults = {
        fontFamily: 'Inter, sans-serif',
        toolbar: { show: false },
    };
    const monthLabels = @json($monthLabels);

    new ApexCharts(document.querySelector("#reports-line-chart"), {
        series: [{ name: 'Revenue', data: @json($revenueMonthCounts) }],
        chart: { type: 'area', height: 340, ...chartDefaults },
        stroke: { curve: 'smooth', width: 2.5 },
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05 } },
        colors: ['#4f46e5'],
        xaxis: {
            categories: monthLabels,
            labels: { style: { colors: '#94a3b8', fontSize: '11px' }, rotate: -45 }
        },
        yaxis: {
            labels: {
                style: { colors: '#94a3b8', fontSize: '11px' },
                formatter: val => '₹' + Math.round(val).toLocaleString()
            }
        },
        grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
        dataLabels: { enabled: false },
        tooltip: {
            y: { formatter: val => '₹' + parseFloat(val).toLocaleString('en-IN', { maximumFractionDigits: 0 }) }
        }
    }).render();

    new ApexCharts(document.querySelector("#reports-bar-chart"), {
        series: [
            { name: 'Pending', data: @json($pendingMonthCounts) },
            { name: 'Assigned', data: @json($assignedMonthCounts) },
            { name: 'Completed', data: @json($completedMonthCounts) },
            { name: 'Rejected', data: @json($rejectedMonthCounts) }
        ],
        chart: { type: 'bar', height: 320, stacked: true, ...chartDefaults },
        plotOptions: { bar: { borderRadius: 4, columnWidth: '55%' } },
        colors: ['#f59e0b', '#3b82f6', '#22c55e', '#ef4444'],
        xaxis: {
            categories: monthLabels,
            labels: { style: { colors: '#94a3b8', fontSize: '10px' }, rotate: -45 }
        },
        yaxis: { labels: { style: { colors: '#94a3b8', fontSize: '11px' } }, forceNiceScale: true, tickAmount: 5 },
        legend: { position: 'top', fontSize: '12px', fontWeight: 600 },
        grid: { borderColor: '#f1f5f9' },
        dataLabels: { enabled: false },
        tooltip: { y: { formatter: val => val + ' appointments' } }
    }).render();

    new ApexCharts(document.querySelector("#reports-users-chart"), {
        series: [
            { name: 'Clients', data: @json($clientMonthCounts) },
            { name: 'Beauticians', data: @json($beauticianMonthCounts) }
        ],
        chart: { type: 'bar', height: 320, stacked: true, ...chartDefaults },
        plotOptions: { bar: { borderRadius: 4, columnWidth: '55%' } },
        colors: ['#3b82f6', '#4f46e5'],
        xaxis: {
            categories: monthLabels,
            labels: { style: { colors: '#94a3b8', fontSize: '10px' }, rotate: -45 }
        },
        yaxis: { labels: { style: { colors: '#94a3b8', fontSize: '11px' } }, forceNiceScale: true, tickAmount: 5 },
        legend: { position: 'top', fontSize: '12px', fontWeight: 600 },
        grid: { borderColor: '#f1f5f9' },
        dataLabels: { enabled: false },
        tooltip: { y: { formatter: val => val + ' registered' } }
    }).render();
});
</script>
@endsection
