<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductTemplateExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        $products = Product::select('id', 'sku', 'qty', 'product_type')
            ->where('status', 1)
            ->with('productVariants:id,product_id,sku,qty')
            ->get();

        $rows = new Collection();

        foreach ($products as $product) {
            if ($product->productVariants->isEmpty()) {
                $rows->push([
                    'Mã sản phẩm'  => $product->sku,
                    'Mã biến thể'  => '',
                    'Tồn hiện tại' => (string) ($product->qty ?? 0),
                    'Số lượng'     => (string)  0,
                    'Giá nhập'     => (string)  0,
                ]);
            } else {
                foreach ($product->productVariants as $variant) {
                    $rows->push([
                        'Mã sản phẩm'  => $product->sku,
                        'Mã biến thể'  => $variant->sku,
                        'Tồn hiện tại' => (string) ($variant->qty ?? 0),
                        'Số lượng'     => (string) 0,
                        'Giá nhập'     => (string) 0,
                    ]);
                }
            }
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Mã sản phẩm',
            'Mã biến thể',
            'Tồn hiện tại',
            'Số lượng',
            'Giá nhập'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header styling (hàng 1)
        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['rgb' => '4F81BD'], // xanh nhạt
            ],
        ]);

        // Tạo viền cho toàn bộ bảng
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle("A1:E{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '999999'],
                ],
            ],
        ]);

        // Căn giữa cho các cột số lượng, giá
        $sheet->getStyle('D2:E' . $lastRow)->getAlignment()->setHorizontal('center');

        // Cố định dòng đầu
        $sheet->freezePane('A2');

        // Tự điều chỉnh độ rộng cột
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [];
    }
}
