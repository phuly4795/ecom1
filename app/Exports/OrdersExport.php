<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class OrdersExport implements FromCollection, WithHeadings
{
    protected $status;

    public function __construct($status = null)
    {
        $this->status = $status;
    }

    public function collection($status = null)
    {
        $query = Order::with(['user', 'orderDetails']);

        if ($this->status) {
            $query->where('status', $this->status);
        }
        
        $orders = $query->get();
        $exportData = new Collection();

        foreach ($orders as $order) {
            foreach ($order->orderDetails as $detail) {
                $exportData->push([
                    'Mã đơn hàng'       => $order->order_code,
                    'Khách hàng'       => optional($order->user)->name,
                    'Email'            => $order->billing_email,
                    'Số điện thoại'    => $order->billing_telephone,
                    'Địa chỉ'          => $order->billing_address,
                    'PT Thanh toán'    => $order->payment_method,
                    'Trạng thái'       => $order->status,
                    'Ngày tạo'         => $order->created_at->format('Y-m-d H:i:s'),
                    'Tên sản phẩm'     => $detail->product_name,
                    'Giá'              => number_format($detail->price),
                    'Số lượng'         => $detail->quantity,
                    'Tổng tiền SP'     => number_format($detail->total_price),
                ]);
            }
        }

        return $exportData;
    }

    public function headings(): array
    {
        return [
            'Mã đơn hàng',
            'Khách hàng',
            'Email',
            'Số điện thoại',
            'Địa chỉ',
            'PT Thanh toán',
            'Trạng thái',
            'Ngày tạo',
            'Tên sản phẩm',
            'Giá',
            'Số lượng',
            'Tổng tiền SP',
        ];
    }
}
