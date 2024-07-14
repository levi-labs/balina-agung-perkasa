<div class="sidebar" data-background-color="dark">
    <div class="sidebar-logo">
        <!-- Logo Header -->
        <div class="logo-header" data-background-color="dark">
            <a href="{{ route('dashboard') }}" class="logo mt-2">
                <img src="{{ asset('assets/logo.png') }}" alt="navbar brand" class="navbar-brand" width=25%" />
                <div class="text-center">
                    <small class="text-sm text-white">Belina Agung Perkasa</small>
                </div>
            </a>

            <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar">
                    <i class="gg-menu-right"></i>
                </button>
                <button class="btn btn-toggle sidenav-toggler">
                    <i class="gg-menu-left"></i>
                </button>
            </div>
            <button class="topbar-toggler more">
                <i class="gg-more-vertical-alt"></i>
            </button>
        </div>

        <!-- End Logo Header -->
    </div>
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <ul class="nav nav-secondary">
                <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}">
                        <i class="fas fa-home"></i>
                        <span class="sub-item">Dashboard</span>
                    </a>
                </li>
                <li class="nav-section">
                    <span class="sidebar-mini-icon">
                        <i class="fa fa-ellipsis-h"></i>
                    </span>
                    <h4 class="text-section">Menu Data</h4>
                </li>
                <li class="nav-item {{ request()->routeIs('data-training') ? 'active' : '' }}">
                    <a href="{{ route('data-training') }}">
                        <i class="fas fa-layer-group"></i>
                        <span class="sub-item">Data Training</span>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('prediksi') ? 'active' : '' }}">
                    <a href="{{ route('prediksi') }}">
                        <i class="far fa-chart-bar"></i>
                        <span class="sub-item">Data Prediksi</span>
                    </a>
                </li>

                @if (Auth::user()->level == 'superadmin')
                    <li class="nav-section">
                        <span class="sidebar-mini-icon">
                            <i class="fa fa-ellipsis-h"></i>
                        </span>
                        <h4 class="text-section">Daftar Users</h4>
                    </li>
                    <li class="nav-item {{ request()->routeIs('user') ? 'active' : '' }}">
                        <a href="{{ route('user') }}">
                            <i class="fas fa-users"></i>
                            <span class="sub-item">Users</span>
                        </a>
                    </li>
                @endif

            </ul>
        </div>
    </div>
</div>
