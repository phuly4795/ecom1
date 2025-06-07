<header>
    <!-- TOP HEADER -->
    <div id="top-header">
        <div class="container">
            <ul class="header-links pull-left">
                <li><a href="#"><i class="fa fa-phone"></i> +021-95-51-84</a></li>
                <li><a href="#"><i class="fa fa-envelope-o"></i> email@email.com</a></li>
                <li><a href="#"><i class="fa fa-map-marker"></i> 1734 Stonecoal Road</a></li>
            </ul>
            <ul class="header-links pull-right"
                style="list-style: none; display: flex; align-items: center; padding: 0; margin: 0;">
                @if (Auth::user())
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Xin chào, {{ Illuminate\Support\Str::limit(Auth::user()->name, 20, '...') }}
                        </a>

                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                            aria-labelledby="userDropdown">

                            <a class="dropdown-item" href="{{ route('my.account') }}">
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
                @else
                    <li style="display: flex; align-items: center;">
                        <a href="{{ route('login') }}"><i class="fa fa-user-o" style="margin-right: 5px;"></i>Đăng
                            nhập</a>
                    </li>
                    <li style="color: #aaa;">|</li>
                    <li style="display: flex; align-items: center;">
                        <a href="#">Đăng ký</a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
    <!-- /TOP HEADER -->

    <!-- MAIN HEADER -->
    <div id="header">
        <!-- container -->
        <div class="container">
            <!-- row -->
            <div class="row">
                <!-- LOGO -->
                <div class="col-md-3">
                    <div class="header-logo">
                        <a href="{{ route('home') }}" class="logo">
                            <img src="{{ asset('asset/guest/img/logo.png') }}" alt="">
                        </a>
                    </div>
                </div>
                <!-- /LOGO -->

                <!-- SEARCH BAR -->
                <div class="col-md-6">
                    <div class="header-search">
                        <form>
                            <select class="input-select" style="height: 41px;">
                                <option value="0">All Categories</option>
                                <option value="1">Category 01</option>
                                <option value="1">Category 02</option>
                            </select>
                            <input class="input" placeholder="Nhập sản phẩm muốn tìm kiếm...">
                            <button class="search-btn">Tìm kíếm</button>
                        </form>
                    </div>
                </div>
                <!-- /SEARCH BAR -->

                <!-- ACCOUNT -->
                <div class="col-md-3 clearfix">
                    <div class="header-ctn">
                        <!-- Wishlist -->
                        <div>
                            <a href="#">
                                <i class="fa fa-heart-o"></i>
                                <span>Yêu thích</span>
                                <div class="qty">2</div>
                            </a>
                        </div>
                        <!-- /Wishlist -->

                        <!-- Cart -->
                        <div class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                                <i class="fa fa-shopping-cart"></i>
                                <span>Giỏ hàng</span>
                                <div class="qty">{{ $countQtyCart }}</div>
                            </a>
                            <div class="cart-dropdown">
                                <div class="cart-list">
                                    @forelse ($cartItems as $item)
                                        <div class="product-widget">
                                            <div class="product-img">
                                                @php
                                                    $image = $item->product->productImages->where('type', 1)->first();
                                                    $imagePath = $image
                                                        ? asset('storage/' . $image->image)
                                                        : asset('asset/img/no-image.png');
                                                @endphp
                                                <img src="{{ $imagePath }}" alt="{{ $item->product->title }}">
                                            </div>
                                            <div class="product-body">
                                                <h3 class="product-name"><a
                                                        href="{{ route('product.show', ['slug' => $item->product->slug]) }}">{{ $item->product->title }}</a>
                                                </h3>
                                                <h4 class="product-price"><span
                                                        class="qty">{{ $item->qty }}x</span>
                                                    {{ number_format($item->product->price) . ' vnđ' }}</h4>
                                            </div>
                                            <form action="{{ route('cart.remove', $item->id) }}" method="POST"
                                                style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button class="delete" type="submit"><i
                                                        class="fa fa-close"></i></button>
                                            </form>
                                        </div>
                                    @empty
                                        <p>Giỏ hàng trống</p>
                                    @endforelse
                                </div>
                                <div class="cart-summary">
                                    <small>Có {{ $countQtyCart }} sản phẩm trong giỏ hàng</small>
                                    <h5>Tổng tiền: {{ number_format($totalPrice) . ' vnđ' }}</h5>
                                </div>
                                <div class="cart-btns">
                                    <a href="{{ route('cart.show') }}">Xem giỏ hàng</a>
                                    <a href="{{ route('cart.checkout') }}">Thanh toán <i
                                            class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <!-- /Cart -->

                        <!-- Menu Toogle -->
                        <div class="menu-toggle">
                            <a href="#">
                                <i class="fa fa-bars"></i>
                                <span>Danh mục</span>
                            </a>
                        </div>
                        <!-- /Menu Toogle -->
                    </div>
                </div>
                <!-- /ACCOUNT -->
            </div>
            <!-- row -->
        </div>
        <!-- container -->
    </div>
    <!-- /MAIN HEADER -->
</header>
