<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Yajra\DataTables\DataTables;

class BrandController extends Controller
{
    public function index()
    {
        return view('layouts.pages.admin.brand.index');
    }

    public function data()
    {
        $query = Brand::query(); // Thêm eager loading

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('actions', function ($brand) {
                $editUrl = route('admin.brand.edit', $brand);
                $deleteUrl = route('admin.brand.destroy', $brand);

                $html = '<div class="d-flex gap-2">';
                $html .= '<a href="' . $editUrl . '" class="btn btn-sm btn-warning mr-2">Sửa</a>';
                $html .= '<form action="' . $deleteUrl . '" method="POST" class="d-inline">';
                $html .= csrf_field();
                $html .= method_field('DELETE');
                $html .= '<button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Bạn có chắc chắn muốn xóa?\')">Xóa</button>';
                $html .= '</form>';
                $html .= '</div>';

                return new HtmlString($html);
            })
            ->editColumn('created_at', function ($brand) {
                return $brand->created_at->format('d/m/Y');
            })
            ->editColumn('status', function ($brand) {
                return $brand->status == 1
                    ? '<i class="fa-solid fa-circle-check text-success" style="font-size: 22px"></i>'
                    : '<i class="fa-regular fa-circle-xmark text-danger" style="font-size: 22px"></i>';
            })
            ->rawColumns(['actions', 'status'])
            ->make(true);
    }

    public function create()
    {
        return view('layouts.pages.admin.brand.upsert');
    }
    public function edit(Brand $brand)
    {
        return view('layouts.pages.admin.brand.upsert', compact('brand'));
    }

    public function storeOrUpdate(Request $request, $id = null)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:brands,slug,' . $id,
            'status' => 'required|in:0,1',
        ], [
            'slug.unique' => 'Slug này đã tồn tại, vui lòng chọn tên khác.'
        ]);

        Brand::updateOrCreate(
            ['id' => $id],
            $validated
        );

        return redirect()->back()->with(['status' => 'success', 'message' => $id ? 'Cập nhật thành công' : 'Thêm mới thành công']);
    }


    public function destroy(Brand $brand)
    {
        $brand->delete();
        return redirect()->route('admin.brand.index')->with(['status' => 'success', 'message' => 'Xóa thành công']);
    }

    // CategoryController.php
    public function massDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:brands,id'
        ]);

        brand::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => 'success',
            'message' => 'Xóa hàng loạt thành công'
        ]);
    }
}
