    <!-- Topbar -->
    <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

        <!-- Sidebar Toggle (Topbar) -->
        <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
            <i class="fa fa-bars"></i>
        </button>

        <!-- Topbar Search -->
        <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
            <div class="input-group">
                <input type="text" class="form-control bg-light border-0 small" placeholder="Tìm kiếm..."
                    aria-label="Search" aria-describedby="basic-addon2">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="button">
                        <i class="fas fa-search fa-sm"></i>
                    </button>
                </div>
            </div>
        </form>

        <!-- Topbar Navbar -->
        <ul class="navbar-nav ml-auto">

            <!-- Nav Item - Search Dropdown (Visible Only XS) -->
            <li class="nav-item dropdown no-arrow d-sm-none">
                <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-search fa-fw"></i>
                </a>
                <!-- Dropdown - Messages -->
                <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                    aria-labelledby="searchDropdown">
                    <form class="form-inline mr-auto w-100 navbar-search">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small"
                                placeholder="Tìm kiếm..." aria-label="Search" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </li>

            <!-- Nav Item - Alerts -->
            <li class="nav-item dropdown no-arrow mx-1">
                <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-bell fa-fw"></i>
                    <!-- Counter - Alerts -->
                    <span class="badge badge-danger badge-counter" id="messages-count-alert">
                        {{ $notifications->count() }}</span>
                </a>
                <!-- Dropdown - Alerts -->
                <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                    aria-labelledby="alertsDropdown">
                    <h6 class="dropdown-header">
                        Thông báo
                    </h6>
                    <div id="messages-list-alert" style="max-height: 300px; overflow-y: auto;">
                        @foreach ($notifications as $notification)
                            <?php
                            $href = '/admin/dashboard';
                            if ($notification->type == 'new-order') {
                                $href = '/admin/order/' . $notification->reference_id;
                            } elseif ($notification->type == 'paymented') {
                                $href = '/admin/order/' . $notification->reference_id;
                            } elseif ($notification->type == 'new-user') {
                                $href = '/admin/users';
                            } elseif ($notification->type == 'promotion-expire') {
                                $href = '/admin/coupons';
                            } elseif ($notification->type == 'low-stock') {
                                $href = '/admin/product/' . $notification->reference_id . '/edit';
                            }
                            ?>
                            <a class="dropdown-item d-flex align-items-center notification-link"
                                href="{{ $href }}" data-id="{{ $notification->id }}">
                                <div class="mr-3">
                                    @switch($notification->type)
                                        @case('new-order')
                                            <div class="icon-circle bg-primary">
                                                <i class="fas fa-shopping-cart text-white"></i>
                                            </div>
                                        @break

                                        @case('paymented')
                                            <div class="icon-circle bg-success">
                                                <i class="fas fa-credit-card text-white"></i>
                                            </div>
                                        @break

                                        @case('new-user')
                                            <div class="icon-circle bg-info">
                                                <i class="fas fa-user-plus text-white"></i>
                                            </div>
                                        @break

                                        @case('promotion-expire')
                                            <div class="icon-circle bg-warning">
                                                <i class="fas fa-clock text-white"></i>
                                            </div>
                                        @break

                                        @case('low-stock')
                                            <div class="icon-circle bg-warning">
                                                <i class="fas fa-clock text-white"></i>
                                            </div>
                                        @break

                                        @default
                                    @endswitch
                                </div>
                                <div>
                                    <div class="small text-gray-500">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </div>
                                    <span class="font-weight-bold">{{ $notification->title }}</span>
                                    <div>{{ Str::limit($notification->message, 50) }}</div>
                                </div>
                            </a>
                        @endforeach
                        <a class="dropdown-item text-center small text-gray-500" href="#">Xóa tất cả </a>
                    </div>

                </div>
            </li>

            <!-- Nav Item - Messages -->
            <li class="nav-item dropdown no-arrow mx-1">
                <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-envelope fa-fw"></i>
                    <!-- Counter - Messages -->
                    <span class="badge badge-danger badge-counter" id="messages-count">
                        {{ $notificationContacts->count() }}
                    </span>
                </a>
                <!-- Dropdown - Messages -->
                <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                    aria-labelledby="messagesDropdown">
                    <h6 class="dropdown-header">
                        Tin nhắn người dùng
                    </h6>
                    <div id="messages-list" style="max-height: 300px; overflow-y: auto;">
                        @if ($notificationContacts->count() > 0)
                            @foreach ($notificationContacts as $notification)
                                <a class="dropdown-item d-flex align-items-center"
                                    href="{{ route('admin.contacts.show', $notification->id) }}">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-primary">
                                            <i class="fas fa-envelope text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </div>
                                        <span class="font-weight-bold">{{ $notification->name }}</span>
                                        <div>{{ Str::limit($notification->content, 50) }}</div>
                                    </div>
                                </a>
                            @endforeach
                            <a class="dropdown-item text-center small text-gray-500"
                                href="{{ route('admin.contacts.index',  ['seen_all' => "true"]) }}">Xem tất cả</a>
                        @else
                            <p class="text-center text-muted mb-0">Không có tin nhắn</p>
                        @endif

                    </div>
                </div>
            </li>

            <div class="topbar-divider d-none d-sm-block"></div>
            <!-- Nav Item - User Information -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Xin chào, {{ Illuminate\Support\Str::limit(Auth::user()->name, 20, '...') }}
                </a>

                <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                    aria-labelledby="userDropdown">

                    <a class="dropdown-item" href="{{ route('admin.profile.edit') }}">
                        <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                        Hồ sơ cá nhân
                    </a>

                    <div class="dropdown-divider"></div>

                    <a class="dropdown-item" href="#"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                        Đăng xuất
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </li>
        </ul>
    </nav>
    <!-- End of Topbar -->
