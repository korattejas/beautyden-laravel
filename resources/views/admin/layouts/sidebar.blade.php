<style>
    /* Premium Sidebar Redesign - Deep Indigo Edition */
    .main-menu {
        background: linear-gradient(180deg, #1e1b4b 0%, #312e81 100%) !important;
        border-right: none !important;
        box-shadow: 15px 0 35px rgba(0, 0, 0, 0.1) !important;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }

    .main-menu .navbar-header {
        height: 110px !important;
        padding: 2rem 1.5rem !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        background: rgba(255, 255, 255, 0.03) !important;
        backdrop-filter: blur(10px);
        margin-bottom: 1rem;
    }

    .sidebar-main-logo {
        height: 65px !important;
        width: auto !important;
        filter: drop-shadow(0 4px 6px rgba(0,0,0,0.2));
        transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .navbar-brand:hover .sidebar-main-logo {
        transform: scale(1.1) rotate(-2deg);
    }

    .main-menu-content {
        padding: 0.5rem 1rem !important;
    }

    .navigation-main {
        background: transparent !important;
    }

    .navigation-main .nav-item {
        margin: 8px 0 !important;
        border-radius: 14px !important;
        position: relative;
    }

    .navigation-main .nav-item a {
        padding: 14px 20px !important;
        border-radius: 14px !important;
        color: #cbd5e1 !important;
        font-weight: 500 !important;
        font-size: 0.9rem !important;
        transition: all 0.3s ease !important;
        background: transparent !important;
        display: flex !important;
        align-items: center !important;
        gap: 14px !important;
        letter-spacing: 0.3px;
    }

    /* Icon Styling */
    .navigation-main .nav-item a i, 
    .navigation-main .nav-item a svg {
        width: 18px !important;
        height: 18px !important;
        transition: all 0.3s ease !important;
        color: #94a3b8 !important;
        opacity: 0.8;
    }

    /* Hover State */
    .navigation-main .nav-item:not(.active) a:hover {
        background: rgba(255, 255, 255, 0.08) !important;
        color: #ffffff !important;
        transform: translateX(8px);
    }

    .navigation-main .nav-item:not(.active) a:hover i,
    .navigation-main .nav-item:not(.active) a:hover svg {
        color: #ffffff !important;
        opacity: 1;
        transform: scale(1.1);
    }

    /* Active State - Premium Gradient Look */
    .navigation-main .nav-item.active {
        background: rgba(255, 255, 255, 0.1) !important;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2) !important;
    }

    .navigation-main .nav-item.active a {
        background: linear-gradient(90deg, #6366f1 0%, #818cf8 100%) !important;
        color: #ffffff !important;
        font-weight: 600 !important;
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
    }

    .navigation-main .nav-item.active a i,
    .navigation-main .nav-item.active a svg {
        color: #ffffff !important;
        opacity: 1;
        transform: scale(1.1);
    }

    /* Navigation Header */
    .navigation-header {
        margin: 2rem 0 1rem 1.5rem !important;
        padding: 0 !important;
        text-transform: uppercase !important;
        letter-spacing: 2px !important;
        font-size: 0.7rem !important;
        font-weight: 800 !important;
        color: #6366f1 !important;
        position: relative;
    }

    .navigation-header::after {
        content: '';
        display: inline-block;
        width: 20px;
        height: 2px;
        background: #6366f1;
        margin-left: 10px;
        vertical-align: middle;
        border-radius: 10px;
    }

    /* Scrollbar Style */
    .main-menu-content::-webkit-scrollbar {
        width: 5px;
    }

    .main-menu-content::-webkit-scrollbar-track {
        background: transparent;
    }

    .main-menu-content::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
    }

    .main-menu-content:hover::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.2);
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
                    <span class="menu-title text-truncate">Service Pricing</span>
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

            <li class=" navigation-header">
                <span>Communication</span>
            </li>

            <li class="nav-item {{ Request::routeIs('admin.contact-submissions.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.contact-submissions.index') }}">
                    <i data-feather="mail"></i>
                    <span class="menu-title text-truncate">Inquiries</span>
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

