<x-app-layout>
    <?php $title = isset($coupon->id) ? 'Cập nhật khuyến mãi' : 'Thêm khuyến mãi'; ?>
    @section('title', $title)
    <div class="container-fluid">
        <div class="card p-4 bg-white shadow-sm rounded">
            @if (isset($coupon->id))
                <h1 class="h3 mb-4 text-gray-800">Cập nhật khuyến mãi</h1>
            @else
                <h1 class="h3 mb-4 text-gray-800">Thêm khuyến mãi</h1>
            @endif

            {{-- @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif --}}

            <form
                action="{{ isset($coupon->id) ? route('admin.coupons.update', $coupon->id) : route('admin.coupons.store') }}"
                method="POST" enctype="multipart/form-data">
                @csrf
                @if (isset($coupon->id))
                    @method('PUT')
                @endif

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="code" class="form-label">Mã khuyến mãi</label>
                        <input type="text" name="code" id="code"
                            class="form-control  @error('code') is-invalid @enderror" required
                            placeholder="Nhập mã khuyến mãi" value="{{ old('code', $coupon->code ?? '') }}">
                        @error('code')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="description" class="form-label">Mô tả</label>
                        <input type="text" name="description" id="description" name="description"
                            class="form-control " placeholder="Nhập mô tả khuyến mãi">
                        @error('description')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="type" class="form-label">Loại khuyến mãi</label>
                        <select name="type" id="type" class="form-control @error('type') is-invalid @enderror">
                            <option value="-1">Chọn loại khuyến mãi</option>
                            <option value="fixed" {{ old('type', $coupon->type ?? '') == 'fixed' ? 'selected' : '' }}>
                                Giảm thẳng
                            </option>
                            <option value="percent"
                                {{ old('type', $coupon->type ?? '') == 'percent' ? 'selected' : '' }}>
                                Giảm phần trăm
                            </option>
                        </select>
                        @error('type')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="col-md-6">
                        <label for="value" class="form-label">Giá trị</label>
                        <input type="number" name="value" value="{{ old('value', $coupon->value ?? '') }}"
                            class="form-control  @error('value') is-invalid @enderror"
                            placeholder="Nhập giá trị giảm giá">
                        @error('value')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="start_date" class="form-label">Ngày bắt đầu</label>
                        <input type="datetime-local" name="start_date" id="start_date"
                            class="form-control @error('start_date') is-invalid @enderror"
                            value="{{ old('start_date', isset($coupon) ? $coupon->start_date->format('Y-m-d H:i:s') : '') }}">
                        @error('start_date')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="end_date" class="form-label">Ngày kết thúc</label>
                        <input type="datetime-local" name="end_date" id="end_date"
                            class="form-control @error('end_date') is-invalid @enderror"
                            value="{{ old('end_date', isset($coupon) ? $coupon->end_date->format('Y-m-d H:i:s') : '') }}">
                        @error('end_date')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="usage_limit" class="form-label">Số lượng mã</label>
                        <input type="number" name="usage_limit" id="usage_limit"
                            class="form-control @error('usage_limit') is-invalid @enderror"
                            value="{{ old('usage_limit', $coupon->usage_limit ?? '') }}"
                            placeholder="Nhập số lượng mã">
                        @error('usage_limit')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="is_active" class="form-label">Trạng thái</label>
                        <select name="is_active" id="is_active" class="form-control">
                            <option value="1"
                                {{ old('is_active', $coupon->is_active ?? '') == 1 ? 'selected' : '' }}>
                                Hiển thị</option>
                            <option value="0"
                                {{ old('is_active', $coupon->is_active ?? '') == 0 ? 'selected' : '' }}>
                                Ẩn</option>
                        </select>
                        @error('is_active')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">
                    {{ isset($coupon->id) ? 'Cập nhật' : 'Thêm mới' }}</button>
                <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">Trở về</a>
            </form>
        </div>
    </div>

    <script></script>
</x-app-layout>
