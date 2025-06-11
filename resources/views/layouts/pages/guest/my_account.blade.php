<x-guest-layout>
    @section('title', 'Tài khoản của tôi')

    <!-- BREADCRUMB -->
    <div id="breadcrumb" class="section">
        <!-- container -->
        <div class="container">
            <!-- row -->
            <div class="row">
                <div class="col-md-12">
                    <ul class="breadcrumb-tree">
                        @foreach ($breadcrumbs as $breadcrumb)
                            @if ($loop->last)
                                <li class="active">{{ $breadcrumb['name'] }}</li>
                            @else
                                <li><a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['name'] }}</a></li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
            <!-- /row -->
        </div>
        <!-- /container -->
    </div>
    <!-- /BREADCRUMB -->

    <!-- Account Section -->
    <section class="account-section py-5">
        <div class="container">
            <div class="row">
                <!-- Sidebar Menu -->
                <div class="col-md-3">
                    <div class="account-menu">
                        <h4 class="menu-title">Tài khoản của tôi</h4>
                        <ul class="list-group">
                            <li class="list-group-item"><a href="{{ route('my.account') }}?tab=profile"
                                    class="{{ request()->query('tab', 'profile') == 'profile' ? 'active' : '' }}"><i
                                        class="fa fa-user"></i> Hồ sơ cá nhân</a></li>
                            <li class="list-group-item"><a href="{{ route('my.account') }}?tab=orders"
                                    class="{{ request()->query('tab') == 'orders' ? 'active' : '' }}"><i
                                        class="fa fa-shopping-cart"></i> Đơn hàng của tôi</a></li>
                            <li class="list-group-item"><a href="{{ route('my.account') }}?tab=wishlist"
                                    class="{{ request()->query('tab') == 'wishlist' ? 'active' : '' }}"><i
                                        class="fa fa-heart"></i> Danh sách yêu thích</a></li>
                            <li class="list-group-item"><a href="{{ route('my.account') }}?tab=change-password"
                                    class="{{ request()->query('tab') == 'change-password' ? 'active' : '' }}"><i
                                        class="fa fa-lock"></i> Thay đổi mật khẩu</a></li>
                            <li class="list-group-item">
                                <a href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fa fa-sign-out"></i> Đăng xuất
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                    style="display: none;">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="col-md-9">
                    <div class="account-content">
                        @php
                            $tab = request()->query('tab', 'profile'); // Mặc định là 'profile' nếu không có tab
                        @endphp

                        @if ($tab == 'orders')
                            <h2 class="account-welcome">Đơn hàng của tôi</h2>
                            @if ($orders->isEmpty())
                                <p class="account-description">Bạn chưa có đơn hàng nào. Hãy bắt đầu mua sắm ngay!</p>
                            @else
                                <div class="table-responsive">
                                    <table class="table order-table">
                                        <thead>
                                            <tr>
                                                <th>Mã đơn hàng</th>
                                                <th>Ngày đặt</th>
                                                <th>Trạng thái</th>
                                                <th>Tổng tiền</th>
                                                <th>Hành động</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($orders as $order)
                                                <tr>
                                                    <td>{{ $order->order_code }}</td>
                                                    <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                                    <td>
                                                        <span
                                                            class="badge {{ match ($order->status) {
                                                                'pending' => 'bg-warning',
                                                                'processing' => 'bg-info',
                                                                'completed' => 'bg-success',
                                                                'delivered' => 'bg-success',
                                                                'cancelled' => 'bg-danger',
                                                                default => 'bg-secondary',
                                                            } }}">
                                                            {{ match ($order->status) {
                                                                'pending' => 'Đang chờ',
                                                                'processing' => 'Đang xử lý',
                                                                'completed' => 'Hoàn thành',
                                                                'delivered' => 'Đã giao',
                                                                'cancelled' => 'Đã hủy',
                                                                default => ucfirst($order->status),
                                                            } }}
                                                        </span>
                                                    </td>
                                                    <td>{{ number_format($order->total_amount, 0, ',', '.') }} VNĐ</td>
                                                    <td>
                                                        <button type="button" class="btn btn-info btn-sm view-details"
                                                            data-toggle="modal" data-target="#orderDetailModal"
                                                            data-order-id="{{ $order->id }}">
                                                            Xem chi tiết
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div class="mt-4">
                                        {{ $orders->appends(['tab' => 'orders'])->links() }}
                                    </div>
                                </div>
                            @endif
                        @elseif ($tab == 'profile')
                            <h2 class="account-welcome">Hồ sơ cá nhân</h2>
                            {{-- <p class="account-description">Xem và chỉnh sửa thông tin cá nhân của bạn tại đây.</p> --}}
                            <div class="rounded bg-white">
                                <div class="row">
                                    <div class="col-md-3">
                                        <!-- Phần hiển thị avatar -->
                                        <div class="d-flex flex-column align-items-center text-center p-3 py-5">
                                            <img id="preview-avatar" class="rounded-circle mt-5" width="150px"
                                                src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://st3.depositphotos.com/15648834/17930/v/600/depositphotos_179308454-stock-illustration-unknown-person-silhouette-glasses-profile.jpg' }}">
                                            <input type="file" name="avatar" id="avatar"
                                                class="form-control mt-3" accept="image/*">
                                            <span class="text-muted small">Chỉ chấp nhận ảnh PNG, JPG, JPEG (tối đa
                                                2MB)</span>
                                        </div>

                                    </div>
                                    <div class="col-md-9">
                                        @method('PUT')
                                        <div class="p-3 py-5">
                                            <div class="row mt-2">
                                                <div class="col-md-12"><label class="labels">Họ và tên</label><input
                                                        type="text" class="form-control" placeholder="Nhập họ và tên"
                                                        name="name" value="{{ $user->name ?? '' }}"></div>
                                                <div class="col-md-12"><label class="labels">Email</label><input
                                                        type="text" class="form-control" placeholder="Nhập email"
                                                        name="email" value="{{ $user->email ?? '' }}" readonly></div>
                                                <div class="col-md-12"><label class="labels">Số điện thoại</label><input
                                                        type="text" class="form-control"
                                                        placeholder="Nhập số điện thoại" name="phone"
                                                        value="{{ $user->phone ?? '' }}">
                                                </div>
                                                <div class="col-md-12"><label class="labels">Ngày sinh</label><input
                                                        type="date" class="form-control" name="birthday"
                                                        value="{{ $user->birthday ? \Carbon\Carbon::parse($user->birthday)->format('Y-m-d') : '' }}">
                                                </div>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-md-6">
                                                    <label class="labels">Tỉnh/Thành phố</label>
                                                    <select id="province" name="province_id" class="form-control">
                                                        <option value="">Chọn Tỉnh/Thành phố</option>
                                                        @foreach ($provinces as $province)
                                                            <option value="{{ $province->code }}"
                                                                {{ $user->province_id == $province->code ? 'selected' : '' }}>
                                                                {{ $province->full_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-6"><label class="labels">Quận/Huyện</label>
                                                    <select id="district" name="district_id" class="form-control"
                                                        disabled>
                                                        <option value="">Chọn Quận/Huyện</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-md-6"><label class="labels">Phường/Xã</label>
                                                    <select id="ward" name="ward_id" class="form-control"
                                                        disabled>
                                                        <option value="">Chọn Phường/Xã</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6"><label class="labels">Địa chỉ</label>
                                                    <input type="text" name="address" class="form-control"
                                                        placeholder="Nhập địa chỉ"
                                                        value="{{ $user->address ?? '' }}">
                                                </div>
                                            </div>
                                            <div class="mt-5 text-center"><button
                                                    class="btn btn-primary profile-button" type="button">Lưu hồ
                                                    sơ</button></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif ($tab == 'wishlist')
                            <!-- Wishlist (Placeholder) -->
                            <h2 class="account-welcome">Wishlist</h2>
                            <p class="account-description">Xem danh sách sản phẩm yêu thích của bạn tại đây.</p>
                        @elseif ($tab == 'change-password')
                            <!-- Change Password (Placeholder) -->
                            <h2 class="account-welcome">Change Password</h2>
                            <p class="account-description">Thay đổi mật khẩu của bạn tại đây.</p>
                        @else
                            <!-- Default Welcome Message -->
                            <h2 class="account-welcome">Welcome to Your Account</h2>
                            <p class="account-description">Chào mừng bạn! Hãy chọn một tùy chọn bên trái để quản lý
                                thông tin cá nhân, đơn hàng, danh sách yêu thích hoặc thay đổi mật khẩu.</p>
                        @endif

                        <!-- Modal -->
                        <div class="modal fade" id="orderDetailModal" tabindex="-1"
                            aria-labelledby="orderDetailModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="orderDetailModalLabel">Chi tiết đơn hàng</h5>
                                        <button type="button" class="close" data-dismiss="modal"
                                            aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body" id="orderDetailContent">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Đóng</button>
                                        <a href="#" id="trackOrderLink" class="btn btn-primary"
                                            style="display: none;">Theo dõi</a>
                                        @if (isset($order) && ($order->status === 'pending' || $order->status === 'processing'))
                                            <button type="button" class="btn btn-danger cancel-order"
                                                data-order-id="{{ $order->id }}">Hủy đơn</button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-guest-layout>
<style>
    .account-section {
        background-color: #f5f7fa;
        padding: 50px 0;
    }

    .account-menu {
        background-color: #ffffff;
        border: 1px solid #e1e4e8;
        border-radius: 8px;
        padding: 25px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .menu-title {
        font-size: 20px;
        margin-bottom: 20px;
        font-weight: 600;
        color: #2c3e50;
        border-bottom: 2px solid #17a2b8;
        padding-bottom: 10px;
    }

    .list-group-item {
        background-color: transparent;
        border: none;
        padding: 12px 0;
    }

    .list-group-item a {
        color: #495057;
        text-decoration: none;
        display: flex;
        align-items: center;
        font-size: 16px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .list-group-item a i {
        margin-right: 12px;
        color: #17a2b8;
    }

    .list-group-item a:hover,
    .list-group-item a.active {
        color: #17a2b8;
        transform: translateX(5px);
    }

    .account-content {
        background-color: #ffffff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        border: 1px solid #e1e4e8;
    }

    .account-welcome {
        font-size: 28px;
        margin-bottom: 15px;
        color: #2c3e50;
        font-weight: 600;
    }

    .account-description {
        font-size: 16px;
        color: #6c757d;
        line-height: 1.6;
    }

    .breadcrumb {
        background-color: #ffffff;
        padding: 15px 0;
        border-bottom: 1px solid #e1e4e8;
    }

    .breadcrumb .breadcrumb-item a {
        color: #17a2b8;
        font-weight: 500;
    }

    .breadcrumb .breadcrumb-item.active {
        color: #6c757d;
    }

    .container {
        max-width: 1200px;
    }

    /* CSS cho bảng đơn hàng */
    .order-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 15px;
    }

    .order-table thead {
        background-color: #f8f9fa;
        color: #2c3e50;
        font-weight: 600;
    }

    .order-table th,
    .order-table td {
        padding: 15px;
        text-align: left;
        border-bottom: 1px solid #e1e4e8;
    }

    .order-table th {
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .order-table tbody tr:hover {
        background-color: #f9fbfc;
    }

    .order-table .badge {
        padding: 6px 12px;
        border-radius: 12px;
        color: #fff;
        font-size: 14px;
        font-weight: 500;
    }

    .order-table .bg-warning {
        background-color: #f39c12;
    }

    .order-table .bg-info {
        background-color: #3498db;
    }

    .order-table .bg-success {
        background-color: #2ecc71;
    }

    .order-table .bg-danger {
        background-color: #e74c3c;
    }

    .order-table .bg-secondary {
        background-color: #7f8c8d;
    }

    .view-details {
        padding: 5px 10px;
        font-size: 14px;
    }

    /* CSS cho modal */
    .modal-content {
        border-radius: 8px;
        border: 1px solid #e1e4e8;
    }

    .modal-header {
        border-bottom: 1px solid #e1e4e8;
        background-color: #f8f9fa;
    }

    .modal-body {
        padding: 20px;
        color: #495057;
    }

    .cancel-order {
        margin-right: 10px;
    }
</style>


<script>
    document.querySelectorAll('.view-details').forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.dataset.orderId;
            const modalBody = document.getElementById('orderDetailContent');
            const modalFooter = document.querySelector('#orderDetailModal .modal-footer');
            modalBody.innerHTML = '<p>Đang tải dữ liệu...</p>';

            fetch(`/account/orders/${orderId}`)
                .then(res => res.json())
                .then(data => {
                    const order = data.order;
                    const items = data.items;

                    // Điền nội dung modal
                    modalBody.innerHTML = `
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="font-weight-bold text-primary">Địa chỉ thanh toán</h6>
                                    <p><strong>Khách hàng:</strong> ${order.billing_full_name || 'N/A'}</p>
                                    <p><strong>Email:</strong> ${order.billing_email || 'N/A'}</p>
                                    <p><strong>Số điện thoại:</strong> ${order.billing_telephone || 'N/A'}</p>
                                    <p><strong>Địa chỉ:</strong>
                                        ${[
                                            order.billing_address,
                                            order.billingWard?.name,
                                            order.billingDistrict?.name,
                                            order.billingProvince?.name
                                        ].filter(Boolean).join(', ') || 'N/A'}
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="font-weight-bold text-primary">Địa chỉ nhận hàng</h6>
                                    ${order.shippingAddress ? `
                                        <p><strong>Khách hàng:</strong> ${order.shippingAddress.full_name || 'N/A'}</p>
                                        <p><strong>Email:</strong> ${order.shippingAddress.email || 'N/A'}</p>
                                        <p><strong>Số điện thoại:</strong> ${order.shippingAddress.telephone || 'N/A'}</p>
                                        <p><strong>Địa chỉ:</strong>
                                            ${[
                                                order.shippingAddress.address,
                                                order.shippingAddress.ward?.name,
                                                order.shippingAddress.district?.name,
                                                order.shippingAddress.province?.name
                                            ].filter(Boolean).join(', ') || 'N/A'}
                                        </p>
                                    ` : `<p>Không có địa chỉ nhận hàng riêng, sử dụng địa chỉ thanh toán.</p>`}
                                </div>
                            </div>
                            <h6 class="m-0 font-weight-bold text-primary">Chi tiết sản phẩm</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Hình ảnh</th>
                                            <th>Sản phẩm</th>
                                            <th>Biến thểroot
                                            <th>Giá</th>
                                            <th>Số lượng</th>
                                            <th>Tổng</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${items.length > 0
                                            ? items.map(item => `
                                                <tr>
                                                    <td>
                                                        <img src="${item.image || 'https://via.placeholder.com/60'}" 
                                                             alt="Ảnh sản phẩm" 
                                                             style="width: 60px; height: 60px; object-fit: cover;" 
                                                             class="img-thumbnail" />
                                                    </td>
                                                    <td>${item.name || 'N/A'}</td>
                                                    <td>${item.variant_name || '-'}</td>
                                                    <td>${item.price || '0'}</td>
                                                    <td>${item.quantity || '0'}</td>
                                                    <td>${item.total_price || '0'}</td>
                                                </tr>
                                            `).join('')
                                            : `<tr><td colspan="6" class="text-center">Không có sản phẩm</td></tr>`
                                        }
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                `;

                    // Thêm nút hủy có điều kiện vào footer modal
                    const existingCancelButton = modalFooter.querySelector('.cancel-order');
                    if (existingCancelButton) {
                        existingCancelButton.remove();
                    }

                    if (order.status === 'pending' || order.status === 'processing') {
                        const cancelButton = document.createElement('button');
                        cancelButton.type = 'button';
                        cancelButton.className = 'btn btn-danger cancel-order';
                        cancelButton.dataset.orderId = order.id;
                        cancelButton.textContent = 'Hủy đơn';
                        modalFooter.insertBefore(cancelButton, modalFooter.querySelector(
                            '.btn-secondary'));
                    }
                })
                .catch(() => {
                    modalBody.innerHTML = '<p>Lỗi tải chi tiết đơn hàng.</p>';
                });
        });
    });
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('cancel-order')) {
            const orderId = event.target.dataset.orderId;
            if (confirm('Bạn có chắc muốn hủy đơn hàng này?')) {
                fetch(`/account/orders/${orderId}/cancel`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            alert('Đơn hàng đã được hủy.');
                            location.reload();
                        } else {
                            alert('Lỗi khi hủy đơn hàng: ' + (data.message || 'Không xác định'));
                        }
                    })
                    .catch(() => alert('Lỗi khi hủy đơn hàng.'));
            }
        }
    });
</script>
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
                        if (district.code == "{{ $user->district_id }}") {
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
                        if (ward.code == "{{ $user->ward_id }}") {
                            option.selected = true;
                        }

                        wardSelect.appendChild(option);
                    });
                });
        }
    });
    // Kích hoạt district select nếu đã có province_id
    if ("{{ $user->province_id }}") {
        districtSelect.disabled = false;
    }

    // Kích hoạt ward select nếu đã có district_id
    if ("{{ $user->district_id }}") {
        wardSelect.disabled = false;
    }
</script>
<script>
    $('.profile-button').click(function(e) {
        e.preventDefault();
        var formData = new FormData();

        formData.append('name', $('input[name="name"]').val());
        formData.append('email', $('input[name="email"]').val());
        formData.append('phone', $('input[name="phone"]').val());
        formData.append('birthday', $('input[name="birthday"]').val());
        formData.append('province_id', $('#province').val());
        formData.append('district_id', $('#district').val());
        formData.append('ward_id', $('#ward').val());
        formData.append('address', $('input[name="address"]').val());
        formData.append('is_active', $('#status').val());
        formData.append('user_roles', $('#role').val());
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', 'PUT');
        const avatarFile = $('#avatar')[0].files[0];
        if (avatarFile) {
            formData.append('avatar', avatarFile);
        }
        $.ajax({
            url: '{{ route('admin.profile.updateInfo') }}',
            type: 'POST',
            data: formData,
            processData: false, // Bắt buộc khi dùng FormData
            contentType: false, // Bắt buộc khi dùng FormData
            success: function(response) {
                if (response.success) {
                    showAlertModal(response.message, 'success');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                }
            },
            error: function(xhr) {
                var errors = xhr.responseJSON?.errors;
                if (errors) {
                    var errorMessage = '';
                    $.each(errors, function(key, value) {
                        errorMessage += value[0] + '\n';
                    });
                    showAlertModal(errorMessage, 'error');
                } else {
                    showAlertModal('Đã xảy ra lỗi khi cập nhật', 'error');
                }
            }
        });
    });
</script>
<script>
    document.getElementById('avatar').addEventListener('change', function(e) {
        const [file] = e.target.files;
        if (file) {
            document.getElementById('preview-avatar').src = URL.createObjectURL(file);
        }
    });
</script>
