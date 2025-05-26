<x-app-layout>
    <?php $title = isset($product->id) ? 'Cập nhật sản phẩm' : 'Thêm sản phẩm'; ?>
    @section('title', $title)
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 text-gray-800">
                {{ $title }}
            </h1>
            <a href="{{ route('admin.product.index') }}" class="btn btn-primary">Trở về</a>
        </div>

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
            action="{{ isset($product->id) ? route('admin.product.update', $product->id) : route('admin.product.store') }}"
            method="POST" enctype="multipart/form-data">
            @csrf
            @if (isset($product->id))
                @method('PUT')
            @endif

            <div class="row">
                <div class="col-md-8">
                    <div class="card p-4 mb-3 shadow-sm rounded bg-white">
                        <div class="mb-3">
                            <label for="name" class="form-label h5 mb-3" style="font-weight: 700">Tên sản
                                phẩm</label>
                            <input type="text" name="title" id="name" class="form-control" required
                                value="{{ old('title', $product->title ?? '') }}" placeholder="Nhập tên sản phẩm">
                        </div>

                        <div class="mb-3">
                            <label for="slug" class="form-label h5 mb-3" style="font-weight: 700">Slug</label>
                            <input type="text" id="slug" class="form-control" readonly
                                value="{{ old('slug', $product->slug ?? '') }}" placeholder="Slug">
                            <input type="hidden" name="slug" id="slug-hidden"
                                value="{{ old('slug', $product->slug ?? '') }}">
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label h5 mb-3" style="font-weight: 700">Mô tả</label>
                            <textarea id="description" name="description" class="form-control" rows="8">{!! old('description', $product->description ?? '') !!}</textarea>
                        </div>

                    </div>
                    <div class="card p-4 mb-3 shadow-sm rounded bg-white">
                        <div class="mb-3">
                            <label for="image-dropzone" class="form-label h5 mb-3" style="font-weight: 700">Hình ảnh đại
                                diện</label>
                            <div class="dropzone" id="image-dropzone"></div>
                            <input type="hidden" name="image" id="image-hidden"
                                value="{{ old('image', $category->image ?? '') }}">
                            @error('image')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="image-dropzone-thumbnail" class="form-label h5 mb-3"
                                style="font-weight: 700">Hình ảnh thumbnail</label>
                            <div class="dropzone" id="image-dropzone-thumbnail"></div>
                            <input type="hidden" name="imageThumbnail" id="image-hidden"
                                value="{{ old('image', $category->image ?? '') }}">
                            @error('image')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="card p-4 mb-3 shadow-sm rounded bg-white">
                        <div class="mb-3">
                            <label for="image-dropzone" class="form-label h5 mb-3" style="font-weight: 700">Giá sản
                                phẩm</label>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="price" class="form-label">Giá bán</label>
                                    <input type="number" name="price" id="price" class="form-control" required
                                        placeholder="Nhập giá bán sản phẩm"
                                        value="{{ old('price', $product->price ?? '') }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="compare_price" class="form-label">Giá giảm</label>
                                    <input type="number" name="compare_price" id="compare_price" class="form-control"
                                        required placeholder="Nhập giá giảm"
                                        value="{{ old('compare_price', $product->compare_price ?? '') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card p-4 mb-3 shadow-sm rounded bg-white">
                        <div class="mb-3">
                            <label class="form-label h5 mb-3" style="font-weight: 700">Quản lý kho</label>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="sku" class="form-label">SKU (Mã hàng hóa)</label>
                                    <input type="text" name="sku" id="sku" class="form-control"
                                        required placeholder="Nhập mã hàng hóa"
                                        value="{{ old('sku', $product->sku ?? '') }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="barcode" class="form-label">Mã vạch</label>
                                    <input type="text" name="barcode" id="barcode" class="form-control"
                                        placeholder="Nhập mã vạch"
                                        value="{{ old('barcode', $product->barcode ?? $barcode) }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group form-check">
                                        <input type="checkbox" name="track_qty" id="track_qty"
                                            class="form-check-input"
                                            {{ old('track_qty', $product->track_qty ?? 'yes') == 'yes' ? 'checked' : '' }}>
                                        <label for="track_qty" class="form-check-label">Kiểm soát tồn kho</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="qty" class="form-label">Số lượng</label>
                                    <input type="number" name="qty" id="qty" class="form-control"
                                        placeholder="Nhập số lượng" value="{{ old('qty', $product->qty ?? '') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card p-4 mb-3 shadow-sm rounded bg-white">
                        <label for="status" class="form-label h5 mb-3" style="font-weight: 700">Trạng thái</label>
                        <select name="status" id="status" class="form-control">
                            <option value="1" {{ old('status', $product->status ?? '') == 1 ? 'selected' : '' }}>
                                Đang bán</option>
                            <option value="0" {{ old('status', $product->status ?? '') == 0 ? 'selected' : '' }}>
                                Ngừng bán</option>
                        </select>
                    </div>

                    <div class="card p-4 mb-3 shadow-sm rounded bg-white">
                        <div class="mb-3">
                            <label for="category" class="form-label h5 mb-3" style="font-weight: 700">Danh mục sản
                                phẩm</label>
                            <select name="category_id" id="category" class="form-control">
                                <option value="-1" selected>Chọn danh mục sản phẩm</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-0">
                            <label for="subcategory" class="form-label h5 mb-3" style="font-weight: 700">Danh mục
                                phụ</label>
                            <select name="subcategory_id" id="subcategory" class="form-control" disabled>
                            </select>
                        </div>
                    </div>

                    <div class="card p-4 mb-3 shadow-sm rounded bg-white">
                        <div class="mb-3">
                            <label for="brand_id" class="form-label h5 mb-3" style="font-weight: 700">Thương hiệu sản
                                phẩm</label>
                            <select name="brand_id" id="brand_id" class="form-control">
                                @foreach ($brands as $id => $brand)
                                    <option value="{{ $brand->id }}"
                                        {{ old('brand_id', $product->brand_id ?? '') == $brand->id ? 'selected' : '' }}>
                                        {{ $brand->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="card p-4 mb-3 shadow-sm rounded bg-white">
                        <div class="mb-3">
                            <label for="is_featured" class="form-label h5 mb-3" style="font-weight: 700">Sản phẩm nổi
                                bật</label>
                            <select name="is_featured" id="is_featured" class="form-control">
                                <option value="yes"
                                    {{ old('is_featured', $product->is_featured ?? '') == 'yes' ? 'selected' : '' }}>
                                    Nổi bật</option>
                                <option value="no"
                                    {{ old('is_featured', $product->is_featured ?? '') == 'no' ? 'selected' : '' }}>
                                    Không nổi bật</option>
                            </select>
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-success">
                            {{ isset($product->id) ? 'Cập nhật' : 'Thêm mới' }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        ClassicEditor
            .create(document.querySelector('#description'), {
                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote']
            })
            .catch(error => {
                console.error(error);
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

        Dropzone.autoDiscover = false;
        const dropzone = new Dropzone("#image-dropzone", {
            url: "{{ route('admin.product.uploadImage') }}",
            method: 'post',
            paramName: 'file',
            maxFiles: 1,
            acceptedFiles: 'image/*',
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            init: function() {
                @if (!empty(old('image', $image->image ?? '')))
                    var mockFile = {
                        name: "Image",
                        size: 12345
                    };
                    this.emit("addedfile", mockFile);
                    this.emit("thumbnail", mockFile,
                        "{{ asset('storage/' . old('image', $image->image ?? '')) }}");
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

        const dropzoneThumbnail = new Dropzone("#image-dropzone-thumbnail", {
            url: "{{ route('admin.product.uploadImage') }}",
            method: 'post',
            paramName: 'file',
            maxFiles: 10,
            acceptedFiles: 'image/*',
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            init: function() {
                @php
                    $thumbnails = old('imageThumbnail', $imageThumbnail ?? []);
                @endphp
                @if (!empty($thumbnails))
                    @foreach ($thumbnails as $index => $thumb)
                        @php
                            // dd($thumb->image);
                            // Nếu là object, lấy thuộc tính path (hoặc tên cột bạn dùng để lưu đường dẫn ảnh)
                            $filePath = is_string($thumb) ? $thumb : $thumb->image ?? '';
                        @endphp
                        @if (!empty($filePath))
                            var mockFile = {
                                name: "Image",
                                size: 12345 // Bạn có thể dùng $thumb->size nếu có
                            };
                            this.emit("addedfile", mockFile);
                            this.emit("thumbnail", mockFile, "{{ asset('storage/' . $filePath) }}");
                            this.emit("complete", mockFile);
                            this.files.push(mockFile);

                            let hiddenInput{{ $index }} = document.createElement('input');
                            hiddenInput{{ $index }}.type = 'hidden';
                            hiddenInput{{ $index }}.name = 'imageThumbnails[]';
                            hiddenInput{{ $index }}.value = "{{ $filePath }}";
                            document.getElementById('image-dropzone-thumbnail').appendChild(
                                hiddenInput{{ $index }});
                        @endif
                    @endforeach
                @endif
            },
            success: function(file, response) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'imageThumbnails[]';
                input.value = response.filePath;
                file.previewElement.appendChild(input);
            },
            removedfile: function(file) {
                file.previewElement.remove();
                // Xoá input hidden tương ứng
                const inputs = document.querySelectorAll('input[name="imageThumbnails[]"]');
                inputs.forEach(input => {
                    if (input.value === file.upload?.filename || file.name === "Image") {
                        input.remove();
                    }
                });
            }
        });
    </script>
    <script>
        const SubcategoriesUrl = "{{ route('admin.product.getSubcategories', ['category_id' => ':categoryId']) }}";
        const categorySelect = document.getElementById('category');
        const subCategorySelect = document.getElementById('subcategory');

        if (categorySelect.value) {
            loadSubcategories(categorySelect.value);
        }

        categorySelect.addEventListener('change', function() {
            const categoryId = this.value;
            subCategorySelect.innerHTML = '<option value="">Chọn danh mục phụ</option>';
            if (categoryId) {
                loadSubcategories(categoryId);
            } else {
                subCategorySelect.disabled = true;
            }
        });

        function loadSubcategories(categoryId) {
            const url = SubcategoriesUrl.replace(':categoryId', categoryId);

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    subCategorySelect.innerHTML = '<option value="">Chọn danh mục phụ</option>';

                    if (data.length === 0) {
                        subCategorySelect.disabled = true;
                    } else {
                        subCategorySelect.disabled = false;
                        data.forEach(subCategory => {
                            const option = document.createElement('option');
                            option.value = subCategory.id;
                            option.textContent = subCategory.name;

                            if (subCategory.id == "{{ $product->subcategory_id ?? old('subcategory_id') }}") {
                                option.selected = true;
                            }

                            subCategorySelect.appendChild(option);
                        });
                    }
                })
                .catch(error => {
                    console.error('Lỗi khi tải danh mục phụ:', error);
                    subCategorySelect.disabled = true;

                });
        }
    </script>

    <style>
        .ck-editor__editable {
            min-height: 200px;
        }

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

        #image-dropzone-thumbnail .dz-image {
            width: 150px;
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border: 1px dashed #ccc;
            background: #fafafa;
        }

        #image-dropzone-thumbnail .dz-image img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 0.5rem;
            background-color: #f8f9fa;
        }
    </style>
</x-app-layout>
