<x-app-layout>
    <?php $title = isset($product->id) ? 'Cập nhật sản phẩm' : 'Thêm sản phẩm'; ?>
    @section('title', $title)
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 text-gray-800">{{ $title }}</h1>
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

                        <div class="mb-3">
                            <label for="specifications" class="form-label h5 mb-3" style="font-weight: 700">Thông số kỹ
                                thuật</label>
                            <textarea name="specifications" id="specifications" class="form-control" rows="5"
                                placeholder="Nhập thông số (ví dụ: CPU: Intel i5, RAM: 8GB...)">{{ old('specifications', $product->specifications ?? '') }}</textarea>
                        </div>
                    </div>

                    <div class="card p-4 mb-3 shadow-sm rounded bg-white">
                        <div class="mb-3">
                            <label for="image-dropzone" class="form-label h5 mb-3" style="font-weight: 700">Hình ảnh đại
                                diện</label>
                            <div class="dropzone" id="image-dropzone"></div>
                            <input type="hidden" name="image" id="image-main-hidden"
                                value="{{ old('image', $image ? $image->image : '') }}">
                            @error('image')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="image-dropzone-thumbnail" class="form-label h5 mb-3"
                                style="font-weight: 700">Hình ảnh bổ sung</label>
                            <div class="dropzone" id="image-dropzone-thumbnail"></div>
                            @error('imageThumbnails')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="card p-4 mb-3 shadow-sm rounded bg-white">
                        <div class="mb-3">
                            <label class="form-label h5 mb-3" style="font-weight: 700">Giá sản phẩm</label>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="price" class="form-label">Giá bán</label>
                                    <input type="number" name="price" id="price" class="form-control" required
                                        placeholder="Nhập giá bán" value="{{ old('price', $product->price ?? '') }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="original_price" class="form-label">Giá gốc</label>
                                    <input type="number" name="original_price" id="original_price" class="form-control"
                                        placeholder="Nhập giá gốc"
                                        value="{{ old('original_price', $product->original_price ?? '') }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="discount_percentage" class="form-label">Phần trăm giảm giá (%)</label>
                                    <input type="number" name="discount_percentage" id="discount_percentage"
                                        class="form-control" min="0" max="100"
                                        value="{{ old('discount_percentage', $product->discount_percentage ?? 0) }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="discount_start_date" class="form-label">Ngày bắt đầu khuyến
                                        mãi</label>
                                    <input type="date" name="discount_start_date" id="discount_start_date"
                                        class="form-control"
                                        value="{{ old('discount_start_date', $product->discount_start_date ?? '') }}">
                                </div>
                                <div class="col-md-6 mt-3">
                                    <label for="discount_end_date" class="form-label">Ngày kết thúc khuyến mãi</label>
                                    <input type="date" name="discount_end_date" id="discount_end_date"
                                        class="form-control"
                                        value="{{ old('discount_end_date', $product->discount_end_date ?? '') }}">
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
                                        value="{{ old('barcode', $product->barcode ?? ($barcode ?? '')) }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group form-check">
                                        <input type="checkbox" name="track_qty" id="track_qty"
                                            class="form-check-input form-switch"
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
                                <div class="col-md-6">
                                    <label for="variants" class="form-label">Biến thể (Ví dụ: 256GB Đen: 10)</label>
                                    <input type="text" name="variants" id="variants" class="form-control"
                                        placeholder="Nhập biến thể"
                                        value="{{ old('variants', $product->variants ?? '') }}">
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
                                <option value="">Chọn danh mục sản phẩm</option>
                                @foreach ($categoryList as $id => $name)
                                    <option value="{{ $id }}"
                                        {{ old('category_id', $product->subcategory_id ?? ($product->category_id ?? '')) == $id ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="card p-4 mb-3 shadow-sm rounded bg-white">
                        <div class="mb-3">
                            <label for="brand_id" class="form-label h5 mb-3" style="font-weight: 700">Thương hiệu sản
                                phẩm</label>
                            <select name="brand_id" id="brand_id" class="form-control">
                                <option value="">Chọn thương hiệu</option>
                                @foreach ($brands as $brand)
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

                    <div class="card p-4 mb-3 shadow-sm rounded bg-white">
                        <div class="mb-3">
                            <label class="form-label h5 mb-3" style="font-weight: 700">Thông tin bảo hành</label>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="warranty_period" class="form-label">Thời gian bảo hành (tháng)</label>
                                    <input type="number" name="warranty_period" id="warranty_period"
                                        class="form-control"
                                        value="{{ old('warranty_period', $product->warranty_period ?? '') }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="warranty_policy" class="form-label">Chính sách bảo hành</label>
                                    <input type="text" name="warranty_policy" id="warranty_policy"
                                        class="form-control" placeholder="Ví dụ: Đổi trả trong 7 ngày"
                                        value="{{ old('warranty_policy', $product->warranty_policy ?? '') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card p-4 mb-3 shadow-sm rounded bg-white">
                        <div class="mb-3">
                            <label class="form-label h5 mb-3" style="font-weight: 700">Tối ưu SEO</label>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="meta_title" class="form-label">Meta Title</label>
                                    <input type="text" name="meta_title" id="meta_title" class="form-control"
                                        value="{{ old('meta_title', $product->meta_title ?? '') }}">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="meta_description" class="form-label">Meta Description</label>
                                    <textarea name="meta_description" id="meta_description" class="form-control" rows="3">{{ old('meta_description', $product->meta_description ?? '') }}</textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="meta_keywords" class="form-label">Meta Keywords</label>
                                    <input type="text" name="meta_keywords" id="meta_keywords"
                                        class="form-control" placeholder="Ví dụ: laptop, macbook, giá rẻ"
                                        value="{{ old('meta_keywords', $product->meta_keywords ?? '') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit"
                            class="btn btn-success">{{ isset($product->id) ? 'Cập nhật' : 'Thêm mới' }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        ClassicEditor.create(document.querySelector('#description'), {
                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote'],
            })
            .then(editor => {
                editor.ui.view.editable.element.style.maxHeight = "200px";
                editor.ui.view.editable.element.style.overflowY = "auto";
            })
            .catch(error => console.error(error));

        function slugify(str) {
            return str.toLowerCase()
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
            if (!document.getElementById('sku').value) {
                document.getElementById('sku').value = slugify(nameValue).toUpperCase();
            }
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
                @if (isset($image) && !empty($image->image))
                    var mockFile = {
                        name: "Image",
                        size: 12345
                    };
                    this.emit("addedfile", mockFile);
                    this.emit("thumbnail", mockFile, "{{ asset('storage/' . $image->image) }}");
                    this.emit("complete", mockFile);
                    this.files.push(mockFile);
                @endif
            },
            success: function(file, response) {
                document.getElementById('image-main-hidden').value = response.filePath;
                this.emit("thumbnail", file, response.url);
            },
            removedfile: function(file) {
                file.previewElement.remove();
                document.getElementById('image-main-hidden').value = '';
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
                @if (isset($imageThumbnails) && count($imageThumbnails) > 0)
                    @foreach ($imageThumbnails as $thumb)
                        @if (!empty($thumb->image))
                            {
                                var mockFile = {
                                    name: "Thumbnail",
                                    size: 12345
                                };
                                this.emit("addedfile", mockFile);
                                this.emit("thumbnail", mockFile, "{{ asset('storage/' . $thumb->image) }}");
                                this.emit("complete", mockFile);
                                this.files.push(mockFile);
                                let hiddenInput = document.createElement('input');
                                hiddenInput.type = 'hidden';
                                hiddenInput.name = 'imageThumbnails[]';
                                hiddenInput.value = "{{ $thumb->image }}";
                                document.getElementById('image-dropzone-thumbnail').appendChild(hiddenInput);
                            }
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
                const inputs = document.querySelectorAll('input[name="imageThumbnails[]"]');
                inputs.forEach(input => {
                    if (input.value === file.upload?.filename || file.name === "Thumbnail") {
                        input.remove();
                    }
                });
            }
        });
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
