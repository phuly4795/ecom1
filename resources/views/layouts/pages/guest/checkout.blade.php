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
                            <input class="input" type="text" name="name" placeholder="Nhập họ và tên">
                        </div>
                        <div class="form-group">
                            <input class="input" type="email" name="email" placeholder="Nhập địa chỉ email">
                        </div>
                        <div class="form-group">
                            <select id="province" name="province_id" class="form-control">
                                <option value="">Chọn Tỉnh/Thành phố</option>
                                @foreach ($provinces as $province)
                                    <option value="{{ $province->code }}"
                                        {{ isset($user) ? ($user->province_id == $province->code ? 'selected' : '') : '' }}>
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
                            <input class="input" type="text" name="address" placeholder="Nhập địa chỉ nhận hàng">
                        </div>
                        <div class="form-group">
                            <input class="input" type="tel" name="tel" placeholder="Nhập số điện thoại">
                        </div>
                        <div class="form-group">
                            <div class="input-checkbox">
                                <input type="checkbox" id="create-account">
                                <label for="create-account">
                                    <span></span>
                                    Create Account?
                                </label>
                                <div class="caption">
                                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor
                                        incididunt.</p>
                                    <input class="input" type="password" name="password"
                                        placeholder="Enter Your Password">
                                </div>
                            </div>
                        </div>
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
                            <div><strong>PRODUCT</strong></div>
                            <div><strong>TOTAL</strong></div>
                        </div>
                        <div class="order-products">
                            <div class="order-col">
                                <div>1x Product Name Goes Here</div>
                                <div>$980.00</div>
                            </div>
                            <div class="order-col">
                                <div>2x Product Name Goes Here</div>
                                <div>$980.00</div>
                            </div>
                        </div>
                        <div class="order-col">
                            <div>Shiping</div>
                            <div><strong>FREE</strong></div>
                        </div>
                        <div class="order-col">
                            <div><strong>TOTAL</strong></div>
                            <div><strong class="order-total">$2940.00</strong></div>
                        </div>
                    </div>
                    <div class="payment-method">
                        <div class="input-radio">
                            <input type="radio" name="payment" id="payment-1">
                            <label for="payment-1">
                                <span></span>
                                Direct Bank Transfer
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
                            <span></span>
                            I've read and accept the <a href="#">terms & conditions</a>
                        </label>
                    </div>
                    <a href="#" class="primary-btn order-submit">Place order</a>
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
                        if (district.code == "{{ $user->district_id ?? '' }}") {
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
                        if (ward.code == "{{ $user->ward_id ?? '' }}") {
                            option.selected = true;
                        }

                        wardSelect.appendChild(option);
                    });
                });
        }
    });
    // Kích hoạt district select nếu đã có province_id
    if ("{{ $user->province_id ?? '' }}") {
        districtSelect.disabled = false;
    }

    // Kích hoạt ward select nếu đã có district_id
    if ("{{ $user->district_id ?? '' }}") {
        wardSelect.disabled = false;
    }
</script>
