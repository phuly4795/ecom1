<x-guest-layout>
    @section('title', 'Thanh toán')

    <!-- SECTION -->
    <div class="section">
        <!-- container -->
        <div class="container">
            <!-- row -->
            <div class="row">

                <div class="col-md-7">
                    <!-- Billing Details -->
                    <div class="billing-details">
                        <div class="section-title">
                            <h3 class="title">Địa chỉ thanh toán</h3>
                        </div>
                        <div class="form-group">
                            <input class="input" type="text" name="name" placeholder="Nhập họ và tên"
                                value="{{ old('name', $userInfo->name ?? '') }}">
                        </div>
                        <div class="form-group">
                            <input class="input" type="email" name="email" placeholder="Nhập địa chỉ email"
                                value="{{ old('email', $userInfo->email ?? '') }}">
                        </div>
                        <div class="form-group">
                            <select id="province" name="province_id" class="form-control">
                                <option value="">Chọn Tỉnh/Thành phố</option>
                                @foreach ($provinces as $province)
                                    <option value="{{ $province->code }}"
                                        {{ isset($userInfo) ? ($userInfo->province_id == $province->code ? 'selected' : '') : '' }}>
                                        {{ $province->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <select id="district" name="district_id" class="form-control" disabled>
                                <option value="">Chọn Quận/Huyện</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <select id="ward" name="ward_id" class="form-control" disabled>
                                <option value="">Chọn Phường/Xã</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <input class="input" type="text" name="address" placeholder="Nhập địa chỉ nhận hàng"
                                value="{{ old('address', $userInfo->address ?? '') }}">
                        </div>
                        <div class="form-group">
                            <input class="input" type="number" name="phone" placeholder="Nhập số điện thoại"
                                value="{{ old('phone', $userInfo->phone ?? '') }}">
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
                                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                                            tempor
                                            incididunt.</p>
                                        <input class="input" type="password" name="password"
                                            placeholder="Enter Your Password">
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <!-- /Billing Details -->

                    <!-- Shiping Details -->
                    <div class="shiping-details">
                        <div class="section-title">
                            <h3 class="title">Shiping address</h3>
                        </div>
                        <div class="input-checkbox">
                            <input type="checkbox" id="shiping-address">
                            <label for="shiping-address">
                                <span></span>
                                Ship to a diffrent address?
                            </label>
                            <div class="caption">
                                <div class="form-group">
                                    <input class="input" type="text" name="first-name" placeholder="First Name">
                                </div>
                                <div class="form-group">
                                    <input class="input" type="text" name="last-name" placeholder="Last Name">
                                </div>
                                <div class="form-group">
                                    <input class="input" type="email" name="email" placeholder="Email">
                                </div>
                                <div class="form-group">
                                    <input class="input" type="text" name="address" placeholder="Address">
                                </div>
                                <div class="form-group">
                                    <input class="input" type="text" name="city" placeholder="City">
                                </div>
                                <div class="form-group">
                                    <input class="input" type="text" name="country" placeholder="Country">
                                </div>
                                <div class="form-group">
                                    <input class="input" type="text" name="zip-code" placeholder="ZIP Code">
                                </div>
                                <div class="form-group">
                                    <input class="input" type="tel" name="tel" placeholder="Telephone">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Shiping Details -->

                    <!-- Order notes -->
                    <div class="order-notes">
                        <textarea class="input" placeholder="Order Notes"></textarea>
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
                                    <div>{{ $item->qty }}x {{ Str::limit($item->product->title, 30, '...') }}</div>
                                    <div>{{ number_format($item->price) . ' VNĐ' }}</div>
                                </div>
                            @endforeach

                        </div>
                        <div class="order-col">
                            <div>Vận chuyển</div>
                            <div><strong>Miễn phí</strong></div>
                        </div>
                        <div class="order-col">
                            <div><strong>Tổng tiền</strong></div>
                            <div><strong class="order-total">{{ number_format($total) . ' VNĐ' }}</strong></div>
                        </div>
                    </div>
                    <div class="payment-method">
                        <div class="input-radio">
                            <input type="radio" name="payment" id="payment-1">
                            <label for="payment-1">
                                <span></span>
                                Thanh toán tiền mặt
                            </label>
                            <div class="caption">
                                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor
                                    incididunt ut labore et dolore magna aliqua.</p>
                            </div>
                        </div>
                        <div class="input-radio">
                            <input type="radio" name="payment" id="payment-2">
                            <label for="payment-2">
                                <span></span>
                                Cheque Payment
                            </label>
                            <div class="caption">
                                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor
                                    incididunt ut labore et dolore magna aliqua.</p>
                            </div>
                        </div>
                        <div class="input-radio">
                            <input type="radio" name="payment" id="payment-3">
                            <label for="payment-3">
                                <span></span>
                                Paypal System
                            </label>
                            <div class="caption">
                                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor
                                    incididunt ut labore et dolore magna aliqua.</p>
                            </div>
                        </div>
                    </div>
                    <div class="input-checkbox">
                        <input type="checkbox" id="terms">
                        <label for="terms">
                            <span></span>Tôi đã đọc và chấp nhận các <a href="#">điều khoản và điều kiện</a>
                        </label>
                    </div>
                    <a href="#" class="primary-btn order-submit">Đặt hàng</a>
                </div>
                <!-- /Order Details -->
            </div>
            <!-- /row -->
        </div>
        <!-- /container -->
    </div>
    <!-- /SECTION -->
</x-guest-layout>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const districtsUrl = "{{ route('getDistricts', ['provinceId' => ':provinceId']) }}";
        const wardsUrl = "{{ route('getWards', ['districtId' => ':districtId']) }}";
        const provinceSelect = document.getElementById('province');
        const districtSelect = document.getElementById('district');
        const wardSelect = document.getElementById('ward');

        // Load districts khi trang được tải nếu đã có province_id
        if (provinceSelect.value) {
            loadDistricts(provinceSelect.value);
        }

        // Load wards khi trang được tải nếu đã có district_id
        if (districtSelect.value) {
            loadWards(districtSelect.value);
        }

        provinceSelect.addEventListener('change', function() {
            const provinceId = this.value;
            districtSelect.innerHTML = '<option value="">Chọn Quận/Huyện</option>';
            wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
            wardSelect.disabled = true;

            if (provinceId) {
                loadDistricts(provinceId);
            } else {
                districtSelect.disabled = true;
            }
        });

        districtSelect.addEventListener('change', function() {
            const districtId = this.value;
            wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';

            if (districtId) {
                loadWards(districtId);
            } else {
                wardSelect.disabled = true;
            }
        });

        function loadDistricts(provinceId) {
            const url = districtsUrl.replace(':provinceId', provinceId);

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    districtSelect.disabled = false;
                    data.forEach(district => {
                        const option = document.createElement('option');
                        option.value = district.code;
                        option.textContent = district.name;

                        // Thêm selected nếu là district_id của user
                        if (district.code == "{{ $userInfo->district_id ?? '' }}") {
                            option.selected = true;
                            // Tự động load wards khi đã có district_id
                            loadWards(district.code);
                        }

                        districtSelect.appendChild(option);
                    });
                });
        }

        function loadWards(districtId) {
            const url = wardsUrl.replace(':districtId', districtId);

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    wardSelect.disabled = false;
                    data.forEach(ward => {
                        const option = document.createElement('option');
                        option.value = ward.code;
                        option.textContent = ward.name;

                        // Thêm selected nếu là ward_id của user
                        if (ward.code == "{{ $userInfo->ward_id ?? '' }}") {
                            option.selected = true;
                        }

                        wardSelect.appendChild(option);
                    });
                });
        }
    });
    // Kích hoạt district select nếu đã có province_id
    if ("{{ $userInfo->province_id ?? '' }}") {
        districtSelect.disabled = false;
    }

    // Kích hoạt ward select nếu đã có district_id
    if ("{{ $userInfo->district_id ?? '' }}") {
        wardSelect.disabled = false;
    }
</script>
