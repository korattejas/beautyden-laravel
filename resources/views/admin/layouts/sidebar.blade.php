<style>
    /* Premium Sidebar Redesign */
    .main-menu {
        background: #ffffff !important;
        border-right: 1px solid rgba(0, 0, 0, 0.05) !important;
        box-shadow: 10px 0 30px rgba(0, 0, 0, 0.02) !important;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }

    .main-menu .navbar-header {
        height: 100px !important;
        padding: 1.5rem 1rem !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        background: transparent !important;
    }

    .sidebar-main-logo {
        height: 75px !important;
        width: auto !important;
        transition: transform 0.3s ease;
    }

    .navbar-brand:hover .sidebar-main-logo {
        transform: scale(1.05);
    }

    .main-menu-content {
        padding: 1rem 0.8rem !important;
    }

    .navigation-main {
        background: transparent !important;
    }

    .navigation-main .nav-item {
        margin: 6px 0 !important;
        border-radius: 12px !important;
        overflow: hidden;
        transition: all 0.3s ease !important;
    }

    .navigation-main .nav-item a {
        padding: 12px 18px !important;
        border-radius: 12px !important;
        color: #64748b !important;
        font-weight: 500 !important;
        font-size: 0.95rem !important;
        transition: all 0.3s ease !important;
        background: transparent !important;
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
    }

    /* Icon Styling */
    .navigation-main .nav-item a i, 
    .navigation-main .nav-item a svg {
        width: 20px !important;
        height: 20px !important;
        font-size: 1.25rem !important;
        transition: all 0.3s ease !important;
        color: #94a3b8 !important;
    }

    /* Hover State */
    .navigation-main .nav-item:not(.active) a:hover {
        background: #f8fafc !important;
        color: #1a237e !important;
        transform: translateX(5px);
    }

    .navigation-main .nav-item:not(.active) a:hover i,
    .navigation-main .nav-item:not(.active) a:hover svg {
        color: #1a237e !important;
    }

    /* Active State - Premium Look */
    .navigation-main .nav-item.active {
        box-shadow: 0 10px 20px rgba(26, 35, 126, 0.1) !important;
    }

    .navigation-main .nav-item.active a {
        background: linear-gradient(135deg, #1a237e 0%, #311b92 100%) !important;
        color: #ffffff !important;
        font-weight: 600 !important;
    }

    .navigation-main .nav-item.active a i,
    .navigation-main .nav-item.active a svg {
        color: #ffffff !important;
        transform: scale(1.1);
    }

    /* Navigation Header */
    .navigation-header {
        margin: 1.5rem 0 0.8rem 1.2rem !important;
        padding: 0 !important;
        text-transform: uppercase !important;
        letter-spacing: 1.5px !important;
        font-size: 0.75rem !important;
        font-weight: 700 !important;
        color: #94a3b8 !important;
        opacity: 0.8;
    }

    .navigation-header::after {
        content: '';
        display: block;
        width: 30px;
        height: 2px;
        background: #e2e8f0;
        margin-top: 5px;
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

