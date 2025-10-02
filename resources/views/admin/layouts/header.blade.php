<nav
    class="header-navbar navbar navbar-expand-lg align-items-center floating-nav navbar-light navbar-shadow  bg-primary ">
    <div class="navbar-container d-flex content">
        {{-- <div class="bookmark-wrapper d-flex align-items-center">
            <ul class="nav navbar-nav d-xl-none">
                <li class="nav-item"><a class="nav-link menu-toggle" href="#"><i class="ficon"
                            data-feather="menu"></i></a>
                </li>
            </ul>
        </div> --}}
        <span style="color: white; font-weight: bold; font-size: 16px; display: flex; text-align: center; justify-content: space-between; align-content: space-between;align-items: center;">Trusted Beauty Service at Your Doorstep</span>
        <ul class="nav navbar-nav align-items-center ms-auto">
            <li class="nav-item dropdown dropdown-user">
                <a class="nav-link dropdown-toggle dropdown-user-link" id="dropdown-user" href="#"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <div class="user-nav d-sm-flex d-none"><span
                            class="user-name fw-bolder">{{ Auth::guard('admin')->user()->name }}</span><span
                            class="user-status">Admin</span></div>
                    <span class="avatar"><img class="round"
                            src="{{ asset('panel-assets/images/portrait/small/avatar-s-11.jpg') }}" alt="avatar"
                            height="40" width="40"><span class="avatar-status-online"></span></span>
                </a>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown-user">
                    <a class="dropdown-item" href="#">
                        <i class="me-50" data-feather="user"></i> Profile
                    </a>
                    <a class="dropdown-item" href="#">
                        <i class="me-50" data-feather="settings"></i>Settings
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('admin.logout') }}">
                        <i class="me-50" data-feather="power"></i>
                        Logout
                    </a>
                </div>
            </li>
        </ul>
    </div>
</nav>
