<x-guest-layout>
    @section('title', 'Thanh toán')
    <!-- SECTION -->
    <div class="section">
        <div class="container">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="row">
                <form action="{{ route('checkout.placeOrder') }}" method="POST">
                    @csrf
                    <input type="hidden" name="paypal_order_id" id="paypal_order_id">
                    <div class="col-md-7">
                        <!-- Shipping Information -->
                        <div class="billing-details">
                            <div class="section-title">
                                <h3 class="title font-weight-bold text-dark"><i class="fas fa-truck mr-2 text-primary"></i> Thông tin nhận hàng</h3>
                            </div>

                            @if (auth()->check() && isset($ShippingAddress) && $ShippingAddress->isNotEmpty())
                                <div class="form-group card p-3 mb-4 bg-light border-0 shadow-sm" style="border-radius: 8px;">
                                    <label class="font-weight-bold text-dark mb-2"><i class="fas fa-address-book text-info mr-1"></i> Chọn địa chỉ giao hàng đã lưu:</label>
                                    <select name="shipping_address_id" class="form-control" id="shipping-address-select" style="border-radius: 4px;">
                                        @foreach ($ShippingAddress as $address)
                                            <option value="{{ $address->id }}"
                                                data-name="{{ $address->full_name }}"
                                                data-phone="{{ $address->telephone }}"
                                                data-email="{{ $address->email ?? auth()->user()->email }}"
                                                data-province="{{ $address->province_id }}"
                                                data-district="{{ $address->district_id }}"
                                                data-ward="{{ $address->ward_id }}"
                                                data-address="{{ $address->address }}"
                                                {{ old('shipping_address_id', $address->is_default == 1 ? $address->id : null) == $address->id ? 'selected' : '' }}>
                                                {{ $address->full_name }} ({{ $address->telephone }}) - {{ $address->address }}, {{ $address->ward->name ?? '' }}, {{ $address->district->name ?? '' }}, {{ $address->province->name ?? '' }}
                                            </option>
                                        @endforeach
                                        <option value="new" {{ old('shipping_address_id') === 'new' ? 'selected' : '' }}>-- Sử dụng địa chỉ nhận hàng khác --</option>
                                    </select>
                                </div>

                                <!-- Card tóm tắt địa chỉ đã chọn -->
                                <div class="card p-3 mb-4 border-left-success shadow-xs bg-white" id="address-summary-card" style="display: none; border-left: 4px solid #28a745 !important; border-radius: 8px;">
                                    <div class="card-body p-0">
                                        <h6 class="font-weight-bold text-success mb-2"><i class="fas fa-check-circle mr-1"></i> Giao đến địa chỉ đã chọn:</h6>
                                        <p class="mb-0 text-dark" id="address-summary-text" style="font-size: 14px;"></p>
                                    </div>
                                </div>
                            @endif

                            <div id="shipping-form-fields">
                                <div class="form-group">
                                    <input class="input" type="text" name="billing_full_name"
                                        placeholder="Nhập họ và tên *" required
                                        value="{{ old('billing_full_name', $userInfo->name ?? '') }}">
                                </div>
                                <div class="form-group">
                                    <input class="input" type="tel" name="billing_telephone"
                                        placeholder="Nhập số điện thoại *" required
                                        value="{{ old('billing_telephone', $userInfo->phone ?? '') }}">
                                </div>
                                <div class="form-group">
                                    <input class="input" type="email" name="billing_email"
                                        placeholder="Nhập địa chỉ email *" required
                                        value="{{ old('billing_email', $userInfo->email ?? '') }}">
                                </div>
                                <div class="form-group">
                                    <select id="province" name="billing_province_id" class="form-control" required>
                                        <option value="">Chọn Tỉnh/Thành phố *</option>
                                        @foreach ($provinces as $province)
                                            <option value="{{ $province->code }}"
                                                {{ old('billing_province_id', $userInfo->province_id ?? '') == $province->code ? 'selected' : '' }}>
                                                {{ $province->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select id="district" name="billing_district_id" class="form-control" disabled required>
                                        <option value="">Chọn Quận/Huyện *</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select id="ward" name="billing_ward_id" class="form-control" disabled required>
                                        <option value="">Chọn Phường/Xã *</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <input class="input" type="text" name="billing_address"
                                        placeholder="Số nhà, tên đường... *" required
                                        value="{{ old('billing_address', $userInfo->address ?? '') }}">
                                </div>
                            </div>
                        </div>



                        <!-- Order notes -->
                        <div class="order-notes">
                            <textarea class="input" name="note" placeholder="Ghi chú đơn hàng">{{ old('note') }}</textarea>
                        </div>
                        <!-- /Order notes -->
                    </div>

                    <!-- Order Details -->
                    <div class="col-md-5 order-details">
                        <div class="section-title text-center">
                            <h3 class="title">Đơn hàng của bạn</h3>
                        </div>
                        <div class="order-summary">
                            <div class="order-col">
                                <div><strong>Sản phẩm</strong></div>
                                <div><strong>Tổng tiền</strong></div>
                            </div>
                            <div class="order-products">
                                @foreach ($cart->cartDetails as $item)
                                    <div class="order-col">
                                        <div>{{ $item->qty }}x {{ Str::limit($item->product->title, 20, '...') }}
                                        </div>
                                        <div>
                                            <p class="item-price">
                                                {{ number_format($item->final_price * $item->qty) }} vnđ
                                            </p>
                                        </div>

                                    </div>
                                @endforeach
                            </div>
                            <div class="order-col">
                                <div>Vận chuyển</div>
                                <div><strong><span id="shippingFeeDisplay">{{ number_format($shippingFee) }}
                                            vnđ</span></strong></div>
                            </div>


                            @if (isset($cart->coupon_code))
                                <div class="order-col">
                                    <div class="col-xs-6">Giảm giá:</div>
                                    <div class="col-xs-6 text-right text-danger">
                                        -{{ number_format($discount) }}
                                        vnđ
                                    </div>
                                </div>
                                <form action="{{ route('cart.removeCoupon') }}" method="POST">
                                    @csrf
                                    <div class="order-col">
                                        <div class="col-xs-6">Mã giảm giá:</div>
                                        <div class="col-xs-6 text-right">
                                            <strong>{{ $cart->coupon_code }}</strong>
                                            <button class="btn btn-sm btn-link text-danger" data-toggle="tooltip"
                                                title="Xóa mã giảm giá" type="submit">X</button>
                                        </div>
                                    </div>
                                </form>
                            @endif
                            <div class="order-col">
                                <div><strong>Tổng tiền</strong></div>
                                <div><strong class="order-total">{{ number_format($total) . ' vnđ' }}</strong>
                                </div>
                            </div>
                            <input type="hidden" id="subtotalValue" value="{{ $subtotal }}">
                            <input type="hidden" id="discountValue" value="{{ $discount ?? 0 }}">
                        </div>
                        <div class="payment-method">
                            <div class="input-radio">
                                <input type="radio" name="payment_method" id="payment-1" value="cash">
                                <label for="payment-1">
                                    <span></span>
                                    Thanh toán tiền mặt
                                </label>
                                <div class="caption">
                                    <p>Thanh toán khi nhận hàng.</p>
                                </div>
                            </div>
                            <div class="input-radio">
                                <input type="radio" name="payment_method" id="payment-2" value="transfer">
                                <label for="payment-2">
                                    <span></span>
                                    Thanh toán chuyển khoản
                                </label>
                                <div class="caption">
                                    <p>Thanh toán số tiền <b>{{ number_format($total) . ' vnđ' }}</b> đến tài khoản
                                        ngân hàng</p>
                                    <p>TP Bank: <b>0000 1860 446</b></p>
                                    <p>Chủ tài khoản: <b>LÝ THÀNH PHÚ</b></p>
                                    <p><b>Khi thanh toán vui lòng điền mã đơn hàng vào nội dung chuyển khoản.</b></p>
                                </div>
                            </div>
                            <div class="input-radio">
                                <input type="radio" name="payment_method" id="payment-3" value="paypal">
                                <label for="payment-3">
                                    <span></span>
                                    Thanh toán qua PayPal
                                </label>
                                <div class="caption">
                                    <p>Thanh toán bằng tài khoản PayPal hoặc thẻ quốc tế (Visa, Master...).</p>
                                    <!-- Đây là nơi nút PayPal sẽ hiển thị -->
                                    <div id="paypal-button-container" style="margin-top:10px;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="input-checkbox">
                            <input type="checkbox" id="terms" name="terms">
                            <label for="terms">
                                <span></span>Tôi đã đọc và chấp nhận các <a href="#">điều khoản và điều kiện</a>
                            </label>
                        </div>
                        <button type="submit" class="primary-btn order-submit btn-block font-weight-bold" id="btn-place-order" style="border-radius: 4px; padding: 12px; font-size: 16px;">
                            <i class="fas fa-check-circle mr-1"></i> Đặt hàng
                        </button>
                    </div>
                    <!-- /Order Details -->
                </form>
            </div>
        </div>
    </div>
    <!-- /SECTION -->
    <script src="https://www.paypal.com/sdk/js?client-id={{ env('PAYPAL_SANDBOX_CLIENT_ID') }}&currency=USD"></script>

</x-guest-layout>
<style>
    .order-col .item-price {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 150px;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const districtsUrl = "{{ route('getDistricts', ['provinceId' => ':provinceId']) }}";
        const wardsUrl = "{{ route('getWards', ['districtId' => ':districtId']) }}";

        const provinceSelect = document.getElementById('province');
        const districtSelect = document.getElementById('district');
        const wardSelect = document.getElementById('ward');
        const addressSelect = document.getElementById('shipping-address-select');
        const summaryCard = document.getElementById('address-summary-card');
        const summaryText = document.getElementById('address-summary-text');
        const formFields = document.getElementById('shipping-form-fields');

        // Old inputs or fallbacks
        const oldProvince = "{{ old('billing_province_id', $userInfo->province_id ?? '') }}";
        const oldDistrict = "{{ old('billing_district_id', $userInfo->district_id ?? '') }}";
        const oldWard = "{{ old('billing_ward_id', $userInfo->ward_id ?? '') }}";

        // Gửi AJAX update phí vận chuyển
        function updateShippingFee(provinceId, districtId) {
            fetch(`/api/shipping-fee?province_id=${provinceId}&district_id=${districtId}`)
                .then(res => res.json())
                .then(data => {
                    const shippingFee = data.fee ?? 50000;
                    document.getElementById('shippingFeeDisplay').innerText = shippingFee.toLocaleString('vi-VN') + ' vnđ';

                    const subtotal = parseInt(document.getElementById('subtotalValue').value);
                    const discount = parseInt(document.getElementById('discountValue').value || 0);
                    const total = Math.max(subtotal + shippingFee - discount, 0);

                    document.querySelector('.order-total').innerText = total.toLocaleString('vi-VN') + ' vnđ';

                    const captionText = document.querySelector('#payment-2 ~ .caption p b');
                    if (captionText) captionText.innerText = total.toLocaleString('vi-VN') + ' vnđ';
                });
        }

        // Tải Quận/Huyện bằng AJAX
        function loadDistricts(provinceId, districtSelect, wardSelect, selectedDistrictId = '', selectedWardId = '') {
            const url = districtsUrl.replace(':provinceId', provinceId);
            districtSelect.innerHTML = '<option value="">Chọn Quận/Huyện *</option>';
            wardSelect.innerHTML = '<option value="">Chọn Phường/Xã *</option>';
            wardSelect.disabled = true;

            return fetch(url)
                .then(response => response.json())
                .then(data => {
                    districtSelect.disabled = false;
                    data.forEach(district => {
                        const option = document.createElement('option');
                        option.value = district.code;
                        option.textContent = district.name;
                        if (String(district.code) === String(selectedDistrictId)) {
                            option.selected = true;
                        }
                        districtSelect.appendChild(option);
                    });

                    if (selectedDistrictId) {
                        return loadWards(selectedDistrictId, wardSelect, selectedWardId).then(() => {
                            updateShippingFee(provinceId, selectedDistrictId);
                        });
                    }
                });
        }

        // Tải Phường/Xã bằng AJAX
        function loadWards(districtId, wardSelect, selectedWardId = '') {
            const url = wardsUrl.replace(':districtId', districtId);
            wardSelect.innerHTML = '<option value="">Chọn Phường/Xã *</option>';

            return fetch(url)
                .then(response => response.json())
                .then(data => {
                    wardSelect.disabled = false;
                    data.forEach(ward => {
                        const option = document.createElement('option');
                        option.value = ward.code;
                        option.textContent = ward.name;
                        if (String(ward.code) === String(selectedWardId)) {
                            option.selected = true;
                        }
                        wardSelect.appendChild(option);
                    });
                });
        }

        // Đăng ký sự kiện thay đổi Tỉnh/Thành
        provinceSelect.addEventListener('change', function() {
            const provinceId = this.value;
            if (provinceId) {
                loadDistricts(provinceId, districtSelect, wardSelect);
            } else {
                districtSelect.innerHTML = '<option value="">Chọn Quận/Huyện *</option>';
                districtSelect.disabled = true;
                wardSelect.innerHTML = '<option value="">Chọn Phường/Xã *</option>';
                wardSelect.disabled = true;
            }
        });

        // Đăng ký sự kiện thay đổi Quận/Huyện
        districtSelect.addEventListener('change', function() {
            const districtId = this.value;
            if (districtId) {
                loadWards(districtId, wardSelect).then(() => {
                    updateShippingFee(provinceSelect.value, districtId);
                });
            } else {
                wardSelect.innerHTML = '<option value="">Chọn Phường/Xã *</option>';
                wardSelect.disabled = true;
            }
        });

        // Hàm auto-fill dữ liệu từ một địa chỉ cụ thể
        function applyAddressData(name, phone, email, provinceId, districtId, wardId, address) {
            document.querySelector('input[name="billing_full_name"]').value = name;
            document.querySelector('input[name="billing_telephone"]').value = phone;
            document.querySelector('input[name="billing_email"]').value = email;
            document.querySelector('input[name="billing_address"]').value = address;
            provinceSelect.value = provinceId;

            loadDistricts(provinceId, districtSelect, wardSelect, districtId, wardId);
        }

        const hasOldInput = {{ old('billing_full_name') || old('billing_telephone') || old('billing_address') ? 'true' : 'false' }};

        // Xử lý khi Thay đổi Địa chỉ Giao hàng đã lưu
        if (addressSelect) {
            function handleAddressChange(isInit = false) {
                const selected = addressSelect.options[addressSelect.selectedIndex];
                const isNew = !selected || selected.value === 'new';

                if (isNew) {
                    summaryCard.style.display = 'none';
                    formFields.style.display = 'block';

                    // Chỉ ghi đè/clear nếu KHÔNG PHẢI là tải trang có lỗi (old input)
                    if (!isInit || !hasOldInput) {
                        document.querySelector('input[name="billing_full_name"]').value = "{{ $userInfo->name ?? '' }}";
                        document.querySelector('input[name="billing_telephone"]').value = "{{ $userInfo->phone ?? '' }}";
                        document.querySelector('input[name="billing_email"]').value = "{{ $userInfo->email ?? '' }}";
                        document.querySelector('input[name="billing_address"]').value = "";
                        provinceSelect.value = "";
                        districtSelect.innerHTML = '<option value="">Chọn Quận/Huyện *</option>';
                        districtSelect.disabled = true;
                        wardSelect.innerHTML = '<option value="">Chọn Phường/Xã *</option>';
                        wardSelect.disabled = true;
                    }
                } else {
                    // Chọn một địa chỉ đã lưu
                    const name = selected.getAttribute('data-name');
                    const phone = selected.getAttribute('data-phone');
                    
                    summaryText.innerHTML = `<strong>Người nhận:</strong> ${name} (${phone})<br/><strong>Địa chỉ:</strong> ${selected.text.split(' - ')[1]}`;
                    summaryCard.style.display = 'block';
                    formFields.style.display = 'none';

                    // Chỉ điền tự động nếu không phải là tải trang có lỗi
                    if (!isInit || !hasOldInput) {
                        const email = selected.getAttribute('data-email');
                        const provinceId = selected.getAttribute('data-province');
                        const districtId = selected.getAttribute('data-district');
                        const wardId = selected.getAttribute('data-ward');
                        const address = selected.getAttribute('data-address');
                        
                        applyAddressData(name, phone, email, provinceId, districtId, wardId, address);
                    }
                }
            }

            addressSelect.addEventListener('change', function() {
                handleAddressChange(false);
            });
            
            // Chạy lần đầu tiên để thiết lập form theo địa chỉ mặc định được chọn
            handleAddressChange(true);
            
            // Nếu chọn địa chỉ mới và có old input thì load quận huyện/xã dựa trên old inputs
            const isNewAddressMode = !addressSelect || addressSelect.value === 'new';
            if (isNewAddressMode && oldProvince) {
                provinceSelect.value = oldProvince;
                loadDistricts(oldProvince, districtSelect, wardSelect, oldDistrict, oldWard);
            }
        } else {
            // Khách vãng lai: Tự động load quận huyện/xã dựa trên old inputs hoặc database
            if (oldProvince) {
                provinceSelect.value = oldProvince;
                loadDistricts(oldProvince, districtSelect, wardSelect, oldDistrict, oldWard);
            }
        }

        // Xử lý trước khi Submit Form đặt hàng
        const checkoutForm = document.querySelector('form[action="{{ route('checkout.placeOrder') }}"]');
        const submitBtn = document.getElementById('btn-place-order');

        if (checkoutForm) {
            checkoutForm.addEventListener('submit', function(e) {
                // Nếu chọn thanh toán qua PayPal, việc submit sẽ do PayPal SDK tự kích hoạt sau khi capture tiền
                const selectedPayment = document.querySelector('input[name="payment_method"]:checked');
                if (selectedPayment && selectedPayment.value === 'paypal' && !document.getElementById('paypal_order_id').value) {
                    return; // Để PayPal capture trước
                }



                // Hiển thị hiệu ứng loading & vô hiệu hóa nút đặt hàng
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Đang xử lý đặt hàng... Vui lòng không đóng trình duyệt';
            });
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkoutForm = document.querySelector('form[action="{{ route('checkout.placeOrder') }}"]');
        paypal.Buttons({
            style: {
                layout: 'vertical',
                color: 'gold'
            },
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            currency_code: "USD",
                            value: parseFloat(
                                "{{ number_format($total / 24000, 2, '.', '') }}"
                            )
                        }
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    // Gửi thông tin PayPal về server xác nhận
                    fetch("{{ route('paypal.success') }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                orderID: data.orderID,
                                payerID: data.payerID,
                                details: details
                            })
                        })
                        .then(res => res.json())
                        .then(result => {
                            if (result.success) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Thanh toán thành công!",
                                    text: "Đang xử lý đơn hàng của bạn...",
                                    timer: 1500,
                                    showConfirmButton: false
                                });

                                // ✅ Tự động submit form đặt hàng
                                setTimeout(() => {
                                    // Gán phương thức thanh toán là paypal và lưu mã giao dịch
                                    document.getElementById('paypal_order_id').value = data.orderID;
                                    checkoutForm.querySelector(
                                        'input[name="payment_method"][value="paypal"]'
                                    ).checked = true;
                                    checkoutForm.submit();
                                }, 1600);
                            } else {
                                Swal.fire("Lỗi", "Không thể xác nhận thanh toán!",
                                    "error");
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            Swal.fire("Lỗi",
                                "Có lỗi xảy ra khi xác nhận thanh toán!",
                                "error");
                        });
                });
            }
        }).render('#paypal-button-container');
    });
</script>
