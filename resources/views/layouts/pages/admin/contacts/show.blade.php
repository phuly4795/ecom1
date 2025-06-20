<x-app-layout>
    @section('title', 'Chi tiết liên hệ')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Chi tiết liên hệ</h1>

        <div class="card shadow mb-4">
            <div class="card-body">
                <p><strong>Tên:</strong> {{ $contact->name }}</p>
                <p><strong>Email:</strong> {{ $contact->email }}</p>
                <p><strong>Nội dung:</strong><br>{{ $contact->content }}</p>
                <p><strong>Thời gian gửi:</strong> {{ $contact->created_at->format('d/m/Y H:i') }}</p>
                <p><strong>Trạng thái:</strong> {!! $contact->is_read
                    ? '<span class="badge badge-success">Đã đọc</span>'
                    : '<span class="badge badge-warning">Chưa đọc</span>' !!}</p>

                <a href="{{ route('admin.contacts.index') }}" class="btn btn-secondary">Quay lại danh sách</a>
            </div>
        </div>
    </div>
</x-app-layout>
