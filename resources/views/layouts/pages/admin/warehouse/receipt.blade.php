<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        h2 {
            text-align: center;
        }
    </style>
</head>

<body>

    <h2>PHIẾU NHẬP KHO</h2>

    <<strong>Mã phiếu:</strong> {{ 'PN-' . now()->year . '-' . str_pad($warehouse->id, 6, '0', STR_PAD_LEFT) }}

        <p><strong>Tên đợt nhập:</strong> {{ $warehouse->name }}</p>
        <p><strong>Người nhập:</strong> {{ $warehouse->created_by }}</p>
        <p><strong>Ngày nhập:</strong> {{ $warehouse->created_at->format('d/m/Y H:i') }}</p>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tên sản phẩm</th>
                    <th>Tên biến thể</th>
                    <th>Số lượng</th>
                    <th>Giá nhập</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($details as $key => $item)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $item->product->title }}</td>
                        <td>{{ $item->productVariant->variant_name ?? '-' }}</td>
                        <td>{{ number_format($item->qty) }}</td>
                        <td>{{ number_format($item->price) }}</td>
                        <td>{{ number_format($item->price * $item->qty) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <h3 style="text-align:right; margin-top:20px;">
            Tổng tiền:
            {{ number_format($details->sum(fn($d) => $d->qty * $d->price)) }}
        </h3>

</body>

</html>
