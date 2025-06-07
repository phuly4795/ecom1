<x-guest-layout>
    @section('title', 'Giỏ hàng')

    <div class="container">
        <!-- Main Cart Content -->
        <div class="row" style="margin-top: 5%">
            @if (session('success'))
                <div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <i class="fa fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            <h1 class="text-center" style="margin-bottom: 3%">Giỏ hàng của bạn</h1>
            @if($cartItems == [])
                <div class="text-center">
                    <i class="fa fa-shopping-cart fa-3x text-muted"></i>
                    <p class="lead text-muted">Giỏ hàng của bạn hiện đang trống</p>
                    <a href="{{ route('home') }}" class="btn btn-primary">Tiếp tục mua sắm</a>
                </div>
            @else
                <!-- Cart Items Table -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="row hidden-xs">
                            <div class="col-sm-5"><strong>Sản phẩm</strong></div>
                            <div class="col-sm-2 text-center"><strong>Giá</strong></div>
                            <div class="col-sm-2 text-center"><strong>Số lượng</strong></div>
                            <div class="col-sm-2 text-center"><strong>Tổng</strong></div>
                            <div class="col-sm-1"></div>
                        </div>
                    </div>
                    <div class="panel-body">
                        @foreach ($cartItems as $item)
                            <div class="row cart-item">
                                <div class="col-sm-5 col-xs-12">
                                    <div class="media">
                                        <div class="media-left">
                                            <img src="{{ asset('storage/' . $item->product->productImages->where('type', 1)->first()->image ?? 'asset/guest/img/product01.png') }}"
                                                alt="{{ $item->product->title }}" class="media-object product-image">
                                        </div>
                                        <div class="media-body">
                                            <h4 class="media-heading">{{ $item->product->title }}</h4>
                                            <p class="text-muted">Màu:
                                                {{ isset($item->productVariant) ? $item->productVariant->variant_name : 'N/A' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-2 col-xs-12 text-center">
                                    <p class="item-price">
                                        {{ isset($item->productVariant) ? number_format($item->productVariant->price) : number_format($item->product->price) }}
                                        vnđ</p>
                                </div>
                                <div class="col-sm-2 col-xs-12 text-center">
                                    <div class="input-group input-group-sm quantity-control">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default decrement"
                                                data-id="{{ $item->id }}">-</button>
                                        </span>
                                        <input type="number" min="1" value="{{ $item->qty }}"
                                            class="form-control text-center qty-input" data-id="{{ $item->id }}">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default increment"
                                                data-id="{{ $item->id }}">+</button>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-sm-2 col-xs-12 text-center">
                                    <p class="item-total">
                                        {{ number_format($item->product->price * $item->qty) }} vnđ</p>
                                </div>
                                <div class="col-sm-1 col-xs-12 text-center">
                                    <form method="POST" action="{{ route('cart.remove', $item->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm remove-btn"
                                            title="Xóa sản phẩm">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Cart Actions -->
                <div class="row">
                    <div class="col-sm-6 col-xs-12">
                        <form method="POST" action="{{ route('cart.applyCoupon') }}" class="form-inline coupon-form">
                            @csrf
                            <div class="form-group">
                                <input type="text" name="coupon_code" class="form-control"
                                    placeholder="Nhập mã giảm giá">
                            </div>
                            <button type="submit" class="btn btn-default">Áp dụng</button>
                        </form>
                    </div>
                    <div class="col-sm-6 col-xs-12 text-right">
                        <!-- Cart Summary -->
                        <div class="panel panel-default cart-summary">
                            <div class="panel-heading">
                                <h3 class="panel-title">Tóm tắt đơn hàng</h3>
                            </div>
                            <div class="panel-body">
                                <div class="row summary-row">
                                    <div class="col-xs-6">Tạm tính:</div>
                                    <div class="col-xs-6 text-right">{{ number_format($subtotal) }} vnđ</div>
                                </div>
                                <div class="row summary-row">
                                    <div class="col-xs-6">Phí vận chuyển:</div>
                                    <div class="col-xs-6 text-right">{{ number_format($shippingFee) }} vnđ</div>
                                </div>
                                <div class="row summary-row discount">
                                    <div class="col-xs-6">Giảm giá:</div>
                                    <div class="col-xs-6 text-right">-{{ number_format($discount) }} vnđ</div>
                                </div>
                                <div class="row summary-row total">
                                    <div class="col-xs-6"><strong>Tổng cộng:</strong></div>
                                    <div class="col-xs-6 text-right"><strong>{{ number_format($total) }} vnđ</strong>
                                    </div>
                                </div>
                                <a href="{{ route('cart.checkout') }}"
                                    class="btn btn-primary btn-block checkout-btn">Thanh toán</a>
                                <a href="{{ route('home') }}" class="btn btn-link btn-block continue-shopping">Tiếp tục
                                    mua
                                    sắm</a>
                            </div>
                        </div>
                    </div>
                </div>


            @endif
        </div>
    </div>
</x-guest-layout>

<style>
    /* Minimal CSS for adjustments */
    .cart-item {
        padding: 15px 0;
        border-bottom: 1px solid #ddd;
    }

    .cart-item:last-child {
        border-bottom: none;
    }

    .product-image {
        width: 80px;
        height: 80px;
        background-color: #f5f5f5;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .product-image img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    .item-price,
    .item-total {
        margin-top: 10px;
    }

    .quantity-control {
        max-width: 120px;
        margin: 0 auto;
    }

    .coupon-form {
        margin-bottom: 15px;
    }

    .coupon-form .form-group {
        margin-right: 10px;
    }

    .cart-summary {
        max-width: 350px;
        margin-left: auto;
        margin-top: 20px;
    }

    .summary-row {
        padding: 8px 0;
    }

    .summary-row.discount {
        color: #d9534f;
    }

    .summary-row.total {
        border-top: 1px solid #ddd;
        padding-top: 15px;
        margin-top: 15px;
    }

    .checkout-btn {
        margin: 10px 0;
    }

    @media (max-width: 767px) {
        .cart-item .col-xs-12 {
            margin-bottom: 10px;
        }

        .text-right {
            text-align: left !important;
        }

        .cart-summary {
            max-width: 100%;
        }
    }
</style>

<script>
    // Quantity controls
    document.querySelectorAll('.increment').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const input = document.querySelector(`.qty-input[data-id="${id}"]`);
            input.value = parseInt(input.value) + 1;
        });
    });

    document.querySelectorAll('.decrement').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const input = document.querySelector(`.qty-input[data-id="${id}"]`);
            if (parseInt(input.value) > 1) {
                input.value = parseInt(input.value) - 1;
            }
        });
    });

    // Input validation
    document.querySelectorAll('.qty-input').forEach(input => {
        input.addEventListener('change', function() {
            if (this.value < 1) {
                this.value = 1;
            }
            console.log(this.value);
            
        });
    });
</script>
