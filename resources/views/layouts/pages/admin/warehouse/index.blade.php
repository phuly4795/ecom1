<x-app-layout>
    @section('title', 'Quản lý kho')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Quản lý kho</h1>
        <div class="action" id="action">
            <div>
                <a href="{{ route('admin.warehouse.create') }}" class="btn btn-primary mb-3 mr-2">
                    <i class="fas fa-plus fa-sm mr-1"></i> Lập phiếu nhập kho
                </a>
                <button type="button" class="btn btn-success mb-3" data-toggle="modal" data-target="#importModal">
                    <i class="fas fa-file-excel fa-sm mr-1"></i> Nhập từ Excel
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
                                <label for="name">Tên đợt nhập</label>
                                <input type="text" class="form-control" name="name" id="name" required
                                    placeholder="Nhập tên đợt nhập...">
                            </div>
                            <div class="form-group">
                                <label for="file">Chọn tệp Excel</label>
                                <input type="file" class="form-control" name="file" id="file" required
                                    accept=".xlsx,.xls">
                                <p class="help-block">
                                    Bạn có thể <a href="{{ route('admin.warehouse.exportTemplate') }}"
                                        target="_blank">tải
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
                            <th>#</th>
                            <th>Tên đợt nhập</th>
                            <th>Người nhập</th>
                            <th>Ngày nhập</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ Str::limit($item->name, 50, '...') }}</td>
                                <td>{{ $item->created_at ? $item->created_at->format('d/m/Y') : '' }}</td>
                                <td>{{ $item->user->name ?? 'System' }}</td>
                                <td><a href="{{ route('admin.warehouse.detail', $item) }}"
                                        class="btn btn-primary">Xem chi tiết</a>
                                    |

                                    <a href="{{ route('admin.warehouse.exportWarehouseReceipt', $item) }}"
                                        class="btn btn-warning">Xuất phiếu nhập</a>
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
