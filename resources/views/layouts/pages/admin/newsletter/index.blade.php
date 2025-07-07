<x-app-layout>
    @section('title', 'Danh sách đăng ký nhận tin mới')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Danh sách đăng ký nhận tin mới</h1>
        <div class="action" id="action">
            <div class="btn-group mb-3" id="bulk-delete" style="display: none;">
                <button type="button" class="btn btn-primary">
                    <span class="visually-hidden"><i class="fa-solid fa-eraser"></i> Xóa đăng ký nhận tin mới</span>
                </button>
            </div>
        </div>
        <div class="card shadow">
            <div class="card-body">
                <table class="table table-bordered" id="categories-table" width="100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Địa chỉ email</th>
                            <th>Ngày đăng ký</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($getAll as $item)
                            <tr>
                                <td>
                                    {{ $item->id }}
                                </td>
                                <td>
                                    {{ $item->email }}
                                </td>
                                <td>{{ \Carbon\Carbon::parse($item->created_at)->format('H:i:s d/m/Y') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

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
