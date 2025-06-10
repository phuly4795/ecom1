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
                                </div>
                                <input type="hidden" id="product_variant_id" name="product_variant_id">
                                <button type="submit" class="add-to-cart-btn">
                                    <i class="fa fa-shopping-cart"></i> Thêm giỏ hàng
                                </button>
                            </div>
                        </form>

                        <ul class="product-btns">
                            <li><a href="#"><i class="fa fa-heart-o"></i> Thêm yêu thích</a></li>
                        </ul>

                        <ul class="product-links">
                            <li>Danh mục:</li>
                            <li>
                                @if ($product->category)
                                    <a href="{{ route('category.show', $product->category->slug) }}"
                                        class="text-blue-600 font-semibold">
                                        {{ $product->category->name }}
                                    </a>
                                @elseif ($product->subCategory && $product->subCategory->categories->count())
                                    @foreach ($product->subCategory->categories as $cat)
                                        <a href="{{ route('category.show', $cat->slug) }}">
                                            <span
                                                class="inline-block bg-gray-200 px-2 py-1 rounded text-sm">{{ $cat->name }}</span>
                                        </a>{{ !$loop->last ? ',' : '' }}
                                    @endforeach
                                @else
                                    <span class="text-gray-500">Chưa có</span>
                                @endif
                            </li>
                        </ul>

                        @if ($product->brand)
                            <ul class="product-links">
                                <li>Thương hiệu:</li>
                                <li>
                                    <a href="{{ route('home', $product->brand->slug) }}"
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
                                                        <div class="card-header bg-light font-weight-bold">
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
                    <div class="col-md-3 col-xs-6">
                        <div class="product">
                            <div class="product-img">
                                <img src="{{ asset('asset/guest/img/product01.png') }}" alt="">
                                <div class="product-label">
                                    <span class="sale">-30%</span>
                                </div>
                            </div>
                            <div class="product-body">
                                <p class="product-category">Category</p>
                                <h3 class="product-name"><a href="#">product name goes here</a></h3>
                                <h4 class="product-price">$980.00 <del class="product-old-price">$990.00</del></h4>
                                <div class="product-rating">
                                </div>
                                <div class="product-btns">
                                    <button class="add-to-wishlist"><i class="fa fa-heart-o"></i><span
                                            class="tooltipp">add to wishlist</span></button>
                                    <button class="add-to-compare"><i class="fa fa-exchange"></i><span
                                            class="tooltipp">add to compare</span></button>
                                    <button class="quick-view"><i class="fa fa-eye"></i><span class="tooltipp">quick
                                            view</span></button>
                                </div>
                            </div>
                            <div class="add-to-cart">
                                <button class="add-to-cart-btn"><i class="fa fa-shopping-cart"></i> Thêm giỏ
                                    hàng</button>
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
    $(document).ready(function() {
        $('.review-link').click(function(e) {
            e.preventDefault();

            var tabId = $(this).attr('href');
            var $tabLink = $('a[href="' + tabId + '"]');

            // Kích hoạt tab (nếu đang ẩn)
            $tabLink.tab('show');

            // Cuộn mượt đến tab
            setTimeout(function() {
                $('html, body').animate({
                    scrollTop: $(tabId).offset().top - 100 // trừ header nếu có
                }, 600);
            }, 200); // Delay một chút để tab hiển thị xong
        });
    });
</script>
<script>
    function changeVariant(select) {
        const selectedOption = select.options[select.selectedIndex];
        const url = selectedOption.value;
        const variantId = selectedOption.getAttribute('data-original-id');
        const originalPrice = parseFloat(selectedOption.getAttribute('data-original-price'));
        const discountPercentage = parseFloat(selectedOption.getAttribute('data-discount-percentage'));
        const qty = parseInt(selectedOption.getAttribute('data-qty'));

        // Lấy ngày bắt đầu và kết thúc khuyến mãi (nếu có)
        const saleStartStr = selectedOption.getAttribute('data-sale-start');
        const saleEndStr = selectedOption.getAttribute('data-sale-end');
        const now = new Date();

        let isOnSale = false;
        if (discountPercentage > 0 && saleStartStr && saleEndStr) {
            const saleStart = new Date(saleStartStr);
            const saleEnd = new Date(saleEndStr);
            // Kiểm tra nếu now nằm trong khoảng saleStart đến saleEnd (bao gồm cả 2 đầu)
            isOnSale = now >= saleStart && now <= saleEnd;
        }

        // Tính finalPrice dựa trên isOnSale
        const finalPrice = isOnSale ?
            Math.round(originalPrice * (1 - discountPercentage / 100)) :
            originalPrice;

        // Cập nhật giá
        const priceElement = document.getElementById('product-price');
        priceElement.innerHTML = `<span class="text-danger fw-bold">${numberFormat(finalPrice)} vnđ</span>`;

        if (isOnSale && originalPrice != finalPrice) {
            priceElement.innerHTML += ` <del class="text-muted">${numberFormat(originalPrice)} vnđ</del>`;
            priceElement.innerHTML +=
                ` <span class="discount-label" style="color: red; font-weight: bold; margin-left: 10px;">Giảm ${discountPercentage}%</span>`;
        }

        // Cập nhật URL
        window.history.pushState({}, document.title, url);

        // Cập nhật số lượng
        const qtyInput = document.getElementById('product-qty');
        const availabilityElement = document.getElementById('product-availability');
        qtyInput.setAttribute('max', qty);
        qtyInput.value = Math.min(qtyInput.value, qty);
        const productVariantId = document.getElementById('product_variant_id');
        productVariantId.value = variantId;
        availabilityElement.textContent = qty > 0 ? 'Còn hàng' : 'Hết hàng';
    }

    function numberFormat(number) {
        return new Intl.NumberFormat('vi-VN').format(number);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const select = document.getElementById('variant-select');
        if (select && select.value) {
            changeVariant(select);
        }

        // Xử lý nút tăng/giảm số lượng
        const qtyUp = document.querySelector('.qty-up');
        const qtyDown = document.querySelector('.qty-down');
        const qtyInput = document.getElementById('product-qty');
        const maxQty = qtyInput.getAttribute('max');

        qtyUp.addEventListener('click', function() {
            let value = parseInt(qtyInput.value);
            if (value < maxQty) qtyInput.value = value + 1;
        });

        qtyDown.addEventListener('click', function() {
            let value = parseInt(qtyInput.value);
            if (value > 1) qtyInput.value = value - 1;
        });
    });
</script>
