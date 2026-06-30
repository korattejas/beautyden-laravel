<header class="pa-topbar">
    <div class="pa-topbar-left">
        <button class="pa-mobile-toggle" id="paMobileToggle" type="button" aria-label="Toggle menu">
            <i data-feather="menu"></i>
        </button>
        <h2 class="pa-page-title">@yield('page_heading', 'Dashboard')</h2>
    </div>

    <div class="pa-search-wrap d-none d-xl-block">
        <i class="bi bi-search pa-search-icon"></i>
        <input type="text" class="pa-search-input" id="paSearchTrigger" placeholder="Search pages, users, orders..." readonly>
        <span class="pa-search-kbd">⌘K</span>
    </div>

    <div class="pa-topbar-right">
        <button class="pa-topbar-btn d-xl-none" id="paSearchTriggerMobile" type="button" aria-label="Search" onclick="PremiumAdmin && PremiumAdmin.openSearch()">
            <i data-feather="search"></i>
        </button>

        <div class="dropdown">
            <button class="pa-topbar-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notifications">
                <i data-feather="bell"></i>
                <span class="pa-badge-dot"></span>
            </button>
            <div class="dropdown-menu dropdown-menu-end pa-notif-dropdown">
                <div class="pa-notif-header">
                    <h6>Notifications</h6>
                    <a href="{{ route('admin.notifications.index') }}" class="pa-btn pa-btn-sm pa-btn-outline">View all</a>
                </div>
                <div class="pa-notif-list">
                    <a href="{{ route('admin.appointments.index') }}" class="pa-notif-item">
                        <div class="pa-notif-icon success"><i class="bi bi-calendar-check"></i></div>
                        <div class="pa-notif-body">
                            <p>New appointment booked today</p>
                            <small>Just now</small>
                        </div>
                    </a>
                    <a href="{{ route('admin.user.index') }}" class="pa-notif-item">
                        <div class="pa-notif-icon info"><i class="bi bi-person-plus"></i></div>
                        <div class="pa-notif-body">
                            <p>New user registration</p>
                            <small>15 minutes ago</small>
                        </div>
                    </a>
                    <a href="{{ route('admin.reviews.index') }}" class="pa-notif-item">
                        <div class="pa-notif-icon warning"><i class="bi bi-star"></i></div>
                        <div class="pa-notif-body">
                            <p>Pending review awaiting approval</p>
                            <small>1 hour ago</small>
                        </div>
                    </a>
                    <a href="{{ route('admin.product-order.index') }}" class="pa-notif-item">
                        <div class="pa-notif-icon success"><i class="bi bi-bag-check"></i></div>
                        <div class="pa-notif-body">
                            <p>New product order received</p>
                            <small>2 hours ago</small>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <div class="dropdown">
            <a class="pa-profile dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <img class="pa-profile-avatar" src="{{ asset('panel-assets/images/portrait/small/avatar-s-11.jpg') }}" alt="Avatar">
                <div class="pa-profile-info">
                    <span class="pa-profile-name">Hi, {{ Auth::guard('admin')->user()->name }}</span>
                    <span class="pa-profile-role">{{ Auth::guard('admin')->user()->role ? Auth::guard('admin')->user()->role->name : 'Super Admin' }}</span>
                </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end pa-dropdown-menu">
                <li>
                    <a class="dropdown-item" href="{{ route('admin.profile.index') }}">
                        <i class="bi bi-person me-2"></i> My Profile
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('admin.setting.index') }}">
                        <i class="bi bi-gear me-2"></i> Settings
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('admin.reports.index') }}">
                        <i class="bi bi-bar-chart me-2"></i> Reports
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item text-danger" href="{{ route('admin.logout') }}">
                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</header>

<script>
    document.getElementById('paSearchTrigger')?.addEventListener('click', function() {
        if (window.PremiumAdmin) PremiumAdmin.openSearch();
    });
</script>
