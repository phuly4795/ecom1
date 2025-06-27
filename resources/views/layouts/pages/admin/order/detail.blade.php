<x-app-layout>
    @section('title', ($isEdit ? 'Chỉnh sửa' : 'Chi tiết') . ' đơn hàng #' . $order->order_code)
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">{{ $isEdit ? 'Chỉnh sửa' : 'Chi tiết' }} đơn hàng #{{ $order->order_code }}
        </h1>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Thông tin đơn hàng</h6>
            </div>
            <div class="card-body">
                @if ($isEdit)
                    <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <h6 class="font-weight-bold text-primary">Địa chỉ thanh toán</h6>
                        <p><strong>Khách hàng:</strong> {{ $order->billing_full_name }}</p>
                        <p><strong>Email:</strong> {{ $order->billing_email }}</p>
                        <p><strong>Số điện thoại:</strong> {{ $order->billing_telephone }}</p>
                        <p><strong>Địa chỉ:</strong>
                            {{ implode(
                                ', ',
                                array_filter([
                                    $order->billing_address,
                                    $order->billingWard ? $order->billingWard->name : null,
                                    $order->billingDistrict ? $order->billingDistrict->name : null,
                                    $order->billingProvince ? $order->billingProvince->name : null,
                                ]),
                            ) }}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="font-weight-bold text-primary">Địa chỉ nhận hàng</h6>
                        @if ($order->shippingAddress)
                            <p><strong>Khách hàng:</strong> {{ $order->shippingAddress->full_name }}</p>
                            <p><strong>Email:</strong> {{ $order->shippingAddress->email }}</p>
                            <p><strong>Số điện thoại:</strong> {{ $order->shippingAddress->telephone }}</p>
                            <p><strong>Địa chỉ:</strong>
                                {{ implode(
                                    ', ',
                                    array_filter([
                                        $order->shippingAddress->address,
                                        $order->shippingAddress->ward ? $order->shippingAddress->ward->name : null,
                                        $order->shippingAddress->district ? $order->shippingAddress->district->name : null,
                                        $order->shippingAddress->province ? $order->shippingAddress->province->name : null,
                                    ]),
                                ) }}
                            </p>
                        @else
                            <p>Không có địa chỉ nhận hàng riêng, sử dụng địa chỉ thanh toán.</p>
                        @endif
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status"><strong>Trạng thái:</strong></label>
                            @if ($isEdit)
                                <select name="status" id="status"
                                    class="form-control @error('status') is-invalid @enderror">
                                    @foreach ($statuses as $value => $label)
                                        <option value="{{ $value }}"
                                            {{ $order->status == $value ? 'selected' : '' }}>{{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            @else
                                <span
                                    class="badge {{ match ($order->status) {
                                        'waiting_pay' => 'bg-danger',
                                        'pending' => 'bg-warning',
                                        'processing' => 'bg-info',
                                        'completed' => 'bg-success',
                                        'delivered' => 'bg-success',
                                        'cancelled' => 'bg-danger',
                                        default => 'bg-secondary',
                                    } }}">
                                    {{ match ($order->status) {
                                        'waiting_pay' => 'Chờ thanh toán',
                                        'pending' => 'Đang chờ',
                                        'processing' => 'Đang xử lý',
                                        'completed' => 'Hoàn thành',
                                        'delivered' => 'Đã giao',
                                        'cancelled' => 'Đã hủy',
                                        default => ucfirst($order->status),
                                    } }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Phương thức thanh toán:</strong>
                            {{ match ($order->payment_method) {
                                'cash' => 'Tiền mặt',
                                'transfer' => 'Chuyển khoản',
                                default => ucfirst($order->payment_method),
                            } }}
                        <p><strong>Phí vận chuyển:</strong> {{ number_format($order->shipping_fee) }} đ</p>
                        <p><strong>Tổng tiền:</strong> {{ number_format($order->total_amount) }} đ</p>
                        <p><strong>Ngày tạo:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                        @if (isset($order->coupon_code))
                            <p><strong>Mã giảm giá:</strong> {{ $order->coupon_code }} <b
                                    style="color: red">(-{{ number_format($order->discount_amount) . ' vnđ' }})</b></p>
                        @endif

                    </div>
                </div>

                @if ($isEdit)
                    <div class="form-group">
                        <label for="note"><strong>Ghi chú:</strong></label>
                        <textarea name="note" id="note" class="form-control @error('note') is-invalid @enderror" rows="4">{{ old('note', $order->note) }}</textarea>
                        @error('note')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                @endif

                @if ($isEdit)
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Hủy</a>
                    </div>
                @endif

                @if ($isEdit)
                    </form>
                @endif
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Chi tiết sản phẩm</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Hình ảnh</th>
                                <th>Sản phẩm</th>
                                <th>Biến thể</th>
                                <th>Giá</th>
                                <th>Số lượng</th>
                                <th>Tổng</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->orderDetails as $detail)
                                @php
                                    $image = $detail->product->productImages->where('type', 1)->first()->image ?? '';
                                    $imagePath = $image ? asset('storage/' . $image) : asset('asset/img/no-image.png');
                                @endphp
                                <tr>
                                    <td><img src="{{ $imagePath }}" alt="ảnh đại diện"
                                            style="width: 60px; height: 60px; object-fit: cover;" class="img-thumbnail">
                                    </td>
                                    <td>{{ $detail->product_name }}</td>
                                    <td>{{ $detail->productVariant ? $detail->productVariant->variant_name : '-' }}
                                    </td>
                                    <td>{{ number_format($detail->price) }} đ</td>
                                    <td>{{ $detail->quantity }}</td>
                                    <td>{{ number_format($detail->total_price) }} đ</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if (!$isEdit && $order->note)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Ghi chú</h6>
                </div>
                <div class="card-body">
                    <p>{{ $order->note }}</p>
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                @if (session('success'))
                    showAlertModal('{{ session('success') }}', 'success');
                @endif
            });
        </script>
    @endpush
</x-app-layout>
