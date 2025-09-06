<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\ShippingFeeImport;
use App\Models\District;
use App\Models\Province;
use App\Models\ShippingFee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;


class ShippingFeeController extends Controller
{
    public function index()
    {
        $fees = ShippingFee::with(['province', 'district'])->paginate(20);
        return view('layouts.pages.admin.shipping_fees.index', compact('fees'));
    }

    public function create()
    {
        $provinces = Province::all();
        return view('layouts.pages.admin.shipping_fees.upsert', [
            'provinces' => $provinces,
            'shippingFee' => null,
            'districts' => []
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'province_id' => 'required',
            'district_id' => 'required',
            'fee' => 'required|numeric',
        ]);

        ShippingFee::updateOrCreate(
            [
                'province_id' => $request->province_id,
                'district_id' => $request->district_id,
            ],
            ['fee' => $request->fee]
        );

        return redirect()->route('admin.shipping_fees.index')->with('success', 'Đã lưu phí vận chuyển');
    }

    public function edit(ShippingFee $shippingFee)
    {
        $provinces = Province::all();
        $districts = District::where('city_code', $shippingFee->province_id)->get();
        return view('layouts.pages.admin.shipping_fees.upsert', [
            'shippingFee' => $shippingFee,
            'provinces' => $provinces,
            'districts' => $districts,
        ]);
    }

    public function update(Request $request, ShippingFee $shippingFee)
    {
        $request->validate([
            'province_id' => 'required',
            'district_id' => 'required',
            'fee' => 'required|numeric',
        ]);

        $shippingFee->update([
            'province_id' => $request->province_id,
            'district_id' => $request->district_id,
            'fee' => $request->fee
        ]);

        return redirect()->route('admin.shipping_fees.index')->with('success', 'Cập nhật thành công');
    }

    public function destroy(ShippingFee $shippingFee)
    {
        $shippingFee->delete();
        return redirect()->route('admin.shipping_fees.index')->with('success', 'Đã xóa phí vận chuyển');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            Excel::import(new ShippingFeeImport, $request->file('file'));
            return redirect()->route('admin.shipping_fees.index')->with('success', 'Import thành công!');
        } catch (ValidationException $e) {
            $failures = $e->failures();

            return redirect()->back()->withErrors([
                'file' => 'Một số dòng trong file có lỗi: ' . $failures[0]->errors()[0] ?? 'Lỗi không xác định'
            ]);
        }
    }
    public function getFee(Request $request)
    {
        $provinceId = $request->province_id;
        $districtId = $request->district_id;

        $fee = ShippingFee::where('province_id', $provinceId)
            ->where('district_id', $districtId)
            ->value('fee');

        return response()->json([
            'fee' => $fee ?? config('settings.default_shipping_fee', 50000) // mặc định nếu không tìm thấy
        ]);
    }
}
