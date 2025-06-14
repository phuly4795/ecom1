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
            method="POST" enctype="multipart/form-data" id="product-form">
            @csrf
            @if (isset($product->id))
                @method('PUT')
            @endif

            <div class="row">
                <div class="col-md-8">
                    <div class="card p-4 mb-3 shadow-sm rounded bg-white">
                        <div class="mb-3">
                            @if (!isset($product->id))
                                <label for="product_type" class="form-label h5 mb-3" style="font-weight: 700">Loại sản
                                    phẩm</label>
                                <select name="product_type" id="product_type" class="form-control" required>
                                    <option value="single" {{ old('product_type') == 'single' ? 'selected' : '' }}>Sản
                                        phẩm đơn</option>
                                    <option value="variant" {{ old('product_type') == 'variant' ? 'selected' : '' }}>Sản
                                        phẩm biến thể</option>
                                </select>
                            @else
                                <label class="form-label h5 mb-3" style="font-weight: 700">Loại sản phẩm</label>
                                <p class="form-control-static">
                                    {{ $product->product_type == 'single' ? 'Sản phẩm đơn' : 'Sản phẩm biến thể' }}</p>
                                <input type="hidden" name="product_type" value="{{ $product->product_type }}">
                            @endif
                        </div>

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
                            <label class="form-label h5" style="font-weight: 700">Thông số kỹ thuật</label>
                            <div id="specifications-groups">
                                @if (isset($product->id) && !empty($product->specifications))
                                    @php
                                        $groupIndex = 0;
                                    @endphp
                                    @foreach ($product->specifications as $groupName => $items)
                                        <div class="spec-group border rounded p-3 mb-3"
                                            data-index="{{ $groupIndex }}">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <input type="text" name="groups[{{ $groupIndex }}][name]"
                                                    class="form-control me-2"
                                                    placeholder="Tên nhóm (VD: Thông tin chung)"
                                                    value="{{ $groupName }}" />
                                                <button type="button"
                                                    class="btn btn-danger btn-sm remove-group">X</button>
                                            </div>
                                            <div class="spec-items">
                                                @php
                                                    $itemIndex = 0;
                                                @endphp
                                                @foreach ($items as $key => $value)
                                                    <div class="input-group mb-2">
                                                        <input type="text"
                                                            name="groups[{{ $groupIndex }}][items][{{ $itemIndex }}][key]"
                                                            class="form-control"
                                                            placeholder="Thuộc tính (VD: Loại sản phẩm)"
                                                            value="{{ $key }}" />
                                                        <input type="text"
                                                            name="groups[{{ $groupIndex }}][items][{{ $itemIndex }}][value]"
                                                            class="form-control"
                                                            placeholder="Giá trị (VD: Loa bluetooth)"
                                                            value="{{ $value }}" />
                                                        <button type="button"
                                                            class="btn btn-outline-secondary btn-sm add-item">+</button>
                                                    </div>
                                                    @php
                                                        $itemIndex++;
                                                    @endphp
                                                @endforeach
                                            </div>
                                        </div>
                                        @php
                                            $groupIndex++;
                                        @endphp
                                    @endforeach
                                @else
                                    <!-- Nhóm thông số đầu tiên -->
                                    <div class="spec-group border rounded p-3 mb-3" data-index="0">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <input type="text" name="groups[0][name]" class="form-control me-2"
                                                placeholder="Tên nhóm (VD: Thông tin chung)" />
                                            <button type="button" class="btn btn-danger btn-sm remove-group">X</button>
                                        </div>
                                        <div class="spec-items">
                                            <div class="input-group mb-2">
                                                <input type="text" name="groups[0][items][0][key]"
                                                    class="form-control"
                                                    placeholder="Thuộc tính (VD: Loại sản phẩm)" />
                                                <input type="text" name="groups[0][items][0][value]"
                                                    class="form-control" placeholder="Giá trị (VD: Loa bluetooth)" />
                                                <button type="button"
                                                    class="btn btn-outline-secondary btn-sm add-item">+</button>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <button type="button" class="btn btn-primary btn-sm" id="add-group">+ Thêm nhóm</button>

                            <input type="hidden" name="specifications" id="specifications-json" value="{}">
                        </div>
                    </div>

                    <div class="card p-4 mb-3 shadow-sm rounded bg-white">
                        <div class="mb-3">
                            <label for="image-dropzone" class="form-label h5 mb-3" style="font-weight: 700">Hình ảnh
                                đại diện</label>
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
                        </div>
                    </div>
                    <!-- Phần Giá sản phẩm (Sản phẩm đơn) -->
                    <div class="card p-4 mb-3 shadow-sm rounded bg-white product_single"
                        style="{{ (!isset($product->id) && old('product_type') != 'single') || (isset($product->id) && $product->product_type != 'single') ? 'display: none;' : '' }}">
                        <div class="mb-3">
                            <label class="form-label h5 mb-3" style="font-weight: 700">Giá sản phẩm</label>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="original_price" class="form-label">Giá gốc</label>
                                    <input type="number" name="original_price" id="original_price"
                                        class="form-control" placeholder="Nhập giá gốc"
                                        value="{{ old('original_price', $product->original_price ?? '') }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="discount_percentage" class="form-label">Phần trăm giảm giá (%)</label>
                                    <input type="number" name="discount_percentage" id="discount_percentage"
                                        class="form-control" min="0" max="100"
                                        value="{{ old('discount_percentage', $product->discount_percentage ?? 0) }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mt-3">
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
                                <div class="col-md-6 mt-3">
                                    <label for="qty" class="form-label">Số lượng</label>
                                    <input type="number" name="qty" id="qty" class="form-control"
                                        value="{{ old('qty', $product->qty ?? '') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Phần Biến thể sản phẩm -->
                    <div class="card p-4 mb-3 shadow-sm rounded bg-white product_variations"
                        style="{{ (!isset($product->id) && old('product_type') != 'variant') || (isset($product->id) && $product->product_type != 'variant') ? 'display: none;' : '' }}">
                        <div class="mb-3">
                            <label class="form-label h5 mb-3" style="font-weight: 700">Biến thể sản phẩm</label>
                            <div class="row">
                                <div class="col-md-12">
                                    <div id="variant-container">
                                        @if (isset($product->id) && $product->product_type == 'variant' && $product->productVariants->isNotEmpty())
                                            @foreach ($product->productVariants as $variant)
                                                <div class="variant-row row mb-3">
                                                    <div class="col-md-2">
                                                        <input type="text"
                                                            name="variants[existing][name][{{ $variant->id }}]"
                                                            class="form-control"
                                                            placeholder="Tên biến thể (VD: Size S)"
                                                            value="{{ $variant->variant_name }}">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input type="number"
                                                            name="variants[existing][original_price][{{ $variant->id }}]"
                                                            class="form-control" placeholder="Giá gốc"
                                                            value="{{ $variant->original_price }}">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input type="number"
                                                            name="variants[existing][discount_percentage][{{ $variant->id }}]"
                                                            class="form-control" placeholder="% giảm"
                                                            value="{{ $variant->discount_percentage ?? '' }}"
                                                            min="0" max="100">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input type="date"
                                                            name="variants[existing][discount_start_date][{{ $variant->id }}]"
                                                            class="form-control" placeholder="Ngày bắt đầu giảm giá"
                                                            value="{{ optional($variant->discount_start_date)->format('Y-m-d') ?? '' }}">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input type="date"
                                                            name="variants[existing][discount_end_date][{{ $variant->id }}]"
                                                            class="form-control" placeholder="Ngày kết thúc giảm giá"
                                                            value="{{ optional($variant->discount_end_date)->format('Y-m-d') ?? '' }}">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input type="text"
                                                            name="variants[existing][sku][{{ $variant->id }}]"
                                                            class="form-control" placeholder="SKU"
                                                            value="{{ $variant->sku }}">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input type="number"
                                                            name="variants[existing][qty][{{ $variant->id }}]"
                                                            class="form-control" placeholder="Số lượng"
                                                            value="{{ $variant->qty }}">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="button"
                                                            class="btn btn-danger remove-variant">Xóa</button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                        <div class="variant-row row mb-3">
                                            <div class="col-md-2">
                                                <input type="text" name="variants[new][name][]"
                                                    class="form-control" placeholder="Tên biến thể (VD: Size S)"
                                                    value="{{ old('variants.new.name.0') }}">
                                            </div>
                                            <div class="col-md-2">
                                                <input type="number" name="variants[new][original_price][]"
                                                    class="form-control" placeholder="Giá gốc"
                                                    value="{{ old('variants.new.original_price.0') }}">
                                            </div>
                                            <div class="col-md-2">
                                                <input type="number" name="variants[new][discount_percentage][]"
                                                    class="form-control" placeholder="Giá giảm"
                                                    value="{{ old('variants.new.discount_percentage.0') }}">
                                            </div>
                                            <div class="col-md-2">
                                                <input type="date" name="variants[new][discount_start_date][]"
                                                    class="form-control" placeholder="Ngày bắt đầu giảm giá"
                                                    value="{{ old('variants.new.discount_start_date.0') }}">
                                            </div>
                                            <div class="col-md-2">
                                                <input type="date" name="variants[new][discount_end_date][]"
                                                    class="form-control" placeholder="Ngày kết thúc giảm giá"
                                                    value="{{ old('variants.new.discount_end_date.0') }}">
                                            </div>
                                            <div class="col-md-2">
                                                <input type="text" name="variants[new][sku][]"
                                                    class="form-control" placeholder="SKU"
                                                    value="{{ old('variants.new.sku.0') }}">
                                            </div>
                                            <div class="col-md-2">
                                                <input type="number" name="variants[new][qty][]"
                                                    class="form-control" placeholder="Số lượng"
                                                    value="{{ old('variants.new.qty.0') }}">
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button"
                                                    class="btn btn-success add-variant">Thêm</button>
                                            </div>
                                        </div>
                                    </div>
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
                            @php
                                // Giá trị mặc định được chọn
                                $selectedJson = json_encode([
                                    'category_id' => $product->category_id ?? '',
                                    'sub_category_id' => $product->subcategory_id ?? null,
                                ]);

                                // Ưu tiên lấy lại giá trị từ form cũ (nếu có)
                                $selectedJson = old('category_json', $selectedJson);
                            @endphp

                            <select name="category_json" id="category" class="form-control">
                                <option value="">Chọn danh mục sản phẩm</option>
                                @foreach ($categoryList as $jsonValue => $label)
                                    <option value="{{ $jsonValue }}"
                                        {{ $selectedJson === $jsonValue ? 'selected' : '' }}>
                                        {{ $label }}
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
        let groupIndex =
            {{ isset($product->id) && !empty($product->specifications) ? count($product->specifications) : 1 }};

        // Thêm nhóm thông số mới
        document.getElementById('add-group').addEventListener('click', function() {
            const container = document.getElementById('specifications-groups');
            const html = `
                <div class="spec-group border rounded p-3 mb-3" data-index="${groupIndex}">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <input type="text" name="groups[${groupIndex}][name]" class="form-control me-2" placeholder="Tên nhóm" />
                        <button type="button" class="btn btn-danger btn-sm remove-group">X</button>
                    </div>
                    <div class="spec-items">
                        <div class="input-group mb-2">
                            <input type="text" name="groups[${groupIndex}][items][0][key]" class="form-control" placeholder="Thuộc tính" />
                            <input type="text" name="groups[${groupIndex}][items][0][value]" class="form-control" placeholder="Giá trị" />
                            <button type="button" class="btn btn-outline-secondary btn-sm add-item">+</button>
                        </div>
                    </div>
                </div>`;
            container.insertAdjacentHTML('beforeend', html);
            groupIndex++;
        });

        // Xử lý sự kiện click (xóa nhóm hoặc thêm dòng thông số)
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-group')) {
                if (confirm('Bạn có chắc chắn muốn xóa nhóm này không?')) {
                    e.target.closest('.spec-group').remove();
                }
            }

            if (e.target.classList.contains('add-item')) {
                const group = e.target.closest('.spec-group');
                const itemsContainer = group.querySelector('.spec-items');
                const groupId = group.dataset.index;
                const itemCount = itemsContainer.querySelectorAll('.input-group').length;

                const itemHtml = `
                    <div class="input-group mb-2">
                        <input type="text" name="groups[${groupId}][items][${itemCount}][key]" class="form-control" placeholder="Thuộc tính" />
                        <input type="text" name="groups[${groupId}][items][${itemCount}][value]" class="form-control" placeholder="Giá trị" />
                        <button type="button" class="btn btn-outline-secondary btn-sm add-item">+</button>
                    </div>`;
                itemsContainer.insertAdjacentHTML('beforeend', itemHtml);
            }
        });

        // Xử lý form submit
        document.getElementById('product-form').addEventListener('submit', function(e) {
            e.preventDefault();
            try {
                const specData = {};
                const specGroups = document.querySelectorAll('.spec-group');
                specGroups.forEach(group => {
                    const groupNameInput = group.querySelector('input[name*="[name]"]');
                    const groupName = groupNameInput ? groupNameInput.value.trim() : '';
                    if (!groupName) {
                        console.warn('Skipping group with empty name');
                        return;
                    }

                    specData[groupName] = {};
                    const inputRows = group.querySelectorAll('.input-group');
                    inputRows.forEach(row => {
                        const keyInput = row.querySelector('input[name*="[key]"]');
                        const valueInput = row.querySelector('input[name*="[value]"]');
                        const key = keyInput ? keyInput.value.trim() : '';
                        const value = valueInput ? valueInput.value.trim() : '';
                        if (key) {
                            specData[groupName][key] = value;
                        }
                    });

                    if (Object.keys(specData[groupName]).length === 0) {
                        delete specData[groupName];
                    }
                });

                const jsonString = Object.keys(specData).length > 0 ? JSON.stringify(specData) : '{}';

                const specInput = document.getElementById('specifications-json');
                if (specInput) {
                    specInput.value = jsonString;
                } else {
                    console.error('Hidden input #specifications-json not found');
                }

                this.submit();
            } catch (error) {
                console.error('Error processing specifications:', error);
                alert('Có lỗi khi xử lý thông số kỹ thuật. Vui lòng kiểm tra console và thử lại.');
            }
        });

        // CKEditor for description
        ClassicEditor.create(document.querySelector('#description'), {
                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote'],
            })
            .then(editor => {
                editor.ui.view.editable.element.style.maxHeight = "200px";
                editor.ui.view.editable.element.style.overflowY = "auto";
            })
            .catch(error => console.error('CKEditor error:', error));

        // Slug generation
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

        // Dropzone for main image
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
                        name: "MainImage",
                        size: 12345,
                        accepted: true
                    };
                    this.emit("addedfile", mockFile);
                    this.emit("thumbnail", mockFile, "{{ asset('storage/' . $image->image) }}");
                    this.emit("complete", mockFile);
                    this.files.push(mockFile);
                    document.getElementById('image-main-hidden').value = "{{ $image->image }}";
                    mockFile.storedPath = "{{ $image->image }}";
                @endif
                this.originalImageValue = document.getElementById('image-main-hidden').value;
                this.on("sending", function(file, xhr, formData) {
                    if (!file.accepted) formData.append('image', this.originalImageValue);
                });
            },
            success: function(file, response) {
                file.storedPath = response.filePath;
                document.getElementById('image-main-hidden').value = response.filePath;
                this.originalImageValue = response.filePath;
            },
            removedfile: function(file) {
                file.previewElement.remove();
                if (document.getElementById('image-main-hidden').value === file.storedPath) {
                    document.getElementById('image-main-hidden').value = '';
                    this.originalImageValue = '';
                }
            }
        });

        // Dropzone for thumbnails
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
                this.originalThumbnails = [];
                @if (isset($imageThumbnails) && count($imageThumbnails) > 0)
                    @foreach ($imageThumbnails as $thumb)
                        @if (!empty($thumb->image))
                            {
                                var mockFile = {
                                    name: "Thumbnail",
                                    size: 12345,
                                    accepted: true
                                };
                                this.emit("addedfile", mockFile);
                                this.emit("thumbnail", mockFile, "{{ asset('storage/' . $thumb->image) }}");
                                this.emit("complete", mockFile);
                                this.files.push(mockFile);
                                mockFile.storedPath = "{{ $thumb->image }}";
                                let hiddenInput = document.createElement('input');
                                hiddenInput.type = 'hidden';
                                hiddenInput.name = 'imageThumbnails[]';
                                hiddenInput.value = "{{ $thumb->image }}";
                                hiddenInput.classList.add('thumb-hidden');
                                mockFile._hiddenInput = hiddenInput;
                                document.getElementById('image-dropzone-thumbnail').appendChild(hiddenInput);
                                this.originalThumbnails.push("{{ $thumb->image }}");
                            }
                        @endif
                    @endforeach
                @endif
                this.on("sending", function(file, xhr, formData) {
                    if (!file.accepted) this.originalThumbnails.forEach(thumb => formData.append(
                        'imageThumbnails[]', thumb));
                });
            },
            success: function(file, response) {
                file.storedPath = response.filePath;
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'imageThumbnails[]';
                input.value = response.filePath;
                input.classList.add('thumb-hidden');
                file._hiddenInput = input;
                document.getElementById('image-dropzone-thumbnail').appendChild(input);
                this.originalThumbnails.push(response.filePath);
            },
            removedfile: function(file) {
                file.previewElement.remove();
                if (file._hiddenInput) file._hiddenInput.remove();
                const index = this.originalThumbnails.indexOf(file.storedPath);
                if (index !== -1) this.originalThumbnails.splice(index, 1);
            }
        });

        // Variant management
        document.addEventListener('DOMContentLoaded', function() {
            const variantContainer = document.getElementById('variant-container');
            const addVariantBtn = document.querySelector('.add-variant');

            if (addVariantBtn) {
                addVariantBtn.addEventListener('click', function() {
                    const newRow = document.createElement('div');
                    newRow.classList.add('variant-row', 'row', 'mb-3');
                    newRow.innerHTML = `
                        <div class="col-md-2"><input type="text" name="variants[new][name][]" class="form-control" placeholder="Tên biến thể (VD: Size S)"></div>
                        <div class="col-md-2"><input type="number" name="variants[new][original_price][]" class="form-control" placeholder="Giá gốc"></div>
                        <div class="col-md-2"><input type="number" name="variants[new][discount_percentage][]" class="form-control" placeholder="Giá giảm"></div>
                        <div class="col-md-2"><input type="date" name="variants[new][discount_start_date][]" class="form-control" placeholder="Ngày bắt đầu giảm giá"></div>
                        <div class="col-md-2"><input type="date" name="variants[new][discount_end_date][]" class="form-control" placeholder="Ngày kết thúc giảm giá"></div>
                        <div class="col-md-2"><input type="text" name="variants[new][sku][]" class="form-control" placeholder="SKU"></div>
                        <div class="col-md-2"><input type="number" name="variants[new][qty][]" class="form-control" placeholder="Số lượng"></div>
                        <div class="col-md-2"><button type="button" class="btn btn-danger remove-variant">Xóa</button></div>
                    `;
                    variantContainer.insertBefore(newRow, addVariantBtn.parentElement.parentElement);
                });
            }

            variantContainer.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-variant')) {
                    e.target.parentElement.parentElement.remove();
                }
            });

            // Product type toggle
            const productType = document.getElementById('product_type');
            const singleSection = document.querySelector('.product_single');
            const variantSection = document.querySelector('.product_variations');

            const isUpdateMode = {!! json_encode(isset($product->id)) !!};
            const productTypeValue = "{{ $product->product_type ?? '' }}";

            if (productType) {
                function updateSections() {
                    const type = productType.value;
                    if (type === 'single') {
                        singleSection.style.display = 'block';
                        variantSection.style.display = 'none';
                        singleSection.querySelectorAll('input, select, textarea').forEach(input => {
                            input.disabled = false;
                        });
                        variantSection.querySelectorAll('input, select, textarea').forEach(input => {
                            input.disabled = true;
                        });
                    } else if (type === 'variant') {
                        singleSection.style.display = 'none';
                        variantSection.style.display = 'block';
                        singleSection.querySelectorAll('input, select, textarea').forEach(input => {
                            input.disabled = true;
                        });
                        variantSection.querySelectorAll('input, select, textarea').forEach(input => {
                            input.disabled = false;
                        });
                    }
                }
                updateSections();
                productType.addEventListener('change', updateSections);
            } else if (isUpdateMode) {
                if (productTypeValue === 'single') {
                    singleSection.style.display = 'block';
                    variantSection.style.display = 'none';
                    singleSection.querySelectorAll('input, select, textarea').forEach(input => {
                        input.disabled = false;
                    });
                    variantSection.querySelectorAll('input, select, textarea').forEach(input => {
                        input.disabled = true;
                    });
                } else if (productTypeValue === 'variant') {
                    singleSection.style.display = 'none';
                    variantSection.style.display = 'block';
                    singleSection.querySelectorAll('input, select, textarea').forEach(input => {
                        input.disabled = true;
                    });
                    variantSection.querySelectorAll('input, select, textarea').forEach(input => {
                        input.disabled = false;
                    });
                }
            }
        });
    </script>

    <style>
        .ck-editor__editable {
            min-height: 200px;
        }

        #image-dropzone .dz-image,
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

        #image-dropzone .dz-image img,
        #image-dropzone-thumbnail .dz-image img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 0.5rem;
            background-color: #f8f9fa;
        }
    </style>
</x-app-layout>
