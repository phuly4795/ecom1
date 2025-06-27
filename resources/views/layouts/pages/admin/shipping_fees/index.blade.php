<x-app-layout>
    @section('title', 'Danh sách phí vận chuyển')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Danh sách phí vận chuyển</h1>
        <div class="action" id="action">
            <a href="{{ route('admin.shipping_fees.create') }}" class="btn btn-primary mb-3">Thêm phí vận chuyển</a>
            <!-- Nút mở modal -->
            <button type="button" class="btn btn-success mb-3" data-toggle="modal" data-target="#importModal">
                Import Excel
            </button>
            <div class="btn-group mb-3" id="bulk-delete" style="display: none;">
                <button type="button" class="btn btn-danger">
                    <i class="fa-solid fa-trash"></i> Xóa phí vận chuyển
                </button>
            </div>
        </div>
        <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('admin.shipping_fees.import') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <h4 class="modal-title" id="importModalLabel">Nhập phí vận chuyển từ Excel</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Đóng"><span
                                    aria-hidden="true">&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="file">Chọn tệp Excel</label>
                                <input type="file" class="form-control" name="file" id="file" required
                                    accept=".xlsx,.xls">
                                <p class="help-block">
                                    Bạn có thể <a href="{{ asset('sample/shipping_fee_template.xlsx') }}"
                                        target="_blank">tải file mẫu tại đây</a>.
                                </p>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Import</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Hủy</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="card shadow">
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tỉnh/TP</th>
                            <th>Quận/Huyện</th>
                            <th>Phí vận chuyển</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($fees as $fee)
                            <tr>
                                <td>{{ $fee->province->name ?? $fee->province_id }}</td>
                                <td>{{ $fee->district->name ?? '-' }}</td>
                                <td>{{ number_format($fee->fee) . ' vnđ' }}</td>
                                <td>
                                    <a href="{{ route('admin.shipping_fees.edit', $fee) }}"
                                        class="btn btn-sm btn-warning">Sửa</a>
                                    <form method="POST" action="{{ route('admin.shipping_fees.destroy', $fee) }}"
                                        style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger"
                                            onclick="return confirm('Xóa phí này?')">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        <tr style="background: #f9f9f9;">
                            <td colspan="2"><strong>Phí mặc định</strong></td>
                            <td><strong>{{ number_format(config('settings.default_shipping_fee', 50000)) }}
                                    vnđ</strong></td>
                            <td>
                                {{-- Nút chỉnh sửa (nếu có cấu hình trong DB) --}}
                                <a href="{{ route('admin.settings.edit', 'default_shipping_fee') }}"
                                    class="btn btn-sm btn-info">Sửa</a>
                            </td>
                        </tr>
                    </tbody>
                </table>

                {{ $fees->links() }}
            </div>
        </div>
    </div>

    @push('styles')
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
        <style>
            #sortable tr {
                cursor: move;
            }

            #sortable tr.ui-sortable-helper {
                background-color: #f8f9fa;
                display: table;
            }

            #sortable tr.ui-sortable-placeholder {
                visibility: visible !important;
                background-color: #f1f1f1;
            }

            .action {
                display: flex;
                flex-direction: row;
                flex-wrap: nowrap;
                align-content: center;
                justify-content: space-between;
                align-items: center;
            }
        </style>
    @endpush
</x-app-layout>
