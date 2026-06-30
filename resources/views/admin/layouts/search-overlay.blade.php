<div class="pa-search-overlay" id="paSearchOverlay">
    <div class="pa-search-modal">
        <input type="text" class="pa-search-modal-input" id="paSearchModalInput" placeholder="Search pages, modules, actions..." autocomplete="off">
        <div class="pa-search-results">
            <a href="{{ route('admin.dashboard') }}" class="pa-search-result">
                <div class="pa-stat-icon primary"><i class="bi bi-grid"></i></div>
                <div><strong>Dashboard</strong><br><small class="text-muted">Analytics overview</small></div>
            </a>
            <a href="{{ route('admin.user.index') }}" class="pa-search-result">
                <div class="pa-stat-icon info"><i class="bi bi-people"></i></div>
                <div><strong>Users</strong><br><small class="text-muted">Registered customers</small></div>
            </a>
            <a href="{{ route('admin.appointments.index') }}" class="pa-search-result">
                <div class="pa-stat-icon success"><i class="bi bi-calendar"></i></div>
                <div><strong>Appointments</strong><br><small class="text-muted">Booking management</small></div>
            </a>
            <a href="{{ route('admin.product-item.index') }}" class="pa-search-result">
                <div class="pa-stat-icon warning"><i class="bi bi-bag"></i></div>
                <div><strong>Products</strong><br><small class="text-muted">Product catalog</small></div>
            </a>
            <a href="{{ route('admin.product-order.index') }}" class="pa-search-result">
                <div class="pa-stat-icon primary"><i class="bi bi-receipt"></i></div>
                <div><strong>Orders</strong><br><small class="text-muted">Product orders</small></div>
            </a>
            <a href="{{ route('admin.service.index') }}" class="pa-search-result">
                <div class="pa-stat-icon info"><i class="bi bi-scissors"></i></div>
                <div><strong>Services</strong><br><small class="text-muted">Service catalog</small></div>
            </a>
            <a href="{{ route('admin.reports.index') }}" class="pa-search-result">
                <div class="pa-stat-icon success"><i class="bi bi-bar-chart-line"></i></div>
                <div><strong>Reports</strong><br><small class="text-muted">Analytics & exports</small></div>
            </a>
            <a href="{{ route('admin.setting.index') }}" class="pa-search-result">
                <div class="pa-stat-icon neutral" style="background:var(--pa-surface-2);color:var(--pa-text-muted)"><i class="bi bi-gear"></i></div>
                <div><strong>Settings</strong><br><small class="text-muted">System configuration</small></div>
            </a>
            <a href="{{ route('admin.profile.index') }}" class="pa-search-result">
                <div class="pa-stat-icon primary"><i class="bi bi-person"></i></div>
                <div><strong>Profile</strong><br><small class="text-muted">Your account</small></div>
            </a>
        </div>
    </div>
</div>
