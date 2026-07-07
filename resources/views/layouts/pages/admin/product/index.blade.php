<x-app-layout>
    @section('title', 'Danh sách sản phẩm')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Danh sách sản phẩm</h1>
        <div class="action" id="action">
            <a href="{{ route('admin.product.create') }}" class="btn btn-primary mb-3">Thêm sản phẩm</a>

            <div class="fillter d-flex flex-wrap align-items-center" style="gap: 10px;">
                <div class="mb-3">
                    <select id="categoryFilter" class="form-control shadow-sm">
                        <option value="">Tất cả danh mục</option>
                        @foreach (\App\Models\Category::all() as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <select id="brandFilter" class="form-control shadow-sm">
                        <option value="">Tất cả thương hiệu</option>
                        @foreach (\App\Models\Brand::where('status', 1)->get() as $brand)
                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <select id="qtyStatusFilter" class="form-control shadow-sm">
                        <option value="">Trạng thái kho (Tất cả)</option>
                        <option value="in_stock">Còn hàng</option>
                        <option value="out_of_stock">Hết hàng</option>
                    </select>
                </div>
                <div class="mb-3">
                    <select id="statusFilter" class="form-control shadow-sm">
                        <option value="">Hiển thị (Tất cả)</option>
                        <option value="1">Hiển thị</option>
                        <option value="0">Ẩn</option>
                    </select>
                </div>
                <div class="mb-3" id="bulk-delete" style="display: none;">
                    <button type="button" class="btn btn-danger shadow-sm">
                        <i class="fa-solid fa-trash-can mr-1"></i> Xóa sản phẩm đã chọn
                    </button>
                </div>
            </div>

        </div>
        <div class="card shadow border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="categories-table" width="100%">
                        <thead class="bg-light text-dark">
                            <tr>
                                <th width="20px"><input type="checkbox" id="select-all"></th>
                                <th>#</th>
                                <th>Hình ảnh</th>
                                <th>Tên sản phẩm</th>
                                <th>Danh mục</th>
                                <th>Thương hiệu</th>
                                <th>Giá bán / Giá gốc</th>
                                <th>Số lượng</th>
                                <th>SKU</th>
                                <th>Trạng thái</th>
                                <th>Ngày tạo</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            $(document).ready(function() {
                var table = $('#categories-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('admin.product.data', [], false) }}",
                        type: 'GET',
                        data: function(d) {
                            d.category_id = $('#categoryFilter').val();
                            d.brand_id = $('#brandFilter').val();
                            d.qty_status = $('#qtyStatusFilter').val();
                            d.status = $('#statusFilter').val();
                        }
                    },
                    columns: [{
                            data: 'checkbox',
                            name: 'checkbox',
                            orderable: false,
                            searchable: false,
                            className: 'text-center'
                        },
                        {
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'image',
                            name: 'image',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'title',
                            name: 'title'
                        },
                        {
                            data: 'category',
                            name: 'category',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'brand',
                            name: 'brand'
                        },
                        {
                            data: 'price_info',
                            name: 'price_info',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'qty',
                            name: 'qty'
                        },
                        {
                            data: 'sku',
                            name: 'sku'
                        },
                        {
                            data: 'status',
                            name: 'status',
                            className: 'text-center'
                        },
                        {
                            data: 'created_at',
                            name: 'created_at'
                        },
                        {
                            data: 'actions',
                            name: 'actions',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    columnDefs: [{
                        targets: 0,
                        render: function(data, type, row) {
                            return '<input type="checkbox" class="row-checkbox" value="' + row.id +
                                '">';
                        }
                    }],
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.1/i18n/vi.json'
                    },
                    order: [
                        [10, 'desc']
                    ], // Sắp xếp mặc định theo ngày tạo
                    responsive: true,
                    autoWidth: false
                });
 
                // Lọc theo các bộ lọc
                $('#categoryFilter, #brandFilter, #qtyStatusFilter, #statusFilter').on('change', function() {
                    table.draw();
                });

                // Khởi tạo tooltip
                $('body').tooltip({
                    selector: '[data-toggle="tooltip"]'
                });

                $('#select-all').click(function() {
                    $('.row-checkbox').prop('checked', this.checked);
                    toggleBulkActions();
                });

                $(document).on('change', '.row-checkbox', function() {
                    var allChecked = $('.row-checkbox:checked').length === $('.row-checkbox').length;
                    $('#select-all').prop('checked', allChecked);
                    toggleBulkActions();
                });

                $(document).on('click', '.clone-product-btn', function(e) {
                    e.preventDefault();
                    var url = $(this).data('url');
                    
                    if (confirm('Bạn có chắc chắn muốn nhân bản sản phẩm này không?')) {
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    showAlertModal(response.message, 'success');
                                    setTimeout(function() {
                                        window.location.href = response.redirect_url;
                                    }, 1000);
                                } else {
                                    showAlertModal(response.message, 'error');
                                }
                            },
                            error: function(xhr) {
                                showAlertModal('Đã xảy ra lỗi khi nhân bản sản phẩm', 'error');
                            }
                        });
                    }
                });

                function toggleBulkActions() {
                    if ($('.row-checkbox:checked').length > 0) {
                        $('#bulk-delete').show();
                    } else {
                        $('#bulk-delete').hide();
                    }
                }

                $('#bulk-delete').click(function(e) {
                    e.preventDefault();
                    var ids = [];
                    $('.row-checkbox:checked').each(function() {
                        ids.push($(this).val());
                    });

                    if (ids.length === 0) {
                        alert('Vui lòng chọn ít nhất một sản phẩm để xóa');
                        return;
                    }

                    if (confirm('Bạn có chắc chắn muốn xóa các sản phẩm đã chọn?')) {
                        $.ajax({
                            url: '{{ route('admin.product.massDestroy') }}',
                            type: 'DELETE',
                            data: {
                                ids: ids,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    table.ajax.reload();
                                    $('#select-all').prop('checked', false);
                                    showAlertModal(response.message, 'success');
                                } else {
                                    showAlertModal(response.message, 'error');
                                }
                            },
                            error: function(xhr) {
                                showAlertModal('Đã xảy ra lỗi khi xóa', 'error');
                            }
                        });
                    }
                });

                // Mở modal Nhập kho nhanh
                $(document).on('click', '.quick-stock-btn', function(e) {
                    e.preventDefault();
                    const productId = $(this).data('id');
                    const productTitle = $(this).data('title');
                    
                    $('#quick-stock-product-id').val(productId);
                    $('#quick-stock-product-title').text(productTitle);
                    $('#quick-stock-name').val(`Nhập bổ sung - ${productTitle} (${new Date().toLocaleDateString('vi-VN')})`);
                    
                    const container = $('#quick-stock-fields-container');
                    container.html('<div class="text-center p-3"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i> <p class="mt-2">Đang tải thông tin sản phẩm...</p></div>');
                    
                    $('#quickStockModal').modal('show');
                    
                    $.ajax({
                        url: `/admin/product/${productId}/stock-info`,
                        type: 'GET',
                        success: function(response) {
                            if (response.success) {
                                const data = response.data;
                                container.empty();
                                
                                if (data.product_type === 'variant') {
                                    let html = `
                                        <table class="table table-bordered table-sm mb-0">
                                            <thead class="bg-dark text-white">
                                                <tr>
                                                    <th>Phiên bản (Biến thể)</th>
                                                    <th>SKU</th>
                                                    <th>Tồn hiện tại</th>
                                                    <th style="width: 25%;">Số lượng nhập <span class="text-danger">*</span></th>
                                                    <th style="width: 30%;">Giá nhập (VNĐ) <span class="text-danger">*</span></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                    `;
                                    
                                    data.variants.forEach(v => {
                                        html += `
                                            <tr>
                                                <td class="align-middle font-weight-bold text-dark">${v.variant_name}</td>
                                                <td class="align-middle">${v.sku}</td>
                                                <td class="align-middle text-center">${v.qty}</td>
                                                <td>
                                                    <input type="number" name="variants[${v.id}][qty]" class="form-control form-control-sm" min="1" placeholder="Số lượng">
                                                </td>
                                                <td>
                                                    <input type="text" name="variants[${v.id}][price]" class="form-control form-control-sm price-format" placeholder="Giá nhập">
                                                </td>
                                            </tr>
                                        `;
                                    });
                                    
                                    html += '</tbody></table>';
                                    container.html(html);
                                } else {
                                    // Single product
                                    let html = `
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label class="small font-weight-bold text-dark">Tồn hiện tại</label>
                                                <input type="text" class="form-control bg-light" readonly value="${data.qty}">
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="small font-weight-bold text-dark">Số lượng nhập <span class="text-danger">*</span></label>
                                                <input type="number" id="quick-stock-qty" class="form-control" min="1" required placeholder="Ví dụ: 10">
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="small font-weight-bold text-dark">Giá nhập (VNĐ) <span class="text-danger">*</span></label>
                                                <input type="text" id="quick-stock-price" class="form-control price-format" required placeholder="Ví dụ: 25.000.000">
                                            </div>
                                        </div>
                                    `;
                                    container.html(html);
                                }
                                
                                // Bind format cho giá
                                bindModalPriceFormatting();
                            } else {
                                container.html(`<div class="alert alert-danger">${response.message}</div>`);
                            }
                        },
                        error: function(xhr) {
                            container.html('<div class="alert alert-danger">Không thể tải thông tin sản phẩm.</div>');
                        }
                    });
                });

                // Format tiền VNĐ tự động khi gõ trong modal
                function formatVND(value) {
                    let num = String(value).replace(/[^\d]/g, '');
                    if (!num || num === '0') return '';
                    return num.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                }

                function unformatVND(value) {
                    return String(value).replace(/[^\d]/g, '');
                }

                function bindModalPriceFormatting() {
                    $('#quickStockModal .price-format').on('input', function() {
                        const pos = this.selectionStart;
                        const oldLen = this.value.length;
                        this.value = formatVND(this.value);
                        const newLen = this.value.length;
                        const newPos = Math.max(0, pos + (newLen - oldLen));
                        this.setSelectionRange(newPos, newPos);
                    });
                }

                // Gửi AJAX submit form nhập kho nhanh
                $('#quick-stock-form').submit(function(e) {
                    e.preventDefault();
                    
                    const productId = $('#quick-stock-product-id').val();
                    const saveBtn = $('#btn-save-quick-stock');
                    const originalHtml = saveBtn.html();
                    
                    saveBtn.html('<i class="fas fa-spinner fa-spin mr-1"></i> Đang lưu...');
                    saveBtn.prop('disabled', true);
                    
                    // Clone data và unformat giá tiền trước khi gửi
                    let formData = {};
                    formData._token = '{{ csrf_token() }}';
                    formData.name = $('#quick-stock-name').val();
                    
                    const priceSingle = document.getElementById('quick-stock-price');
                    if (priceSingle) {
                        formData.qty = $('#quick-stock-qty').val();
                        formData.price = unformatVND(priceSingle.value);
                    } else {
                        // Variants
                        formData.variants = {};
                        $('#quick-stock-fields-container tbody tr').each(function() {
                            const row = $(this);
                            const qtyInput = row.find('input[name*="[qty]"]');
                            const priceInput = row.find('input[name*="[price]"]');
                            
                            const qty = qtyInput.val().trim();
                            const price = unformatVND(priceInput.val());
                            
                            if (qty && price) {
                                // Extract variant ID from name attribute (e.g. variants[15][qty])
                                const nameAttr = qtyInput.attr('name');
                                const matches = nameAttr.match(/variants\[(\d+)\]/);
                                if (matches) {
                                    const variantId = matches[1];
                                    formData.variants[variantId] = {
                                        qty: qty,
                                        price: price
                                    };
                                }
                            }
                        });
                    }
                    
                    $.ajax({
                        url: `/admin/product/${productId}/quick-stock`,
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            saveBtn.html(originalHtml);
                            saveBtn.prop('disabled', false);
                            
                            if (response.success) {
                                $('#quickStockModal').modal('hide');
                                table.ajax.reload(null, false); // reload giữ nguyên trang hiện tại
                                showAlertModal(response.message, 'success');
                            } else {
                                showAlertModal(response.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            saveBtn.html(originalHtml);
                            saveBtn.prop('disabled', false);
                            const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Lỗi không xác định';
                            showAlertModal('Lỗi nhập kho: ' + msg, 'error');
                        }
                    });
                });
            });
        </script>
    @endpush

    <style>
        .action {
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            align-content: center;
            justify-content: space-between;
            align-items: center;
        }

        #bulk-actions {
            gap: 5px;
        }

        .dropdown-menu {
            margin-top: 0 !important;
        }
    </style>
    <!-- Modal Nhập Kho Nhanh -->
    <div class="modal fade" id="quickStockModal" tabindex="-1" role="dialog" aria-labelledby="quickStockModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title font-weight-bold" id="quickStockModalLabel"><i class="fas fa-warehouse mr-1"></i> Nhập kho nhanh</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="quick-stock-form">
                    @csrf
                    <input type="hidden" id="quick-stock-product-id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="font-weight-bold text-dark">Tên đợt nhập kho <span class="text-danger">*</span></label>
                            <input type="text" id="quick-stock-name" class="form-control" required placeholder="Ví dụ: Nhập bổ sung hàng hot...">
                        </div>
                        
                        <div class="card bg-light border-left-info py-2 mb-3">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Sản phẩm đang chọn:</div>
                                        <div class="h6 mb-0 font-weight-bold text-gray-800" id="quick-stock-product-title"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="quick-stock-fields-container">
                            <!-- HTML input generated dynamically via JS -->
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-success" id="btn-save-quick-stock">Xác nhận nhập kho</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
