<x-app-layout>
    @section('title', 'Danh sách danh mục phụ')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Danh sách danh mục phụ</h1>
        <div class="action" id="action">
            <a href="{{ route('admin.sub_category.create') }}" class="btn btn-primary mb-3">Thêm danh mục phụ</a>
            <div class="btn-group mb-3" id="bulk-delete" style="display: none;">
                <button type="button" class="btn btn-primary">
                    <span class="visually-hidden"><i class="fa-solid fa-eraser"></i> Xóa danh mục phụ</span>
                </button>
            </div>
        </div>
        <div class="card shadow">
            <div class="card-body">
                <table class="table table-bordered" id="categories-table" width="100%">
                    <thead>
                        <tr>
                            <th width="20px"><input type="checkbox" id="select-all"></th>
                            <th>ID</th>
                            <th>Tên danh mục phụ</th>
                            <th>Danh mục cha</th>
                            <th>Slug</th>
                            <th>Ngày tạo</th>
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
                var table = $('#categories-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('admin.sub_category.data') }}',
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
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'categories',
                            name: 'categories'
                        },
                        {
                            data: 'slug',
                            name: 'slug'
                        },
                        {
                            data: 'created_at',
                            name: 'created_at'
                        },
                        {
                            data: 'status',
                            name: 'status',
                            className: 'text-center'
                        },
                        {
                            data: 'actions',
                            name: 'actions',
                            orderable: false,
                            searchable: false
                        },
                    ],
                    columnDefs: [{
                        targets: 0,
                        render: function(data, type, row) {
                            return '<input type="checkbox" class="row-checkbox" value="' + row.id +
                                '">';
                        }
                    }]
                });

                // Chọn tất cả/bỏ chọn tất cả
                $('#select-all').click(function() {
                    $('.row-checkbox').prop('checked', this.checked);
                    toggleBulkActions();
                });

                // Khi click vào checkbox từng dòng
                $(document).on('change', '.row-checkbox', function() {
                    var allChecked = $('.row-checkbox:checked').length === $('.row-checkbox').length;
                    $('#select-all').prop('checked', allChecked);
                    toggleBulkActions();
                });

                // Hiển thị/ẩn bulk actions
                function toggleBulkActions() {
                    if ($('.row-checkbox:checked').length > 0) {
                        $('#bulk-delete').show();
                    } else {
                        $('#bulk-delete').hide();
                    }
                }

                // Xóa hàng loạt
                $('#bulk-delete').click(function(e) {
                    e.preventDefault();
                    var ids = [];
                    $('.row-checkbox:checked').each(function() {
                        ids.push($(this).val());
                    });

                    if (ids.length === 0) {
                        alert('Vui lòng chọn ít nhất một danh mục để xóa');
                        return;
                    }

                    if (confirm('Bạn có chắc chắn muốn xóa các danh mục đã chọn?')) {
                        $.ajax({
                            url: '{{ route('admin.sub_category.massDestroy') }}',
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
