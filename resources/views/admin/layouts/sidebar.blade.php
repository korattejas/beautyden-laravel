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
                    <span class="menu-title text-truncate">Dashboard</span>
                </a>
            </li>

            <li class=" navigation-header">
                <span>Pages</span>
                <i data-feather="more-horizontal"></i>
            </li>

            <li class="nav-item {{ Request::routeIs('admin.appointments.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.appointments.index') }}">
                    <i data-feather="calendar"></i>
                    <span class="menu-title text-truncate">Appointments</span>
                </a>
            </li>

            <li class="nav-item {{ Request::routeIs('admin.contact-submissions.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.contact-submissions.index') }}">
                    <i data-feather="mail"></i>
                    <span class="menu-title text-truncate">Customer Contact</span>
                </a>
            </li>

            <li class="nav-item {{ Request::routeIs('admin.service-category.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.service-category.index') }}">
                    <i data-feather="layers"></i>
                    <span class="menu-title text-truncate">Service Category</span>
                </a>
            </li>

            <li class="nav-item {{ Request::routeIs('admin.service-subcategory.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.service-subcategory.index') }}">
                    <i data-feather="layers"></i>
                    <span class="menu-title text-truncate">Service Sub Category</span>
                </a>
            </li>

            <li class="nav-item {{ Request::routeIs('admin.service.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.service.index') }}">
                    <i data-feather="briefcase"></i>
                    <span class="menu-title text-truncate">Service</span>
                </a>
            </li>

            <li class="nav-item {{ Request::routeIs('admin.service-city-price.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.service-city-price.index') }}">
                    <i data-feather="map-pin"></i>
                    <span class="menu-title text-truncate">Service City Price</span>
                </a>
            </li>

             <li class="nav-item {{ Request::routeIs('admin.city.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.city.index') }}">
                    <i data-feather="map-pin"></i>
                    <span class="menu-title text-truncate">City</span>
                </a>
            </li>

            <li class="nav-item {{ Request::routeIs('admin.team.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.team.index') }}">
                    <i data-feather="users"></i>
                    <span class="menu-title text-truncate">Team Member</span>
                </a>
            </li>

            <li class="nav-item {{ Request::routeIs('admin.reviews.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.reviews.index') }}">
                    <i data-feather="star"></i>
                    <span class="menu-title text-truncate">Customer Review</span>
                </a>
            </li>

            <li class="nav-item {{ Request::routeIs('admin.blog-category.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.blog-category.index') }}">
                    <i data-feather="tag"></i>
                    <span class="menu-title text-truncate">Blog Category</span>
                </a>
            </li>

            <li class="nav-item {{ Request::routeIs('admin.blogs.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.blogs.index') }}">
                    <i data-feather="edit-3"></i>
                    <span class="menu-title text-truncate">Blogs</span>
                </a>
            </li>

            <li class="nav-item {{ Request::routeIs('admin.hirings.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.hirings.index') }}">
                    <i data-feather="briefcase"></i>
                    <span class="menu-title text-truncate">Hiring</span>
                </a>
            </li>

            <li class="nav-item {{ Request::routeIs('admin.home-counters.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.home-counters.index') }}">
                    <i data-feather="bar-chart-2"></i>
                    <span class="menu-title text-truncate">Home Counter</span>
                </a>
            </li>

            <li class="nav-item {{ Request::routeIs('admin.product-brand.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.product-brand.index') }}">
                    <i data-feather="tag"></i>
                    <span class="menu-title text-truncate">Product Brand</span>
                </a>
            </li>

            <li class="nav-item {{ Request::routeIs('admin.faqs.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.faqs.index') }}">
                    <i data-feather="help-circle"></i>
                    <span class="menu-title text-truncate">Faqs</span>
                </a>
            </li>

            <li class="nav-item {{ Request::routeIs('admin.setting.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.setting.index') }}">
                    <i data-feather="settings"></i>
                    <span class="menu-title text-truncate">Setting</span>
                </a>
            </li>

            <li class="nav-item {{ Request::routeIs('admin.policies.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.policies.index') }}">
                    <i data-feather="file-text"></i>
                    <span class="menu-title text-truncate">Policies</span>
                </a>
            </li>
        </ul>
    </div>
</div>
