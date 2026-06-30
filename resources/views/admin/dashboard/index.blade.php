@extends('admin.layouts.app')

@section('page_title', 'Dashboard')
@section('page_heading', 'Analytics Overview')

@section('content')
<div class="pa-dashboard">

    {{-- Header --}}
    <div class="pa-dashboard-header">
        <div>
            <h1>Analytics Overview</h1>
            <p>Your business performance at a glance</p>
        </div>
        <div class="pa-page-actions">
            <div class="d-flex align-items-center gap-2 bg-white border rounded-3 px-3 py-1" style="border-color:var(--pa-border)!important;">
                <input type="text" id="global_start_date" class="form-control form-control-sm border-0 shadow-none" style="width:110px;font-size:0.82rem;" value="{{ now()->startOfMonth()->format('d-m-Y') }}">
                <span class="text-muted small">—</span>
                <input type="text" id="global_end_date" class="form-control form-control-sm border-0 shadow-none" style="width:110px;font-size:0.82rem;" value="{{ now()->endOfMonth()->format('d-m-Y') }}">
                <button class="pa-btn pa-btn-primary pa-btn-icon" id="btn-refresh-dashboard" type="button" title="Refresh">
                    <i class="bi bi-arrow-repeat"></i>
                </button>
            </div>
            <a href="{{ route('admin.reports.index') }}" class="pa-btn pa-btn-outline"><i class="bi bi-clock-history"></i> History</a>
            <a href="{{ route('admin.dashboard.export-analytics') }}" class="pa-btn pa-btn-outline"><i class="bi bi-download"></i> Export</a>
        </div>
    </div>

    @if($pendingReviews > 0)
    <div class="alert d-flex align-items-center justify-content-between mb-4 border-0 rounded-3" style="background:#fef2f2;border-left:4px solid #ef4444!important;">
        <div class="d-flex align-items-center gap-3">
            <div class="pa-stat-icon" style="background:#fee2e2;color:#ef4444;"><i class="bi bi-exclamation-triangle"></i></div>
            <div>
                <strong class="text-danger d-block" style="font-size:0.9rem;">Action Required</strong>
                <span class="text-muted" style="font-size:0.82rem;">{{ $pendingReviews }} reviews awaiting approval</span>
            </div>
        </div>
        <a href="{{ route('admin.reviews.index', ['status' => 0]) }}" class="pa-btn pa-btn-sm" style="background:#ef4444;color:#fff;">Review Now</a>
    </div>
    @endif

    {{-- Stat Cards Row --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-4">
            <div class="pa-stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="pa-stat-label">Total Revenue</div>
                        <div class="pa-stat-value" id="stat-revenue">₹{{ number_format($totalRevenue, 0) }}</div>
                        <div class="pa-stat-meta up"><i class="bi bi-arrow-up-short"></i> All time</div>
                    </div>
                    <div class="pa-stat-icon primary"><i class="bi bi-wallet2"></i></div>
                </div>
            </div>
        </div>
        {{-- Active Plans (Membership) — currently not in use
        <div class="col-sm-6 col-xl-3">
            <a href="{{ route('admin.membership.index') }}" class="text-decoration-none">
                <div class="pa-stat-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="pa-stat-label">Active Plans</div>
                            <div class="pa-stat-value" id="stat-plans">{{ $activeMemberships }}</div>
                            <div class="pa-stat-meta up"><span class="pa-badge pa-badge-success">Active</span></div>
                        </div>
                        <div class="pa-stat-icon success"><i class="bi bi-gem"></i></div>
                    </div>
                </div>
            </a>
        </div>
        --}}
        <div class="col-sm-6 col-xl-4">
            <a href="{{ route('admin.user.index') }}" class="text-decoration-none">
                <div class="pa-stat-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="pa-stat-label">Total Customers</div>
                            <div class="pa-stat-value" id="stat-users">{{ $totalUsers }}</div>
                            <div class="pa-stat-meta"><i class="bi bi-people"></i> Registered</div>
                        </div>
                        <div class="pa-stat-icon info"><i class="bi bi-people"></i></div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-xl-4">
            <a href="{{ route('admin.appointments.index', ['date' => date('Y-m-d')]) }}" class="text-decoration-none">
                <div class="pa-stat-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="pa-stat-label">Today's Appointments</div>
                            <div class="pa-stat-value" id="stat-appts">{{ $todayAppointments }}</div>
                            <div class="pa-stat-meta"><i class="bi bi-calendar-event"></i> Scheduled</div>
                        </div>
                        <div class="pa-stat-icon warning"><i class="bi bi-calendar-check"></i></div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="pa-card pa-chart-card h-100">
                <div class="pa-card-header">
                    <h6>Revenue Trend</h6>
                    <div class="pa-timeframe">
                        <button type="button">W</button>
                        <button type="button" class="active">M</button>
                        <button type="button">Y</button>
                    </div>
                </div>
                <div class="pa-card-body pt-0">
                    <div id="completed-appointments-chart"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bottom Row --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-4">
            <div class="pa-card h-100">
                <div class="pa-card-header">
                    <h6>Staff on Leave Today</h6>
                    <a href="{{ route('admin.attendance.index') }}" class="pa-btn pa-btn-sm pa-btn-outline">View All</a>
                </div>
                <div class="pa-card-body">
                    @forelse($onLeaveToday as $leave)
                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom" style="border-color:var(--pa-border-light)!important;">
                        <div class="d-flex align-items-center gap-2">
                            <div class="pa-stat-icon primary" style="width:36px;height:36px;font-size:0.85rem;"><i class="bi bi-person"></i></div>
                            <div>
                                <strong style="font-size:0.85rem;">{{ $leave->beautician->name ?? 'N/A' }}</strong>
                                <span class="pa-badge pa-badge-danger d-block mt-1" style="width:fit-content;">On Leave</span>
                            </div>
                        </div>
                        <span class="text-muted small">Full Day</span>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <div class="pa-stat-icon success mx-auto mb-2"><i class="bi bi-check-circle"></i></div>
                        <strong style="font-size:0.9rem;">All Staff Available</strong>
                        <p class="text-muted small mb-0 mt-1">Everyone is on duty today</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="pa-card h-100">
                <div class="pa-card-header">
                    <h6>Recent Activity</h6>
                </div>
                <div class="pa-card-body">
                    <div class="pa-activity-item">
                        <div class="pa-activity-dot" style="background:var(--pa-success);"></div>
                        <div>
                            <div class="pa-activity-text"><strong>New appointment</strong> booked for today</div>
                            <div class="pa-activity-time">Just now</div>
                        </div>
                    </div>
                    <div class="pa-activity-item">
                        <div class="pa-activity-dot" style="background:var(--pa-info);"></div>
                        <div>
                            <div class="pa-activity-text"><strong>{{ $totalUsers }}</strong> total registered customers</div>
                            <div class="pa-activity-time">Updated live</div>
                        </div>
                    </div>
                    <div class="pa-activity-item">
                        <div class="pa-activity-dot" style="background:var(--pa-warning);"></div>
                        <div>
                            <div class="pa-activity-text"><strong>{{ $pendingReviews }}</strong> reviews pending approval</div>
                            <div class="pa-activity-time">Requires action</div>
                        </div>
                    </div>
                    {{-- Membership activity — currently not in use
                    <div class="pa-activity-item">
                        <div class="pa-activity-dot" style="background:var(--pa-primary);"></div>
                        <div>
                            <div class="pa-activity-text"><strong>{{ $activeMemberships }}</strong> active membership plans</div>
                            <div class="pa-activity-time">Current period</div>
                        </div>
                    </div>
                    --}}
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="pa-quick-actions">
                <div class="pa-qa-header">
                    <div class="pa-qa-header-icon"><i class="bi bi-lightning-charge"></i></div>
                    <div>
                        <h6>Quick Actions</h6>
                        <span>Jump to key modules</span>
                    </div>
                </div>
                <div class="pa-qa-list">
                    @php
                        $quickLinks = [
                            ['icon' => 'calendar-plus', 'label' => 'Appointments', 'desc' => 'Manage bookings', 'route' => 'admin.appointments.index', 'color' => 'primary'],
                            ['icon' => 'person-plus', 'label' => 'Customers', 'desc' => 'View all users', 'route' => 'admin.user.index', 'color' => 'info'],
                            ['icon' => 'bag-plus', 'label' => 'Products', 'desc' => 'Product catalog', 'route' => 'admin.product-item.index', 'color' => 'warning'],
                            ['icon' => 'scissors', 'label' => 'Services', 'desc' => 'Service catalog', 'route' => 'admin.service.index', 'color' => 'success'],
                            ['icon' => 'receipt', 'label' => 'Orders', 'desc' => 'Product orders', 'route' => 'admin.product-order.index', 'color' => 'primary'],
                            ['icon' => 'bar-chart', 'label' => 'Reports', 'desc' => 'Analytics & exports', 'route' => 'admin.reports.index', 'color' => 'info'],
                        ];
                    @endphp
                    @foreach($quickLinks as $link)
                    <a href="{{ route($link['route']) }}" class="pa-qa-item">
                        <span class="pa-qa-item-icon {{ $link['color'] }}"><i class="bi bi-{{ $link['icon'] }}"></i></span>
                        <span class="pa-qa-item-text">
                            <strong>{{ $link['label'] }}</strong>
                            <small>{{ $link['desc'] }}</small>
                        </span>
                        <i class="bi bi-arrow-right pa-qa-item-arrow"></i>
                    </a>
                    @endforeach
                </div>
                <div class="pa-qa-footer">
                    <a href="{{ route('admin.reports.index') }}">View all reports <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>

    {{-- Detailed Analytics --}}
    <section class="pa-detailed-analytics mb-4">
        <div class="pa-da-header">
            <div class="pa-da-header-left">
                <div class="pa-da-header-icon"><i class="bi bi-bar-chart-line"></i></div>
                <div>
                    <h5>Detailed Analytics</h5>
                    <p>Performance breakdown for your selected date range</p>
                </div>
            </div>
            <div class="pa-timeframe">
                <button type="button">W</button>
                <button type="button" class="active">M</button>
                <button type="button">Y</button>
            </div>
        </div>

        <div class="pa-da-body">
            <div class="analytics-filter-row d-none">
                <input type="text" id="report_start_date" value="{{ now()->startOfMonth()->format('d-m-Y') }}">
                <input type="text" id="report_end_date" value="{{ now()->endOfMonth()->format('d-m-Y') }}">
            </div>

            <div class="row g-4">
                {{-- Daily Revenue --}}
                <div class="col-lg-6">
                    <div class="pa-da-report pa-da-report--revenue">
                        <div class="pa-da-report-head">
                            <div class="pa-da-report-title">
                                <span class="pa-da-report-icon"><i class="bi bi-wallet2"></i></span>
                                <div>
                                    <h6>Daily Revenue</h6>
                                    <span>Income tracked per day</span>
                                </div>
                            </div>
                            <button type="button" class="pa-da-download" onclick="downloadReport('revenue')" title="Export CSV">
                                <i class="bi bi-download"></i>
                            </button>
                        </div>
                        <div class="pa-da-report-body">
                            <table class="pa-da-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th class="text-end">Revenue</th>
                                    </tr>
                                </thead>
                                <tbody id="revenue-report-body">
                                    <tr><td colspan="2"><div class="pa-da-empty"><i class="bi bi-hourglass-split"></i><p>Loading data...</p></div></td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="pa-da-report-footer">
                            <span>Total Revenue</span>
                            <strong id="total-revenue-val">₹0</strong>
                        </div>
                    </div>
                </div>

                {{-- Top Staff by Revenue --}}
                <div class="col-lg-6">
                    <div class="pa-da-report pa-da-report--staff-rev">
                        <div class="pa-da-report-head">
                            <div class="pa-da-report-title">
                                <span class="pa-da-report-icon"><i class="bi bi-trophy"></i></span>
                                <div>
                                    <h6>Top Staff by Revenue</h6>
                                    <span>Highest earning team members</span>
                                </div>
                            </div>
                            <button type="button" class="pa-da-download" onclick="downloadReport('staff_revenue')" title="Export CSV">
                                <i class="bi bi-download"></i>
                            </button>
                        </div>
                        <div class="pa-da-report-body">
                            <table class="pa-da-table">
                                <thead>
                                    <tr>
                                        <th>Staff</th>
                                        <th class="text-end">Revenue</th>
                                    </tr>
                                </thead>
                                <tbody id="staff-revenue-body">
                                    <tr><td colspan="2"><div class="pa-da-empty"><i class="bi bi-hourglass-split"></i><p>Loading data...</p></div></td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="pa-da-report-footer">
                            <span>Top Performers</span>
                            <strong id="staff-revenue-count">—</strong>
                        </div>
                    </div>
                </div>

                {{-- Top Staff by Services --}}
                <div class="col-lg-6">
                    <div class="pa-da-report pa-da-report--staff-svc">
                        <div class="pa-da-report-head">
                            <div class="pa-da-report-title">
                                <span class="pa-da-report-icon"><i class="bi bi-person-check"></i></span>
                                <div>
                                    <h6>Top Staff by Services</h6>
                                    <span>Most services completed</span>
                                </div>
                            </div>
                            <button type="button" class="pa-da-download" onclick="downloadReport('staff_services')" title="Export CSV">
                                <i class="bi bi-download"></i>
                            </button>
                        </div>
                        <div class="pa-da-report-body">
                            <table class="pa-da-table">
                                <thead>
                                    <tr>
                                        <th>Staff</th>
                                        <th class="text-center">Services</th>
                                        <th class="text-end">Revenue</th>
                                    </tr>
                                </thead>
                                <tbody id="staff-services-body">
                                    <tr><td colspan="3"><div class="pa-da-empty"><i class="bi bi-hourglass-split"></i><p>Loading data...</p></div></td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="pa-da-report-footer">
                            <span>Active Staff</span>
                            <strong id="staff-services-count">—</strong>
                        </div>
                    </div>
                </div>

                {{-- Appointments Per Day --}}
                <div class="col-lg-6">
                    <div class="pa-da-report pa-da-report--appts">
                        <div class="pa-da-report-head">
                            <div class="pa-da-report-title">
                                <span class="pa-da-report-icon"><i class="bi bi-calendar3"></i></span>
                                <div>
                                    <h6>Appointments Per Day</h6>
                                    <span>Daily booking volume</span>
                                </div>
                            </div>
                            <button type="button" class="pa-da-download" onclick="downloadReport('appointments')" title="Export CSV">
                                <i class="bi bi-download"></i>
                            </button>
                        </div>
                        <div class="pa-da-report-body">
                            <table class="pa-da-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th class="text-end">Bookings</th>
                                    </tr>
                                </thead>
                                <tbody id="appt-report-body">
                                    <tr><td colspan="2"><div class="pa-da-empty"><i class="bi bi-hourglass-split"></i><p>Loading data...</p></div></td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="pa-da-report-footer">
                            <span>Total Bookings</span>
                            <strong id="total-appt-val">0</strong>
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
        const el = document.querySelector("#completed-appointments-chart");
        if (!el) return;
        const options = {
            series: series,
            chart: { type: 'area', height: 280, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2.5 },
            xaxis: { categories: labels, labels: { style: { colors: '#94a3b8', fontSize: '11px' } } },
            yaxis: { labels: { style: { colors: '#94a3b8', fontSize: '11px' } } },
            grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05 } },
            colors: ['#4f46e5'],
            tooltip: { theme: 'light' },
            legend: { show: false }
        };
        if (dailyChart) { dailyChart.updateOptions(options); }
        else { dailyChart = new ApexCharts(el, options); dailyChart.render(); }
    }

    initChart(@json($chartLabels), [{ name: 'Completed', data: @json($chartData) }]);

    function staffInitials(name) {
        if (!name) return '?';
        return name.split(' ').map(w => w[0]).join('').substring(0, 2).toUpperCase();
    }

    function emptyRow(cols, msg) {
        return `<tr><td colspan="${cols}"><div class="pa-da-empty"><i class="bi bi-inbox"></i><p>${msg}</p></div></td></tr>`;
    }

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
                $('#stat-users').text(res.stats.total_users);
                $('#stat-appts').text(res.stats.total_appts);
                initChart(res.chart.labels, res.chart.series);

                let revHtml = '';
                res.daily_revenue.forEach(item => {
                    revHtml += `<tr><td>${item.date}</td><td class="text-end td-value">₹${parseFloat(item.revenue).toLocaleString()}</td></tr>`;
                });
                $('#revenue-report-body').html(revHtml || emptyRow(2, 'No revenue data for this period'));
                $('#total-revenue-val').text('₹' + res.stats.total_revenue);

                let apptTotal = 0;
                let apptHtml = '';
                if (res.chart.labels) {
                    res.chart.labels.forEach((label, i) => {
                        const val = res.chart.series[0]?.data[i] || 0;
                        apptTotal += val;
                        apptHtml += `<tr><td>${label}</td><td class="text-end"><span class="td-badge">${val}</span></td></tr>`;
                    });
                }
                $('#appt-report-body').html(apptHtml || emptyRow(2, 'No appointment data for this period'));
                $('#total-appt-val').text(apptTotal.toLocaleString());

                let ssHtml = '';
                res.top_staff_services.forEach(item => {
                    ssHtml += `<tr>
                        <td><div class="pa-da-staff-name"><span class="pa-da-staff-avatar">${staffInitials(item.staff)}</span>${item.staff}</div></td>
                        <td class="text-center"><span class="td-badge">${item.services}</span></td>
                        <td class="text-end td-value">₹${parseFloat(item.revenue).toLocaleString()}</td>
                    </tr>`;
                });
                $('#staff-services-body').html(ssHtml || emptyRow(3, 'No staff performance data'));
                $('#staff-services-count').text(res.top_staff_services.length + ' staff');

                let srHtml = '';
                res.top_staff_revenue.forEach(item => {
                    srHtml += `<tr>
                        <td><div class="pa-da-staff-name"><span class="pa-da-staff-avatar">${staffInitials(item.staff)}</span>${item.staff}</div></td>
                        <td class="text-end td-value">₹${parseFloat(item.revenue).toLocaleString()}</td>
                    </tr>`;
                });
                $('#staff-revenue-body').html(srHtml || emptyRow(2, 'No staff revenue data'));
                $('#staff-revenue-count').text(res.top_staff_revenue.length + ' staff');
            },
            error: function() { $('#btn-refresh-dashboard i').removeClass('fa-spin'); }
        });
    }

    $('#btn-refresh-dashboard').on('click', refreshAnalytics);
    setTimeout(refreshAnalytics, 500);

    window.downloadReport = function(type) {
        let start = $('#global_start_date').val();
        let end = $('#global_end_date').val();
        window.location.href = "{{ route('admin.dashboard.export-analytics') }}?type=" + type + "&start_date=" + start + "&end_date=" + end;
    };
});
</script>
@endsection
