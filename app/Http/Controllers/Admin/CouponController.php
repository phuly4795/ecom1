<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rule;

class CouponController extends Controller
{
    public function index()
    {
        return view('layouts.pages.admin.coupon.index');
    }

    public function data()
    {
        $query = Coupon::query();
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('actions', function ($coupon) {
                $editUrl = route('admin.coupons.edit', $coupon);
                $deleteUrl = route('admin.coupons.destroy', $coupon);
                $updateStatusUrl = route('admin.coupons.toggleStatus', $coupon);

                $html = '<div class="d-flex gap-2">';
                $html .= '<a href="' . $editUrl . '" class="btn btn-sm btn-warning mr-2">Sửa</a>';
                $html .= '<form action="' . $deleteUrl . '" method="POST" class="d-inline" style="margin-right: 2%;">';
                $html .= csrf_field();
                $html .= method_field('DELETE');
                $html .= '<button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Bạn có chắc chắn muốn xóa?\')">Xóa</button>';
                $html .= '</form>';
                $html .= '<form action="' . $updateStatusUrl . '" method="POST" class="d-inline">';
                $html .= csrf_field();
                $html .= method_field('PATCH');
                $html .= '<button type="submit" class="btn btn-sm ' . ($coupon->is_active == 1 ? 'btn-success' : 'btn-secondary') . '" data-toggle="tooltip" title="' . ($coupon->is_active == 1 ? 'Kích hoạt khuyến mãi' : 'Hủy kích hoạt') . '"><i class="fas ' . ($coupon->is_active == 1 ? 'fa-eye' : 'fa-eye-slash') . '"></i></button>';
                $html .= '</form>';
                $html .= '</div>';

                return new HtmlString($html);
            })
            ->editColumn('start_date', function ($coupon) {
                return $coupon->start_date->format('H:i:s d/m/Y');
            })
            ->editColumn('end_date', function ($coupon) {
                return $coupon->end_date->format('H:i:s d/m/Y');
            })
            ->editColumn('type', function ($coupon) {
                return $coupon->type == 'fixed' ? "Giảm thẳng" : "Giảm phần trăm";
            })
            ->editColumn('value', function ($coupon) {
                return $coupon->type == 'fixed' ? number_format($coupon->value) . ' vnđ' : (int)$coupon->value . "%";
            })
            ->editColumn('is_active', function ($coupon) {
                return $coupon->is_active == 1
                    ? '<i class="fa-solid fa-circle-check text-success" style="font-size: 22px"></i>'
                    : '<i class="fa-regular fa-circle-xmark text-danger" style="font-size: 22px"></i>';
            })
            ->rawColumns(['actions', 'is_active', 'type'])
            ->make(true);
    }

    public function create()
    {
        return view('layouts.pages.admin.coupon.upsert');
    }

    public function edit(Coupon $coupon)
    {
        return view('layouts.pages.admin.coupon.upsert', compact('coupon'));
    }


    public function storeOrUpdate(Request $request, $id = null)
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'max:50',
                Rule::unique('coupons', 'code')->ignore($id),
            ],
            'type' => 'required|in:fixed,percent',
            'value' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'usage_limit' => 'required|integer|min:1',
            'is_active' => 'required|integer|in:0,1',
            'description' => 'nullable',
        ], [

            'required' => 'Vui lòng nhập :attribute',
            'code.required' => 'Vui lòng nhập mã khuyến mãi',
            'code.max' => 'Mã khuyến mãi không được vượt quá 50 ký tự',
            'code.unique' => 'Mã khuyến mãi đã tồn tại',
            'type.in' => 'Loại khuyến mãi không hợp lệ',
            'value.numeric' => 'Giá trị phải là số',
            'value.min' => 'Giá trị không được nhỏ hơn 0',
            'start_date.date' => 'Ngày bắt đầu không hợp lệ',
            'end_date.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu',
            'usage_limit.integer' => 'Số lượng mã phải là số nguyên',
            'usage_limit.min' => 'Số lượng mã tối thiểu là 1',
            'is_active.in' => 'Trạng thái không hợp lệ',
        ], [
            'code' => 'mã khuyến mãi',
            'type' => 'loại khuyến mãi',
            'value' => 'giá trị',
            'start_date' => 'ngày bắt đầu',
            'end_date' => 'ngày kết thúc',
            'usage_limit' => 'số lượng mã',
            'is_active' => 'trạng thái',
        ]);

        try {
            if ($id) {
                $coupon = Coupon::findOrFail($id);
                $coupon->update($validated);
                $message = "Cập nhật mã khuyến mãi thành công";
            } else {
                Coupon::create($validated);
                $message = "Tạo mã khuyến mãi thành công";
            }

            return redirect()->back()->with(['status' => 'success', 'message' => $message]);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    public function destroy(Category $coupon)
    {
        $coupon->delete();
        return redirect()->route('admin.category.index')->with(['status' => 'success', 'message' => 'Xóa thành công']);
    }

    // CategoryController.php
    public function massDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:coupons,id'
        ]);

        Coupon::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => 'success',
            'message' => 'Xóa hàng loạt thành công'
        ]);
    }

    public function toggleStatus(Coupon $coupon)
    {
        $coupon->update([
            'is_active' => $coupon->is_active == 1 ? 0 : 1
        ]);

        return redirect()->route('admin.coupons.index')->with(['status' => 'success', 'message' => 'Cập nhật trạng thái thành công']);
    }
}
