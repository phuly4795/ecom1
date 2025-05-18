<x-app-layout>
    @section('title', 'Hồ sơ cá nhân')
    <div class="rounded bg-white">
        <div class="row">
            <div class="col-md-3 border-right">
                    <!-- Phần hiển thị avatar -->
                    <div class="d-flex flex-column align-items-center text-center p-3 py-5">
                        <img id="preview-avatar" class="rounded-circle mt-5" width="150px"
                            src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://st3.depositphotos.com/15648834/17930/v/600/depositphotos_179308454-stock-illustration-unknown-person-silhouette-glasses-profile.jpg' }}">
                        <input type="file" name="avatar" id="avatar" class="form-control mt-3" accept="image/*">
                        <span class="text-muted small">Chỉ chấp nhận ảnh PNG, JPG, JPEG (tối đa 2MB)</span>
                    </div>
                
            </div>
            <div class="col-md-5 border-right">
                @method('PUT')
                <div class="p-3 py-5">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="text-right">Hồ sơ cá nhân</h4>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12"><label class="labels">Họ và tên</label><input type="text"
                                class="form-control" placeholder="Nhập họ và tên" name="name"
                                value="{{ $user->name ?? '' }}"></div>
                        <div class="col-md-12"><label class="labels">Email</label><input type="text"
                                class="form-control" placeholder="Nhập email" name="email"
                                value="{{ $user->email ?? '' }}" readonly></div>
                        <div class="col-md-12"><label class="labels">Số điện thoại</label><input type="text"
                                class="form-control" placeholder="Nhập số điện thoại" name="phone"
                                value="{{ $user->phone ?? '' }}">
                        </div>
                        <div class="col-md-12"><label class="labels">Ngày sinh</label><input type="date"
                                class="form-control" name="birthday"
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
                            <select id="district" name="district_id" class="form-control" disabled>
                                <option value="">Chọn Quận/Huyện</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6"><label class="labels">Phường/Xã</label>
                            <select id="ward" name="ward_id" class="form-control" disabled>
                                <option value="">Chọn Phường/Xã</option>
                            </select>
                        </div>
                        <div class="col-md-6"><label class="labels">Địa chỉ</label>
                            <input type="text" name="address" class="form-control" placeholder="Nhập địa chỉ"
                                value="{{ $user->address ?? '' }}">
                        </div>
                    </div>
                    <div class="mt-5 text-center"><button class="btn btn-primary profile-button" type="button">Lưu hồ
                            sơ</button></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 py-5">
                    <div class="d-flex justify-content-between align-items-center experience"><span>Trạng thái</span>
                    </div><br>
                    <div class="col-md-12">
                        <select name="status" id="status" class="form-control">
                            <option value="1" {{ old('status', $user->is_active ?? '') == 1 ? 'selected' : '' }}>
                                Kích hoạt</option>
                            <option value="0" {{ old('status', $user->is_active ?? '') == 0 ? 'selected' : '' }}>
                                Khóa tài khoản</option>
                        </select>
                    </div>
                </div>

                <div class="p-3 py-5">
                    <div class="d-flex justify-content-between align-items-center experience"><span>Quyền
                            hạn</span><span class="border px-3 p-1 add-experience"><i class="fa fa-plus"></i>&nbsp;Thêm
                            quyền hạn</span></div><br>
                    <div class="col-md-12">
                        <select name="role" id="role" class="form-control">
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}"
                                    {{ old('role', $user->user_roles ?? '') == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const districtsUrl = "{{ route('admin.getDistricts', ['provinceId' => ':provinceId']) }}";
        const wardsUrl = "{{ route('admin.getWards', ['districtId' => ':districtId']) }}";
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
