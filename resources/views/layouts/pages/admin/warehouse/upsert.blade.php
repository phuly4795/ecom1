<x-app-layout>
    <?php $isEdit = isset($shippingFee) && $shippingFee->id; ?>
    @section('title', $isEdit ? 'Cập nhật phí vận chuyển' : 'Thêm phí vận chuyển')

    <div class="container-fluid">
        <div class="card p-4 bg-white shadow-sm rounded">
            <h1 class="h3 mb-4 text-gray-800">
                {{ $isEdit ? 'Cập nhật phí vận chuyển' : 'Thêm phí vận chuyển' }}
            </h1>

            <form
                action="{{ $isEdit ? route('admin.shipping_fees.update', $shippingFee->id) : route('admin.shipping_fees.store') }}"
                method="POST">
                @csrf
                @if ($isEdit)
                    @method('PUT')
                @endif

                <div class="mb-3">
                    <label for="province_id" class="form-label">Tỉnh/Thành phố</label>
                    <select name="province_id" id="province_id" class="form-control" required>
                        <option value="">-- Chọn tỉnh/thành --</option>
                        @foreach ($provinces as $province)
                            <option value="{{ $province->code }}"
                                {{ old('province_id', $shippingFee->province_id ?? '') == $province->code ? 'selected' : '' }}>
                                {{ $province->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="district_id" class="form-label">Quận/Huyện</label>
                    <select name="district_id" id="district_id" class="form-control" required>
                        <option value="">-- Chọn quận/huyện --</option>
                        @if ($isEdit && isset($districts))
                            @foreach ($districts as $district)
                                <option value="{{ $district->code }}"
                                    {{ old('district_id', $shippingFee->district_id ?? '') == $district->code ? 'selected' : '' }}>
                                    {{ $district->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="mb-3">
                    <label for="fee" class="form-label">Phí vận chuyển (VNĐ)</label>
                    <input type="number" name="fee" id="fee" class="form-control"
                        value="{{ old('fee', $shippingFee->fee ?? '') }}" placeholder="Nhập phí vận chuyển" required>
                </div>

                <button type="submit" class="btn btn-primary">
                    {{ $isEdit ? 'Cập nhật' : 'Thêm mới' }}
                </button>
                <a href="{{ route('admin.shipping_fees.index') }}" class="btn btn-secondary">Trở về</a>
            </form>
        </div>
    </div>


    <script>
        document.getElementById('province_id').addEventListener('change', function() {
            let provinceId = this.value;
            let districtSelect = document.getElementById('district_id');
            districtSelect.innerHTML = '<option value="">Đang tải...</option>';

            fetch(`/districts/${provinceId}`)
                .then(res => res.json())
                .then(data => {
                    let options = '<option value="">-- Chọn quận/huyện --</option>';
                    data.forEach(d => {
                        options += `<option value="${d.code}">${d.name}</option>`;
                    });
                    districtSelect.innerHTML = options;
                });
        });
    </script>

</x-app-layout>
