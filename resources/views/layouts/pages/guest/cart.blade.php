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
            @if (session('error'))
                <div class="alert alert-danger alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <i class="fa fa-exclamation-triangle"></i> {{ session('error') }}
                </div>
            @endif


            <h1 class="text-center" style="margin-bottom: 3%">Giỏ hàng của bạn</h1>
            @if (empty($cartItems) && $cartItems == [])
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
                                            @php
                                                $image =
                                                    $item->product->productImages->where('type', 1)->first()->image ??
                                                    '';
                                                $imagePath = $image
                                                    ? asset('storage/' . $image)
                                                    : asset('asset/img/no-image.png');
                                            @endphp

                                            <img src="{{ $imagePath }}" alt="{{ $item->product->title }}"
                                                class="media-object product-image">
                                        </div>
                                        <div class="media-body">
                                            <h4 class="media-heading"><a class="h4" style="font-weight: 700"
                                                    href="{{ route('product.show', ['slug' => $item->product->slug]) }}">{{ $item->product->title }}</a>
                                            </h4>
                                            <p class="text-muted"><strong>Tuỳ chọn:</strong>
                                                {{ isset($item->productVariant) ? $item->productVariant->variant_name : 'Mặc định' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-2 col-xs-12 text-center">
                                    <p class="item-price">
                                        @if ($item->final_price < $item->original_price)
                                            <span class="text-danger">
                                                {{ number_format($item->final_price) }} vnđ
                                                <del class="text-muted">{{ number_format($item->original_price) }}
                                                    vnđ</del>
                                            </span>
                                        @else
                                            <span>{{ number_format($item->original_price) }} vnđ</span>
                                        @endif
                                    </p>
                                </div>

                                <div class="col-sm-2 col-xs-12 text-center">
                                    <div class="input-group input-group-sm quantity-control">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default decrement" data-id="{{ $item->product_id }}"
                                                data-variant-id="{{ isset($item->productVariant) ? $item->productVariant->id : null }}">-</button>
                                        </span>
                                        <input type="number" min="1" value="{{ $item->qty }}"
                                            class="form-control text-center qty-input" data-id="{{ $item->product_id }}"
                                            data-variant-id="{{ isset($item->productVariant) ? $item->productVariant->id : null }}">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default increment" data-id="{{ $item->product_id }}"
                                                data-variant-id="{{ isset($item->productVariant) ? $item->productVariant->id : null }}">+</button>
                                        </span>
                                    </div>
                                    <small class="text-danger d-none error-msg" data-id="{{ $item->product_id }}"></small>
                                </div>
                                <div class="col-sm-2 col-xs-12 text-center">
                                    <p class="item-total">
                                        {{ number_format($item->final_price * $item->qty) }} vnđ
                                    </p>
                                </div>
                                <div class="col-sm-1 col-xs-12 text-center">
                                    <form method="POST"
                                        action="{{ route('cart.remove', [
                                            'productId' => $item->product_id,
                                            'productVariantId' => $item->product_variant_id ?? null, // hoặc giá trị mặc định
                                        ]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Bạn có chắc chắn muốn xóa?')"
                                            data-toggle="tooltip" title="Xóa sản phẩm" class="btn btn-danger btn-sm">
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
                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#couponModal">
                            Nhận mã giảm giá
                        </button>
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
                                    <div class="col-xs-6 text-right text-danger">
                                        -{{ isset($cart->discount_amount) ? number_format(abs($cart->discount_amount)) : 0 }}
                                        vnđ
                                    </div>
                                </div>
                                @if (isset($cart->coupon_code))
                                    <form action="{{ route('cart.removeCoupon') }}" method="POST">
                                        @csrf
                                        <div class="row summary-row coupon-code">
                                            <div class="col-xs-6">Mã giảm giá:</div>
                                            <div class="col-xs-6 text-right">
                                                <strong>{{ $cart->coupon_code }}</strong>
                                                <button class="btn btn-sm btn-link text-danger" data-toggle="tooltip"
                                                    title="Xóa mã giảm giá" type="submit">X</button>
                                            </div>
                                        </div>
                                    </form>
                                @endif
                                <div class="row summary-row total">
                                    <div class="col-xs-6"><strong>Tổng cộng:</strong></div>
                                    <div class="col-xs-6 text-right"><strong>{{ number_format($total) }} vnđ</strong>
                                    </div>
                                </div>
                                <a href="{{ route('cart.checkout') }}"
                                    class="btn btn-primary btn-block checkout-btn">Thanh toán</a>
                                <a href="{{ route('home') }}" class="btn btn-link btn-block continue-shopping">Tiếp
                                    tục
                                    mua
                                    sắm</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="couponModal" tabindex="-1" role="dialog"
                    aria-labelledby="couponModalLabel">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title" id="couponModalLabel">Danh sách mã giảm giá còn hiệu lực</h4>
                            </div>
                            <div class="modal-body">
                                @if ($coupons->isEmpty())
                                    <p class="text-muted">Hiện tại không có mã giảm giá khả dụng.</p>
                                @else
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>Mã</th>
                                                <th>Loại giảm giá</th>
                                                <th>Giá trị</th>
                                                <th>Hạn sử dụng</th>
                                                <th>Lượt dùng</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($coupons as $coupon)
                                                <tr>
                                                    <td>
                                                        <span class="copy-code" data-code="{{ $coupon->code }}"
                                                            data-toggle="tooltip" data-placement="top"
                                                            title="Nhấn để sao chép"
                                                            style="cursor: pointer; color: #007bff;">
                                                            <strong>{{ $coupon->code }}</strong>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if ($coupon->type === 'percent')
                                                            Phần trăm
                                                        @else
                                                            Giảm thẳng
                                                        @endif
                                                    </td>
                                                    <td>{{ $coupon->type == 'fixed' ? number_format($coupon->value) . ' vnđ' : (int) $coupon->value . '%' }}
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($coupon->expired_at)->format('H:i:s d/m/Y') }}
                                                    </td>
                                                    <td>{{ $coupon->used }}/{{ $coupon->usage_limit }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
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
    const updateTimeouts = {}; // Tạm lưu timeout cho từng sản phẩm

    function updateCartQuantity(id, variant, qty) {
        if (qty < 1) qty = 1;

        // Nếu đã có timeout trước đó thì clear để reset
        if (updateTimeouts[id]) {
            clearTimeout(updateTimeouts[id]);
        }

        // Đặt lại timeout sau 500ms mới gọi API
        updateTimeouts[id] = setTimeout(() => {
            const bodyData = {
                id,
                qty
            };

            // Chỉ thêm variant nếu có
            if (variant) {
                bodyData.variant = variant;
            }

            fetch('{{ route('cart.updateQuantity') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(bodyData)
                })
                .then(res => res.json())
                .then(data => {
                    const input = document.querySelector(`.qty-input[data-id="${id}"]`);
                    const errorMsg = document.querySelector(`.error-msg[data-id="${id}"]`);

                    if (data.error) {
                        input.classList.add('is-invalid');

                        if (data.max_qty) {
                            input.value = data.max_qty;
                        }

                        if (errorMsg) {
                            errorMsg.textContent = data.message;
                            errorMsg.classList.remove('d-none');
                        }

                        return;
                    }

                    input.classList.remove('is-invalid');
                    if (errorMsg) {
                        errorMsg.textContent = '';
                        errorMsg.classList.add('d-none');
                    }

                    const itemTotal = input.closest('.cart-item').querySelector('.item-total');
                    itemTotal.textContent = `${data.item_total} vnđ`;

                    document.querySelector('.summary-row:nth-child(1) .text-right')
                        .textContent = `${data.subtotal} vnđ`;
                    document.querySelector('.summary-row.total .text-right')
                        .textContent = `${data.total} vnđ`;
                });
        }, 500);
    }


    // Nút tăng
    document.querySelectorAll('.increment').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const variant = this.dataset.variantId;

            const input = document.querySelector(`.qty-input[data-id="${id}"]`);
            input.value = parseInt(input.value) + 1;

            updateCartQuantity(id, variant, input.value);
        });
    });

    // Nút giảm
    document.querySelectorAll('.decrement').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const variant = this.dataset.variantId;

            const input = document.querySelector(`.qty-input[data-id="${id}"]`);
            input.value = Math.max(1, parseInt(input.value) - 1);

            updateCartQuantity(id, variant, input.value);
        });
    });

    // Nhập tay
    document.querySelectorAll('.qty-input').forEach(input => {
        input.addEventListener('change', function() {
            const id = this.dataset.id;
            const variant = this.dataset.variantId;

            this.value = Math.max(1, parseInt(this.value));
            updateCartQuantity(id, variant, this.value);
        });
    });
    // Tự động ẩn alert 
    setTimeout(function() {
        const alert = document.querySelector('.alert');
        if (alert) {
            alert.style.transition = "opacity 0.5s ease";
            alert.style.opacity = 0;
            setTimeout(() => alert.remove(), 500);
        }
    }, 6000); // 3 giây
    $('body').tooltip({
        selector: '[data-toggle="tooltip"]'
    });
</script>
<script>
    // $(document).ready(function() {
    //     $('[data-toggle="tooltip"]').tooltip();

    //     $('.copy-code').on('click', function() {
    //         var code = $(this).data('code');
    //         var $el = $(this);

    //         var $temp = $('<input>');
    //         $('body').append($temp);
    //         $temp.val(code).select();
          
            
    //         try {

    //             navigator.clipboard.writeText(code);
    //             console.log('Text copied to clipboard');

    //             var success = document.execCommand('copy');
    //             if (success) {
    //                   console.log(success);
    //                 $el.attr('data-original-title', '✓ Đã sao chép').tooltip('show');
    //                 setTimeout(function() {
    //                     $el.attr('data-original-title', 'Nhấn để sao chép');
    //                 }, 1500);
    //             }
    //         } catch (err) {
    //             console.log(err);
                
    //             alert('Trình duyệt của bạn không hỗ trợ sao chép.');
    //         }

    //         $temp.remove();
    //     });
    // });


    $(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip();

    $('.copy-code').on('click', async function () {
        var code = $(this).data('code');
        var $el = $(this);

        // Tạo input tạm
        var $temp = $('<input>');
        $('body').append($temp);
        $temp.val(code).select();

        try {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                // ✅ Cách mới
                await navigator.clipboard.writeText(code).then(function () {
                    showTooltip($el);
                }).catch(function (err) {
                    console.error('Clipboard API lỗi:', err);
                    fallbackCopy($el, $temp[0]);
                });
            } else {
                // ✅ Fallback
                fallbackCopy($el, $temp[0]);
            }
        } catch (err) {
            console.log(err);
            alert('Trình duyệt của bạn không hỗ trợ sao chép.');
        }

        $temp.remove();
    });

    function fallbackCopy($el, element) {
        var success = document.execCommand('copy');
        if (success) {
            showTooltip($el);
        }
    }

    function showTooltip($el) {
        $el.attr('data-original-title', '✓ Đã sao chép').tooltip('show');
        setTimeout(function () {
            $el.attr('data-original-title', 'Nhấn để sao chép');
        }, 1500);
    }
});

</script>
