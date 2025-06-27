<x-guest-layout>
    @section('title', 'Trang chủ')
    <!-- SECTION -->
    <div class="section">
        <!-- container -->
        <div class="container">
            <!-- row -->
            <div class="row">
                <!-- shop -->
                @foreach ($collectionCategory as $item)
                    <div class="col-md-4 col-xs-6">
                        <div class="shop">
                            <div class="shop-img">
                                <img src="{{ asset('storage/' . $item->image) }}" alt="" class="category-img">
                            </div>
                            <div class="shop-body">
                                <h3>Bộ sưu tập<br />{{ $item->name }}</h3>
                                <a href="{{ route('category.show', $item->slug) }}" class="cta-btn">Xem ngay <i
                                        class="fa fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    </div>
                @endforeach
                <!-- /shop -->
            </div>
            <!-- /row -->
        </div>
        <!-- /container -->
    </div>
    <!-- /SECTION -->

    <!-- SECTION -->
    <div class="section">
        <!-- container -->
        <div class="container">
            <!-- row -->
            <div class="row">
                <!-- section title -->
                <div class="col-md-12">
                    <div class="section-title">
                        <h3 class="title">Sản phẩm mới</h3>
                        <div class="section-nav">
                        </div>
                    </div>
                </div>
                <!-- /section title -->
                <!-- Products tab & slick -->
                <div class="col-md-12">
                    <div class="row">
                        <div class="products-tabs">
                            <!-- tab -->
                            <div id="tab1" class="tab-pane active">
                                <div class="products-slick" data-nav="#slick-nav-1" data-slidesToShow="2"
                                    data-slidesToScroll="1">
                                    <!-- product -->
                                    @foreach ($productLatest as $item)
                                        @php
                                            // Chọn item để hiển thị giá: nếu có variant thì dùng variant đầu tiên
                                            $variant = $item->productVariants->first();
                                            $displayItem = $variant ?? $item;
                                            $variant =
                                                isset($item->productVariants) && $item->productVariants != '[]'
                                                    ? $item->productVariants->where('product_id', $item->id)->first()
                                                        ->id
                                                    : null;
                                            $isFavorited = $item->favoritedByUsers->contains(auth()->id()); // luôn check từ $product
                                        @endphp
                                        <div class="product">
                                            <div class="product-img">
                                                @php
                                                    $image =
                                                        $item->productImages->where('type', 1)->first()->image ?? '';
                                                    $imagePath = $image
                                                        ? asset('storage/' . $image)
                                                        : asset('asset/img/no-image.png');
                                                @endphp

                                                <div class="product-img-wrapper">
                                                    <img src="{{ $imagePath }}" alt="" class="product-img">
                                                </div>

                                                <div class="product-label">
                                                    @if ($displayItem->getIsOnSaleAttribute() && $displayItem->discount_percentage > 0)
                                                        {!! isset($displayItem->discount_percentage)
                                                            ? '<span class="sale">-' . $displayItem->discount_percentage . '%</span>'
                                                            : '' !!}
                                                    @endif
                                                    <span class="new">Mới</span>
                                                </div>
                                            </div>
                                            <div class="product-body">
                                                <p class="product-category">
                                                    {{ $item->category ? $item->category->name : ($item->subCategory ? $item->subCategory->categories->pluck('name')->implode(', ') : 'Chưa có') }}
                                                </p>
                                                <h3 class="product-name"><a
                                                        href="{{ route('product.show', ['slug' => $item->slug]) }}">{{ Str::limit($item->title, 20, '...') }}</a>
                                                </h3>
                                                <h4 class="product-price">
                                                    <h4 class="product-price">
                                                        @if ($displayItem->getIsOnSaleAttribute())
                                                            <span class="text-danger fw-bold">
                                                                {{ number_format($displayItem->getDisplayPriceAttribute()) }}
                                                                vnđ
                                                            </span>
                                                            <del class="text-muted">
                                                                {{ number_format($displayItem->original_price) }} vnđ
                                                            </del>
                                                        @else
                                                            <span>
                                                                {{ number_format($displayItem->original_price) }} vnđ
                                                            </span>
                                                        @endif
                                                    </h4>
                                                </h4>
                                                <?php
                                                $averageRating = $item->reviews->avg('rating') ?? 0;
                                                ?>
                                                <div class="product-rating">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <i class="fa fa-star{{ $i <= $averageRating ? '' : '-o' }}"
                                                            style="color: red"></i>
                                                    @endfor
                                                </div>

                                                <div class="product-btns">
                                                    @if (Auth::check())
                                                        <button class="add-to-wishlist" data-id="{{ $item->id }}"
                                                            data-variant-id="{{ $variant }}">
                                                            <i
                                                                class="fa fa-heart{{ $isFavorited ? '' : '-o' }} wishlist-icon"></i>
                                                            <span
                                                                class="tooltipp">{{ $isFavorited ? 'Đã yêu thích' : 'Yêu thích' }}</span>
                                                        </button>
                                                    @else
                                                        <button onclick="window.location='{{ route('login') }}'"
                                                            class="add-to-wishlist">
                                                            <i class="fa fa-heart-o"></i>
                                                            <span class="tooltipp">Đăng nhập để yêu thích</span>
                                                        </button>
                                                    @endif

                                                    <button class="quick-view"
                                                        onclick="window.location='{{ route('product.show', ['slug' => $item->slug]) }}'"><i
                                                            class="fa fa-eye"></i><span class="tooltipp">Xem sản
                                                            phẩm</span></button>
                                                </div>
                                            </div>
                                            <div class="add-to-cart">
                                                <form action="{{ route('cart.add', $item->id) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="qty" value="1">
                                                    <input type="hidden" name="product_variant_id"
                                                        value="{{ $variant }}">
                                                    <button type="submit" class="add-to-cart-btn">
                                                        <i class="fa fa-shopping-cart"></i> Thêm giỏ hàng
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                    <!-- /product -->
                                </div>
                                <div id="slick-nav-1" class="products-slick-nav"></div>
                            </div>
                            <!-- /tab -->
                        </div>
                    </div>
                </div>
                <!-- Products tab & slick -->
            </div>
            <!-- /row -->
        </div>
        <!-- /container -->
    </div>
    <!-- /SECTION -->

    <!-- HOT DEAL SECTION -->
    <div id="hot-deal" class="section">
        <!-- container -->
        <div class="container">
            <!-- row -->
            <div class="row">
                <div class="col-md-12">
                    <div class="hot-deal">
                        <ul class="hot-deal-countdown" id="deal-countdown" data-end="{{ $dealCountdown['end'] }}">
                            <li>
                                <div>
                                    <h3 id="deal-days">00</h3><span>Ngày</span>
                                </div>
                            </li>
                            <li>
                                <div>
                                    <h3 id="deal-hours">00</h3><span>Giờ</span>
                                </div>
                            </li>
                            <li>
                                <div>
                                    <h3 id="deal-mins">00</h3><span>Phút</span>
                                </div>
                            </li>
                            <li>
                                <div>
                                    <h3 id="deal-secs">00</h3><span>Giây</span>
                                </div>
                            </li>
                        </ul>
                        <h2 class="text-uppercase">GIẢM GIÁ CỰC SÂU</h2>
                        <p>CÁC SẢN PHẨM CÓ THỂ GIẢM GIÁ ĐẾN {{ $dealCountdown['maxDiscount'] }}%</p>
                        <a class="primary-btn cta-btn"
                            href="{{ route('category.show', ['slug' => 'khuyen-mai']) }}">MUA
                            NGAY</a>
                    </div>
                </div>
            </div>
            <!-- /row -->
        </div>
        <!-- /container -->
    </div>
    <!-- /HOT DEAL SECTION -->

    <!-- SECTION -->
    <div class="section">
        <!-- container -->
        <div class="container">
            <!-- row -->
            <div class="row">

                <!-- section title -->
                <div class="col-md-12">
                    <div class="section-title">
                        <h3 class="title">Sản phẩm bán chạy</h3>
                    </div>
                </div>
                <!-- /section title -->

                <!-- Products tab & slick -->
                <div class="col-md-12">
                    <div class="row">
                        <div class="products-tabs">
                            <!-- tab -->
                            <div id="tab2" class="tab-pane fade in active">
                                <div class="products-slick" data-nav="#slick-nav-2">
                                    <!-- product -->
                                    @foreach ($topSellingProducts as $topSellingProduct)
                                        @php
                                            $variant = $topSellingProduct->productVariants->first();
                                            $displayItem = $variant ?? $topSellingProduct;
                                            $variant =
                                                isset($topSellingProduct->productVariants) &&
                                                $topSellingProduct->productVariants != '[]'
                                                    ? $topSellingProduct->productVariants
                                                        ->where('product_id', $topSellingProduct->id)
                                                        ->first()->id
                                                    : null;
                                            $isFavorited = $topSellingProduct->favoritedByUsers->contains(auth()->id()); // luôn check từ $product
                                        @endphp
                                        <div class="product">
                                            <div class="product-img">
                                                @php
                                                    $image =
                                                        $topSellingProduct->productImages->where('type', 1)->first()
                                                            ->image ?? '';
                                                    $imagePath = $image
                                                        ? asset('storage/' . $image)
                                                        : asset('asset/img/no-image.png');
                                                @endphp
                                                <img src="{{ $imagePath }}"
                                                    alt="{{ $topSellingProduct->title }}">
                                                <div class="product-label">
                                                    @if ($displayItem->getIsOnSaleAttribute() && $displayItem->discount_percentage > 0)
                                                        {!! isset($displayItem->discount_percentage)
                                                            ? '<span class="sale">-' . $displayItem->discount_percentage . '%</span>'
                                                            : '' !!}
                                                    @endif
                                                    <span class="new">HOT</span>
                                                </div>
                                            </div>
                                            <div class="product-body">
                                                <p class="product-category">
                                                    {{ $topSellingProduct->category ? $topSellingProduct->category->name : ($topSellingProduct->subCategory ? $topSellingProduct->subCategory->categories->pluck('name')->implode(', ') : 'Chưa có') }}
                                                </p>
                                                <h3 class="product-name"><a
                                                        href="{{ route('product.show', ['slug' => $topSellingProduct->slug]) }}">{{ Str::limit($topSellingProduct->title, 20, '...') }}</a>
                                                </h3>
                                                </h3>
                                                <h4 class="product-price">
                                                    <h4 class="product-price">
                                                        @if ($displayItem->getIsOnSaleAttribute())
                                                            <span class="text-danger fw-bold">
                                                                {{ number_format($displayItem->getDisplayPriceAttribute()) }}
                                                                vnđ
                                                            </span>
                                                            <del class="text-muted">
                                                                {{ number_format($displayItem->original_price) }} vnđ
                                                            </del>
                                                        @else
                                                            <span>
                                                                {{ number_format($displayItem->original_price) }} vnđ
                                                            </span>
                                                        @endif
                                                    </h4>
                                                </h4>
                                                <div class="product-rating">
                                                    <?php
                                                    $averageRating = $topSellingProduct->reviews->avg('rating') ?? 0;
                                                    ?>
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <i class="fa fa-star{{ $i <= $averageRating ? '' : '-o' }}"
                                                            style="color: red"></i>
                                                    @endfor
                                                </div>
                                                <div class="product-btns">
                                                    @if (Auth::check())
                                                        <button class="add-to-wishlist"
                                                            data-id="{{ $topSellingProduct->id }}"
                                                            data-variant-id="{{ $variant }}">
                                                            <i
                                                                class="fa fa-heart{{ $isFavorited ? '' : '-o' }} wishlist-icon"></i>
                                                            <span
                                                                class="tooltipp">{{ $isFavorited ? 'Đã yêu thích' : 'Yêu thích' }}</span>
                                                        </button>
                                                    @else
                                                        <button onclick="window.location='{{ route('login') }}'"
                                                            class="add-to-wishlist">
                                                            <i class="fa fa-heart-o"></i>
                                                            <span class="tooltipp">Đăng nhập để yêu thích</span>
                                                        </button>
                                                    @endif

                                                    <button class="quick-view"
                                                        onclick="window.location='{{ route('product.show', ['slug' => $topSellingProduct->slug]) }}'"><i
                                                            class="fa fa-eye"></i><span class="tooltipp">Xem sản
                                                            phẩm</span></button>
                                                </div>
                                            </div>
                                            <div class="add-to-cart">
                                                <form action="{{ route('cart.add', $topSellingProduct->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    <input type="hidden" name="qty" value="1">
                                                    <input type="hidden" name="product_variant_id"
                                                        value="{{ $variant }}">
                                                    <button type="submit" class="add-to-cart-btn">
                                                        <i class="fa fa-shopping-cart"></i> Thêm giỏ hàng
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                    <!-- /product -->
                                </div>
                                <div id="slick-nav-2" class="products-slick-nav"></div>
                            </div>
                            <!-- /tab -->
                        </div>
                    </div>
                </div>
                <!-- /Products tab & slick -->
            </div>
            <!-- /row -->
        </div>
        <!-- /container -->
    </div>
    <!-- /SECTION -->

    <!-- SECTION -->
    <div class="section">
        <!-- container -->
        <div class="container">
            <!-- row -->
            <div class="row">
                <div class="col-md-4 col-xs-6">
                    <div class="section-title">
                        <h4 class="title">Sản phẩm được đánh giá cao</h4>
                        <div class="section-nav">
                            <div id="slick-nav-3" class="products-slick-nav"></div>
                        </div>
                    </div>

                    <div class="products-widget-slick" data-nav="#slick-nav-3">
                        @foreach ($topRated as $topRatedGroup)
                            <div>
                                @foreach ($topRatedGroup as $productTopRated)
                                    <!-- product widget -->
                                    <div class="product-widget">
                                        <div class="product-img">
                                            @php
                                                $imageProductTopRated =
                                                    $productTopRated->productImages->where('type', 1)->first()->image ??
                                                    '';
                                                $imagePathProductTopRated = $imageProductTopRated
                                                    ? asset('storage/' . $imageProductTopRated)
                                                    : asset('asset/img/no-image.png');
                                            @endphp
                                            <span class="product-img" style="cursor: pointer"
                                                onclick="window.location='{{ route('product.show', ['slug' => $productTopRated->slug]) }}'">
                                                <img src="{{ $imagePathProductTopRated }}"
                                                    alt="{{ $productTopRated->title }}"></span>

                                        </div>
                                        <div class="product-body">
                                            @php
                                                // Chọn item để hiển thị giá: nếu có variant thì dùng variant đầu tiên
                                                $variantProductTopRated = $productTopRated->productVariants->first();
                                                $displayProductTopRated = $variantProductTopRated ?? $productTopRated;
                                                $variantProductTopRated =
                                                    isset($productTopRated->productVariants) &&
                                                    $productTopRated->productVariants != '[]'
                                                        ? $productTopRated->productVariants
                                                            ->where('product_id', $productTopRated->id)
                                                            ->first()->id
                                                        : null;
                                                $isFavorited = $productTopRated->favoritedByUsers->contains(
                                                    auth()->id(),
                                                ); // luôn check từ $productTopRated

                                            @endphp
                                            {{ $productTopRated->category ? $productTopRated->category->name : ($productTopRated->subCategory ? $productTopRated->subCategory->categories->pluck('name')->implode(', ') : 'Chưa có') }}
                                            <h3 class="product-name"><a
                                                    href="{{ route('product.show', ['slug' => $productTopRated->slug]) }}">{{ Str::limit($productTopRated->title, 20, '...') }}</a>
                                            </h3>
                                            <h4 class="product-price">
                                                <h4 class="product-price">
                                                    @if ($displayProductTopRated->getIsOnSaleAttribute())
                                                        <span class="text-danger fw-bold">
                                                            {{ number_format($displayProductTopRated->getDisplayPriceAttribute()) }}
                                                            vnđ
                                                        </span>
                                                        <del class="text-muted">
                                                            {{ number_format($displayProductTopRated->original_price) }}
                                                            vnđ
                                                        </del>
                                                    @else
                                                        <span>
                                                            {{ number_format($displayProductTopRated->original_price) }}
                                                            vnđ
                                                        </span>
                                                    @endif
                                                </h4>
                                            </h4>
                                        </div>
                                    </div>
                                @endforeach
                                <!-- /product widget -->
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="col-md-4 col-xs-6">
                    <div class="section-title">
                        <h4 class="title">Sản phẩm nổi bật</h4>
                        <div class="section-nav">
                            <div id="slick-nav-4" class="products-slick-nav"></div>
                        </div>
                    </div>

                    <div class="products-widget-slick" data-nav="#slick-nav-4">
                        @foreach ($featured as $featuredGroup)
                            <div>
                                @foreach ($featuredGroup as $ProductFeatured)
                                    <!-- product widget -->
                                    <div class="product-widget">
                                        <div class="product-img">
                                            @php
                                                $imageProductFeatured =
                                                    $ProductFeatured->productImages->where('type', 1)->first()->image ??
                                                    '';
                                                $imagePathProductFeatured = $imageProductFeatured
                                                    ? asset('storage/' . $imageProductFeatured)
                                                    : asset('asset/img/no-image.png');
                                            @endphp
                                            <span class="product-img" style="cursor: pointer"
                                                onclick="window.location='{{ route('product.show', ['slug' => $ProductFeatured->slug]) }}'">
                                                <img src="{{ $imagePathProductFeatured }}"
                                                    alt="{{ $ProductFeatured->title }}"></span>

                                        </div>
                                        <div class="product-body">
                                            @php
                                                // Chọn item để hiển thị giá: nếu có variant thì dùng variant đầu tiên
                                                $variantProductFeatured = $ProductFeatured->productVariants->first();
                                                $displayProductFeatured = $variantProductFeatured ?? $ProductFeatured;
                                                $variantProductFeatured =
                                                    isset($ProductFeatured->productVariants) &&
                                                    $ProductFeatured->productVariants != '[]'
                                                        ? $ProductFeatured->productVariants
                                                            ->where('product_id', $ProductFeatured->id)
                                                            ->first()->id
                                                        : null;
                                                $isFavorited = $ProductFeatured->favoritedByUsers->contains(
                                                    auth()->id(),
                                                ); // luôn check từ $ProductFeatured

                                            @endphp
                                            {{ $ProductFeatured->category ? $ProductFeatured->category->name : ($ProductFeatured->subCategory ? $ProductFeatured->subCategory->categories->pluck('name')->implode(', ') : 'Chưa có') }}
                                            <h3 class="product-name"><a
                                                    href="{{ route('product.show', ['slug' => $ProductFeatured->slug]) }}">{{ Str::limit($ProductFeatured->title, 20, '...') }}</a>
                                            </h3>
                                            <h4 class="product-price">
                                                <h4 class="product-price">
                                                    @if ($displayProductFeatured->getIsOnSaleAttribute())
                                                        <span class="text-danger fw-bold">
                                                            {{ number_format($displayProductFeatured->getDisplayPriceAttribute()) }}
                                                            vnđ
                                                        </span>
                                                        <del class="text-muted">
                                                            {{ number_format($displayProductFeatured->original_price) }}
                                                            vnđ
                                                        </del>
                                                    @else
                                                        <span>
                                                            {{ number_format($displayProductFeatured->original_price) }}
                                                            vnđ
                                                        </span>
                                                    @endif
                                                </h4>
                                            </h4>
                                        </div>
                                    </div>
                                @endforeach
                                <!-- /product widget -->
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="clearfix visible-sm visible-xs"></div>

                <div class="col-md-4 col-xs-6">
                    <div class="section-title">
                        <h4 class="title">Sản phẩm được yêu thích</h4>
                        <div class="section-nav">
                            <div id="slick-nav-5" class="products-slick-nav"></div>
                        </div>
                    </div>

                    <div class="products-widget-slick" data-nav="#slick-nav-5">
                        @foreach ($mostFavorited as $mostFavoritedGroup)
                            <div>
                                @foreach ($mostFavoritedGroup as $ProductmostFavorited)
                                    <!-- product widget -->
                                    <div class="product-widget">
                                        <div class="product-img">
                                            @php
                                                $imageProductmostFavorited =
                                                    $ProductmostFavorited->productImages->where('type', 1)->first()
                                                        ->image ?? '';
                                                $imagePathProductmostFavorited = $imageProductmostFavorited
                                                    ? asset('storage/' . $imageProductmostFavorited)
                                                    : asset('asset/img/no-image.png');
                                            @endphp
                                            <span class="product-img" style="cursor: pointer"
                                                onclick="window.location='{{ route('product.show', ['slug' => $ProductmostFavorited->slug]) }}'">
                                                <img src="{{ $imagePathProductmostFavorited }}"
                                                    alt="{{ $ProductmostFavorited->title }}"></span>

                                        </div>
                                        <div class="product-body">
                                            @php
                                                // Chọn item để hiển thị giá: nếu có variant thì dùng variant đầu tiên
                                                $variantProductmostFavorited = $ProductmostFavorited->productVariants->first();
                                                $displayProductmostFavorited =
                                                    $variantProductmostFavorited ?? $ProductmostFavorited;
                                                $variantProductmostFavorited =
                                                    isset($ProductmostFavorited->productVariants) &&
                                                    $ProductmostFavorited->productVariants != '[]'
                                                        ? $ProductmostFavorited->productVariants
                                                            ->where('product_id', $ProductmostFavorited->id)
                                                            ->first()->id
                                                        : null;
                                                $isFavorited = $ProductmostFavorited->favoritedByUsers->contains(
                                                    auth()->id(),
                                                ); // luôn check từ $ProductmostFavorited

                                            @endphp
                                            {{ $ProductmostFavorited->category ? $ProductmostFavorited->category->name : ($ProductmostFavorited->subCategory ? $ProductmostFavorited->subCategory->categories->pluck('name')->implode(', ') : 'Chưa có') }}
                                            <h3 class="product-name"><a
                                                    href="{{ route('product.show', ['slug' => $ProductmostFavorited->slug]) }}">{{ Str::limit($ProductmostFavorited->title, 20, '...') }}</a>
                                            </h3>
                                            <h4 class="product-price">
                                                <h4 class="product-price">
                                                    @if ($displayProductmostFavorited->getIsOnSaleAttribute())
                                                        <span class="text-danger fw-bold">
                                                            {{ number_format($displayProductmostFavorited->getDisplayPriceAttribute()) }}
                                                            vnđ
                                                        </span>
                                                        <del class="text-muted">
                                                            {{ number_format($displayProductmostFavorited->original_price) }}
                                                            vnđ
                                                        </del>
                                                    @else
                                                        <span>
                                                            {{ number_format($displayProductmostFavorited->original_price) }}
                                                            vnđ
                                                        </span>
                                                    @endif
                                                </h4>
                                            </h4>
                                        </div>
                                    </div>
                                @endforeach
                                <!-- /product widget -->
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
            <!-- /row -->
        </div>
        <!-- /container -->
    </div>
    <!-- /SECTION -->
    <!-- NEWSLETTER -->
    <div id="newsletter" class="section">
        <!-- container -->
        <div class="container">
            <!-- row -->
            <div class="row">
                <div class="col-md-12">
                    <div class="newsletter">
                        <p>Đăng ký để nhận <strong>TIN MỚI NGAY</strong></p>
                        @if (session('success'))
                            <div class="alert alert-success text-center">{{ session('success') }}</div>
                        @endif
                        @error('email')
                            <div class="alert alert-danger text-center">{{ $message }}</div>
                        @enderror
                        <form action="{{ route('newsletter.subscribe') }}" method="POST" class="newsletter-form">
                            @csrf
                            <input class="input @error('email') is-invalid @enderror" name="email" type="email"
                                placeholder="Nhập địa chỉ email của bạn" value="{{ old('email') }}">

                            <button class="newsletter-btn" type="submit"><i class="fa fa-envelope"></i> Đăng
                                ký</button>
                        </form>
                        <ul class="newsletter-follow">
                            <li>
                                <a href="#"><i class="fa fa-facebook"></i></a>
                            </li>
                            <li>
                                <a href="#"><i class="fa fa-twitter"></i></a>
                            </li>
                            <li>
                                <a href="#"><i class="fa fa-instagram"></i></a>
                            </li>
                            <li>
                                <a href="#"><i class="fa fa-pinterest"></i></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /row -->
        </div>
        <!-- /container -->
    </div>
    <!-- /NEWSLETTER -->
</x-guest-layout>

<style>
    .shop-img {
        width: 100%;
        height: 250px;
        /* hoặc chiều cao bạn muốn */
        overflow: hidden;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .category-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        /* Đảm bảo ảnh không bị méo */
    }

    .product-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .product-img-wrapper {
        width: 100%;
        height: 250px;
        /* hoặc bất kỳ chiều cao nào bạn muốn */
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
<script>
    document.querySelectorAll('.add-to-wishlist').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            const productId = this.dataset.id;
            const variantId = this.dataset.variantId;

            fetch('/favorites/' + productId, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                            .getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        variant_id: variantId
                    })
                })
                .then(response => {
                    if (!response.ok) throw new Error('Phản hồi không hợp lệ');
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'added') {
                        this.querySelector('i').classList.remove('fa-heart-o');
                        this.querySelector('i').classList.add('fa-heart');
                        this.querySelector('.tooltipp').textContent = 'Đã yêu thích';
                        showAlertModal('Đã thêm vào yêu thích', 'success');
                    } else if (data.status === 'removed') {
                        this.querySelector('i').classList.remove('fa-heart');
                        this.querySelector('i').classList.add('fa-heart-o');
                        this.querySelector('.tooltipp').textContent = 'Yêu thích';
                        showAlertModal('Đã xóa yêu thích', 'success');
                    }
                })
                .catch(err => {
                    showAlertModal('Vui lòng đăng nhập để sử dụng tính năng này', 'warning');
                });
        });
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const countdownEl = document.getElementById("deal-countdown");
        if (!countdownEl) return;

        const endTime = new Date(countdownEl.dataset.end).getTime();

        const daysEl = document.getElementById("deal-days");
        const hoursEl = document.getElementById("deal-hours");
        const minsEl = document.getElementById("deal-mins");
        const secsEl = document.getElementById("deal-secs");

        function updateCountdown() {
            const now = new Date().getTime();
            const distance = endTime - now;

            if (distance < 0) {
                daysEl.textContent = "00";
                hoursEl.textContent = "00";
                minsEl.textContent = "00";
                secsEl.textContent = "00";
                return;
            }

            const d = Math.floor(distance / (1000 * 60 * 60 * 24));
            const h = Math.floor((distance / (1000 * 60 * 60)) % 24);
            const m = Math.floor((distance / 1000 / 60) % 60);
            const s = Math.floor((distance / 1000) % 60);

            daysEl.textContent = String(d).padStart(2, '0');
            hoursEl.textContent = String(h).padStart(2, '0');
            minsEl.textContent = String(m).padStart(2, '0');
            secsEl.textContent = String(s).padStart(2, '0');
        }

        updateCountdown();
        setInterval(updateCountdown, 1000);
    });
</script>
@if (session('success') || session('error') || $errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const target = document.getElementById("newsletter");
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    </script>
@endif
