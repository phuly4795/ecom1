@if (count($products) > 0)
    @foreach ($products as $product)
        <div class="col-md-4 col-xs-6">
            <div class="product">
                <div class="product-img-wrapper">
                    <div class="product-img">
                        @php
                            $image = $product->productImages->where('type', 1)->first()->image ?? '';
                            $imagePath = $image ? asset('storage/' . $image) : asset('asset/img/no-image.png');
                        @endphp
                        <img src="{{ $imagePath }}" alt="{{ $product->title }}">
                    </div>
                </div>
                <div class="product-body">
                    <p class="product-category">
                        {{ $product->subCategory->name ?? ($product->category->name ?? 'Không rõ') }}
                    </p>
                    <h3 class="product-name"><a
                            href="{{ route('product.show', ['slug' => $product->slug]) }}">{{ $product->title }}</a></h3>
                    <h4 class="product-price">{{ number_format($product->price) }}đ
                        @if ($product->old_price)
                            <del class="product-old-price">{{ number_format($product->old_price) }}đ</del>
                        @endif
                    </h4>
                    <div class="product-rating">
                        @for ($i = 1; $i <= 5; $i++)
                            <i class="fa fa-star{{ $i <= $product->rating ? '' : '-o' }}"></i>
                        @endfor
                    </div>
                    <div class="product-btns">
                        <button class="add-to-wishlist"><i class="fa fa-heart-o"></i></button>
                        <button class="quick-view"
                            onclick="window.location='{{ route('product.show', ['slug' => $product->slug]) }}'"><i
                                class="fa fa-eye"></i><span class="tooltipp">Xem sản phẩm 1</span></button>
                    </div>
                </div>
                <div class="add-to-cart">
                    <form action="{{ route('cart.add', $product->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="qty" value="1">
                        <input type="text" name="product_variant_id"
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
    <p>Không có sản phẩm nào trong danh mục này.</p>
@endif
