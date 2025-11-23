<x-app-layout>
    @section('title', 'Chi tiết đợt nhập')
    <div class="container-fluid">
        <div class="head">
            <h1 class="h3 mb-4 text-gray-800">Chi tiết đợt nhập</h1>
            <a href="{{route('admin.warehouse.index')}}" class="btn btn-success mb-3">
                Trở về
            </a>
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
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                            <tr>

                                <td><a
                                        href="{{ route('admin.product.edit', $item->product->id) }}">{{ Str::limit($item->product->title, 50) }}</a>
                                </td>
                                <td>{{ Str::limit($item->productVariant->variant_name ?? '-', 50) }}</td>
                                <td>{{ $item->qty }}</td>
                                <td>{{ number_format($item->price) . ' vnđ' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{ $data->links() }}
            </div>
        </div>
    </div>

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

        .head {
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</x-app-layout>
