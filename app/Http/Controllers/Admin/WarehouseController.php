<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ProductTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\WarehourseProductImport;
use App\Models\Warehouse;
use App\Models\WarehouseDetail;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class WarehouseController extends Controller
{
    public function index()
    {
        $data = Warehouse::with(['user'])->latest('id')->paginate(20);
        return view('layouts.pages.admin.warehouse.index', compact('data'));
    }

    public function detail(Warehouse $Warehouse)
    {
        $data = WarehouseDetail::with(['warehouse', 'product', 'productVariant'])->where('warehouse_id', $Warehouse->id)->paginate(20);

        return view('layouts.pages.admin.warehouse.detail', [
            'data' => $data
        ]);
    }

    public function exportTemplate()
    {
        return Excel::download(new ProductTemplateExport, 'template_san_pham.xlsx');
    }

    public function exportWarehouseReceipt(Warehouse $warehouse)
    {
        // Lấy chi tiết sản phẩm thuộc đợt nhập
        $details = $warehouse->details()
            ->with(['product', 'productVariant'])
            ->get();

        $pdf = Pdf::loadView('layouts.pages.admin.warehouse.receipt', [
            'warehouse' => $warehouse,
            'details'   => $details
        ]);

        return $pdf->download('xuat-phieu-nhap-' . $warehouse->id . '.pdf');
    }


    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'Vui lòng nhập tên đợt nhập',
            'name.string' => 'Tên đợt nhập phải là chuỗi ký tự',
            'name.max' => 'Tên đợt nhập không được vượt quá 255 ký tự',
            'file.required' => 'Vui lòng chọn tệp Excel để nhập',
            'file.mimes' => 'Tệp phải có định dạng xlsx hoặc xls',
        ]);

        try {
            $name = $request->input('name');
            $import = new WarehourseProductImport($name);

            Excel::import($import, $request->file('file'));

            if (!empty($import->errors)) {
                return redirect()->back()->withErrors($import->errors)->withInput();
            }

            return redirect()->route('admin.warehouse.index')->with('success', 'Import thành công!');
            return back()->with('success', 'Import dữ liệu kho thành công!');
        } catch (ValidationException $e) {
            $failures = $e->failures();

            return redirect()->back()->withErrors([
                'file' => 'Một số dòng trong file có lỗi: ' . $failures[0]->errors()[0] ?? 'Lỗi không xác định'
            ]);
        }
    }

    public function create()
    {
        return view('layouts.pages.admin.warehouse.create');
    }

    public function searchProducts(Request $request)
    {
        $q = $request->input('q');

        // Query products
        $products = Product::where('status', 1)
            ->where(function($query) use ($q) {
                $query->where('title', 'LIKE', "%{$q}%")
                      ->orWhere('sku', 'LIKE', "%{$q}%");
            })
            ->with(['productVariants'])
            ->get();

        $results = [];

        foreach ($products as $product) {
            if ($product->product_type === 'variant') {
                foreach ($product->productVariants as $variant) {
                    $results[] = [
                        'id' => 'variant_' . $variant->id,
                        'text' => $product->title . ' - ' . $variant->variant_name . ' (SKU: ' . ($variant->sku ?: 'N/A') . ') - Tồn: ' . $variant->qty,
                        'current_qty' => $variant->qty
                    ];
                }
            } else {
                $results[] = [
                    'id' => 'product_' . $product->id,
                    'text' => $product->title . ' (SKU: ' . ($product->sku ?: 'N/A') . ') - Tồn: ' . $product->qty,
                    'current_qty' => $product->qty
                ];
            }
        }

        // Also query variants directly by SKU if not already covered
        if (empty($results) && !empty($q)) {
            $variantsBySku = ProductVariant::where('sku', 'LIKE', "%{$q}%")
                ->with(['product'])
                ->get();
            foreach ($variantsBySku as $variant) {
                if ($variant->product && $variant->product->status == 1) {
                    $results[] = [
                        'id' => 'variant_' . $variant->id,
                        'text' => $variant->product->title . ' - ' . $variant->variant_name . ' (SKU: ' . ($variant->sku ?: 'N/A') . ') - Tồn: ' . $variant->qty,
                        'current_qty' => $variant->qty
                    ];
                }
            }
        }

        return response()->json([
            'results' => array_values(array_unique($results, SORT_REGULAR))
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.identifier' => 'required|string',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.price' => 'required|string',
        ], [
            'name.required' => 'Vui lòng nhập tên đợt nhập.',
            'items.required' => 'Vui lòng chọn ít nhất một sản phẩm để nhập kho.',
            'items.*.identifier.required' => 'Vui lòng chọn sản phẩm.',
            'items.*.qty.required' => 'Vui lòng nhập số lượng.',
            'items.*.qty.min' => 'Số lượng nhập phải lớn hơn 0.',
            'items.*.price.required' => 'Vui lòng nhập giá nhập.',
        ]);

        $name = $request->input('name');
        $items = $request->input('items');

        DB::beginTransaction();
        try {
            $warehouse = Warehouse::create([
                'name' => $name,
                'user_id' => Auth::id(),
                'created_by' => Auth::id(),
            ]);

            foreach ($items as $item) {
                $identifier = $item['identifier'];
                $qty = intval($item['qty']);
                $price = floatval(preg_replace('/[^\d]/', '', $item['price']));

                if (str_starts_with($identifier, 'product_')) {
                    $productId = intval(str_replace('product_', '', $identifier));
                    $product = Product::findOrFail($productId);
                    
                    WarehouseDetail::create([
                        'warehouse_id' => $warehouse->id,
                        'product_id' => $product->id,
                        'product_variant_id' => null,
                        'qty' => $qty,
                        'price' => $price,
                        'created_by' => Auth::id(),
                    ]);

                    $product->increment('qty', $qty);

                } elseif (str_starts_with($identifier, 'variant_')) {
                    $variantId = intval(str_replace('variant_', '', $identifier));
                    $variant = ProductVariant::findOrFail($variantId);

                    WarehouseDetail::create([
                        'warehouse_id' => $warehouse->id,
                        'product_id' => $variant->product_id,
                        'product_variant_id' => $variant->id,
                        'qty' => $qty,
                        'price' => $price,
                        'created_by' => Auth::id(),
                    ]);

                    $variant->increment('qty', $qty);
                }
            }

            DB::commit();
            return redirect()->route('admin.warehouse.index')->with('success', 'Tạo phiếu nhập kho thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Lỗi hệ thống: ' . $e->getMessage()])->withInput();
        }
    }
}
