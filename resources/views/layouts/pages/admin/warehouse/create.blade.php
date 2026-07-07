<x-app-layout>
    @section('title', 'Tạo phiếu nhập kho thủ công')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Tạo phiếu nhập kho</h1>
            <a href="{{ route('admin.warehouse.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50 mr-1"></i> Quay lại
            </a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger shadow-xs">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold"><i class="fas fa-file-invoice mr-1"></i> Chi tiết phiếu nhập kho</h6>
            </div>
            <div class="card-body">
                <form id="manual-receipt-form" action="{{ route('admin.warehouse.store') }}" method="POST">
                    @csrf

                    <div class="form-group col-md-6 pl-0">
                        <label for="name" class="font-weight-bold text-dark">Tên đợt nhập kho <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="name" required 
                            value="{{ old('name') }}" placeholder="Ví dụ: Nhập lô hàng iPhone tháng 7...">
                    </div>

                    <div class="table-responsive mt-4">
                        <table class="table table-bordered" id="items-table">
                            <thead class="bg-light text-dark font-weight-bold">
                                <tr>
                                    <th style="width: 50%;">Sản phẩm / Biến thể <span class="text-danger">*</span></th>
                                    <th style="width: 20%;">Số lượng nhập <span class="text-danger">*</span></th>
                                    <th style="width: 25%;">Giá nhập (VNĐ) <span class="text-danger">*</span></th>
                                    <th style="width: 5%;" class="text-center">Xóa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="item-row" data-index="0">
                                    <td>
                                        <select name="items[0][identifier]" class="form-control product-select" required style="width: 100%;">
                                            <option value="">-- Gõ để tìm kiếm sản phẩm hoặc SKU --</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="items[0][qty]" class="form-control" min="1" required placeholder="Ví dụ: 10">
                                    </td>
                                    <td>
                                        <input type="text" name="items[0][price]" class="form-control price-format" required placeholder="Ví dụ: 25.000.000">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-circle btn-sm remove-row-btn" disabled>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <button type="button" class="btn btn-info btn-icon-split mb-4" id="add-row-btn">
                        <span class="icon text-white-50">
                            <i class="fas fa-plus"></i>
                        </span>
                        <span class="text">Thêm sản phẩm</span>
                    </button>

                    <hr>

                    <div class="text-right">
                        <button type="submit" class="btn btn-success btn-icon-split">
                            <span class="icon text-white-50">
                                <i class="fas fa-check"></i>
                            </span>
                            <span class="text">Xác nhận nhập kho</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                let rowIndex = 1;

                // Cấu hình Select2 tìm kiếm sản phẩm qua AJAX
                function initProductSelect2(element) {
                    $(element).select2({
                        ajax: {
                            url: "{{ route('admin.warehouse.searchProducts') }}",
                            dataType: 'json',
                            delay: 250,
                            data: function(params) {
                                return {
                                    q: params.term // từ khóa tìm kiếm
                                };
                            },
                            processResults: function(data) {
                                return {
                                    results: data.results
                                };
                            },
                            cache: true
                        },
                        minimumInputLength: 1,
                        placeholder: '-- Gõ để tìm kiếm sản phẩm hoặc SKU --',
                        language: {
                            inputTooShort: function() {
                                return "Vui lòng nhập 1 ký tự trở lên để tìm kiếm";
                            },
                            noResults: function() {
                                return "Không tìm thấy sản phẩm";
                            },
                            searching: function() {
                                return "Đang tìm...";
                            }
                        }
                    });
                }

                // Format tiền VNĐ tự động khi gõ
                function formatVND(value) {
                    let num = String(value).replace(/[^\d]/g, '');
                    if (!num || num === '0') return '';
                    return num.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                }

                function unformatVND(value) {
                    return String(value).replace(/[^\d]/g, '');
                }

                // Bind định dạng cho các input giá
                function bindPriceFormatting(element) {
                    $(element).on('input', function() {
                        const pos = this.selectionStart;
                        const oldLen = this.value.length;
                        this.value = formatVND(this.value);
                        const newLen = this.value.length;
                        const newPos = Math.max(0, pos + (newLen - oldLen));
                        this.setSelectionRange(newPos, newPos);
                    });
                }

                // Khởi tạo dòng đầu tiên
                initProductSelect2('.product-select');
                bindPriceFormatting('.price-format');

                // Thêm dòng mới
                $('#add-row-btn').click(function() {
                    const newRow = `
                        <tr class="item-row" data-index="${rowIndex}">
                            <td>
                                <select name="items[${rowIndex}][identifier]" class="form-control product-select" required style="width: 100%;">
                                    <option value="">-- Gõ để tìm kiếm sản phẩm hoặc SKU --</option>
                                </select>
                            </td>
                            <td>
                                <input type="number" name="items[${rowIndex}][qty]" class="form-control" min="1" required placeholder="Ví dụ: 10">
                            </td>
                            <td>
                                <input type="text" name="items[${rowIndex}][price]" class="form-control price-format" required placeholder="Ví dụ: 25.000.000">
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-danger btn-circle btn-sm remove-row-btn">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;

                    $('#items-table tbody').append(newRow);
                    
                    // Khởi tạo select2 và format giá cho dòng mới
                    initProductSelect2(`tr[data-index="${rowIndex}"] .product-select`);
                    bindPriceFormatting(`tr[data-index="${rowIndex}"] .price-format`);

                    rowIndex++;
                    toggleRemoveButtons();
                });

                // Xóa dòng
                $(document).on('click', '.remove-row-btn', function() {
                    $(this).closest('tr').remove();
                    toggleRemoveButtons();
                });

                // Kiểm soát việc cho phép bấm nút Xóa (nếu chỉ còn 1 dòng thì disable)
                function toggleRemoveButtons() {
                    const rowCount = $('#items-table tbody tr').length;
                    if (rowCount <= 1) {
                        $('.remove-row-btn').prop('disabled', true);
                    } else {
                        $('.remove-row-btn').prop('disabled', false);
                    }
                }

                // Trước khi submit: Loại bỏ dấu chấm định dạng VNĐ để gửi số nguyên về server
                $('#manual-receipt-form').submit(function() {
                    $('.price-format').each(function() {
                        $(this).val(unformatVND($(this).val()));
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
