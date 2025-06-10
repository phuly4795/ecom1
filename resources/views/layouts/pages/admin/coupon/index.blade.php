<x-app-layout>
    @section('title', 'Danh sách khuyến mãi')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Danh sách khuyến mãi</h1>
        <div class="action" id="action">
            <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary mb-3">Thêm khuyến mãi</a>
            <div class="btn-group mb-3" id="bulk-delete" style="display: none;">
                <button type="button" class="btn btn-danger">
                    <i class="fa-solid fa-trash"></i> Xóa khuyến mãi đã chọn
                </button>
            </div>
        </div>
        <div class="card shadow">
            <div class="card-body">
                <table class="table table-bordered" id="coupons-table" width="100%">
                    <thead>
                        <tr>
                            <th width="20px"><input type="checkbox" id="select-all"></th>
                            <th>Mã khuyến mãi</th>
                            <th>Loại giảm giá</th>
                            <th>Giá trị</th>
                            <th>Ngày hoạt động</th>
                            <th>Ngày kết thúc</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody id="sortable">
                        <!-- Dữ liệu sẽ được tải bằng DataTables -->
                    </tbody>
                </table>
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

    @push('scripts')
        <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
        <script>
            $(document).ready(function() {
                var table = $('#coupons-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('admin.coupons.data') }}',
                    columns: [{
                            data: 'checkbox',
                            name: 'checkbox',
                            orderable: false,
                            searchable: false,
                            className: 'text-center'
                        },
                        {
                            data: 'code',
                            name: 'code'
                        },
                        {
                            data: 'type',
                            name: 'type'
                        },
                        {
                            data: 'value',
                            name: 'value',
                            className: 'text-center'
                        },
                        {
                            data: 'start_date',
                            name: 'start_date'
                        },
                        {
                            data: 'end_date',
                            name: 'end_date'
                        },
                        {
                            data: 'is_active',
                            name: 'is_active',
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
                    }],
                });

                // Chọn tất cả/bỏ chọn tất cả
                $('#select-all').click(function() {
                    $('.row-checkbox').prop('checked', this.checked);
                    toggleBulkActions();
                });

                $('body').tooltip({
                    selector: '[data-toggle="tooltip"]'
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
                        alert('Vui lòng chọn ít nhất một khuyến mãi để xóa');
                        return;
                    }

                    if (confirm('Bạn có chắc chắn muốn xóa các khuyến mãi đã chọn?')) {
                        $.ajax({
                            url: '{{ route('admin.coupons.massDestroy') }}',
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
</x-app-layout>
