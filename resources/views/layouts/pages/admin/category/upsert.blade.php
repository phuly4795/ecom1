<x-app-layout>
    <?php $title = isset($category->id) ? 'Cập nhật danh mục' : 'Thêm danh mục'; ?>
    @section('title', $title)
    <div class="container-fluid">
        <div class="card p-4 bg-white shadow-sm rounded">
            @if (isset($category->id))
                <h1 class="h3 mb-4 text-gray-800">Cập nhật danh mục</h1>
            @else
                <h1 class="h3 mb-4 text-gray-800">Thêm danh mục</h1>
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
                action="{{ isset($category->id) ? route('admin.category.update', $category->id) : route('admin.category.store') }}"
                method="POST" enctype="multipart/form-data">
                @csrf
                @if (isset($category->id))
                    @method('PUT')
                @endif

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Tên danh mục</label>
                        <input type="text" name="name" id="name"
                            class="form-control @error('slug') is-invalid @enderror" required
                            placeholder="Nhập tên danh mục" value="{{ old('name', $category->name ?? '') }}">
                        @error('slug')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="slug" class="form-label">Slug</label>
                        <input type="text" id="slug" class="form-control" readonly placeholder="Slug"
                            value="{{ old('slug', $category->slug ?? '') }}">
                        <input type="hidden" name="slug" id="slug-hidden"
                            value="{{ old('slug', $category->slug ?? '') }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="image-dropzone" class="form-label">Hình ảnh</label>
                        <div class="dropzone" id="image-dropzone"></div>
                        <input type="hidden" name="image" id="image-hidden"
                            value="{{ old('image', $category->image ?? '') }}">
                        @error('image')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <!-- Status -->
                    <div class="col-md-6">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select name="status" id="status" class="form-control mb-3">
                            <option value="1" {{ old('status', $category->status ?? '') == 1 ? 'selected' : '' }}>
                                Hiển thị</option>
                            <option value="0" {{ old('status', $category->status ?? '') == 0 ? 'selected' : '' }}>
                                Ẩn</option>
                        </select>

                        <label for="status" class="form-label">Vị trí hiển thị</label>
                        <select name="sort" id="sort" class="form-control" required>
                            @for ($i = 1; $i <= $maxPosition; $i++)
                                <option value="{{ $i }}"
                                    {{ old('sort', $category->sort ?? $maxPosition) == $i ? 'selected' : '' }}>
                                    Vị trí {{ $i }}
                                </option>
                            @endfor
                        </select>
                    </div>

                </div>

                <button type="submit" class="btn btn-primary">
                    {{ isset($category->id) ? 'Cập nhật' : 'Thêm mới' }}</button>
                <a href="{{ route('admin.category.index') }}" class="btn btn-secondary">Trở về</a>
            </form>
        </div>
    </div>

    <script>
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

        Dropzone.autoDiscover = false;
        const dropzone = new Dropzone("#image-dropzone", {
            url: "{{ route('admin.category.uploadImage') }}",
            method: 'post',
            paramName: 'file',
            maxFiles: 1,
            acceptedFiles: 'image/*',
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            init: function() {
                @if (!empty(old('image', $category->image ?? '')))
                    var mockFile = {
                        name: "Image",
                        size: 12345
                    };
                    this.emit("addedfile", mockFile);
                    this.emit("thumbnail", mockFile,
                        "{{ asset('storage/' . old('image', $category->image ?? '')) }}");
                    this.emit("complete", mockFile);
                    this.files.push(mockFile);
                @endif
            },
            success: function(file, response) {
                document.getElementById('image-hidden').value = response.filePath;
                this.emit("thumbnail", file, response.url);
            },
            removedfile: function(file) {
                file.previewElement.remove();
                document.getElementById('image-hidden').value = '';
            }
        });
    </script>
    <style>
        #image-dropzone .dz-image {
            width: 150px;
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border: 1px dashed #ccc;
            background: #fafafa;
        }

        #image-dropzone .dz-image img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 0.5rem;
            background-color: #f8f9fa;
        }
    </style>
</x-app-layout>
