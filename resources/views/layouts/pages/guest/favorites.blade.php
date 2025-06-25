<x-guest-layout>
    @section('title', 'Sản phẩm yêu thích')

    <div class="container mt-5">
        <h1 class="text-center mb-4" style="padding: 4%">Sản phẩm yêu thích của bạn</h1>

        @if ($favorites->count() > 0)
            @foreach ($favorites as $favorite)
                @php
                    // Chọn item để hiển thị giá: nếu có variant thì dùng variant đầu tiên
                    $variant = $favorite->products->productVariants->first();
                    $displayItem = $variant ?? $favorite->products;
                    $variant =
                        isset($favorite->products->productVariants) && $favorite->products->productVariants != '[]'
                            ? $favorite->products->productVariants
                                ->where('product_id', $favorite->products->id)
                                ->first()->id
                            : null;
                    $isFavorited = $favorite->products->favoritedByUsers->contains(auth()->id()); // luôn check từ $favorite->product
                @endphp
                <div class="col-md-4 col-xs-6">
                    <div class="product">
                        <div class="product-img">
                            @php
                                $image = $favorite->products->productImages->where('type', 1)->first()->image ?? '';
                                $imagePath = $image ? asset('storage/' . $image) : asset('asset/img/no-image.png');
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
                                {{ $favorite->products->category->name ?? 'Không rõ' }}
                            </p>
                            <h3 class="product-name"><a
                                    href="{{ route('product.show', ['slug' => $favorite->products->slug]) }}">{{ Str::limit($favorite->products->title, 20, '...') }}</a>
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
                            $averageRating = $favorite->products->reviews->avg('rating') ?? 0;
                            ?>
                            <div class="product-rating">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="fa fa-star{{ $i <= $averageRating ? '' : '-o' }}" style="color: red"></i>
                                @endfor
                            </div>
                            <div class="product-btns">
                                @auth
                                    <button class="add-to-wishlist" data-id="{{ $favorite->products->id }}"
                                        data-variant-id="{{ $variant }}">
                                        <i class="fa fa-heart{{ $isFavorited ? '' : '-o' }} wishlist-icon"></i>
                                        <span class="tooltipp">{{ $isFavorited ? 'Đã yêu thích' : 'Yêu thích' }}</span>
                                    </button>
                                @endauth
                                @guest
                                    <button onclick="window.location='{{ route('login') }}'" class="add-to-wishlist">
                                        <i class="fa fa-heart-o"></i>
                                        <span class="tooltipp">Đăng nhập để yêu thích</span>
                                    </button>
                                @endguest
                                <button class="quick-view"
                                    onclick="window.location='{{ route('product.show', ['slug' => $favorite->products->slug]) }}'"><i
                                        class="fa fa-eye"></i><span class="tooltipp">Xem sản
                                        phẩm</span></button>
                            </div>
                        </div>
                        <div class="add-to-cart">
                            <form action="{{ route('cart.add', $favorite->products->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="qty" value="1">
                                <input type="hidden" name="product_variant_id"
                                    value="{{ $favorite->products->productVariants->first()->id ?? '' }}">
                                <button type="submit" class="add-to-cart-btn">
                                    <i class="fa fa-shopping-cart"></i> Thêm giỏ hàng
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="not-found text-center">
                <p>Không có sản phẩm nào trong danh mục này.</p>
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.remove-favorite').forEach(button => {
                    button.addEventListener('click', function(e) {
                        e.preventDefault();

                        const productId = this.getAttribute('data-id');
                        const variantId = this.getAttribute('data-variant-id');

                        fetch(`/favorites/${productId}`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({
                                    variant_id: variantId
                                })
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.status === 'removed') {
                                    this.closest('.col-md-3').remove();
                                }
                            })
                            .catch(err => console.error('Lỗi:', err));
                    });
                });
            });
        </script>
    @endpush
</x-guest-layout>

<style>
    .product-img-wrapper {
        width: 100%;
        height: 220px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f8f8;
        border-radius: 8px;
    }

    .product-img-wrapper img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        transition: transform 0.3s ease-in-out;
    }

    .product-img-wrapper img:hover {
        transform: scale(1.05);
    }
</style>
<style>
    .product-card {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .product-image-wrapper {
        width: 100%;
        height: 220px;
        background: #f4f4f4;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        border-radius: 8px;
        padding: 10px;
    }

    .product-image-wrapper img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        transition: transform 0.3s;
    }

    .product-image-wrapper img:hover {
        transform: scale(1.05);
    }

    .product-title {
        font-size: 15px;
        font-weight: 500;
        min-height: 40px;
    }

    .product-price {
        font-size: 14px;
    }
</style>
<script>
    document.querySelectorAll('.add-to-wishlist').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            const productId = this.dataset.id;
            const variantId = this.dataset.variantId;
            console.log(productId, variantId);

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
                        setTimeout(() => {
                            location.reload(); // ✅ Reload sau khi bỏ yêu thích
                        }, 800); // Delay nhẹ để người dùng thấy thông báo
                    }
                })
                .catch(err => {
                    showAlertModal('Vui lòng đăng nhập để sử dụng tính năng này', 'warning');
                });
        });
    });
</script>
