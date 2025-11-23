<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ProductTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\WarehourseProductImport;
use App\Models\Warehouse;
use App\Models\WarehouseDetail;
use Illuminate\Http\Request;
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
}
