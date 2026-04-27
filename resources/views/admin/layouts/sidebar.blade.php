<style>
    /* Premium Sidebar Redesign - Sleek Light Edition */
    .main-menu {
        background: #ffffff !important;
        border-right: 1px solid rgba(0, 0, 0, 0.06) !important;
        box-shadow: 10px 0 30px rgba(0, 0, 0, 0.03) !important;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }

    .main-menu .navbar-header {
        height: 110px !important;
        padding: 1.5rem !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        background: #ffffff !important;
        border-bottom: 1px solid rgba(0, 0, 0, 0.04) !important;
        margin-bottom: 0.5rem;
    }

    .sidebar-main-logo {
        height: 80px !important;
        width: auto !important;
        transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .navbar-brand:hover .sidebar-main-logo {
        transform: scale(1.08);
    }

    .main-menu-content {
        padding: 0.8rem 1rem !important;
    }

    .navigation-main {
        background: transparent !important;
    }

    .navigation-main .nav-item {
        margin: 4px 0 !important;
        border-radius: 12px !important;
        transition: all 0.3s ease !important;
    }

    .navigation-main .nav-item a {
        padding: 12px 18px !important;
        border-radius: 12px !important;
        color: #4b5563 !important;
        font-weight: 500 !important;
        font-size: 0.92rem !important;
        transition: all 0.3s ease !important;
        background: transparent !important;
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
    }

    /* Icon Styling */
    .navigation-main .nav-item a i, 
    .navigation-main .nav-item a svg {
        width: 19px !important;
        height: 19px !important;
        transition: all 0.3s ease !important;
        color: #94a3b8 !important;
    }

    /* Hover State */
    .navigation-main .nav-item:not(.active) a:hover {
        background: #f8fafc !important;
        color: #6366f1 !important;
        transform: translateX(5px);
    }

    .navigation-main .nav-item:not(.active) a:hover i,
    .navigation-main .nav-item:not(.active) a:hover svg {
        color: #6366f1 !important;
    }

    /* Active State - Premium Soft Look */
    .navigation-main .nav-item.active {
        background: #f5f3ff !important;
    }

    .navigation-main .nav-item.active a {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%) !important;
        color: #ffffff !important;
        font-weight: 600 !important;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);
    }

    .navigation-main .nav-item.active a i,
    .navigation-main .nav-item.active a svg {
        color: #ffffff !important;
        transform: scale(1.1);
    }

    /* Navigation Header */
    .navigation-header {
        margin: 1.8rem 0 0.8rem 1.2rem !important;
        padding: 0 !important;
        text-transform: uppercase !important;
        letter-spacing: 1.2px !important;
        font-size: 0.72rem !important;
        font-weight: 700 !important;
        color: #94a3b8 !important;
    }

    .navigation-header span {
        background: #f1f5f9;
        padding: 2px 8px;
        border-radius: 4px;
    }

    /* Scrollbar Style */
    .main-menu-content::-webkit-scrollbar {
        width: 4px;
    }

    .main-menu-content::-webkit-scrollbar-track {
        background: transparent;
    }

    .main-menu-content::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 10px;
    }

    .main-menu-content:hover::-webkit-scrollbar-thumb {
        background: #cbd5e1;
    }
</style>

</style>

<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item me-auto">
                <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
                    <span class="brand-logo">
                        <img src="{{ URL::asset('panel-assets/admin-logo/sidebar-Logo.png') }}" class="sidebar-main-logo" alt="Logo" />
                    </span>
                </a>
            </li>
        </ul>
    </div>

    <div class="shadow-bottom"></div>
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">

            <li class=" nav-item {{ Request::routeIs('admin.dashboard') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.dashboard') }}">
                    <i data-feather="grid"></i>
                    <span class="menu-title text-truncate">Dashboard</span>
                </a>
            </li>

            <li class=" navigation-header">
                <span>Management</span>
            </li>

            <li class="nav-item {{ Request::routeIs('admin.appointments.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.appointments.index') }}">
                    <i data-feather="calendar"></i>
                    <span class="menu-title text-truncate">Appointments</span>
                </a>
            </li>

            <li class="nav-item {{ Request::routeIs('admin.team.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.team.index') }}">
                    <i data-feather="users"></i>
                    <span class="menu-title text-truncate">Team Members</span>
                </a>
            </li>

            <li class="nav-item {{ Request::routeIs('admin.attendance.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.attendance.index') }}">
                    <i data-feather="clock"></i>
                    <span class="menu-title text-truncate">Attendance</span>
                </a>
            </li>

            <li class="nav-item {{ Request::routeIs('admin.service.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.service.index') }}">
                    <i data-feather="shopping-bag"></i>
                    <span class="menu-title text-truncate">Service Catalog</span>
                </a>
            </li>

            <li class="nav-item {{ Request::routeIs('admin.service-master.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.service-master.index') }}">
                    <i data-feather="zap"></i>
                    <span class="menu-title text-truncate">Advanced Catalog</span>
                </a>
            </li>

            <li class="nav-item {{ Request::routeIs('admin.service-essential.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.service-essential.index') }}">
                    <i data-feather="grid"></i>
                    <span class="menu-title text-truncate">Master Essentials</span>
                </a>
            </li>

            <li class=" navigation-header">
                <span>Configuration</span>
            </li>

            <li class="nav-item {{ Request::routeIs('admin.service-category.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.service-category.index') }}">
                    <i data-feather="box"></i>
                    <span class="menu-title text-truncate">Categories</span>
                </a>
            </li>

            <li class="nav-item {{ Request::routeIs('admin.service-subcategory.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.service-subcategory.index') }}">
                    <i data-feather="layers"></i>
                    <span class="menu-title text-truncate">Sub Categories</span>
                </a>
            </li>

            <li class="nav-item {{ Request::routeIs('admin.city.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.city.index') }}">
                    <i data-feather="map-pin"></i>
                    <span class="menu-title text-truncate">City List</span>
                </a>
            </li>

            <li class="nav-item {{ Request::routeIs('admin.service-city-price.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.service-city-price.index') }}">
                    <i data-feather="dollar-sign"></i>
                    <span class="menu-title text-truncate">Service Pricing (Web)</span>
                </a>
            </li>

            <li class="nav-item {{ Request::routeIs('admin.service-city-master.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.service-city-master.index') }}">
                    <i data-feather="monitor"></i>
                    <span class="menu-title text-truncate">Service Pricing (App)</span>
                </a>
            </li>

            <li class=" navigation-header">
                <span>Promotions</span>
            </li>

            <li class="nav-item {{ Request::routeIs('admin.offers.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.offers.index') }}">
                    <i data-feather="gift"></i>
                    <span class="menu-title text-truncate">Offers (Banners)</span>
                </a>
            </li>

            <li class="nav-item {{ Request::routeIs('admin.coupon-codes.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.coupon-codes.index') }}">
                    <i data-feather="tag"></i>
                    <span class="menu-title text-truncate">Coupon Codes</span>
                </a>
            </li>

            <li class="nav-item {{ Request::routeIs('admin.coupon-usage.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.coupon-usage.index') }}">
                    <i data-feather="file-text"></i>
                    <span class="menu-title text-truncate">Coupon Usage Logs</span>
                </a>
            </li>

            <li class="nav-item {{ Request::routeIs('admin.membership.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.membership.index') }}">
                    <i data-feather="award"></i>
                    <span class="menu-title text-truncate">Memberships</span>
                </a>
            </li>

            <li class="nav-item {{ Request::routeIs('admin.combo.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.combo.index') }}">
                    <i data-feather="package"></i>
                    <span class="menu-title text-truncate">Service Combos</span>
                </a>
            </li>

            <li class=" navigation-header">
                <span>Finance</span>
            </li>

            <li class="nav-item {{ Request::routeIs('admin.razorpay.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.razorpay.index') }}">
                    <i data-feather="credit-card"></i>
                    <span class="menu-title text-truncate">Razorpay Trans.</span>
                </a>
            </li>

            <li class=" navigation-header">
                <span>Communication</span>
            </li>

            <li class="nav-item {{ Request::routeIs('admin.contact-submissions.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.contact-submissions.index') }}">
                    <i data-feather="mail"></i>
                    <span class="menu-title text-truncate">Inquiries</span>
                </a>
            </li>

            <li class="nav-item {{ Request::routeIs('admin.notifications.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.notifications.index') }}">
                    <i data-feather="bell"></i>
                    <span class="menu-title text-truncate">Push Notifications</span>
                </a>
            </li>

            <li class="nav-item {{ Request::routeIs('admin.reviews.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.reviews.index') }}">
                    <i data-feather="star"></i>
                    <span class="menu-title text-truncate">Reviews</span>
                </a>
            </li>

            <li class=" navigation-header">
                <span>Content</span>
            </li>

            <li class="nav-item {{ Request::routeIs('admin.portfolio.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.portfolio.index') }}">
                    <i data-feather="image"></i>
                    <span class="menu-title text-truncate">Portfolio</span>
                </a>
            </li>

            <li class="nav-item {{ Request::routeIs('admin.blogs.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.blogs.index') }}">
                    <i data-feather="edit"></i>
                    <span class="menu-title text-truncate">Blog Posts</span>
                </a>
            </li>

            <li class=" navigation-header">
                <span>System</span>
            </li>

            <li class="nav-item {{ Request::routeIs('admin.setting.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.setting.index') }}">
                    <i data-feather="settings"></i>
                    <span class="menu-title text-truncate">Settings</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="d-flex align-items-center" href="{{ route('admin.logout') }}">
                    <i data-feather="log-out"></i>
                    <span class="menu-title text-truncate">Logout</span>
                </a>
            </li>
        </ul>
    </div>
</div>

