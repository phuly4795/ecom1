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
</x-app-layout>
