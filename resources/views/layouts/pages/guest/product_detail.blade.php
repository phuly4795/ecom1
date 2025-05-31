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
                            <a class="review-link" href="#tab2">{{ $product->reviews->count() ?? 0 }} Đánh giá | Thêm
                                đánh giá</a>
                        </div>
                        <div>
                            <h3 class="product-price">
                                {{ number_format($product->price) }} vnđ
                                @if ($product->compare_price != 0 || $product->original_price != 0)
                                    <del class="product-old-price">
                                        {{ $product->compare_price != 0 ? number_format($product->compare_price) : ($product->original_price != 0 ? number_format($product->original_price) : '') }}
                                        vnđ
                                    </del>
                                @endif
                                @if ($product->discount_percentage > 0)
                                    <span class="discount-label"
                                        style="color: red; font-weight: bold; margin-left: 10px;">
                                        Giảm {{ $product->discount_percentage }}%
                                    </span>
                                @endif
                            </h3>
                            <span class="product-available">{{ $product->qty > 0 ? 'Còn hàng' : 'Hết hàng' }}</span>
                        </div>

                        <form action="{{ route('cart.add', $product->id) }}" method="POST">
                            @csrf
                            <div class="add-to-cart">
                                <div class="qty-label">
                                    Số lượng
                                    <div class="input-number">
                                        <input type="number" name="qty" min="1" value="1">
                                        <span class="qty-up">+</span>
                                        <span class="qty-down">-</span>
                                    </div>
                                </div>
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
                        @if ($product->variants)
                            <ul class="product-links">
                                <li>Biến thể:</li>
                                <li>
                                    <select name="variant" id="variant-select" class="input-select"
                                        onchange="changeVariant(this)">
                                        @php
                                            $variants = !empty($product->variants)
                                                ? explode(',', str_replace(['[', ']', '"'], '', $product->variants))
                                                : [];
                                        @endphp
                                        @foreach ($variants as $variant)
                                            @php
                                                $variant = trim($variant);
                                                $variantSlug = \Illuminate\Support\Str::slug(
                                                    $product->slug . '-' . $variant,
                                                );
                                            @endphp
                                            <option value="{{ route('product.show', $variantSlug) }}"
                                                {{ $variant === 'Đen' ? 'selected' : '' }}>
                                                {{ $variant }}
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

                            <!-- tab2  -->
                            <div id="tab2" class="tab-pane fade in active">
                                <div class="row">
                                    <div class="col-md-12">
                                        @if ($product->specifications)
                                            <ul class="product-links">
                                                <li>Thông số kỹ thuật:</li>
                                                <li>
                                                    <span>{!! nl2br(e($product->specifications)) !!}</span>
                                                </li>
                                            </ul>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <!-- /tab2  -->

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
                                                @foreach ($product->reviews as $review)
                                                    <li>
                                                        <div class="review-heading">
                                                            <h5 class="name">{{ $review->user_name }}</h5>
                                                            <p class="date">
                                                                {{ $review->created_at->format('d M Y, h:i A') }}</p>
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
                                            </ul>
                                            <ul class="reviews-pagination">
                                                {{-- {{$product->reviews->link()}} --}}
                                                <li class="active">1</li>
                                                <li><a href="#">2</a></li>
                                                <li><a href="#">3</a></li>
                                                <li><a href="#">4</a></li>
                                                <li><a href="#"><i class="fa fa-angle-right"></i></a></li>
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
        var url = select.value;
        if (url) {
            window.location.href = url; // Chuyển hướng đến URL của biến thể
        }
    }
</script>
