<x-app-layout>
    @section('title', 'Danh sách đơn hàng')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Danh sách đơn hàng</h1>
        <div class="action" id="action">
            <div class="fillter">
                <div class="btn-group mb-3">
                    <select id="statusFilter" class="form-control">
                        <option value="">Tất cả trạng thái</option>
                        <option value="pending">Đang chờ</option>
                        <option value="processing">Đang xử lý</option>
                        <option value="completed">Hoàn thành</option>
                        <option value="cancelled">Đã hủy</option>
                    </select>
                </div>
                <div class="btn-group mb-3" id="bulk-delete" style="display: none;">
                    <button type="button" class="btn btn-primary">
                        <span><i class="fa-solid fa-eraser"></i> Xóa đơn hàng</span>
                    </button>
                </div>
            </div>
        </div>
        <div class="card shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="orders-table" width="100%">
                        <thead>
                            <tr>
                                <th width="20px"><input type="checkbox" id="select-all"></th>
                                <th>Mã đơn hàng</th>
                                <th>Khách hàng</th>
                                <th>Số lượng sản phẩm</th>
                                <th>Tổng tiền</th>
                                <th>Phương thức thanh toán</th>
                                <th>Trạng thái</th>
                                <th>Ngày tạo</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            $(document).ready(function() {
                var table = $('#orders-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('admin.orders.data') }}",
                        type: 'GET',
                        data: function(d) {
                            d.status = $('#statusFilter').val();
                        }
                    },
                    columns: [{
                            data: 'checkbox',
                            name: 'checkbox',
                            orderable: false,
                            searchable: false,
                            className: 'text-center'
                        },
                        {
                            data: 'order_code',
                            name: 'order_code'
                        },
                        {
                            data: 'customer',
                            name: 'customer'
                        },
                        {
                            data: 'total_items',
                            name: 'total_items',
                            width: '8%',
                            className: 'text-center'
                        },
                        {
                            data: 'total_amount',
                            name: 'total_amount'
                        },
                        {
                            data: 'payment_method',
                            name: 'payment_method',
                            width: '10%',
                        },
                        {
                            data: 'status',
                            name: 'status',
                            className: 'text-center'
                        },
                        {
                            data: 'created_at',
                            name: 'created_at'
                        },
                        {
                            data: 'actions',
                            name: 'actions',
                            orderable: false,
                            searchable: false,
                            className: 'text-center'
                        }
                    ],
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.1/i18n/vi.json'
                    },
                    order: [
                        [8, 'desc']
                    ], // Cột "Ngày tạo" giờ là cột thứ 8 (0-based index)
                    responsive: true,
                    autoWidth: false
                });

                $('#statusFilter').on('change', function() {
                    table.draw();
                });

                $('body').tooltip({
                    selector: '[data-toggle="tooltip"]'
                });

                $('#select-all').click(function() {
                    $('.row-checkbox').prop('checked', this.checked);
                    toggleBulkActions();
                });

                $(document).on('change', '.row-checkbox', function() {
                    var allChecked = $('.row-checkbox:checked').length === $('.row-checkbox').length;
                    $('#select-all').prop('checked', allChecked);
                    toggleBulkActions();
                });

                function toggleBulkActions() {
                    if ($('.row-checkbox:checked').length > 0) {
                        $('#bulk-delete').show();
                    } else {
                        $('#bulk-delete').hide();
                    }
                }

                $('#bulk-delete').click(function(e) {
                    e.preventDefault();
                    var ids = [];
                    $('.row-checkbox:checked').each(function() {
                        ids.push($(this).val());
                    });

                    if (ids.length === 0) {
                        alert('Vui lòng chọn ít nhất một đơn hàng để xóa');
                        return;
                    }

                    if (confirm('Bạn có chắc chắn muốn xóa các đơn hàng đã chọn?')) {
                        $.ajax({
                            url: '{{ route('admin.orders.massDestroy') }}',
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

                $(document).on('click', '.delete-order', function() {
                    var id = $(this).data('id');
                    if (confirm('Bạn có chắc chắn muốn xóa đơn hàng này?')) {
                        $.ajax({
                            url: '{{ url('admin/orders') }}/' + id,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    table.ajax.reload();
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

        .fillter {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .badge {
            padding: 5px 10px;
            border-radius: 4px;
            color: white;
        }
    </style>
</x-app-layout>
