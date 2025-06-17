<header>

    <!-- TOP HEADER -->
    <div id="top-header">
        <div class="container">
            <ul class="header-links pull-left">
                <li><a href="#"><i class="fa fa-phone"></i> +{{ \App\Models\Setting::get('phone') }}</a>
                </li>
                <li><a href="mailto:{{ \App\Models\Setting::get('contact_email') }}"><i class="fa fa-envelope-o"></i>
                        {{ \App\Models\Setting::get('contact_email') }}</a></li>
                <li><a href="#"><i class="fa fa-map-marker"></i>  {{ \App\Models\Setting::get('address') }}</a></li>
            </ul>
            <ul class="header-links pull-right"
                style="list-style: none; display: flex; align-items: center; padding: 0; margin: 0;">
                @if (Auth::user())
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button">
                            <i class="fa fa-user-circle"></i> {{ Str::limit(Auth::user()->name, 20, '...') }} <span
                                class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            @if (Auth::user()->hasRoles('admin'))
                                <li>
                                    <a href="{{ route('admin.dashboard') }}">
                                        <i class="fa fa-user"></i> Quản trị
                                    </a>
                                </li>
                            @endif
                            <li>
                                <a href="{{ route('my.account') }}">
                                    <i class="fa fa-user"></i> Hồ sơ cá nhân
                                </a>
                            </li>
                            <li>
                                <a href="#"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fa fa-sign-out"></i> Đăng xuất
                                </a>
                            </li>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </ul>
                    </li>
                @else
                    <li style="display: flex; align-items: center;">
                        <a href="{{ route('login') }}"><i class="fa fa-user-o" style="margin-right: 5px;"></i>Đăng
                            nhập</a>
                    </li>
                    <li style="color: #aaa;">|</li>
                    <li style="display: flex; align-items: center;">
                        <a href="{{ route('register') }}">Đăng ký</a>
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
                            <select class="input-select" id="category-input">
                                <option value="">Tất cả</option>
                                @foreach ($globalCategories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <input class="input" id="search-input" placeholder="Nhập sản phẩm muốn tìm kiếm..."
                                autocomplete="off">
                            <button class="search-btn">Tìm kiếm</button>
                            <div id="search-results"
                                style="position:absolute; background:#fff; width:100%; z-index:99;"></div>
                        </form>
                    </div>
                </div>
                <!-- /SEARCH BAR -->

                <!-- ACCOUNT -->
                <div class="col-md-3 clearfix">
                    <div class="header-ctn">
                        <!-- Wishlist -->
                        <div>
                            <a href="{{ route('favorites.index') }}">
                                <i class="fa fa-heart-o"></i>
                                <span>Yêu thích</span>
                                <div class="qty">{{ $countFavoriteProduct }}</div>
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
                                                    <?php
                                                    $product = $item->productVariant ?? $item->product;
                                                    $newFinalPrice = $product->is_on_sale ? $product->display_price : $product->original_price;
                                                    ?>
                                                    @if ($product->is_on_sale)
                                                        <span
                                                            class="text-danger fw-bold">{{ number_format($newFinalPrice) }}
                                                            vnđ</span>
                                                        <del class="text-muted">{{ number_format($product->original_price) }}
                                                            vnđ</del>
                                                    @else
                                                        <span>{{ number_format($product->original_price) }}
                                                            vnđ</span>
                                                    @endif
                                                </h4>
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
                                    <h5>Tổng tiền: {{ number_format($totalPrice) . ' vnđ' }} </h5>
                                    <span>(Đã gồm phí vận chuyển và giảm giá)</span>
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
<style>
    .dropdown-menu {
        padding: 10px;
        min-width: 180px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        border-radius: 6px;
    }

    .dropdown-menu li a {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .dropdown-menu li a i {
        width: 20px;
        text-align: center;
    }

    /* Dropdown menu chỉnh màu chữ & icon bình thường và khi hover */
    .dropdown-menu li a {
        color: #333 !important;
        /* màu chữ bình thường */
        background-color: transparent;
    }

    .dropdown-menu li a:hover {
        color: #c70101 !important;
        /* màu khi hover */
        background-color: #f5f5f5;
    }

    /* Icon trong menu rõ ràng hơn */
    .dropdown-menu li a i {
        color: #c70101 !important;
        /* hoặc #333 nếu muốn đen */
    }

    .search-item:hover {
        background-color: #f8f9fa;
        transition: 0.2s ease;
        cursor: pointer;
    }

    .search-thumb img {
        border-radius: 6px;
        object-fit: cover;
    }

    .search-info a {
        display: block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 230px;
    }
</style>
<script>
    let debounceTimer;

    document.getElementById('search-input').addEventListener('input', function() {
        const query = this.value;
        const categoryId = document.getElementById('category-input').value;
        clearTimeout(debounceTimer);

        debounceTimer = setTimeout(() => {
            if (query.length >= 2) {
                fetch(`/search-products?query=${encodeURIComponent(query)}&category=${categoryId}`)
                    .then(response => response.json())
                    .then(data => {
                        const resultBox = document.getElementById('search-results');
                        resultBox.classList.add("search-suggestion-box");
                        resultBox.innerHTML = '';

                        if (data.items.length === 0) {
                            resultBox.innerHTML =
                                '<div style="display: flex; align-items: center; gap: 10px;">Không tìm thấy sản phẩm nào.</div>';
                            return;
                        }

                        data.items.forEach(product => {
                            resultBox.innerHTML += `
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div class="search-thumb" style="flex-shrink: 0;">
                                        <img src="${product.image}" alt="${product.name}" width="60" height="60" class="rounded" style="object-fit:cover;">
                                    </div>
                                    <div class="search-info" style="flex-grow: 1;">
                                        <a href="${product.route}" class="d-block text-dark font-weight-bold mb-1" style="font-size:14px; line-height: 1.2;">
                                            ${product.name}
                                        </a>
                                        <div class="product-price">
                                            <span class="text-danger font-weight-bold">${product.price}</span>
                                            ${product.is_on_sale
                                                ? `<span class="text-muted ml-2" style="text-decoration:line-through;">${product.original_price}</span>`
                                                : ''
                                            }
                                        </div>
                                    </div>
                                </div>
                            `;
                        });



                    });
            } else {
                document.getElementById('search-results').innerHTML = '';
            }
        }, 500); // debounce 500ms
    });
</script>
