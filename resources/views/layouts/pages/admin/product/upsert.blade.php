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

            @if (!isset($product->id))
                <!-- Bộ cào thông tin tự động từ link đối thủ -->
                <div class="card p-4 mb-4 shadow-sm border-left-primary bg-white rounded" style="border-left: 4px solid #4e73df !important;">
                    <h5 class="font-weight-bold text-primary mb-3"><i class="fa-solid fa-cloud-arrow-down mr-1"></i> Tự động điền nhanh sản phẩm từ URL</h5>
                    <div class="input-group">
                        <input type="url" id="crawl-url-input" class="form-control" placeholder="Dán link sản phẩm (VD: FPT Shop, Tiki, CellphoneS...)">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-primary px-4 shadow-sm" id="btn-start-crawl">
                                <i class="fa-solid fa-wand-magic-sparkles mr-1"></i> Nạp dữ liệu
                            </button>
                        </div>
                    </div>
                    <small class="text-muted mt-2 d-block">
                        <i class="fa-solid fa-circle-question mr-1"></i> Dán đường dẫn trang chi tiết sản phẩm, hệ thống sẽ tự quét Tên, Giá, Mô tả, Thông số kỹ thuật và tải ảnh sản phẩm về server cho bạn.
                    </small>

                    <!-- Vùng hiển thị chọn ảnh trực quan (Ẩn mặc định) -->
                    <div id="crawler-image-picker-section" style="display: none;" class="mt-4 border-top pt-3">
                        <h6 class="font-weight-bold text-dark mb-3"><i class="fa-solid fa-images mr-1 text-info"></i> Chọn ảnh sản phẩm cào được:</h6>
                        <div class="alert alert-info py-2 mb-3" style="font-size: 13px;">
                            <i class="fa-solid fa-circle-info mr-1"></i> Chọn <strong>1 ảnh chính</strong> (vòng tròn) và tích chọn các <strong>ảnh phụ</strong> (ô vuông) bạn muốn sử dụng. Các ảnh rác/không chọn sẽ bị bỏ qua.
                        </div>
                        <div class="row" id="crawler-image-grid" style="margin-right: -5px; margin-left: -5px;">
                            <!-- Ảnh JS tự chèn vào đây -->
                        </div>
                        <div class="mt-3 text-right">
                            <button type="button" class="btn btn-success btn-sm shadow-sm px-4" id="btn-confirm-import-images">
                                <i class="fa-solid fa-cloud-arrow-down mr-1"></i> Tải & Nạp ảnh đã chọn
                            </button>
                        </div>
                    </div>
                </div>
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
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="form-label h5 mb-0" style="font-weight: 700">Thông số kỹ thuật</label>
                                <div class="d-flex align-items-center" style="gap: 8px;">
                                    <span class="small text-muted font-weight-bold">Mẫu thông số:</span>
                                    <select id="spec-template-selector" class="form-control form-control-sm" style="width: auto;">
                                        <option value="">-- Chọn mẫu thông số --</option>
                                        <option value="phone">Điện thoại / Máy tính bảng</option>
                                        <option value="laptop">Laptop / Máy tính</option>
                                        <option value="accessory">Phụ kiện & Thiết bị khác</option>
                                    </select>
                                </div>
                            </div>
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
                                    <input type="number" name="qty" id="qty" class="form-control bg-light" readonly
                                        value="{{ old('qty', $product->qty ?? '0') }}">
                                    <small class="text-muted d-block mt-1">
                                        <i class="fa-solid fa-circle-info text-primary mr-1"></i> Số lượng cập nhật tự động qua Phiếu Nhập Kho. <a href="{{ route('admin.warehouse.index') }}" target="_blank" class="font-weight-bold">Tới quản lý Kho hàng →</a>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Phần Biến thể sản phẩm -->
                    <div class="card p-4 mb-3 shadow-sm rounded bg-white product_variations"
                        style="{{ (!isset($product->id) && old('product_type') != 'variant') || (isset($product->id) && $product->product_type != 'variant') ? 'display: none;' : '' }}">
                        <div class="mb-3">
                            <label class="form-label h5 mb-3" style="font-weight: 700">Danh sách biến thể</label>

                            <!-- Bộ áp dụng nhanh giá trị cho toàn bộ biến thể -->
                            <div class="border p-3 rounded mb-4 bg-light shadow-xs border-left-success" style="border-left: 4px solid #1cc88a !important;">
                                <h6 class="font-weight-bold text-success mb-2"><i class="fa-solid fa-bolt mr-1"></i> Thiết lập nhanh cho tất cả biến thể:</h6>
                                <div class="row">
                                    <div class="col-md-3 mb-2">
                                        <label class="small font-weight-bold text-muted mb-1">Giá gốc chung</label>
                                        <input type="number" id="bulk-original-price" class="form-control form-control-sm" placeholder="Giá gốc chung">
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label class="small font-weight-bold text-muted mb-1">% giảm giá chung</label>
                                        <input type="number" id="bulk-discount-pct" class="form-control form-control-sm" placeholder="% giảm giá" min="0" max="100">
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label class="small font-weight-bold text-muted mb-1">Ngày bắt đầu KM</label>
                                        <input type="date" id="bulk-start-date" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label class="small font-weight-bold text-muted mb-1">Ngày kết thúc KM</label>
                                        <input type="date" id="bulk-end-date" class="form-control form-control-sm">
                                    </div>
                                </div>
                                <div class="text-right mt-2">
                                    <button type="button" class="btn btn-sm btn-success px-3 shadow-xs" id="btn-apply-bulk-variants">
                                        <i class="fa-solid fa-check mr-1"></i> Áp dụng cho tất cả
                                    </button>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div id="variant-container">
                                        @if (isset($product->id) && $product->product_type == 'variant' && $product->productVariants->isNotEmpty())
                                            @foreach ($product->productVariants as $key => $variant)
                                                <div class="variant-row border rounded p-3 mb-3 bg-white shadow-xs" style="border-left: 4px solid #36b9cc !important;">
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <h5 class="font-weight-bold text-dark mb-0"><i class="fa-solid fa-cube text-info mr-1"></i> Biến thể {{ $key + 1 }}</h5>
                                                        <button type="button" class="btn btn-danger btn-sm remove-variant"><i class="fa-solid fa-trash-can mr-1"></i> Xóa biến thể</button>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4 mb-2">
                                                            <label class="small font-weight-bold text-dark">Tên biến thể</label>
                                                            <input type="text"
                                                                name="variants[existing][name][{{ $variant->id }}]"
                                                                class="form-control form-control-sm"
                                                                placeholder="Tên biến thể (VD: Màu Đen, 256GB)"
                                                                value="{{ $variant->variant_name }}">
                                                        </div>
                                                        <div class="col-md-4 mb-2">
                                                            <label class="small font-weight-bold text-dark">Giá gốc</label>
                                                            <input type="number"
                                                                name="variants[existing][original_price][{{ $variant->id }}]"
                                                                class="form-control form-control-sm" placeholder="Giá gốc"
                                                                value="{{ $variant->original_price }}">
                                                        </div>
                                                        <div class="col-md-4 mb-2">
                                                            <label class="small font-weight-bold text-dark">Giá giảm (%)</label>
                                                            <input type="number"
                                                                name="variants[existing][discount_percentage][{{ $variant->id }}]"
                                                                class="form-control form-control-sm" placeholder="% giảm"
                                                                value="{{ $variant->discount_percentage ?? '' }}"
                                                                min="0" max="100">
                                                        </div>
                                                    </div>
                                                    <div class="row mt-2">
                                                        <div class="col-md-4 mb-2">
                                                            <label class="small font-weight-bold text-dark">Ngày bắt đầu giảm giá</label>
                                                            <input type="date"
                                                                name="variants[existing][discount_start_date][{{ $variant->id }}]"
                                                                class="form-control form-control-sm"
                                                                value="{{ optional($variant->discount_start_date)->format('Y-m-d') ?? '' }}">
                                                        </div>
                                                        <div class="col-md-4 mb-2">
                                                            <label class="small font-weight-bold text-dark">Ngày kết thúc giảm giá</label>
                                                            <input type="date"
                                                                name="variants[existing][discount_end_date][{{ $variant->id }}]"
                                                                class="form-control form-control-sm"
                                                                value="{{ optional($variant->discount_end_date)->format('Y-m-d') ?? '' }}">
                                                        </div>
                                                        <div class="col-md-2 mb-2">
                                                            <label class="small font-weight-bold text-dark">SKU biến thể</label>
                                                            <input type="text"
                                                                name="variants[existing][sku][{{ $variant->id }}]"
                                                                class="form-control form-control-sm bg-light" placeholder="SKU"
                                                                value="{{ $variant->sku }}" readonly>
                                                        </div>
                                                        <div class="col-md-2 mb-2">
                                                            <label class="small font-weight-bold text-dark">Số lượng</label>
                                                            <input type="number"
                                                                name="variants[existing][qty][{{ $variant->id }}]"
                                                                class="form-control form-control-sm bg-light" placeholder="Số lượng" readonly
                                                                value="{{ $variant->qty }}">
                                                            <small class="text-muted d-block mt-1" style="font-size: 10px;">Nhập qua phiếu kho</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                        <div class="col-md-12 text-right variant-action" style="margin-top: 4%" id="add-variant-wrapper">
                                            <button type="button" class="btn btn-success add-variant"><i class="fa-solid fa-plus mr-1"></i> Thêm biến thể mới</button>
                                        </div>
                                    </div>
                                    <template id="variant-template">
                                        <div class="variant-row border rounded p-3 mb-3 bg-white shadow-xs" style="border-left: 4px solid #1cc88a !important;">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h5 class="variant-title font-weight-bold text-dark mb-0"><i class="fa-solid fa-cube text-success mr-1"></i> Biến thể mới</h5>
                                                <button type="button" class="btn btn-danger btn-sm remove-variant"><i class="fa-solid fa-trash-can mr-1"></i> Xóa biến thể</button>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4 mb-2">
                                                    <label class="small font-weight-bold text-dark">Tên biến thể</label>
                                                    <input type="text" name="variants[new][name][]"
                                                        class="form-control form-control-sm"
                                                        placeholder="Tên biến thể (VD: Màu Đỏ, 128GB)">
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <label class="small font-weight-bold text-dark">Giá gốc biến thể</label>
                                                    <input type="number" name="variants[new][original_price][]"
                                                        class="form-control form-control-sm" placeholder="Giá gốc">
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <label class="small font-weight-bold text-dark">Giá giảm (%)</label>
                                                    <input type="number"
                                                        name="variants[new][discount_percentage][]"
                                                        class="form-control form-control-sm" placeholder="% giảm" min="0"
                                                        max="100">
                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                <div class="col-md-4 mb-2">
                                                    <label class="small font-weight-bold text-dark">Ngày bắt đầu giảm giá</label>
                                                    <input type="date"
                                                        name="variants[new][discount_start_date][]"
                                                        class="form-control form-control-sm">
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <label class="small font-weight-bold text-dark">Ngày kết thúc giảm giá</label>
                                                    <input type="date"
                                                        name="variants[new][discount_end_date][]"
                                                        class="form-control form-control-sm">
                                                </div>
                                                <div class="col-md-2 mb-2">
                                                    <label class="small font-weight-bold text-dark">SKU biến thể</label>
                                                    <input type="text" name="variants[new][sku][]"
                                                        class="variant-sku form-control form-control-sm bg-light" readonly>
                                                </div>
                                                <div class="col-md-2 mb-2">
                                                    <label class="small font-weight-bold text-dark">Số lượng</label>
                                                    <input type="number" name="variants[new][qty][]"
                                                        class="form-control form-control-sm bg-light" readonly value="0">
                                                    <small class="text-muted d-block mt-1" style="font-size: 10px;">Nhập qua phiếu kho</small>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
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
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label for="category" class="form-label h5 mb-0" style="font-weight: 700">Danh mục sản phẩm</label>
                                <button type="button" class="btn btn-sm btn-link p-0 text-primary font-weight-bold" id="btn-add-quick-category" style="text-decoration: none;">
                                    <i class="fa-solid fa-plus-circle mr-1"></i> Thêm nhanh
                                </button>
                            </div>
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
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label for="brand_id" class="form-label h5 mb-0" style="font-weight: 700">Thương hiệu sản phẩm</label>
                                <button type="button" class="btn btn-sm btn-link p-0 text-primary font-weight-bold" id="btn-add-quick-brand" style="text-decoration: none;">
                                    <i class="fa-solid fa-plus-circle mr-1"></i> Thêm nhanh
                                </button>
                            </div>
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
                                    {{ old('is_featured', $product->is_featured ?? false ? 'yes' : 'no') == 'yes' ? 'selected' : '' }}>
                                    Nổi bật</option>
                                <option value="no"
                                    {{ old('is_featured', $product->is_featured ?? false ? 'yes' : 'no') == 'no' ? 'selected' : '' }}>
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

        // Mẫu cấu hình thông số kỹ thuật tiêu chuẩn
        const specTemplates = {
            phone: [
                {
                    name: "Màn hình & Cấu hình",
                    items: ["Công nghệ màn hình", "Kích thước màn hình", "Độ phân giải", "Hệ điều hành", "Chip xử lý (CPU)", "Bộ nhớ RAM", "Bộ nhớ trong (ROM)"]
                },
                {
                    name: "Camera & Pin",
                    items: ["Camera sau", "Camera trước", "Dung lượng pin", "Cổng sạc"]
                }
            ],
            laptop: [
                {
                    name: "Bộ xử lý & Bộ nhớ",
                    items: ["Công nghệ CPU", "Bộ nhớ RAM", "Loại RAM", "Ổ cứng (SSD/HDD)"]
                },
                {
                    name: "Màn hình & Đồ họa",
                    items: ["Kích thước màn hình", "Độ phân giải", "Card màn hình (GPU)"]
                },
                {
                    name: "Thông tin chung",
                    items: ["Hệ điều hành", "Dung lượng Pin", "Trọng lượng", "Kích thước"]
                }
            ],
            accessory: [
                {
                    name: "Thông số chi tiết",
                    items: ["Loại phụ kiện", "Chất liệu", "Phương thức kết nối", "Thời lượng sử dụng", "Tương thích"]
                }
            ]
        };

        // Nạp cấu hình mẫu
        document.getElementById('spec-template-selector').addEventListener('change', function() {
            const templateKey = this.value;
            if (!templateKey) return;
            
            const template = specTemplates[templateKey];
            if (!template) return;
            
            if (!confirm('Bạn có chắc chắn muốn nạp mẫu này? Hành động này sẽ thay thế các nhóm thông số hiện tại.')) {
                this.value = '';
                return;
            }
            
            const container = document.getElementById('specifications-groups');
            container.innerHTML = ''; // Xóa hết nhóm cũ
            
            groupIndex = 0; // Reset index
            
            template.forEach((group, gIdx) => {
                let itemsHtml = '';
                group.items.forEach((item, iIdx) => {
                    itemsHtml += `
                        <div class="input-group mb-2">
                            <input type="text" name="groups[${groupIndex}][items][${iIdx}][key]" class="form-control" placeholder="Thuộc tính" value="${item}" />
                            <input type="text" name="groups[${groupIndex}][items][${iIdx}][value]" class="form-control" placeholder="Giá trị" value="" />
                            <button type="button" class="btn btn-outline-secondary btn-sm add-item">+</button>
                        </div>`;
                });
                
                const groupHtml = `
                    <div class="spec-group border rounded p-3 mb-3 shadow-xs" data-index="${groupIndex}">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <input type="text" name="groups[${groupIndex}][name]" class="form-control me-2 font-weight-bold text-primary" placeholder="Tên nhóm" value="${group.name}" />
                            <button type="button" class="btn btn-danger btn-sm remove-group">X</button>
                        </div>
                        <div class="spec-items">
                            ${itemsHtml}
                        </div>
                    </div>`;
                
                container.insertAdjacentHTML('beforeend', groupHtml);
                groupIndex++;
            });
            
            this.value = ''; // Reset select
        });

        // Áp dụng nhanh giá trị cho toàn bộ biến thể
        const btnApplyBulk = document.getElementById('btn-apply-bulk-variants');
        if (btnApplyBulk) {
            btnApplyBulk.addEventListener('click', function() {
                const bulkPrice = document.getElementById('bulk-original-price').value.trim();
                const bulkDiscount = document.getElementById('bulk-discount-pct').value.trim();
                const bulkStart = document.getElementById('bulk-start-date').value;
                const bulkEnd = document.getElementById('bulk-end-date').value;
                
                if (!bulkPrice && !bulkDiscount && !bulkStart && !bulkEnd) {
                    alert('Vui lòng nhập ít nhất một giá trị để áp dụng!');
                    return;
                }
                
                const variantRows = document.querySelectorAll('.variant-row');
                if (variantRows.length === 0) {
                    alert('Chưa có biến thể nào được tạo!');
                    return;
                }
                
                if (confirm(`Bạn có chắc chắn muốn điền nhanh các giá trị này cho tất cả ${variantRows.length} biến thể?`)) {
                    variantRows.forEach(row => {
                        if (bulkPrice) {
                            const priceInput = row.querySelector('input[name*="[original_price]"]');
                            if (priceInput) priceInput.value = bulkPrice;
                        }
                        if (bulkDiscount) {
                            const discountInput = row.querySelector('input[name*="[discount_percentage]"]');
                            if (discountInput) discountInput.value = bulkDiscount;
                        }
                        if (bulkStart) {
                            const startInput = row.querySelector('input[name*="[discount_start_date]"]');
                            if (startInput) startInput.value = bulkStart;
                        }
                        if (bulkEnd) {
                            const endInput = row.querySelector('input[name*="[discount_end_date]"]');
                            if (endInput) endInput.value = bulkEnd;
                        }
                    });
                }
            });
        }

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
        let descriptionEditor = null;
        ClassicEditor.create(document.querySelector('#description'), {
                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote'],
            })
            .then(editor => {
                descriptionEditor = editor;
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
            const addVariantWrapper = document.querySelector('.variant-action');
            const variantTemplate = document.getElementById('variant-template');
            const productNameInput = document.getElementById('name');

            // Hàm tạo slug
            function toSlug(str) {
                return str.normalize("NFD").replace(/[\u0300-\u036f]/g, "")
                    .replace(/đ/g, 'd').replace(/Đ/g, 'D')
                    .toLowerCase().replace(/\s+/g, '-').replace(/[^\w\-]+/g, '');
            }

            // Cập nhật SKU khi tên sản phẩm hoặc tên biến thể thay đổi
            function bindAutoSKU(nameInput, skuInput) {
                const updateSKU = () => {
                    const productTitle = productNameInput?.value || '';
                    const variantTitle = nameInput?.value || '';
                    const slugProduct = toSlug(productTitle);
                    const slugVariant = toSlug(variantTitle);
                    skuInput.value = `${slugProduct}-${slugVariant}`.replace(/^-/, '');
                };

                nameInput.addEventListener('input', updateSKU);
                productNameInput?.addEventListener('input', updateSKU);
            }

            if (addVariantBtn && variantTemplate) {
                addVariantBtn.addEventListener('click', function() {
                    const clone = variantTemplate.content.cloneNode(true);
                    const tempWrapper = document.createElement('div');
                    tempWrapper.appendChild(clone);
                    const newRow = tempWrapper.querySelector('.variant-row');

                    // Set tiêu đề biến thể
                    const currentCount = variantContainer.querySelectorAll('.variant-row').length + 1;
                    const title = newRow.querySelector('.variant-title');
                    if (title) {
                        title.innerText = `Biến thể ${currentCount}`;
                    }

                    // Gán sự kiện cập nhật SKU động
                    const nameInput = newRow.querySelector('input[name="variants[new][name][]"]');
                    const skuInput = newRow.querySelector('input[name="variants[new][sku][]"]');

                    if (nameInput && skuInput) {
                        bindAutoSKU(nameInput, skuInput);
                    }

                    variantContainer.insertBefore(newRow, addVariantWrapper);
                });
            }

            // Xóa biến thể
            variantContainer.addEventListener('click', function(e) {
                const removeBtn = e.target.closest('.remove-variant');
                if (removeBtn) {
                    if (confirm('Bạn có chắc chắn muốn xóa biến thể này?')) {
                        removeBtn.closest('.variant-row').remove();
                    }
                }
            });

            // Toggle loại sản phẩm
            const productType = document.getElementById('product_type');
            const singleSection = document.querySelector('.product_single');
            const variantSection = document.querySelector('.product_variations');
            const isUpdateMode = {!! json_encode(isset($product->id)) !!};
            const productTypeValue = "{{ $product->product_type ?? '' }}";

            function updateSections() {
                const type = productType?.value || productTypeValue;
                if (type === 'single') {
                    singleSection.style.display = 'block';
                    variantSection.style.display = 'none';
                    singleSection.querySelectorAll('input, select, textarea').forEach(input => input.disabled =
                        false);
                    variantSection.querySelectorAll('input, select, textarea').forEach(input => input.disabled =
                        true);
                } else if (type === 'variant') {
                    singleSection.style.display = 'none';
                    variantSection.style.display = 'block';
                    singleSection.querySelectorAll('input, select, textarea').forEach(input => input.disabled =
                        true);
                    variantSection.querySelectorAll('input, select, textarea').forEach(input => input.disabled =
                        false);
                }
            }

            if (productType) {
                updateSections();
                productType.addEventListener('change', updateSections);
            } else if (isUpdateMode) {
                updateSections();
            }

            // AJAX Quick Add Category
            const btnAddQuickCat = document.getElementById('btn-add-quick-category');
            const formQuickCat = document.getElementById('quick-category-form');
            if (btnAddQuickCat) {
                btnAddQuickCat.addEventListener('click', function() {
                    $('#quickCategoryModal').modal('show');
                });
            }
            if (formQuickCat) {
                formQuickCat.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const name = document.getElementById('quick-cat-name').value.trim();
                    const subName = document.getElementById('quick-subcat-name').value.trim();
                    const saveBtn = document.getElementById('btn-save-quick-cat');
                    
                    const originalHtml = saveBtn.innerHTML;
                    saveBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Đang lưu...';
                    saveBtn.disabled = true;

                    $.ajax({
                        url: "{{ route('admin.product.quickCategory', [], false) }}",
                        type: 'POST',
                        data: {
                            name: name,
                            sub_name: subName,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            saveBtn.innerHTML = originalHtml;
                            saveBtn.disabled = false;
                            if (response.success) {
                                // Clear inputs
                                document.getElementById('quick-cat-name').value = '';
                                document.getElementById('quick-subcat-name').value = '';
                                $('#quickCategoryModal').modal('hide');
                                
                                // Append and select new option
                                const selectElement = document.getElementById('category');
                                const newOption = new Option(response.label, response.value, true, true);
                                selectElement.add(newOption);
                                $(selectElement).val(response.value).trigger('change');
                                
                                alert('Thêm nhanh danh mục thành công!');
                            } else {
                                alert(response.message);
                            }
                        },
                        error: function(xhr) {
                            saveBtn.innerHTML = originalHtml;
                            saveBtn.disabled = false;
                            alert('Lỗi: ' + (xhr.responseJSON ? xhr.responseJSON.message : 'Không thể lưu danh mục.'));
                        }
                    });
                });
            }

            // AJAX Quick Add Brand
            const btnAddQuickBrand = document.getElementById('btn-add-quick-brand');
            const formQuickBrand = document.getElementById('quick-brand-form');
            if (btnAddQuickBrand) {
                btnAddQuickBrand.addEventListener('click', function() {
                    $('#quickBrandModal').modal('show');
                });
            }
            if (formQuickBrand) {
                formQuickBrand.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const name = document.getElementById('quick-brand-name').value.trim();
                    const saveBtn = document.getElementById('btn-save-quick-brand');

                    const originalHtml = saveBtn.innerHTML;
                    saveBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Đang lưu...';
                    saveBtn.disabled = true;

                    $.ajax({
                        url: "{{ route('admin.product.quickBrand', [], false) }}",
                        type: 'POST',
                        data: {
                            name: name,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            saveBtn.innerHTML = originalHtml;
                            saveBtn.disabled = false;
                            if (response.success) {
                                // Clear inputs
                                document.getElementById('quick-brand-name').value = '';
                                $('#quickBrandModal').modal('hide');

                                // Append and select new option
                                const selectElement = document.getElementById('brand_id');
                                const newOption = new Option(response.name, response.id, true, true);
                                selectElement.add(newOption);
                                $(selectElement).val(response.id).trigger('change');

                                alert('Thêm nhanh thương hiệu thành công!');
                            } else {
                                alert(response.message);
                            }
                        },
                        error: function(xhr) {
                            saveBtn.innerHTML = originalHtml;
                            saveBtn.disabled = false;
                            alert('Lỗi: ' + (xhr.responseJSON ? xhr.responseJSON.message : 'Không thể lưu thương hiệu.'));
                        }
                    });
                });
            }

            // AJAX Web Scraper/Crawler logic
            const btnCrawl = document.getElementById('btn-start-crawl');
            const crawlInput = document.getElementById('crawl-url-input');

            if (btnCrawl && crawlInput) {
                btnCrawl.addEventListener('click', function() {
                    const url = crawlInput.value.trim();
                    if (!url) {
                        alert('Vui lòng nhập đường dẫn URL sản phẩm cần nạp!');
                        return;
                    }

                    // Đổi nút sang trạng thái loading
                    const originalHtml = btnCrawl.innerHTML;
                    btnCrawl.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Đang cào...';
                    btnCrawl.disabled = true;

                    // Ẩn vùng chọn ảnh cũ
                    document.getElementById('crawler-image-picker-section').style.display = 'none';
                    document.getElementById('crawler-image-grid').innerHTML = '';

                    $.ajax({
                        url: "{{ route('admin.product.crawl', [], false) }}",
                        type: 'POST',
                        data: {
                            url: url,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            btnCrawl.innerHTML = originalHtml;
                            btnCrawl.disabled = false;

                            if (response.success) {
                                const data = response.data;

                                // 1. Điền Tên sản phẩm & tạo SKU/Slug
                                if (data.title) {
                                    document.getElementById('name').value = data.title;
                                    document.getElementById('name').dispatchEvent(new Event('input'));
                                }

                                // 2. Điền Mô tả (CKEditor)
                                if (data.description && typeof descriptionEditor !== 'undefined' && descriptionEditor) {
                                    descriptionEditor.setData(data.description);
                                }

                                // 3. Điền Giá gốc và Giá bán (tự tính % giảm giá)
                                if (data.original_price && document.getElementById('original_price')) {
                                    document.getElementById('original_price').value = data.original_price;
                                }
                                // Nếu có giá bán khác giá gốc -> tự động tính % giảm giá
                                if (data.sale_price && data.original_price && data.sale_price != data.original_price) {
                                    const discountPct = Math.round((1 - data.sale_price / data.original_price) * 100);
                                    if (discountPct > 0 && discountPct < 100) {
                                        const discountInput = document.getElementById('discount_percentage');
                                        if (discountInput) {
                                            discountInput.value = discountPct;
                                        }
                                    }
                                }

                                // 4. Nạp thông số kỹ thuật (Specifications)
                                if (data.specifications && data.specifications.length > 0) {
                                    const specContainer = document.getElementById('specifications-groups');
                                    specContainer.innerHTML = '';
                                    groupIndex = 0;

                                    data.specifications.forEach(group => {
                                        let itemsHtml = '';
                                        let itemIndex = 0;
                                        for (const [key, value] of Object.entries(group.items)) {
                                            itemsHtml += `
                                                <div class="input-group mb-2">
                                                    <input type="text" name="groups[${groupIndex}][items][${itemIndex}][key]" class="form-control" placeholder="Thuộc tính" value="${key}" />
                                                    <input type="text" name="groups[${groupIndex}][items][${itemIndex}][value]" class="form-control" placeholder="Giá trị" value="${value}" />
                                                    <button type="button" class="btn btn-outline-secondary btn-sm add-item">+</button>
                                                </div>`;
                                            itemIndex++;
                                        }

                                        const groupHtml = `
                                            <div class="spec-group border rounded p-3 mb-3 shadow-xs" data-index="${groupIndex}">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <input type="text" name="groups[${groupIndex}][name]" class="form-control me-2 font-weight-bold text-primary" placeholder="Tên nhóm" value="${group.name}" />
                                                    <button type="button" class="btn btn-danger btn-sm remove-group">X</button>
                                                </div>
                                                <div class="spec-items">
                                                    ${itemsHtml}
                                                </div>
                                            </div>`;
                                        specContainer.insertAdjacentHTML('beforeend', groupHtml);
                                        groupIndex++;
                                    });
                                }

                                // 5. Nạp danh sách biến thể (nếu có)
                                if (data.variants && data.variants.length > 0) {
                                    if (productType) {
                                        productType.value = 'variant';
                                        updateSections();
                                    }
                                    
                                    const variantContainer = document.getElementById('variant-container');
                                    if (variantContainer) {
                                        variantContainer.querySelectorAll('.variant-row').forEach(el => el.remove());
                                    }

                                    data.variants.forEach(v => {
                                        if (addVariantBtn) {
                                            addVariantBtn.click();
                                            const newRow = addVariantWrapper.previousElementSibling;
                                            if (newRow) {
                                                const nameInput = newRow.querySelector('input[name="variants[new][name][]"]');
                                                if (nameInput) {
                                                    nameInput.value = v.name;
                                                    nameInput.dispatchEvent(new Event('input'));
                                                }
                                                const priceInput = newRow.querySelector('input[name="variants[new][original_price][]"]');
                                                if (priceInput) {
                                                    priceInput.value = v.price;
                                                }
                                            }
                                        }
                                    });
                                } else {
                                    if (productType) {
                                        productType.value = 'single';
                                        updateSections();
                                    }
                                }

                                // 6. Hiển thị danh sách ảnh cào được để chọn trực quan
                                if (data.images && data.images.length > 0) {
                                    const grid = document.getElementById('crawler-image-grid');
                                    grid.innerHTML = '';

                                    data.images.forEach((imgUrl, i) => {
                                        const col = document.createElement('div');
                                        col.className = 'col-md-2 col-4 mb-3 text-center px-1';
                                        col.innerHTML = `
                                            <div class="card p-1 border shadow-xs text-center position-relative" style="height: 140px; border-radius: 6px;">
                                                <img src="${imgUrl}" alt="Ảnh cào" onerror="this.src='{{ asset('asset/img/no-image.png') }}'; this.onerror=null;" style="width: 100%; height: 80px; object-fit: contain; background: #fafafa;">
                                                <div class="mt-2 d-flex justify-content-around align-items-center">
                                                    <label class="mb-0 d-flex flex-column align-items-center cursor-pointer" title="Chọn làm ảnh chính">
                                                        <input type="radio" name="main_image_select" value="${imgUrl}" ${i === 0 ? 'checked' : ''} style="cursor: pointer;">
                                                        <span class="small font-weight-bold text-primary mt-1" style="font-size: 10px;">Chính</span>
                                                    </label>
                                                    <label class="mb-0 d-flex flex-column align-items-center cursor-pointer" title="Chọn làm ảnh phụ">
                                                        <input type="checkbox" name="gallery_image_select[]" value="${imgUrl}" ${i > 0 && i < 5 ? 'checked' : ''} style="cursor: pointer;">
                                                        <span class="small font-weight-bold text-secondary mt-1" style="font-size: 10px;">Phụ</span>
                                                    </label>
                                                </div>
                                            </div>`;
                                        grid.appendChild(col);
                                    });
                                    document.getElementById('crawler-image-picker-section').style.display = 'block';
                                }

                                alert('Cào thông tin sản phẩm thành công! Hãy chọn ảnh chính/phụ mong muốn rồi nhấn nút "Tải & Nạp ảnh".');
                            } else {
                                alert('Không thể nạp dữ liệu: ' + response.message);
                            }
                        },
                        error: function(xhr) {
                            btnCrawl.innerHTML = originalHtml;
                            btnCrawl.disabled = false;
                            const errMessage = xhr.responseJSON ? xhr.responseJSON.message : 'Đường dẫn không hợp lệ hoặc lỗi kết nối.';
                            alert('Đã xảy ra lỗi: ' + errMessage);
                        }
                    });
                });
            }

            // AJAX Downloader logic for chosen crawled images
            const btnImportImages = document.getElementById('btn-confirm-import-images');
            if (btnImportImages) {
                btnImportImages.addEventListener('click', function() {
                    const selectedMainRadio = document.querySelector('input[name="main_image_select"]:checked');
                    if (!selectedMainRadio) {
                        alert('Vui lòng chọn 1 ảnh làm ảnh chính!');
                        return;
                    }
                    const mainImageUrl = selectedMainRadio.value;

                    const selectedGalCheckboxes = document.querySelectorAll('input[name="gallery_image_select[]"]:checked');
                    const galleryImageUrls = [];
                    selectedGalCheckboxes.forEach(cb => {
                        galleryImageUrls.push(cb.value);
                    });

                    // Set button loading
                    const originalHtml = btnImportImages.innerHTML;
                    btnImportImages.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Đang tải ảnh về server...';
                    btnImportImages.disabled = true;

                    $.ajax({
                        url: "{{ route('admin.product.crawl.downloadImages', [], false) }}",
                        type: 'POST',
                        data: {
                            main_image: mainImageUrl,
                            gallery_images: galleryImageUrls,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            btnImportImages.innerHTML = originalHtml;
                            btnImportImages.disabled = false;

                            if (response.success) {
                                const paths = response.data;

                                // Nạp Ảnh chính vào Dropzone
                                if (paths.main && typeof dropzone !== 'undefined' && dropzone) {
                                    dropzone.removeAllFiles(true);
                                    var mockFile = {
                                        name: "MainImage",
                                        size: 12345,
                                        accepted: true,
                                        storedPath: paths.main
                                    };
                                    dropzone.emit("addedfile", mockFile);
                                    dropzone.emit("thumbnail", mockFile, "/storage/" + paths.main);
                                    dropzone.emit("complete", mockFile);
                                    dropzone.files.push(mockFile);
                                    document.getElementById('image-main-hidden').value = paths.main;
                                    dropzone.originalImageValue = paths.main;
                                }

                                // Nạp Ảnh phụ vào Dropzone Thư viện
                                if (typeof dropzoneThumbnail !== 'undefined' && dropzoneThumbnail) {
                                    dropzoneThumbnail.removeAllFiles(true);
                                    $('.thumb-hidden').remove();
                                    dropzoneThumbnail.originalThumbnails = [];

                                    paths.gallery.forEach((imgPath, index) => {
                                        var mockThumb = {
                                            name: "GalleryImage_" + index,
                                            size: 12345,
                                            accepted: true,
                                            storedPath: imgPath
                                        };
                                        dropzoneThumbnail.emit("addedfile", mockThumb);
                                        dropzoneThumbnail.emit("thumbnail", mockThumb, "/storage/" + imgPath);
                                        dropzoneThumbnail.emit("complete", mockThumb);
                                        dropzoneThumbnail.files.push(mockThumb);

                                        let hiddenInput = document.createElement('input');
                                        hiddenInput.type = 'hidden';
                                        hiddenInput.name = 'imageThumbnails[]';
                                        hiddenInput.value = imgPath;
                                        hiddenInput.classList.add('thumb-hidden');
                                        mockThumb._hiddenInput = hiddenInput;
                                        document.getElementById('image-dropzone-thumbnail').appendChild(hiddenInput);
                                        dropzoneThumbnail.originalThumbnails.push(imgPath);
                                    });
                                }

                                alert('Tải và nạp ảnh thành công!');
                            } else {
                                alert('Lỗi tải ảnh: ' + response.message);
                            }
                        },
                        error: function(xhr) {
                            btnImportImages.innerHTML = originalHtml;
                            btnImportImages.disabled = false;
                            alert('Đã xảy ra lỗi khi tải ảnh: ' + (xhr.responseJSON ? xhr.responseJSON.message : 'Lỗi kết nối.'));
                        }
                    });
                });
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

    <!-- Modal Thêm nhanh Danh mục -->
    <div class="modal fade" id="quickCategoryModal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fa-solid fa-folder-plus mr-1"></i> Thêm nhanh Danh mục</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="quick-category-form">
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold mb-1">Tên Danh mục cha <span class="text-danger">*</span></label>
                            <input type="text" id="quick-cat-name" class="form-control" placeholder="Ví dụ: Điện thoại, Laptop..." required>
                        </div>
                        <div class="form-group mb-0">
                            <label class="font-weight-bold mb-1">Tên Danh mục con (Tùy chọn)</label>
                            <input type="text" id="quick-subcat-name" class="form-control" placeholder="Ví dụ: iPhone, Asus ROG...">
                            <small class="text-muted mt-1 d-block">Nếu nhập, hệ thống sẽ tự động liên kết danh mục con này dưới danh mục cha vừa tạo.</small>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary" id="btn-save-quick-cat">Lưu danh mục</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Thêm nhanh Thương hiệu -->
    <div class="modal fade" id="quickBrandModal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fa-solid fa-folder-plus mr-1"></i> Thêm nhanh Thương hiệu</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="quick-brand-form">
                    <div class="modal-body">
                        <div class="form-group mb-0">
                            <label class="font-weight-bold mb-1">Tên Thương hiệu <span class="text-danger">*</span></label>
                            <input type="text" id="quick-brand-name" class="form-control" placeholder="Ví dụ: Apple, Samsung, Dell..." required>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary" id="btn-save-quick-brand">Lưu thương hiệu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
