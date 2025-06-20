<x-app-layout>
    @section('title', 'Danh sách liên hệ')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Danh sách liên hệ</h1>
        <div class="card shadow">
            <div class="card-body">
                <table class="table table-bordered" id="contacts-table" width="100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tên</th>
                            <th>Email</th>
                            <th>Nội dung</th>
                            <th>Thời gian</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                $('#contacts-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('admin.contacts.data') }}',
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'email',
                            name: 'email'
                        },
                        {
                            data: 'content',
                            name: 'content'
                        },
                        {
                            data: 'created_at',
                            name: 'created_at'
                        },
                        {
                            data: 'is_read',
                            name: 'is_read'
                        },
                        {
                            data: 'actions',
                            name: 'actions',
                            orderable: false,
                            searchable: false
                        },
                    ]
                });
            });
        </script>
    @endpush
</x-app-layout>
