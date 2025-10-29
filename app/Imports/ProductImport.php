<?php

namespace App\Imports;

use App\Models\Province;
use App\Models\District;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShippingFee;
use App\Models\Warehouse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Exception;

class ProductImport implements ToCollection, WithHeadingRow
{
    public $errors = [];

    public function collection(Collection $rows)
    {
        // --- 1. Kiểm tra tất cả dữ liệu trước ---
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;

            $product = Product::where('sku', $row['ma_san_pham'])->first();
            if (!$product) {
                $this->errors[] = "Dòng {$rowNumber}: Không tìm thấy sản phẩm có mã {$row['ma_san_pham']}";
                continue;
            }

            if ($product->product_type == 'variant' && empty($row['ma_bien_the'])) {
                $this->errors[] = "Dòng {$rowNumber}: Sản phẩm có biến thể, vui lòng cung cấp mã biến thể!";
                continue;
            }

            if (!empty($row['ma_bien_the'])) {
                $productVariant = ProductVariant::where('sku', $row['ma_bien_the'])->first();
                if (!$productVariant) {
                    $this->errors[] = "Dòng {$rowNumber}: Không tìm thấy biến thể có mã {$row['ma_bien_the']}";
                    continue;
                }
            }

            if (!is_numeric($row['so_luong']) || $row['so_luong'] < 0) {
                $this->errors[] = "Dòng {$rowNumber}: Số lượng không được nhỏ hơn 0 và phải là số!";
                continue;
            }

            if (!is_numeric($row['gia_nhap']) || $row['gia_nhap'] < 0) {
                $this->errors[] = "Dòng {$rowNumber}: Giá nhập không được nhỏ hơn 0 và phải là số!";
                continue;
            }
        }

        // --- 2. Nếu có lỗi thì ném Exception và dừng import ---
        if (!empty($this->errors)) {
            return;
        }

        // --- 3. Nếu không có lỗi thì bắt đầu import ---
        foreach ($rows as $row) {

            $qty = trim($row['so_luong'] ?? '');
            if (!is_numeric($qty) || (float)$qty <= 0) {
                continue;
            }

            $product = Product::where('sku', $row['ma_san_pham'])->first();
            $productVariant = !empty($row['ma_bien_the']) ? ProductVariant::where('sku', $row['ma_bien_the'])->first() : null;

            Warehouse::create([
                'product_id'         => $product->id,
                'product_variant_id' => $productVariant->id ?? null,
                'qty'                => $row['so_luong'],
                'price'              => $row['gia_nhap'],
                'user_id'            => Auth::id(),
                'created_by'         => Auth::user()->name ?? 'System',
            ]);

            if ($productVariant) {
                $productVariant->increment('qty', $row['so_luong']);
            } else {
                $product->increment('qty', $row['so_luong']);
            }
        }
    }
}
