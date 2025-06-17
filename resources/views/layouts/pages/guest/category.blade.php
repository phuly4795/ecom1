<x-guest-layout>
    @section('title', 'Danh mục')
    <!-- SECTION -->
    <div class="section">
        <!-- container -->
        <div class="container">
            <!-- row -->
            <div class="row">
                <!-- ASIDE -->
                <div id="aside" class="col-md-3">
                    <!-- aside Widget -->
                    @if ($products->count() > 0)
                        <div class="aside">
                            <h3 class="aside-title">Giá</h3>
                            <div class="price-filter">
                                <div id="price-slider"></div>
                                <div class="input-number price-min">
                                    <input id="price-min" type="number" value="{{ $priceMin }}">
                                    <span class="qty-up">+</span>
                                    <span class="qty-down">-</span>
                                </div>
                                <span>-</span>
                                <div class="input-number price-max">
                                    <input id="price-max" type="number" value="{{ $priceMax }}">
                                    <span class="qty-up">+</span>
                                    <span class="qty-down">-</span>
                                </div>
                            </div>
                        </div>
                    @endif
                    <!-- /aside Widget -->

                    <!-- aside Widget -->
                    @if (isset($brands) && $brands != '[]')
                        <div class="aside">
                            <h3 class="aside-title">Thương hiệu</h3>
                            <div class="checkbox-filter">
                                @foreach ($brands as $key => $brand)
                                    @php
                                        $count = $productCountsByBrand[$brand->id] ?? 0;
                                    @endphp
                                    <div class="input-checkbox">
                                        <input type="checkbox" id="brand-{{ $key + 1 }}" name="brand_id"
                                            value="{{ $brand->id }}">
                                        <label for="brand-{{ $key + 1 }}" class="uppercase">
                                            <span></span>
                                            {{ $brand->name }}
                                            <small>({{ $count }})</small>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    <!-- /aside Widget -->

                    <!-- aside Widget -->
                    <div class="aside">
                        <h3 class="aside-title">Sản phẩm bán chạy</h3>
                        @foreach ($bestSellingProducts as $productSelling)
                            @php
                                $variantBestSelling = $productSelling->productVariants->first();
                                $displayItemBestSelling = $variantBestSelling ?? $productSelling;
                                $image = $productSelling->productImages->where('type', 1)->first()->image ?? '';
                                $imagePath = $image ? asset('storage/' . $image) : asset('asset/img/no-image.png');
                            @endphp
                            <div class="product-widget">
                                <div class="product-img">
                                    <img src="{{ $imagePath }}" alt="{{ $productSelling->title }}">
                                </div>
                                <div class="product-body">
                                    <p class="product-category">
                                        {{ $productSelling->category ? $productSelling->category->name : ($productSelling->subCategory ? $productSelling->subCategory->categories->pluck('name')->implode(', ') : 'Chưa có') }}
                                    </p>
                                    <h3 class="product-name">
                                        <a
                                            href="{{ route('product.show', $productSelling->slug) }}">{{ Str::limit($productSelling->title, 15, '...') }}</a>
                                    </h3>
                                    <h4 class="product-price">
                                        @if ($displayItemBestSelling->is_on_sale)
                                            <span class="text-danger fw-bold">
                                                {{ number_format($displayItemBestSelling->display_price) }}
                                                vnđ
                                            </span>
                                            <del class="text-muted">
                                                {{ number_format($displayItemBestSelling->original_price) }} vnđ
                                            </del>
                                        @else
                                            <span class="text-danger fw-bold">
                                                {{ number_format($displayItemBestSelling->original_price) }} vnđ
                                            </span>
                                        @endif
                                    </h4>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- /aside Widget -->
                </div>
                <!-- /ASIDE -->

                <!-- STORE -->
                <div id="store" class="col-md-9">
                    <!-- store products -->
                    <div class="row">
                        <!-- product -->
                        @if ($products->count() > 0)
                            @foreach ($products as $product)
                                <?php
                                $variant = $product->productVariants->first();
                                $displayItem = $variant ?? $product;
                                $variantId = $variant?->id; // Dùng null-safe nếu cần lấy ID
                                $isFavorited = $product->favoritedByUsers->contains(auth()->id()); // luôn check từ $product
                                ?>
                                <div class="col-md-4 col-xs-6">
                                    <div class="product">
                                        <div class="product-img">
                                            @php
                                                $image =
                                                    $product->productImages->where('type', 1)->first()->image ?? '';
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
                                                {{ $product->category->name ?? 'Không rõ' }}
                                            </p>
                                            <h3 class="product-name"><a
                                                    href="{{ route('product.show', ['slug' => $product->slug]) }}">{{ Str::limit($product->title, 20, '...') }}</a>
                                            </h3>
                                            <h4 class="product-price">
                                                @if ($displayItem->is_on_sale)
                                                    <span class="text-danger fw-bold">
                                                        {{ number_format($displayItem->display_price) }}
                                                        vnđ
                                                    </span>
                                                    <del class="text-muted">
                                                        {{ number_format($displayItem->original_price) }} vnđ
                                                    </del>
                                                @else
                                                    <span class="text-danger fw-bold">
                                                        {{ number_format($displayItem->original_price) }} vnđ
                                                    </span>
                                                @endif
                                            </h4>
                                            <?php
                                            $averageRating = $product->reviews->avg('rating') ?? 0;
                                            ?>
                                            <div class="product-rating">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <i class="fa fa-star{{ $i <= $averageRating ? '' : '-o' }}"
                                                        style="color: red"></i>
                                                @endfor
                                            </div>
                                            <div class="product-btns">
                                                @auth
                                                    <button class="add-to-wishlist" data-id="{{ $product->id }}"
                                                        data-variant-id="{{ $variantId }}">
                                                        <i
                                                            class="fa fa-heart{{ $isFavorited ? '' : '-o' }} wishlist-icon"></i>
                                                        <span
                                                            class="tooltipp">{{ $isFavorited ? 'Đã yêu thích' : 'Yêu thích' }}</span>
                                                    </button>
                                                @endauth
                                                @guest
                                                    <button onclick="window.location='{{ route('login') }}'"
                                                        class="add-to-wishlist">
                                                        <i class="fa fa-heart-o"></i>
                                                        <span class="tooltipp">Đăng nhập để yêu thích</span>
                                                    </button>
                                                @endguest
                                                <button class="quick-view"
                                                    onclick="window.location='{{ route('product.show', ['slug' => $product->slug]) }}'"><i
                                                        class="fa fa-eye"></i><span class="tooltipp">Xem sản
                                                        phẩm</span></button>
                                            </div>
                                        </div>
                                        <div class="add-to-cart">
                                            <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="qty" value="1">
                                                <input type="hidden" name="product_variant_id"
                                                    value="{{ $product->productVariants->first()->id ?? '' }}">
                                                <button type="submit" class="add-to-cart-btn">
                                                    <i class="fa fa-shopping-cart"></i> Thêm giỏ hàng
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="not-found">
                                <p>Không có sản phẩm nào trong danh mục này.</p>
                            </div>
                        @endif

                        <!-- /product -->
                    </div>
                    <!-- /store products -->

                    <!-- store bottom filter -->
                    <div class="store-filter clearfix mt-4">
                        {{ $products->links() }}
                    </div>
                    <!-- /store bottom filter -->
                </div>
                <!-- /STORE -->
            </div>
            <!-- /row -->
        </div>
        <!-- /container -->
    </div>
    <!-- /SECTION -->

</x-guest-layout>
<style>
    .product-img-wrapper {
        width: 100%;
        height: 250px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .product-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .not-found {
        font-weight: 700;
        font-size: 22px;
        margin-top: 10%;
        display: flex;
        flex-direction: column;
        flex-wrap: nowrap;
        /* align-content: center; */
        justify-content: center;
        align-items: center;
    }
</style>
<script>
    $(document).ready(function() {
        function filterProducts() {
            const minPrice = $('#price-min').val();
            const maxPrice = $('#price-max').val();
            const brandIds = [];
            $('input[id^="brand-"]:checked').each(function() {
                brandIds.push($(this).val());
            });

            $.ajax({
                url: window.location.href,
                method: 'GET',
                data: {
                    min_price: minPrice,
                    max_price: maxPrice,
                    brand_id: brandIds
                },
                success: function(response) {
                    $('#store .row').html(response.html);
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });
        }

        // Gắn sự kiện onchange / blur / click tùy theo nhu cầu
        $('#price-min, #price-max').on('change', filterProducts);
        $('input[id^="brand-"]').on('change', filterProducts);
    });
</script>
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
