<x-app-layout>
    <?php $title = isset($subCategory->id) ? 'Cập nhật danh mục phụ' : 'Thêm danh mục phụ'; ?>
    @section('title', $title)
    <div class="container-fluid">
        <div class="card p-4 bg-white shadow-sm rounded">
            @if (isset($subCategory->id))
                <h1 class="h3 mb-4 text-gray-800">Cập nhật danh mục phụ</h1>
            @else
                <h1 class="h3 mb-4 text-gray-800">Thêm danh mục phụ</h1>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form
                action="{{ isset($subCategory->id) ? route('admin.sub_category.update', $subCategory->id) : route('admin.sub_category.store') }}"
                method="POST" enctype="multipart/form-data">
                @csrf
                @if (isset($subCategory->id))
                    @method('PUT')
                @endif

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Tên danh mục phụ</label>
                        <input type="text" name="name" id="name" class="form-control" required
                            placeholder="Name" value="{{ old('name', $subCategory->name ?? '') }}">
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="slug" class="form-label">Slug</label>
                        <input type="text" id="slug" class="form-control" readonly placeholder="Slug"
                            value="{{ old('slug', $subCategory->slug ?? '') }}">
                        <input type="hidden" name="slug" id="slug-hidden"
                            value="{{ old('slug', $subCategory->slug ?? '') }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="category_ids" class="form-label">Danh mục cha</label>
                        <select name="category_ids[]" multiple class="form-control select2" id="category_ids">
                            @foreach ($category as $cat)
                                <option value="{{ $cat->id }}"
                                    {{ isset($subCategory) && $subCategory->categories->contains($cat->id) ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <!-- Status -->
                    <div class="col-md-6">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select name="status" id="status" class="form-control">
                            <option value="1"
                                {{ old('status', $subCategory->status ?? '') == 1 ? 'selected' : '' }}>
                                Hiển thị</option>
                            <option value="0"
                                {{ old('status', $subCategory->status ?? '') == 0 ? 'selected' : '' }}>
                                Ẩn</option>
                        </select>
                        @error('status')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    {{ isset($subCategory->id) ? 'Cập nhật' : 'Thêm mới' }}</button>
                <a href="{{ route('admin.sub_category.index') }}" class="btn btn-secondary">Trở về</a>
            </form>
        </div>
    </div>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $('#category_ids').select2({
            placeholder: "Chọn danh mục cha",
            allowClear: true
        });

        function slugify(str) {
            return str
                .toLowerCase()
                .replace(/đ/g, 'd')
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
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
