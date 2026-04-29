<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('orders.index') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-cash-register"></i>
        </div>
        <div class="sidebar-brand-text mx-3">
            {{ config('app.name') }}
        </div>
    </a>

    <hr class="sidebar-divider my-0">

    {{-- DASHBOARD --}}
    <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    {{-- TRANSAKSI --}}
    @if(auth()->user()->hasPermission('orders.view') || auth()->user()->hasPermission('orders.create'))
        <div class="sidebar-heading">
            Transaksi
        </div>

        @if(auth()->user()->hasPermission('orders.view'))
            <li class="nav-item {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('orders.index') }}">
                    <i class="fas fa-fw fa-shopping-cart"></i>
                    <span>Orders</span>
                </a>
            </li>
        @endif

        <hr class="sidebar-divider">
    @endif

    {{-- MASTER DATA --}}
    @php
        $showMaster = auth()->user()->hasPermission('customers.view')
            || auth()->user()->hasPermission('products.view')
            || auth()->user()->hasPermission('categories.view');
    @endphp

    @if($showMaster)
        <div class="sidebar-heading">
            Master Data
        </div>

        @if(auth()->user()->hasPermission('customers.view'))
            <li class="nav-item {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('customers.index') }}">
                    <i class="fas fa-fw fa-user-friends"></i>
                    <span>Customers</span>
                </a>
            </li>
        @endif

        @if(auth()->user()->hasPermission('products.view'))
            <li class="nav-item {{ request()->routeIs('products.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('products.index') }}">
                    <i class="fas fa-fw fa-box"></i>
                    <span>Products</span>
                </a>
            </li>
        @endif

        @if(auth()->user()->hasPermission('categories.view'))
            <li class="nav-item {{ request()->routeIs('product-categories.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('product-categories.index') }}">
                    <i class="fas fa-fw fa-tags"></i>
                    <span>Product Categories</span>
                </a>
            </li>
        @endif

        <hr class="sidebar-divider">
    @endif

    {{-- USER & ACCESS --}}
    @php
        $showAccess = auth()->user()->hasPermission('users.view')
            || auth()->user()->hasPermission('roles.view')
            || auth()->user()->hasPermission('permissions.view');
    @endphp

    @if($showAccess)
        <div class="sidebar-heading">
            User & Access
        </div>

        @if(auth()->user()->hasPermission('users.view'))
            <li class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('users.index') }}">
                    <i class="fas fa-fw fa-users"></i>
                    <span>Users</span>
                </a>
            </li>
        @endif

        @if(auth()->user()->hasPermission('roles.view'))
            <li class="nav-item {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('roles.index') }}">
                    <i class="fas fa-fw fa-user-tag"></i>
                    <span>Roles</span>
                </a>
            </li>
        @endif

        @if(auth()->user()->hasPermission('permissions.view'))
            <li class="nav-item {{ request()->routeIs('permissions.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('permissions.index') }}">
                    <i class="fas fa-fw fa-key"></i>
                    <span>Permissions</span>
                </a>
            </li>
        @endif

        <hr class="sidebar-divider d-none d-md-block">
    @endif

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>
