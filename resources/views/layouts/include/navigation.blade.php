<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('admin.dashboard.index') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-store"></i>
        </div>
        <div class="sidebar-brand-text mx-3">SB Admin <sup>2</sup></div>
    </a>

    <hr class="sidebar-divider my-0">

    <!-- Trang chủ -->
    <li class="nav-item {{ request()->routeIs('admin.dashboard.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.dashboard.index') }}">
            <i class="fas fa-home"></i>
            <span>Trang chủ</span></a>
    </li>

    <hr class="sidebar-divider">

    <!-- Danh mục -->
    <li class="nav-item {{ request()->routeIs('admin.category.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.category.index') }}">
            <i class="fas fa-th-large"></i>
            <span>Danh mục sản phẩm</span></a>
    </li>

    <li class="nav-item {{ request()->routeIs('admin.sub_category.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.sub_category.index') }}">
            <i class="fas fa-th-list"></i>
            <span>Danh mục phụ</span></a>
    </li>

    <li class="nav-item {{ request()->routeIs('admin.brand.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.brand.index') }}">
            <i class="fas fa-bold"></i>
            <span>Thương hiệu</span></a>
    </li>

    <hr class="sidebar-divider">

    <li class="nav-item {{ request()->routeIs('admin.product.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.product.index') }}">
            <i class="fas fa-box-open"></i>
            <span>Quản lý sản phẩm</span></a>
    </li>

    <hr class="sidebar-divider">

    <li class="nav-item {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.orders.index') }}">
            <i class="fas fa-shopping-cart"></i>
            <span>Quản lý đơn hàng</span></a>
    </li>

    <li class="nav-item {{ request()->routeIs('admin.shipping_fees.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.shipping_fees.index') }}">
            <i class="fas fa-shopping-cart"></i>
            <span>Quản lý phí vận chuyển</span></a>
    </li>

    <li class="nav-item {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.coupons.index') }}">
            <i class="fas fa-ticket-alt"></i>
            <span>Quản lý khuyến mãi</span></a>
    </li>

    <hr class="sidebar-divider">

    <li class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.users.index') }}">
            <i class="fas fa-users"></i>
            <span>Quản lý người dùng</span></a>
    </li>

    <li class="nav-item {{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.contacts.index') }}">
            <i class="fas fa-users"></i>
            <span>Quản lý liên hệ</span></a>
    </li>
    <li class="nav-item {{ request()->routeIs('admin.newsletters..*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.newsletters.index') }}">
            <i class="fas fa-users"></i>
            <span>Đăng ký nhận tin</span></a>
    </li>

    <hr class="sidebar-divider">

    <li class="nav-item {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.pages.index') }}">
            <i class="fas fa-file-alt"></i>
            <span>Quản lý trang</span></a>
    </li>

    <li class="nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.settings.edit') }}">
            <i class="fas fa-cogs"></i>
            <span>Quản lý cấu hình</span></a>
    </li>

    <hr class="sidebar-divider">

    <li class="nav-item">
        <a class="nav-link" href="/laravel-websockets">
            <i class="fas fa-arrow-left"></i>
            <span>Kiểm tra websockets</span></a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="{{ route('home') }}">
            <i class="fas fa-arrow-left"></i>
            <span>Về trang người dùng</span></a>
    </li>
</ul>

<!-- Sidebar Toggler (Sidebar) -->
{{-- <div class="text-center d-none d-md-inline">
    <button class="rounded-circle border-0" id="sidebarToggle"></button>
</div> --}}
