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
                    <div class="col-md-7">
                        <!-- Billing Details -->
                        <div class="billing-details">
                            <div class="section-title">
                                <h3 class="title">Địa chỉ thanh toán</h3>
                            </div>
                            <div class="form-group">
                                <input class="input" type="text" name="billing_full_name"
                                    placeholder="Nhập họ và tên"
                                    value="{{ old('billing_full_name', $userInfo->name ?? '') }}">
                            </div>
                            <div class="form-group">
                                <input class="input" type="email" name="billing_email"
                                    placeholder="Nhập địa chỉ email"
                                    value="{{ old('billing_email', $userInfo->email ?? '') }}">
                            </div>
                            <div class="form-group">
                                <select id="province" name="billing_province_id" class="form-control">
                                    <option value="">Chọn Tỉnh/Thành phố</option>
                                    @foreach ($provinces as $province)
                                        <option value="{{ $province->code }}"
                                            {{ old('billing_province_id', $userInfo->province_id ?? '') == $province->code ? 'selected' : '' }}>
                                            {{ $province->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <select id="district" name="billing_district_id" class="form-control" disabled>
                                    <option value="">Chọn Quận/Huyện</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <select id="ward" name="billing_ward_id" class="form-control" disabled>
                                    <option value="">Chọn Phường/Xã</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <input class="input" type="text" name="billing_address"
                                    placeholder="Nhập địa chỉ nhận hàng"
                                    value="{{ old('billing_address', $userInfo->address ?? '') }}">
                            </div>
                            <div class="form-group">
                                <input class="input" type="tel" name="billing_telephone"
                                    placeholder="Nhập số điện thoại"
                                    value="{{ old('billing_telephone', $userInfo->phone ?? '') }}">
                            </div>
                            @if (!$userInfo)
                                <div class="form-group">
                                    <div class="input-checkbox">
                                        <input type="checkbox" id="create-account">
                                        <label for="create-account">
                                            <span></span>
                                            Create Account?
                                        </label>
                                        <div class="caption">
                                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
                                            <input class="input" type="password" name="password"
                                                placeholder="Enter Your Password">
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <!-- /Billing Details -->

                        <!-- Shipping Details -->
                        <div class="shiping-details">
                            <div class="section-title">
                                <h3 class="title">Địa chỉ giao hàng</h3>
                            </div>
                            @if (auth()->check() && isset($ShippingAddress))
                                <div class="form-group">
                                    <label>Chọn địa chỉ giao hàng đã lưu:</label>
                                    <select name="shipping_address_id" class="form-control">
                                        <option value="">-- Chọn địa chỉ --</option>
                                        @foreach ($ShippingAddress as $address)
                                            <option value="{{ $address->id }}"
                                                {{ old('shipping_address_id', $address->is_default == 1 ? $address->id : null) == $address->id ? 'selected' : '' }}>
                                                {{ $address->full_name }} - {{ $address->address }},
                                                {{ $address->ward->name }}, {{ $address->district->name }},
                                                {{ $address->province->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <div class="form-group">
                                <label><input type="checkbox" name="use_new_shipping_address"> Giao đến địa chỉ
                                    mới</label>
                                <div class="new-shipping-form" style="display: none;">
                                    <div class="form-group">
                                        <input class="input" type="text" name="shipping_full_name"
                                            placeholder="Họ tên người nhận" value="{{ old('shipping_full_name') }}">
                                    </div>
                                    <div class="form-group">
                                        <input class="input" type="email" name="shipping_email"
                                            placeholder="Email người nhận nhận" value="{{ old('shipping_email') }}">
                                    </div>
                                    <div class="form-group">
                                        <input class="input" type="tel" name="shipping_telephone"
                                            placeholder="Số điện thoại người nhận"
                                            value="{{ old('shipping_telephone') }}">
                                    </div>
                                    <div class="form-group">
                                        <select id="shipping_province" name="shipping_province_id" class="form-control">
                                            <option value="">Chọn Tỉnh/Thành phố</option>
                                            @foreach ($provinces as $province)
                                                <option value="{{ $province->code }}"
                                                    {{ old('shipping_province_id') == $province->code ? 'selected' : '' }}>
                                                    {{ $province->full_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <select id="shipping_district" name="shipping_district_id"
                                            class="form-control" disabled>
                                            <option value="">Chọn Quận/Huyện</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <select id="shipping_ward" name="shipping_ward_id" class="form-control"
                                            disabled>
                                            <option value="">Chọn Phường/Xã</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <input class="input" type="text" name="shipping_address"
                                            placeholder="Số nhà, tên đường..." value="{{ old('shipping_address') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /Shipping Details -->

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
                                <div><strong>{{ number_format($shippingFee) . ' vnđ' }}</strong></div>
                            </div>

                            @if (isset($cart->coupon_code))
                                <div class="order-col">
                                    <div class="col-xs-6">Giảm giá:</div>
                                    <div class="col-xs-6 text-right text-danger">
                                        -{{ isset($cart->discount_amount) ? number_format(abs($cart->discount_amount)) : 0 }}
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
                                <div><strong
                                        class="order-total">{{ number_format($total) . ' vnđ' }}</strong>
                                </div>
                            </div>
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
                                    <p>Thanh toán số tiền <b>{{ number_format($total) . ' vnđ' }}</b> đến tài khoản ngân hàng</p>
                                    <p>TP Bank: <b>0000 1860 446</b></p>
                                    <p>Chủ tài khoản: <b>LÝ THÀNH PHÚ</b></p>
                                    <p><b>Khi thanh toán vui lòng điền mã đơn hàng vào nội dung chuyển khoản.</b></p>
                                </div>
                            </div>
                        </div>
                        <div class="input-checkbox">
                            <input type="checkbox" id="terms" name="terms">
                            <label for="terms">
                                <span></span>Tôi đã đọc và chấp nhận các <a href="#">điều khoản và điều kiện</a>
                            </label>
                        </div>
                        <button type="submit" class="primary-btn order-submit">Đặt hàng</button>
                    </div>
                    <!-- /Order Details -->
                </form>
            </div>
        </div>
    </div>
    <!-- /SECTION -->
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

        // Billing address
        const provinceSelect = document.getElementById('province');
        const districtSelect = document.getElementById('district');
        const wardSelect = document.getElementById('ward');

        // Shipping address
        const shippingProvinceSelect = document.getElementById('shipping_province');
        const shippingDistrictSelect = document.getElementById('shipping_district');
        const shippingWardSelect = document.getElementById('shipping_ward');

        // New shipping address checkbox
        const newShippingCheckbox = document.querySelector('input[name="use_new_shipping_address"]');
        const newShippingForm = document.querySelector('.new-shipping-form');

        // Load districts and wards for billing address
        if (provinceSelect.value) {
            loadDistricts(provinceSelect.value, districtSelect, wardSelect,
                "{{ $userInfo->district_id ?? '' }}", "{{ $userInfo->ward_id ?? '' }}");
        }



        provinceSelect.addEventListener('change', function() {
            const provinceId = this.value;
            districtSelect.innerHTML = '<option value="">Chọn Quận/Huyện</option>';
            wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
            wardSelect.disabled = true;

            if (provinceId) {
                loadDistricts(provinceId, districtSelect, wardSelect);
            } else {
                districtSelect.disabled = true;
            }
        });

        districtSelect.addEventListener('change', function() {
            const districtId = this.value;
            wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';

            if (districtId) {
                loadWards(districtId, wardSelect);
            } else {
                wardSelect.disabled = true;
            }
        });

        // Load districts and wards for shipping address
        if (shippingProvinceSelect.value) {
            loadDistricts(shippingProvinceSelect.value, shippingDistrictSelect, shippingWardSelect,
                "{{ old('shipping_district_id') }}", "{{ old('shipping_ward_id') }}");
        }

        shippingProvinceSelect.addEventListener('change', function() {
            const provinceId = this.value;
            shippingDistrictSelect.innerHTML = '<option value="">Chọn Quận/Huyện</option>';
            shippingWardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
            shippingWardSelect.disabled = true;

            if (provinceId) {
                loadDistricts(provinceId, shippingDistrictSelect, shippingWardSelect);
            } else {
                shippingDistrictSelect.disabled = true;
            }
        });

        shippingDistrictSelect.addEventListener('change', function() {
            const districtId = this.value;
            shippingWardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';

            if (districtId) {
                loadWards(districtId, shippingWardSelect);
            } else {
                shippingWardSelect.disabled = true;
            }
        });

        // Toggle new shipping form
        if (newShippingCheckbox) {
            newShippingCheckbox.addEventListener('change', function() {
                newShippingForm.style.display = this.checked ? 'block' : 'none';
            });
        }

        function loadDistricts(provinceId, districtSelect, wardSelect, selectedDistrictId = '', selectedWardId =
            '') {
            const url = districtsUrl.replace(':provinceId', provinceId);

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    districtSelect.disabled = false;
                    data.forEach(district => {
                        const option = document.createElement('option');
                        option.value = district.code;
                        option.textContent = district.name;
                        if (district.code === selectedDistrictId) {
                            option.selected = true;
                            loadWards(district.code, wardSelect, selectedWardId);
                        }
                        districtSelect.appendChild(option);
                    });
                });
        }

        function loadWards(districtId, wardSelect, selectedWardId = '') {
            const url = wardsUrl.replace(':districtId', districtId);

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    wardSelect.disabled = false;
                    data.forEach(ward => {
                        const option = document.createElement('option');
                        option.value = ward.code;
                        option.textContent = ward.name;
                        if (ward.code === selectedWardId) {
                            option.selected = true;
                        }
                        wardSelect.appendChild(option);
                    });
                });
        }
    });
</script>
