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
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
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
    .card-pending .stat-icon-wrapper { background: #fffbeb; color: var(--mst-warning); }
    .card-completed .stat-icon-wrapper { background: #ecfdf5; color: var(--mst-success); }
    .card-assigned .stat-icon-wrapper { background: #eff6ff; color: var(--mst-info); }

    .stat-value {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 4px;
        color: var(--mst-text-main);
    }
    .card-revenue .stat-value { color: #fff; }

    .stat-label {
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--mst-text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .card-revenue .stat-label { color: rgba(255, 255, 255, 0.8); }

    /* Module Sections */
    .dashboard-section {
        margin-bottom: 3.5rem;
    }

    .section-title {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 1.5rem;
        padding-left: 8px;
    }
    .section-title i {
        font-size: 1.4rem;
        color: var(--mst-indigo);
    }
    .section-title h4 {
        margin: 0;
        font-weight: 700;
        color: var(--mst-text-main);
        font-size: 1.25rem;
    }

    .module-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 1.5rem;
    }

    .module-card-mini {
        background: #fff;
        padding: 30px 24px;
        border-radius: 24px;
        border: 1px solid #f1f5f9;
        text-decoration: none !important;
        transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }

    .module-card-mini:hover {
        background: var(--mst-indigo-light);
        border-color: var(--mst-indigo);
        transform: translateY(-4px);
    }

    .module-icon-mini {
        font-size: 1.85rem;
        color: var(--mst-indigo);
        margin-bottom: 15px;
        background: #f8fafc;
        width: 64px;
        height: 64px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 18px;
        transition: 0.2s;
    }
    .module-card-mini:hover .module-icon-mini {
        background: var(--mst-indigo);
        color: #fff;
    }

    .module-count {
        font-weight: 800;
        font-size: 1.75rem;
        color: var(--mst-text-main);
        display: block;
        margin-bottom: 4px;
    }

    .module-name {
        font-size: 1rem;
        font-weight: 600;
        color: var(--mst-text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .dashboard-welcome h1 { font-size: 1.6rem; }
        .primary-stats-grid { grid-template-columns: 1fr 1fr; }
        .module-grid { grid-template-columns: 1fr 1fr; }
    }

</style>
@endsection

@section('content')
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-body">
            <section id="beautyden-dashboard">
                
                <!-- Welcome Header -->
                <div class="dashboard-welcome">
                    <h1>Welcome Back, BeautyDen Admin! ✨</h1>
                    <p>Here's what's happening with your business today.</p>
                </div>

                <!-- Primary Vital Signs -->
                <div class="primary-stats-grid">
                    <!-- Total Revenue -->
                    <div class="stat-card-luxury card-revenue">
                        <div class="stat-icon-wrapper">
                            <i class="bi bi-wallet2"></i>
                        </div>
                        <div class="stat-value">₹{{ number_format($totalRevenue, 0) }}</div>
                        <div class="stat-label">Total Revenue</div>
                    </div>

                    <!-- Today's Appointments -->
                    <a href="{{ route('admin.appointments.index') }}" class="stat-card-luxury card-pending">
                        <div class="stat-icon-wrapper">
                            <i class="bi bi-calendar-event"></i>
                        </div>
                        <div class="stat-value">{{ $todayAppointments }}</div>
                        <div class="stat-label">Today's Appointments</div>
                    </a>

                    <!-- Pending Approval -->
                    <a href="{{ route('admin.appointments.index', ['status' => 1]) }}" class="stat-card-luxury card-pending" style="--mst-warning: #f59e0b;">
                        <div class="stat-icon-wrapper">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <div class="stat-value">{{ $totalAppointmentsPending }}</div>
                        <div class="stat-label">Pending Approval</div>
                    </a>

                    <!-- Total Experts -->
                    <a href="{{ route('admin.team.index') }}" class="stat-card-luxury card-assigned">
                        <div class="stat-icon-wrapper">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div class="stat-value">{{ $totalTeamMember }}</div>
                        <div class="stat-label">Expert Professionals</div>
                    </a>
                </div>

                <!-- Content Management Group -->
                <div class="dashboard-section">
                    <div class="section-title">
                        <i class="bi bi-grid-1x2-fill"></i>
                        <h4>Service & Catalog</h4>
                    </div>
                    <div class="module-grid">
                        <a href="{{ route('admin.service-category.index') }}" class="module-card-mini">
                            <div class="module-icon-mini"><i class="bi bi-layers"></i></div>
                            <span class="module-count">{{ $totalServiceCategory }}</span>
                            <span class="module-name">Categories</span>
                        </a>
                        <a href="{{ route('admin.service.index') }}" class="module-card-mini">
                            <div class="module-icon-mini"><i class="bi bi-scissors"></i></div>
                            <span class="module-count">{{ $totalServices }}</span>
                            <span class="module-name">Services</span>
                        </a>
                        <a href="{{ route('admin.city.index') }}" class="module-card-mini">
                            <div class="module-icon-mini"><i class="bi bi-geo-alt"></i></div>
                            <span class="module-count">{{ $totalCity }}</span>
                            <span class="module-name">Cities</span>
                        </a>
                        <a href="{{ route('admin.product-brand.index') }}" class="module-card-mini">
                            <div class="module-icon-mini"><i class="bi bi-tag-fill"></i></div>
                            <span class="module-count">{{ $totalProductBrand }}</span>
                            <span class="module-name">Brands</span>
                        </a>
                    </div>
                </div>

                <!-- Engagement Group -->
                <div class="dashboard-section">
                    <div class="section-title">
                        <i class="bi bi-chat-heart-fill"></i>
                        <h4>Community & Growth</h4>
                    </div>
                    <div class="module-grid">
                        <a href="{{ route('admin.blogs.index') }}" class="module-card-mini">
                            <div class="module-icon-mini"><i class="bi bi-journal-text"></i></div>
                            <span class="module-count">{{ $totalBlogs }}</span>
                            <span class="module-name">Articles</span>
                        </a>
                        <a href="{{ route('admin.reviews.index') }}" class="module-card-mini">
                            <div class="module-icon-mini"><i class="bi bi-star-fill"></i></div>
                            <span class="module-count">{{ $totalCustomerReviews }}</span>
                            <span class="module-name">Member Reviews</span>
                        </a>
                        <a href="{{ route('admin.contact-submissions.index') }}" class="module-card-mini">
                            <div class="module-icon-mini"><i class="bi bi-envelope-heart"></i></div>
                            <span class="module-count">{{ $totalContacts }}</span>
                            <span class="module-name">Inquiries</span>
                        </a>
                        <a href="{{ route('admin.hirings.index') }}" class="module-card-mini">
                            <div class="module-icon-mini"><i class="bi bi-person-plus-fill"></i></div>
                            <span class="module-count">{{ $totalHirings }}</span>
                            <span class="module-name">Hiring Apps</span>
                        </a>
                    </div>
                </div>

                <!-- Secondary Appointment Stats -->
                <div class="dashboard-section">
                    <div class="section-title">
                        <i class="bi bi-calendar-check-fill"></i>
                        <h4>Appointment Summary</h4>
                    </div>
                    <div class="module-grid">
                        <a href="{{ route('admin.appointments.index') }}" class="module-card-mini" style="border-left: 4px solid var(--mst-indigo);">
                            <span class="module-count">{{ $totalAppointments }}</span>
                            <span class="module-name">All Time</span>
                        </a>
                        <a href="{{ route('admin.appointments.index', ['status' => 3]) }}" class="module-card-mini" style="border-left: 4px solid var(--mst-success);">
                            <span class="module-count">{{ $totalAppointmentsCompleted }}</span>
                            <span class="module-name">Completed</span>
                        </a>
                        <a href="{{ route('admin.appointments.index', ['status' => 2]) }}" class="module-card-mini" style="border-left: 4px solid var(--mst-info);">
                            <span class="module-count">{{ $totalAppointmentsAssigned }}</span>
                            <span class="module-name">In Progress</span>
                        </a>
                        <a href="{{ route('admin.appointments.index', ['status' => 4]) }}" class="module-card-mini" style="border-left: 4px solid var(--mst-danger);">
                            <span class="module-count">{{ $totalAppointmentsRejected }}</span>
                            <span class="module-name">Rejected</span>
                        </a>
                    </div>
                </div>

            </section>
        </div>
    </div>
</div>
@endsection
