<x-app-layout>
    <?php $title = isset($coupon->id) ? 'Cập nhật thương hiệu' : 'Thêm thương hiệu'; ?>
    @section('title', $title)
    <div class="container-fluid">
        <div class="card p-4 bg-white shadow-sm rounded">
            @if (isset($brand->id))
                <h1 class="h3 mb-4 text-gray-800">Cập nhật thương hiệu</h1>
            @else
                <h1 class="h3 mb-4 text-gray-800">Thêm thương hiệu</h1>
            @endif


            <form action="{{ isset($brand->id) ? route('admin.brand.update', $brand->id) : route('admin.brand.store') }}"
                method="POST" enctype="multipart/form-data">
                @csrf
                @if (isset($brand->id))
                    @method('PUT')
                @endif

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Tên thương hiệu</label>
                        <input type="text" name="name" id="name"
                            class="form-control @error('slug') is-invalid @enderror" required
                            placeholder="Nhập tên thương hiệu" value="{{ old('name', $brand->name ?? '') }}">
                        @error('slug')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="slug" class="form-label">Slug</label>
                        <input type="text" id="slug" class="form-control" readonly placeholder="Slug"
                            value="{{ old('slug', $brand->slug ?? '') }}">
                        <input type="hidden" name="slug" id="slug-hidden"
                            value="{{ old('slug', $brand->slug ?? '') }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <!-- Status -->
                    <div class="col-md-6">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select name="status" id="status" class="form-control">
                            <option value="1" {{ old('status', $brand->status ?? '') == 1 ? 'selected' : '' }}>
                                Hiển thị</option>
                            <option value="0" {{ old('status', $brand->status ?? '') == 0 ? 'selected' : '' }}>
                                Ẩn</option>
                        </select>
                        @error('status')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    {{ isset($brand->id) ? 'Cập nhật' : 'Thêm mới' }}</button>
                <a href="{{ route('admin.brand.index') }}" class="btn btn-secondary">Trở về</a>
            </form>
        </div>
    </div>

    <script>
        function slugify(str) {
            return str
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .toLowerCase()
                .trim()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
        }

        document.getElementById('name').addEventListener('input', function() {
            const nameValue = this.value;
            const slugValue = slugify(nameValue);
            document.getElementById('slug').value = slugValue;
            document.getElementById('slug-hidden').value = slugValue;
        });
    </script>
</x-app-layout>
