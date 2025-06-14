<x-app-layout>
    @section('title', 'Danh sách danh mục')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Danh sách danh mục</h1>
        <div class="action" id="action">
            <a href="{{ route('admin.category.create') }}" class="btn btn-primary mb-3">Thêm danh mục</a>
            <div class="btn-group mb-3" id="bulk-delete" style="display: none;">
                <button type="button" class="btn btn-danger">
                    <i class="fa-solid fa-trash"></i> Xóa danh mục đã chọn
                </button>
            </div>
        </div>
        <div class="card shadow">
            <div class="card-body">
                <table class="table table-bordered" id="categories-table" width="100%">
                    <thead>
                        <tr>
                            <th width="20px"><input type="checkbox" id="select-all"></th>
                            <th>STT</th>
                            <th>Tên danh mục</th>
                            <th>Slug</th>
                            <th>Vị trí</th>
                            <th>Ngày tạo</th>
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
                var table = $('#categories-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('admin.category.data') }}',
                    columns: [
                        {
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
                            data: 'slug',
                            name: 'slug'
                        },
                        {
                            data: 'sort',
                            name: 'sort',
                            className: 'text-center'
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
                    columnDefs: [
                        {
                            targets: 0,
                            render: function(data, type, row) {
                                return '<input type="checkbox" class="row-checkbox" value="' + row.id + '">';
                            }
                        }
                    ],
                    drawCallback: function(settings) {
                        // Khởi tạo sortable sau khi bảng được tải
                        initSortable();
                    }
                });

                function initSortable() {
                    $('#sortable').sortable({
                        items: 'tr',
                        cursor: 'move',
                        opacity: 0.6,
                        update: function(event, ui) {
                            updateOrder();
                        },
                        placeholder: 'ui-sortable-placeholder',
                        forcePlaceholderSize: true,
                        helper: function(e, tr) {
                            var $originals = tr.children();
                            var $helper = tr.clone();
                            $helper.children().each(function(index) {
                                $(this).width($originals.eq(index).width());
                            });
                            return $helper;
                        }
                    }).disableSelection();
                }

                function updateOrder() {
                    var order = [];
                    $('#sortable tr').each(function(index) {
                        var id = $(this).find('.row-checkbox').val();
                        order.push({
                            id: id,
                            position: index + 1
                        });
                    });

                    $.ajax({
                        url: '{{ route("admin.category.updateOrder") }}',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            order: order,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                table.ajax.reload(null, false); // Reload không reset paging
                                toastr.success(response.message);
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function(xhr) {
                            toastr.error('Đã xảy ra lỗi khi cập nhật vị trí');
                            table.ajax.reload(null, false); // Rollback nếu có lỗi
                        }
                    });
                }

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
                            url: '{{ route("admin.category.massDestroy") }}',
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