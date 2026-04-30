<style>
    .header-navbar {
        z-index: 999 !important;
    }

    .navbar-title {
        color: #fff;
        font-weight: 600;
        font-size: 16px;
        display: flex;
        align-items: center;
        margin: 0;
    }
</style>

<nav class="header-navbar navbar navbar-expand-lg align-items-center floating-nav navbar-light navbar-shadow bg-primary">
    <div class="navbar-container d-flex align-items-center justify-content-between w-100">

        <!-- Left Side Title -->
        <div class="navbar-title">
            Trusted Beauty Service at Home
        </div>

        <!-- Right Side User Dropdown -->
        <ul class="nav navbar-nav align-items-center ms-auto">
            <li class="nav-item dropdown dropdown-user">
                <a class="nav-link dropdown-toggle dropdown-user-link"
                   id="dropdown-user"
                   href="#"
                   data-bs-toggle="dropdown"
                   aria-haspopup="true"
                   aria-expanded="false">

                    <div class="user-nav d-sm-flex d-none">
                        <span class="user-name fw-bolder">
                            {{ Auth::guard('admin')->user()->name }}
                        </span>
                        <span class="user-status">Admin</span>
                    </div>

                    <span class="avatar">
                        <img class="round"
                             src="{{ asset('panel-assets/images/portrait/small/avatar-s-11.jpg') }}"
                             alt="avatar"
                             height="40"
                             width="40">
                        <span class="avatar-status-online"></span>
                    </span>
                </a>

                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown-user">
                    <a class="dropdown-item" href="#">
                        <i class="me-50" data-feather="user"></i> Profile
                    </a>

                    <a class="dropdown-item" href="#">
                        <i class="me-50" data-feather="settings"></i> Settings
                    </a>

                    <div class="dropdown-divider"></div>

                    <a class="dropdown-item" href="{{ route('admin.logout') }}">
                        <i class="me-50" data-feather="power"></i> Logout
                    </a>
                </div>
            </li>
        </ul>

    </div>
</nav>