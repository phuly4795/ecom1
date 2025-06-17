<x-app-layout>
    @section('title', 'Danh sách trang nội dung')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Danh sách trang nội dung</h1>
        <div class="action">
            <a href="{{ route('admin.pages.create') }}" class="btn btn-primary mb-3">Thêm trang mới</a>
        </div>
        <div class="card shadow">
            <div class="card-body">
                <table class="table table-bordered" id="pages-table" width="100%">
                    <thead>
                        <tr>
                            <th>Tiêu đề</th>
                            <th>Đường dẫn</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pages as $page)
                            <tr>
                                <td>{{ $page->title }}</td>
                                <td>{{ $page->slug }}</td>
                                <td class="text-center">
                                    @if ($page->is_active)
                                        <span class="badge badge-success">Hiển thị</span>
                                    @else
                                        <span class="badge badge-secondary">Ẩn</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.pages.edit', $page->id) }}"
                                        class="btn btn-sm btn-warning">Sửa</a>
                                    <form action="{{ route('admin.pages.destroy', $page->id) }}" method="POST"
                                        style="display:inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger"
                                            onclick="return confirm('Bạn có chắc chắn muốn xóa?')">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
