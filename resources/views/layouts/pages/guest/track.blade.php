<x-guest-layout>
    <div class="container">
        <h2>Theo dõi đơn hàng #{{ $order->order_code }}</h2>
        <p><strong>Trạng thái:</strong> {{ match ($order->status) {
            'pending' => 'Đang chờ',
            'processing' => 'Đang xử lý',
            'completed' => 'Hoàn thành',
            'delivered' => 'Đã giao',
            'cancelled' => 'Đã hủy',
            default => ucfirst($order->status),
        } }}</p>
        <p><strong>Số vận đơn:</strong> {{ $trackingInfo }}</p>
        <p><strong>Ngày đặt:</strong> {{ $order->created_at->format('d/m/Y') }}</p>
        <a href="{{ route('my.account') }}?tab=orders" class="btn btn-secondary">Quay lại</a>
    </div>
</x-guest-layout>