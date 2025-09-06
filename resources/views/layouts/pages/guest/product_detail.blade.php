<x-guest-layout>
    @section('title', 'Chi tiết sản phẩm')
    @section('meta')
        <meta name="title" content="{{ $product->meta_title ?? $product->title }}">
        <meta name="description" content="{{ $product->meta_description }}">
        <meta name="keywords" content="{{ $product->meta_keywords }}">
    @endsection
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <!-- BREADCRUMB -->
    <div id="breadcrumb" class="section">
        <!-- container -->
        <div class="container">
            <!-- row -->
            <div class="row">
                <div class="col-md-12">
                    <ul class="breadcrumb-tree">
                        @foreach ($breadcrumbs as $breadcrumb)
                            @if ($loop->last)
                                <li class="active">{{ $breadcrumb['name'] }}</li>
                            @else
                                <li><a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['name'] }}</a></li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
            <!-- /row -->
        </div>
        <!-- /container -->
    </div>
    <!-- /BREADCRUMB -->

    <!-- SECTION -->
    <div class="section">
        <!-- container -->
        <div class="container">
            <!-- row -->
            <div class="row">
                <!-- Product main img -->
                <div class="col-md-5 col-md-push-2">
                    <div id="product-main-img">
                        @foreach ($product->productImages as $image)
                            <div class="">
                                <img src="{{ asset('storage/' . $image->image) }}" alt="">
                            </div>
                        @endforeach
                    </div>
                </div>
                <!-- /Product main img -->

                <!-- Product thumb imgs -->
                <div class="col-md-2 col-md-pull-5">
                    <div id="product-imgs">
                        @foreach ($product->productImages as $imageThumb)
                            <div class="product-preview">
                                <img src="{{ asset('storage/' . $imageThumb->image) }}" alt="">
                            </div>
                        @endforeach
                    </div>
                </div>
                <!-- /Product thumb imgs -->

                <!-- Product details -->
                <?php
                $variant = $selectedVariant ?? $product->productVariants->first(fn($v) => $v->qty > 0); // ✅ Ưu tiên biến thể được chọn
                $displayItem = $variant ?? $product;
                $isFavorited = $product->favoritedByUsers->contains(auth()->id()); // luôn check từ $product
                ?>
                <div class="col-md-5">
                    <div class="product-details">
                        <h2 class="product-name">{{ $product->title }}</h2>
                        <div>
                            <div class="product-rating">
                                <div class="rating-stars">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="fa fa-star{{ $i <= $averageRating ? '' : '-o' }}"
                                            style="color: red"></i>
                                    @endfor
                                </div>
                            </div>
                            <a class="review-link" href="#tab3">{{ $product->reviews->count() ?? 0 }} Đánh giá | Thêm
                                đánh giá</a>
                        </div>
                        <div>
                            <h3 class="product-price" id="product-price">
                                @if ($product->getIsOnSaleAttribute())
                                    <span
                                        class="text-danger fw-bold">{{ number_format($product->getDisplayPriceAttribute()) }}
                                        vnđ</span>
                                    <del class="text-muted">{{ number_format($product->original_price) }}
                                        vnđ</del>
                                    <span class="discount-label"
                                        style="color: red; font-weight: bold; margin-left: 10px;">Giảm
                                        {{ $product->discount_percentage }}%</span>
                                @else
                                    <span>{{ number_format($product->original_price) }}
                                        vnđ</span>
                                @endif
                            </h3>
                            <span class="product-available" id="product-availability">
                                {{ $selectedVariant ? ($selectedVariant->qty > 0 ? 'Còn hàng' : 'Hết hàng') : ($product->qty > 0 ? 'Còn hàng' : 'Hết hàng') }}
                            </span>
                        </div>

                        <form action="{{ route('cart.add', $product->id) }}" method="POST">
                            @csrf
                            <div class="add-to-cart">
                                <div class="qty-label">
                                    Số lượng
                                    <div class="input-number">
                                        <input type="number" name="qty" id="product-qty" min="1"
                                            value="1"
                                            max="{{ $selectedVariant ? $selectedVariant->qty : $product->qty }}">
                                        <span class="qty-up">+</span>
                                        <span class="qty-down">-</span>
                                    </div>
                                    <small class="text-danger d-none error-msg" data-id="{{ $product->id }}"></small>
                                </div>
                                <input type="hidden" id="product_variant_id" name="product_variant_id">
                                <button type="submit" id="add-to-cart-btn" class="add-to-cart-btn">
                                    <i class="fa fa-shopping-cart"></i> Thêm giỏ hàng
                                </button>
                                <button id="add-to-cart-disabled" class="add-to-cart-btn" disabled
                                    style="display: none;">
                                    <i class="fa fa-shopping-cart"></i> Hết hàng
                                </button>
                            </div>
                        </form>

                        <ul class="product-btns">
                            @if (Auth::check())
                                <button class="add-to-wishlist" data-id="{{ $product->id }}" data-variant-id>
                                    <i class="fa fa-heart{{ $isFavorited ? '' : '-o' }} wishlist-icon"></i>
                                    <span class="tooltipp">{{ $isFavorited ? 'Đã yêu thích' : 'Yêu thích' }}</span>
                                </button>
                            @else
                                <button onclick="window.location='{{ route('login') }}'" class="add-to-wishlist">
                                    <i class="fa fa-heart-o"></i>
                                    <span class="tooltipp">Đăng nhập để yêu thích</span>
                                </button>
                            @endif

                        </ul>

                        <ul class="product-links">
                            <li>Danh mục:</li>
                            <li>
                                <a href="{{ route('category.show', $product->category->slug) }}">
                                    <span
                                        class="inline-block bg-gray-200 px-2 py-1 rounded text-sm">{{ $product->category ? $product->category->name : ($product->subCategory ? $product->subCategory->categories->pluck('name')->implode(', ') : 'Chưa có') }}</span>
                                </a>
                            </li>
                        </ul>

                        @if ($product->brand)
                            <ul class="product-links">
                                <li>Thương hiệu:</li>
                                <li>
                                    <a href="{{ route('category.show', ['slug' => $product->category->slug, 'brand_id' => $product->brand->id]) }}"
                                        class="text-blue-600 font-semibold">
                                        {{ $product->brand->name }}
                                    </a>
                                </li>
                            </ul>
                        @endif
                        @if ($product->productVariants != '[]')
                            <ul class="product-links">
                                <li>Biến thể:</li>
                                <li>
                                    <select name="variant" id="variant-select" class="input-select"
                                        onchange="changeVariant(this)">
                                        @php
                                            $currentDate = \Carbon\Carbon::now();
                                        @endphp
                                        @foreach ($product->productVariants as $variant)
                                            <option
                                                value="{{ route('product.show', [$product->slug, $variant->variant_name]) }}"
                                                data-original-price="{{ $variant->original_price }}"
                                                data-original-id="{{ $variant->id }}"
                                                data-discount-percentage="{{ $variant->discount_percentage }}"
                                                data-qty="{{ $variant->qty }}"
                                                data-sale-start = "{{ $variant->discount_start_date }}"
                                                data-sale-end = "{{ $variant->discount_end_date }}"
                                                {{ $selectedVariant && $selectedVariant->id === $variant->id ? 'selected' : '' }}>
                                                {{ $variant->variant_name }} (Còn:{{ $variant->qty }})
                                            </option>
                                        @endforeach
                                    </select>
                                </li>
                            </ul>
                        @endif

                        @if ($product->warranty_period || $product->warranty_policy)
                            <ul class="product-links">
                                <li>Bảo hành:</li>
                                <li>
                                    @if ($product->warranty_period)
                                        <span>{{ $product->warranty_period }} tháng</span>
                                    @endif
                                    @if ($product->warranty_policy)
                                        <span>{{ $product->warranty_period ? ', ' : '' }}{{ $product->warranty_policy }}</span>
                                    @endif
                                </li>
                            </ul>
                        @endif

                        <ul class="product-links">
                            <li>Chia sẻ:</li>
                            <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                            <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                            <li><a href="#"><i class="fa fa-google-plus"></i></a></li>
                            <li><a href="#"><i class="fa fa-envelope"></i></a></li>
                        </ul>
                    </div>
                </div>
                <!-- /Product details -->

                <!-- Product tab -->
                <div class="col-md-12">
                    <div id="product-tab">
                        <!-- product tab nav -->
                        <ul class="tab-nav">
                            <li class="active"><a data-toggle="tab" href="#tab1">Mô tả sản phẩm</a></li>
                            <li><a data-toggle="tab" href="#tab2">Thông số kỹ thuật</a></li>
                            <li><a data-toggle="tab" href="#tab3">Đánh giá
                                    ({{ $product->reviews->count() ?? 0 }})</a></li>
                        </ul>
                        <!-- /product tab nav -->

                        <!-- product tab content -->
                        <div class="tab-content">
                            <!-- tab1  -->
                            <div id="tab1" class="tab-pane fade in active">
                                <div class="row">
                                    <div class="col-md-12">
                                        {!! $product->description !!}
                                    </div>
                                </div>
                            </div>
                            <!-- /tab1  -->
                            <!-- tab2 -->
                            <div id="tab2" class="tab-pane fade in">
                                <div class="row">
                                    <div class="col-md-12">
                                        @if ($product->specifications)
                                            @php
                                                $specs = $product->specifications;
                                            @endphp
                                            @if ($specs && is_array($specs) && !empty($specs))
                                                @foreach ($specs as $group => $items)
                                                    <div class="card mb-3">
                                                        <div class="card-header bg-light font-weight-bold"
                                                            style="font-weight: 700; font-size: 20px; margin-bottom: 1%;">
                                                            {{ htmlspecialchars($group, ENT_QUOTES, 'UTF-8') }}
                                                        </div>
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered mb-0">
                                                                <tbody>
                                                                    @foreach ($items as $label => $value)
                                                                        <tr>
                                                                            <th style="width: 30%;">
                                                                                {{ htmlspecialchars($label, ENT_QUOTES, 'UTF-8') }}
                                                                            </th>
                                                                            <td>
                                                                                {!! nl2br(htmlspecialchars($value, ENT_QUOTES, 'UTF-8')) !!}
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="alert alert-info">
                                                    Không có thông số kỹ thuật nào được định nghĩa cho sản phẩm này.
                                                </div>
                                            @endif
                                        @else
                                            <div class="alert alert-info">
                                                Không có thông số kỹ thuật nào được định nghĩa cho sản phẩm này.
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <!-- /tab2 -->

                            <!-- tab3  -->
                            <div id="tab3" class="tab-pane fade in">
                                <div class="row">
                                    <!-- Rating -->
                                    <div class="col-md-3">
                                        <div id="rating">
                                            <div class="rating-avg">
                                                <span>{{ number_format($averageRating, 1) }}</span>
                                                <div class="rating-stars">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <i
                                                            class="fa fa-star{{ $i <= $averageRating ? '' : '-o' }}"></i>
                                                    @endfor
                                                </div>
                                            </div>
                                            <ul class="rating">
                                                @for ($i = 5; $i >= 1; $i--)
                                                    <li>
                                                        <div class="rating-stars">
                                                            @for ($j = 1; $j <= 5; $j++)
                                                                <i class="fa fa-star{{ $j <= $i ? '' : '-o' }}"></i>
                                                            @endfor
                                                        </div>
                                                        <div class="rating-progress">
                                                            <div style="width: {{ $ratings[$i] ?? 0 }}%;"></div>
                                                        </div>
                                                        <span
                                                            class="sum">{{ $product->reviews->where('rating', $i)->count() }}</span>
                                                    </li>
                                                @endfor
                                            </ul>
                                        </div>
                                    </div>
                                    <!-- /Rating -->

                                    <!-- Reviews -->
                                    <div class="col-md-6">
                                        <div id="reviews">
                                            <ul class="reviews">
                                                @if (count($reviews) > 0)
                                                    @foreach ($reviews as $review)
                                                        <li>
                                                            <div class="review-heading">
                                                                <h5 class="name">{{ $review->user_name }}</h5>
                                                                <p class="date">
                                                                    {{ $review->created_at->format('d M Y, h:i A') }}
                                                                </p>
                                                                <div class="review-rating">
                                                                    @for ($i = 1; $i <= 5; $i++)
                                                                        <i
                                                                            class="fa fa-star{{ $i <= $review->rating ? '' : '-o empty' }}"></i>
                                                                    @endfor
                                                                </div>
                                                            </div>
                                                            <div class="review-body">
                                                                <p>{{ $review->comment }}</p>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                @else
                                                    <li>
                                                        <div class="review-body">
                                                            <p>Hiện chưa có đánh giá của sản phẩm</p>
                                                        </div>
                                                    </li>
                                                @endif
                                            </ul>
                                            <ul class="reviews-pagination">
                                                {{ $reviews->links('pagination::tailwind') }}
                                                {{-- <li class="active">1</li>
                                                <li><a href="#">2</a></li>
                                                <li><a href="#">3</a></li>
                                                <li><a href="#">4</a></li>
                                                <li><a href="#"><i class="fa fa-angle-right"></i></a></li> --}}
                                            </ul>
                                        </div>
                                    </div>
                                    <!-- /Reviews -->

                                    <!-- Review Form -->
                                    <div class="col-md-3">
                                        <div id="review-form">
                                            <form class="review-form"
                                                action="{{ route('product.review.store', $product->id) }}"
                                                method="POST">
                                                @csrf
                                                <input class="input" type="text" name="user_name"
                                                    placeholder="Nhập họ và tên" required>
                                                <input class="input" type="email" name="email"
                                                    placeholder="Nhập địa chỉ email" required>
                                                <textarea class="input" name="comment" placeholder="Nhập đánh giá..." rows="4" required
                                                    style="resize: none"></textarea>
                                                <div class="input-rating">
                                                    <span>Đánh giá của bạn: </span>
                                                    <div class="stars">
                                                        @for ($i = 5; $i >= 1; $i--)
                                                            <input id="star{{ $i }}" name="rating"
                                                                value="{{ $i }}" type="radio" required>
                                                            <label for="star{{ $i }}"></label>
                                                        @endfor
                                                    </div>
                                                </div>
                                                <button class="primary-btn">Xác nhận</button>
                                            </form>
                                        </div>
                                    </div>
                                    <!-- /Review Form -->
                                </div>
                            </div>
                            <!-- /tab3  -->
                        </div>
                        <!-- /product tab content  -->
                    </div>
                </div>
                <!-- /product tab -->
            </div>
            <!-- /row -->
        </div>
        <!-- /container -->
    </div>
    <!-- /SECTION -->

    <!-- Section -->
    <div class="section">
        <!-- container -->
        <div class="container">
            <!-- row -->
            <div class="row">
                <div class="col-md-12">
                    <div class="section-title text-center">
                        <h3 class="title">Sản phẩm mới</h3>
                    </div>
                </div>
                <div class="clearfix visible-sm visible-xs"></div>
                <!-- product -->
                @foreach ($productLastest as $item)
                    @php
                        // Chọn item để hiển thị giá: nếu có variant thì dùng variant đầu tiên
                        $variant = $item->productVariants->first(fn($v) => $v->qty > 0);
                        $displayItem = $variant ?? $item;
                        $variant =
                            isset($item->productVariants) && $item->productVariants != '[]'
                                ? $item->productVariants->where('product_id', $item->id)->first(fn($v) => $v->qty > 0)->id
                                : null;
                        $isFavorited = $item->favoritedByUsers->contains(auth()->id()); // luôn check từ $product

                    @endphp
                    <div class="col-md-3 col-xs-6">
                        <div class="product">
                            <div class="product-img">
                                @php
                                    $image = $item->productImages->where('type', 1)->first()->image ?? '';
                                    $imagePath = $image ? asset('storage/' . $image) : asset('asset/img/no-image.png');
                                @endphp

                                <img src="{{ $imagePath }}" alt="">
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
                                <div class="product-rating">
                                    <?php
                                    $averageRating = $item->reviews->avg('rating') ?? 0;
                                    ?>
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="fa fa-star{{ $i <= $averageRating ? '' : '-o' }}"
                                            style="color: red"></i>
                                    @endfor
                                </div>
                                <div class="product-btns">
                                    @if (Auth::check())
                                        <button class="add-to-wishlist" data-id="{{ $item->id }}"
                                            data-variant-id="{{ $variant }}">
                                            <i class="fa fa-heart{{ $isFavorited ? '' : '-o' }} wishlist-icon"></i>
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
                                    <input type="hidden" name="product_variant_id" value="{{ $variant }}">
                                    @if ($displayItem->qty > 0)
                                        <button type="submit" class="add-to-cart-btn">
                                            <i class="fa fa-shopping-cart"></i> Thêm giỏ hàng
                                        </button>
                                    @else
                                        <button class="add-to-cart-btn" disabled>
                                            <i class="fa fa-shopping-cart"></i> Hết hàng
                                        </button>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
                <!-- /product -->
            </div>
            <!-- /row -->
        </div>
        <!-- /container -->
    </div>
    <!-- /Section -->
</x-guest-layout>
<style>
    #product-main-img {
        position: relative;
    }

    #product-main-img .product-preview {
        width: 100%;
        height: 620px;
        /* hoặc bất kỳ chiều cao nào phù hợp giao diện của bạn */
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f9f9f9;
        border: 1px solid #eee;
        border-radius: 6px;
        overflow: hidden;
        margin-bottom: 15px;
    }

    #product-main-img .product-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        /* hoặc contain nếu muốn ảnh không bị cắt */
    }

    #product-main-img img {
        max-width: 100%;
        height: auto;
        display: block;
        margin: 0 auto;
        object-fit: contain;
    }
</style>
<script>
    $('body').tooltip({
        selector: '[data-toggle="tooltip"]'
    });

    $(document).ready(function() {
        $('.review-link').click(function(e) {
            e.preventDefault();
            const tabId = $(this).attr('href');
            const $tabLink = $('a[href="' + tabId + '"]');
            $tabLink.tab('show');

            setTimeout(function() {
                $('html, body').animate({
                    scrollTop: $(tabId).offset().top - 100
                }, 600);
            }, 200);
        });
    });

    function changeVariant(select) {
        const selectedOption = select.options[select.selectedIndex];
        const url = selectedOption.value;
        const variantId = selectedOption.getAttribute('data-original-id');
        const originalPrice = parseFloat(selectedOption.getAttribute('data-original-price'));
        const discountPercentage = parseFloat(selectedOption.getAttribute('data-discount-percentage'));
        const qty = parseInt(selectedOption.getAttribute('data-qty'));
        const saleStartStr = selectedOption.getAttribute('data-sale-start');
        const saleEndStr = selectedOption.getAttribute('data-sale-end');
        const addToCartBtn = document.getElementById('add-to-cart-btn');
        const addToCartDisabled = document.getElementById('add-to-cart-disabled');

        if (qty > 0) {
            addToCartBtn.style.display = 'inline-block';
            addToCartDisabled.style.display = 'none';
        } else {
            addToCartBtn.style.display = 'none';
            addToCartDisabled.style.display = 'inline-block';
        }

        const wishlistButton = document.querySelector('.add-to-wishlist');
        if (wishlistButton) {
            wishlistButton.setAttribute('data-variant-id', variantId);
        }

        let isOnSale = false;
        const now = new Date();
        if (discountPercentage > 0 && saleStartStr && saleEndStr) {
            const saleStart = new Date(saleStartStr);
            const saleEnd = new Date(saleEndStr);
            isOnSale = now >= saleStart && now <= saleEnd;
        }

        const finalPrice = isOnSale ?
            Math.round(originalPrice * (1 - discountPercentage / 100)) :
            originalPrice;

        const priceElement = document.getElementById('product-price');
        priceElement.innerHTML = `<span class="text-danger fw-bold">${numberFormat(finalPrice)} vnđ</span>`;

        if (isOnSale && originalPrice !== finalPrice) {
            priceElement.innerHTML += ` <del class="text-muted">${numberFormat(originalPrice)} vnđ</del>`;
            priceElement.innerHTML +=
                ` <span class="discount-label" style="color: red; font-weight: bold; margin-left: 10px;">Giảm ${discountPercentage}%</span>`;
        }

        const qtyInput = document.getElementById('product-qty');
        const availabilityElement = document.getElementById('product-availability');
        qtyInput.setAttribute('max', qty);
        qtyInput.value = Math.min(qtyInput.value, qty);
        document.getElementById('product_variant_id').value = variantId;
        availabilityElement.textContent = qty > 0 ? 'Còn hàng' : 'Hết hàng';

        // Cập nhật URL để giữ variant_name
        window.history.replaceState({}, document.title, url);
    }

    function numberFormat(number) {
        return new Intl.NumberFormat('vi-VN').format(number);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const select = document.getElementById('variant-select');

        // 🔍 Tìm variant_name từ URL segment cuối
        const pathSegments = decodeURIComponent(window.location.pathname).split('/');
        const variantNameFromUrl = pathSegments[pathSegments.length - 1];

        if (select && variantNameFromUrl) {
            const matchingOption = Array.from(select.options).find(opt => {
                return opt.textContent.trim() === variantNameFromUrl;
            });

            if (matchingOption) {
                select.value = matchingOption.value;
                changeVariant(select);
            } else {
                // Nếu không khớp thì fallback: chọn option đầu
                changeVariant(select);
            }
        }

        // Tăng/giảm số lượng
        const qtyUp = document.querySelector('.qty-up');
        const qtyDown = document.querySelector('.qty-down');
        const qtyInput = document.getElementById('product-qty');

        qtyUp?.addEventListener('click', function() {
            const maxQty = parseInt(qtyInput.getAttribute('max'));
            let value = parseInt(qtyInput.value);
            if (value < maxQty) qtyInput.value = value + 1;
        });

        qtyDown?.addEventListener('click', function() {
            let value = parseInt(qtyInput.value);
            if (value > 1) qtyInput.value = value - 1;
        });
    });

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
                    const favoriteCountEl = document.getElementById('favorite-count');
                    if (favoriteCountEl) {
                        let count = parseInt(favoriteCountEl.textContent) || 0;
                        if (data.status === 'added') {
                            favoriteCountEl.textContent = count + 1;
                        } else if (data.status === 'removed' && count > 0) {
                            favoriteCountEl.textContent = count - 1;
                        }
                    }
                })
                .catch(err => {
                    showAlertModal('Vui lòng đăng nhập để sử dụng tính năng này', 'warning');
                });
        });
    });
</script>
