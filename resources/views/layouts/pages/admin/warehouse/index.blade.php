<x-app-layout>
    @section('title', 'Quản lý kho')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Quản lý kho</h1>
        <div class="action" id="action">
            {{-- <a href="{{ route('admin.shipping_fees.create') }}" class="btn btn-primary mb-3">Thêm phí vận chuyển</a> --}}
            <!-- Nút mở modal -->
            <button type="button" class="btn btn-success mb-3" data-toggle="modal" data-target="#importModal">
                Nhập sản phẩm
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
                    <form action="{{ route('admin.warehouse.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <h4 class="modal-title" id="importModalLabel">Nhập sản phẩm từ Excel</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Đóng"><span
                                    aria-hidden="true">&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="file">Chọn tệp Excel</label>
                                <input type="file" class="form-control" name="file" id="file" required
                                    accept=".xlsx,.xls">
                                <p class="help-block">
                                    Bạn có thể <a href="{{ route('admin.warehouse.exportTemplate') }}" target="_blank">tải
                                        file mẫu tại đây</a>.
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

        @if ($errors->any())
            <div class="alert alert-danger">
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

        <div class="card shadow">
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tên sản phẩm</th>
                            <th>Tên biến thể</th>
                            <th>Số lượng</th>
                            <th>Giá nhập</th>
                            <th>Ngày nhập</th>
                            <th>Người nhập</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                            <tr>

                                <td><a
                                        href="{{ route('admin.product.edit', $item->products->id) }}">{{ Str::limit($item->products->title, 50) }}</a>
                                </td>
                                <td>{{ Str::limit($item->productVariants->variant_name ?? '-', 50) }}</td>
                                <td>{{ $item->qty }}</td>
                                <td>{{ number_format($item->price) . ' vnđ' }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->create_at)->format('d/m/Y') }}</td>
                                <td>{{ $item->created_by }}</td>
                                <td>
                                    <a href="{{ route('admin.shipping_fees.edit', $item) }}"
                                        class="btn btn-sm btn-warning">Sửa</a>
                                    <form method="POST" action="{{ route('admin.shipping_fees.destroy', $item) }}"
                                        style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger"
                                            onclick="return confirm('Xóa phí này?')">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{ $data->links() }}
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
