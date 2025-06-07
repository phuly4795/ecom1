<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('admin.dashboard') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">SB Admin <sup>2</sup></div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Trang chủ</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Nav Items -->
    <li class="nav-item {{ request()->routeIs('admin.category.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.category.index') }}">
            <i class="fas fa-fw fa-list"></i>
            <span>Danh mục sản phẩm</span></a>
    </li>

    <li class="nav-item {{ request()->routeIs('admin.sub_category.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.sub_category.index') }}">
            <i class="fas fa-fw fa-list-ol"></i>
            <span>Danh mục phụ</span></a>
    </li>

    <li class="nav-item {{ request()->routeIs('admin.brand.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.brand.index') }}">
            <i class="fas fa-fw fa-tags"></i>
            <span>Thương hiệu</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <li class="nav-item {{ request()->routeIs('admin.product.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.product.index') }}">
            <i class="fas fa-fw fa-tags"></i>
            <span>Quản lí sản phẩm</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <li class="nav-item {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.orders.index') }}">
            <i class="fas fa-fw fa-tags"></i>
            <span>Quản lí đơn hàng</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <li class="nav-item">
        <a class="nav-link" href="{{ route('home') }}">
            <i class="fas fa-fw fa-tags"></i>
            <span>Về trang người dùng</span></a>
    </li>
    <!-- Sidebar Toggler (Sidebar) -->
    {{-- <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div> --}}
</ul>
