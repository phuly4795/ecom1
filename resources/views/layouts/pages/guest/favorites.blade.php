<x-guest-layout>
    @section('title', 'Sản phẩm yêu thích')

    <div class="container mt-5">
        <h1 class="text-center mb-4">Sản phẩm yêu thích của bạn</h1>

        @if ($favorites->count())
            <div class="row">
                @foreach ($favorites as $favorite)
                    @php
                        $product = $favorite->products;
                        $variant = $favorite->productVariants;
                    @endphp

                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="card h-100">
                            @php
                                $image = $product->productImages->where('type', 1)->first()->image ?? '';
                                $imagePath = $image ? asset('storage/' . $image) : asset('asset/img/no-image.png');
                            @endphp
                            <div class="product-img">
                                <a href="{{ route('product.show', $product->slug) }}">
                                    <div class="product-img-wrapper">
                                        <img src="{{ $imagePath }}" alt="" class="product-img">
                                    </div>
                                </a>
                            </div>
                            <div class="card-body d-flex flex-column justify-content-between">
                                <h5 class="card-title text-truncate">{{ $product->title }}</h5>

                                <div>
                                    @if ($product->price_sale)
                                        <p class="text-danger fw-bold mb-1">{{ number_format($product->price_sale) }} đ
                                        </p>
                                        <small
                                            class="text-muted text-decoration-line-through">{{ number_format($product->price) }}
                                            đ</small>
                                    @else
                                        <p class="fw-bold">{{ number_format($product->price) }} đ</p>
                                    @endif
                                </div>

                                <button class="btn btn-outline-danger btn-sm mt-2 remove-favorite"
                                    data-id="{{ $product->id }}" data-variant-id="{{ $variant?->id }}">
                                    <i class="fa fa-heart"></i> Bỏ yêu thích
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $favorites->links() }}
            </div>
        @else
            <div class="text-center mt-5">
                <h4>Bạn chưa có sản phẩm yêu thích nào.</h4>
                <a href="{{ route('home') }}" class="btn btn-primary mt-3">Tiếp tục mua sắm</a>
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
