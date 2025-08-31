<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item me-auto">
                <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
                    <span class="brand-logo">
                        {{-- <img src="{{ URL::asset('panel-assets/admin-logo/logo.png') }}" /> --}}
                    </span>
                    <h2 class="brand-text">Test</h2>
                </a>
            </li>
        </ul>
    </div>

    <div class="shadow-bottom"></div>
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            <li class=" nav-item {{ Request::routeIs('admin.dashboard') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.dashboard') }}">
                    <i data-feather="home"></i>
                    <span class="menu-title text-truncate" data-i18n="Email">Dashboard</span>
                </a>
            </li>

            <li class=" navigation-header">
                <span data-i18n="Apps &amp; Pages"> Pages </span>
                <i data-feather="more-horizontal"></i>
            </li>

            <li class="nav-item {{ Request::routeIs('admin.appointments.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.appointments.index') }}">
                    <i data-feather="users"></i>
                    <span class="menu-title text-truncate" data-i18n="Email">Appointments</span>
                </a>
            </li>

            <li class="nav-item {{ Request::routeIs('admin.contact-submissions.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.contact-submissions.index') }}">
                    <i data-feather="users"></i>
                    <span class="menu-title text-truncate" data-i18n="Email">Customer Contact</span>
                </a>
            </li>
            
            <li class="nav-item {{ Request::routeIs('admin.service-category.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.service-category.index') }}">
                    <i data-feather="users"></i>
                    <span class="menu-title text-truncate" data-i18n="Email">Service Category</span>
                </a>
            </li>

            <li class="nav-item {{ Request::routeIs('admin.service.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.service.index') }}">
                    <i data-feather="users"></i>
                    <span class="menu-title text-truncate" data-i18n="Email">Service</span>
                </a>
            </li>

            <li class="nav-item {{ Request::routeIs('admin.team.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.team.index') }}">
                    <i data-feather="users"></i>
                    <span class="menu-title text-truncate" data-i18n="Email">Team Member</span>
                </a>
            </li>

            <li class="nav-item {{ Request::routeIs('admin.reviews.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.reviews.index') }}">
                    <i data-feather="users"></i>
                    <span class="menu-title text-truncate" data-i18n="Email">Customer Review</span>
                </a>
            </li>

            <li class="nav-item {{ Request::routeIs('admin.blog-category.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.blog-category.index') }}">
                    <i data-feather="users"></i>
                    <span class="menu-title text-truncate" data-i18n="Email">Blog Category</span>
                </a>
            </li>

            <li class="nav-item {{ Request::routeIs('admin.blogs.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.blogs.index') }}">
                    <i data-feather="users"></i>
                    <span class="menu-title text-truncate" data-i18n="Email">Blogs</span>
                </a>
            </li>

            <li class="nav-item {{ Request::routeIs('admin.hirings.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.hirings.index') }}">
                    <i data-feather="users"></i>
                    <span class="menu-title text-truncate" data-i18n="Email">Hiring</span>
                </a>
            </li>

             <li class="nav-item {{ Request::routeIs('admin.home-counters.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.home-counters.index') }}">
                    <i data-feather="users"></i>
                    <span class="menu-title text-truncate" data-i18n="Email">Home Counter</span>
                </a>
            </li>

             <li class="nav-item {{ Request::routeIs('admin.city.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.city.index') }}">
                    <i data-feather="users"></i>
                    <span class="menu-title text-truncate" data-i18n="Email">City</span>
                </a>
            </li>

             <li class="nav-item {{ Request::routeIs('admin.faqs.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.faqs.index') }}">
                    <i data-feather="users"></i>
                    <span class="menu-title text-truncate" data-i18n="Email">Faqs</span>
                </a>
            </li>

            <li class="nav-item {{ Request::routeIs('admin.setting.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.setting.index') }}">
                    <i data-feather="users"></i>
                    <span class="menu-title text-truncate" data-i18n="Email">Setting</span>
                </a>
            </li>

              <li class="nav-item {{ Request::routeIs('admin.policies.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.policies.index') }}">
                    <i data-feather="users"></i>
                    <span class="menu-title text-truncate" data-i18n="Email">Policies</span>
                </a>
            </li>
        </ul>
    </div>
</div>
