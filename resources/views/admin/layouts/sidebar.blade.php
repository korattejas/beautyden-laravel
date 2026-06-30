@php
    $admin = Auth::guard('admin')->user();
    $isSuperAdmin = empty($admin->role_id);

    $userPermissions = [];
    if (!$isSuperAdmin && $admin->role && $admin->role->permissions) {
        $perms = $admin->role->permissions;
        while(is_string($perms)) {
            $decoded = json_decode($perms, true);
            if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
                break;
            }
            $perms = $decoded;
        }
        $userPermissions = is_array($perms) ? $perms : [];
    }

    if (!function_exists('hasMenuAccess')) {
        function hasMenuAccess($module, $isSuperAdmin, $userPermissions) {
            if ($isSuperAdmin) return true;
            return in_array($module, $userPermissions);
        }
    }
@endphp

<aside class="pa-sidebar" id="paSidebar">
    <div class="pa-sidebar-header">
        <a class="pa-brand" href="{{ route('admin.dashboard') }}">
            {{-- <img src="{{ URL::asset('panel-assets/admin-logo/sidebar-Logo.png') }}" class="sidebar-main-logo pa-sidebar-logo" alt="BeautyDen"> --}}
        </a>
        <button class="pa-sidebar-toggle" id="paSidebarToggle" type="button" aria-label="Toggle sidebar">
            <i data-feather="chevrons-left"></i>
        </button>
    </div>

    <div class="pa-sidebar-tabs">
        <button class="pa-sidebar-tab active" id="show-services" type="button">Services</button>
        <button class="pa-sidebar-tab" id="show-products" type="button">Products</button>
    </div>

    <nav class="pa-sidebar-nav">
        <ul class="pa-nav-section list-unstyled mb-0">

            @if(hasMenuAccess('dashboard', $isSuperAdmin, $userPermissions))
            <li class="pa-nav-item {{ Request::routeIs('admin.dashboard') ? 'active' : '' }}">
                <a class="pa-nav-link" href="{{ route('admin.dashboard') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Dashboard">
                    <i data-feather="grid"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            @endif

            @if(hasMenuAccess('reports', $isSuperAdmin, $userPermissions) || $isSuperAdmin)
            <li class="pa-nav-item {{ Request::routeIs('admin.reports.*') ? 'active' : '' }}">
                <a class="pa-nav-link" href="{{ route('admin.reports.index') }}" data-bs-toggle="tooltip" data-bs-placement="right" title="Reports">
                    <i data-feather="bar-chart-2"></i>
                    <span>Reports</span>
                </a>
            </li>
            @endif

            <div class="services-menu-section">
                @if(hasMenuAccess('appointments', $isSuperAdmin, $userPermissions) || hasMenuAccess('team_members', $isSuperAdmin, $userPermissions) || hasMenuAccess('service_catalog', $isSuperAdmin, $userPermissions))
                <li class="pa-nav-label">Service Management</li>
                @endif

                @if(hasMenuAccess('appointments', $isSuperAdmin, $userPermissions))
                <li class="pa-nav-item {{ Request::routeIs('admin.appointments.index') ? 'active' : '' }}">
                    <a class="pa-nav-link" href="{{ route('admin.appointments.index') }}">
                        <i data-feather="calendar"></i><span>Appointments</span>
                    </a>
                </li>
                @endif

                @if(hasMenuAccess('team_members', $isSuperAdmin, $userPermissions))
                <li class="pa-nav-item {{ Request::routeIs('admin.team.index') ? 'active' : '' }}">
                    <a class="pa-nav-link" href="{{ route('admin.team.index') }}">
                        <i data-feather="users"></i><span>Team Members</span>
                    </a>
                </li>
                <li class="pa-nav-item {{ Request::routeIs('admin.attendance.index') ? 'active' : '' }}">
                    <a class="pa-nav-link" href="{{ route('admin.attendance.index') }}">
                        <i data-feather="clock"></i><span>Attendance</span>
                    </a>
                </li>
                <li class="pa-nav-item {{ Request::routeIs('admin.user.index') ? 'active' : '' }}">
                    <a class="pa-nav-link" href="{{ route('admin.user.index') }}">
                        <i data-feather="user-check"></i><span>Customers</span>
                    </a>
                </li>
                @endif

                @if(hasMenuAccess('service_catalog', $isSuperAdmin, $userPermissions))
                <li class="pa-nav-item {{ Request::routeIs('admin.service.index') ? 'active' : '' }}">
                    <a class="pa-nav-link" href="{{ route('admin.service.index') }}">
                        <i data-feather="heart"></i><span>Services</span>
                    </a>
                </li>
                <li class="pa-nav-item {{ Request::routeIs('admin.service-master.index') ? 'active' : '' }}">
                    <a class="pa-nav-link" href="{{ route('admin.service-master.index') }}">
                        <i data-feather="zap"></i><span>Advanced Catalog</span>
                    </a>
                </li>
                <li class="pa-nav-item {{ Request::routeIs('admin.service-essential.index') ? 'active' : '' }}">
                    <a class="pa-nav-link" href="{{ route('admin.service-essential.index') }}">
                        <i data-feather="layers"></i><span>Master Essentials</span>
                    </a>
                </li>

                <li class="pa-nav-label">Service Config</li>
                <li class="pa-nav-item {{ Request::routeIs('admin.service-type.index') ? 'active' : '' }}">
                    <a class="pa-nav-link" href="{{ route('admin.service-type.index') }}"><i data-feather="list"></i><span>Service Types</span></a>
                </li>
                <li class="pa-nav-item {{ Request::routeIs('admin.service-category.index') ? 'active' : '' }}">
                    <a class="pa-nav-link" href="{{ route('admin.service-category.index') }}"><i data-feather="box"></i><span>Categories</span></a>
                </li>
                <li class="pa-nav-item {{ Request::routeIs('admin.service-subcategory.index') ? 'active' : '' }}">
                    <a class="pa-nav-link" href="{{ route('admin.service-subcategory.index') }}"><i data-feather="folder"></i><span>Sub Categories</span></a>
                </li>
                <li class="pa-nav-item {{ Request::routeIs('admin.city.index') ? 'active' : '' }}">
                    <a class="pa-nav-link" href="{{ route('admin.city.index') }}"><i data-feather="map-pin"></i><span>City List</span></a>
                </li>
                <li class="pa-nav-item {{ Request::routeIs('admin.service-city-price.index') ? 'active' : '' }}">
                    <a class="pa-nav-link" href="{{ route('admin.service-city-price.index') }}"><i data-feather="dollar-sign"></i><span>Pricing (Web)</span></a>
                </li>
                <li class="pa-nav-item {{ Request::routeIs('admin.service-city-master.index') ? 'active' : '' }}">
                    <a class="pa-nav-link" href="{{ route('admin.service-city-master.index') }}"><i data-feather="smartphone"></i><span>Pricing (App)</span></a>
                </li>
                @endif
            </div>

            @if(hasMenuAccess('products', $isSuperAdmin, $userPermissions))
            <div class="products-menu-section" style="display: none;">
                <li class="pa-nav-label">Product Management</li>
                <li class="pa-nav-item {{ Request::routeIs('admin.product-item.index') ? 'active' : '' }}">
                    <a class="pa-nav-link" href="{{ route('admin.product-item.index') }}"><i data-feather="shopping-bag"></i><span>Products</span></a>
                </li>
                <li class="pa-nav-item {{ Request::routeIs('admin.product-brand.index') ? 'active' : '' }}">
                    <a class="pa-nav-link" href="{{ route('admin.product-brand.index') }}"><i data-feather="tag"></i><span>Brands</span></a>
                </li>
                <li class="pa-nav-label">Product Config</li>
                <li class="pa-nav-item {{ Request::routeIs('admin.product-category.index') ? 'active' : '' }}">
                    <a class="pa-nav-link" href="{{ route('admin.product-category.index') }}"><i data-feather="box"></i><span>Categories</span></a>
                </li>
                <li class="pa-nav-item {{ Request::routeIs('admin.product-subcategory.index') ? 'active' : '' }}">
                    <a class="pa-nav-link" href="{{ route('admin.product-subcategory.index') }}"><i data-feather="folder"></i><span>Sub Categories</span></a>
                </li>
                <li class="pa-nav-label">Sales</li>
                <li class="pa-nav-item {{ Request::routeIs('admin.product-order.index') ? 'active' : '' }}">
                    <a class="pa-nav-link" href="{{ route('admin.product-order.index') }}"><i data-feather="file-text"></i><span>Orders</span></a>
                </li>
            </div>
            @endif

            @if(hasMenuAccess('offers', $isSuperAdmin, $userPermissions))
            <li class="pa-nav-label">Promotions</li>
            <li class="pa-nav-item {{ Request::routeIs('admin.offers.index') ? 'active' : '' }}">
                <a class="pa-nav-link" href="{{ route('admin.offers.index') }}"><i data-feather="gift"></i><span>Offers</span></a>
            </li>
            <li class="pa-nav-item {{ Request::routeIs('admin.coupon-codes.index') ? 'active' : '' }}">
                <a class="pa-nav-link" href="{{ route('admin.coupon-codes.index') }}"><i data-feather="percent"></i><span>Coupons</span></a>
            </li>
            <li class="pa-nav-item {{ Request::routeIs('admin.coupon-usage.index') ? 'active' : '' }}">
                <a class="pa-nav-link" href="{{ route('admin.coupon-usage.index') }}"><i data-feather="clipboard"></i><span>Coupon Logs</span></a>
            </li>
            {{-- Membership Plans — currently not in use
            <li class="pa-nav-item {{ Request::routeIs('admin.membership.index') ? 'active' : '' }}">
                <a class="pa-nav-link" href="{{ route('admin.membership.index') }}"><i data-feather="award"></i><span>Memberships</span></a>
            </li>
            --}}
            <li class="pa-nav-item {{ Request::routeIs('admin.combo.index') ? 'active' : '' }}">
                <a class="pa-nav-link" href="{{ route('admin.combo.index') }}"><i data-feather="package"></i><span>Combos</span></a>
            </li>
            @endif

            @if(hasMenuAccess('settings', $isSuperAdmin, $userPermissions))
            <li class="pa-nav-label">Finance</li>
            <li class="pa-nav-item {{ Request::routeIs('admin.razorpay.index') ? 'active' : '' }}">
                <a class="pa-nav-link" href="{{ route('admin.razorpay.index') }}"><i data-feather="credit-card"></i><span>Razorpay</span></a>
            </li>
            <li class="pa-nav-item {{ Request::routeIs('admin.settlement.index') ? 'active' : '' }}">
                <a class="pa-nav-link" href="{{ route('admin.settlement.index') }}"><i data-feather="dollar-sign"></i><span>Settlements</span></a>
            </li>
            @endif

            @if(hasMenuAccess('contact_submissions', $isSuperAdmin, $userPermissions) || hasMenuAccess('reviews', $isSuperAdmin, $userPermissions))
            <li class="pa-nav-label">Communication</li>
            @endif

            @if(hasMenuAccess('contact_submissions', $isSuperAdmin, $userPermissions))
            <li class="pa-nav-item {{ Request::routeIs('admin.contact-submissions.index') ? 'active' : '' }}">
                <a class="pa-nav-link" href="{{ route('admin.contact-submissions.index') }}"><i data-feather="mail"></i><span>Inquiries</span></a>
            </li>
            <li class="pa-nav-item {{ Request::routeIs('admin.notifications.index') ? 'active' : '' }}">
                <a class="pa-nav-link" href="{{ route('admin.notifications.index') }}"><i data-feather="bell"></i><span>Notifications</span></a>
            </li>
            @endif

            @if(hasMenuAccess('reviews', $isSuperAdmin, $userPermissions))
            <li class="pa-nav-item {{ Request::routeIs('admin.reviews.index') ? 'active' : '' }}">
                <a class="pa-nav-link" href="{{ route('admin.reviews.index') }}"><i data-feather="star"></i><span>Reviews</span></a>
            </li>
            @endif

            @if(hasMenuAccess('blogs', $isSuperAdmin, $userPermissions))
            <li class="pa-nav-label">Content</li>
            <li class="pa-nav-item {{ Request::routeIs('admin.portfolio.index') ? 'active' : '' }}">
                <a class="pa-nav-link" href="{{ route('admin.portfolio.index') }}"><i data-feather="image"></i><span>Portfolio</span></a>
            </li>
            <li class="pa-nav-item {{ Request::routeIs('admin.blogs.index') ? 'active' : '' }}">
                <a class="pa-nav-link" href="{{ route('admin.blogs.index') }}"><i data-feather="edit-3"></i><span>Blog Posts</span></a>
            </li>
            @endif

            <li class="pa-nav-label">System</li>

            @if(hasMenuAccess('settings', $isSuperAdmin, $userPermissions))
            <li class="pa-nav-item {{ Request::routeIs('admin.app-setting.index') ? 'active' : '' }}">
                <a class="pa-nav-link" href="{{ route('admin.app-setting.index') }}"><i data-feather="smartphone"></i><span>App Settings</span></a>
            </li>
            <li class="pa-nav-item {{ Request::routeIs('admin.policies.index') ? 'active' : '' }}">
                <a class="pa-nav-link" href="{{ route('admin.policies.index') }}"><i data-feather="file-text"></i><span>Policies</span></a>
            </li>
            @endif

            @if($isSuperAdmin)
            <li class="pa-nav-item {{ Request::routeIs('admin.roles.index') ? 'active' : '' }}">
                <a class="pa-nav-link" href="{{ route('admin.roles.index') }}"><i data-feather="shield"></i><span>Roles & Access</span></a>
            </li>
            <li class="pa-nav-item {{ Request::routeIs('admin.admin-staff.index') ? 'active' : '' }}">
                <a class="pa-nav-link" href="{{ route('admin.admin-staff.index') }}"><i data-feather="user-plus"></i><span>Admin Staff</span></a>
            </li>
            @endif

            @if(hasMenuAccess('settings', $isSuperAdmin, $userPermissions))
            <li class="pa-nav-item {{ Request::routeIs('admin.setting.index') ? 'active' : '' }}">
                <a class="pa-nav-link" href="{{ route('admin.setting.index') }}"><i data-feather="settings"></i><span>Settings</span></a>
            </li>
            @endif

            <li class="pa-nav-item {{ Request::routeIs('admin.profile.*') ? 'active' : '' }}">
                <a class="pa-nav-link" href="{{ route('admin.profile.index') }}"><i data-feather="user"></i><span>Profile</span></a>
            </li>
        </ul>
    </nav>

    <div class="pa-sidebar-footer">
        <a class="pa-nav-link" href="{{ route('admin.logout') }}">
            <i data-feather="log-out"></i><span>Logout</span>
        </a>
    </div>
</aside>
