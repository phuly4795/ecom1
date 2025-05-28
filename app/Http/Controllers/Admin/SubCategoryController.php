<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Yajra\DataTables\DataTables;

class SubCategoryController extends Controller
{
    public function index()
    {
        return view('layouts.pages.admin.sub_category.index');
    }

    public function data()
    {
        $query = SubCategory::with('categories'); // Corrected to 'categories'

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('actions', function ($subCategory) {
                $editUrl = route('admin.sub_category.edit', $subCategory);
                $deleteUrl = route('admin.sub_category.destroy', $subCategory);

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
            ->editColumn('created_at', function ($subCategory) {
                return $subCategory->created_at->format('d/m/Y');
            })
            ->editColumn('status', function ($subCategory) {
                return $subCategory->status == 1
                    ? '<i class="fa-solid fa-circle-check text-success" style="font-size: 22px"></i>'
                    : '<i class="fa-regular fa-circle-xmark text-danger" style="font-size: 22px"></i>';
            })
            ->addColumn('categories', function ($subCategory) {
                return $subCategory->categories->pluck('name')->implode(', ') ?: 'Chưa có danh mục cha';
            })
            ->rawColumns(['actions', 'status', 'categories'])
            ->make(true);
    }

    public function create()
    {
        $category = Category::orderBy('name', 'ASC')->get();
        return view('layouts.pages.admin.sub_category.upsert', compact('category'));
    }
    public function edit(SubCategory $subCategory)
    {
        $category = Category::orderBy('name', 'ASC')->get();
        $subCategory->load('categories');
        return view('layouts.pages.admin.sub_category.upsert', compact('subCategory', 'category'));
    }

    public function storeOrUpdate(Request $request, $id = null)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:sub_categories,slug,' . $id,
                'status' => 'required|in:0,1',
                'category_ids' => 'required|array',
                'category_ids.*' => 'exists:categories,id',
            ], [
                'slug.unique' => 'Slug này đã tồn tại, vui lòng chọn tên khác.',
                'category_ids.required' => 'Vui lòng chọn ít nhất một danh mục cha.',
            ]);

            $subCategory = SubCategory::updateOrCreate(
                ['id' => $id],
                $request->only(['name', 'slug', 'status'])
            );

            $subCategory->categories()->sync($validated['category_ids']);

            return redirect()->back()->with([
                'status' => 'success',
                'message' => $id ? 'Cập nhật thành công' : 'Thêm mới thành công',
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with(['status' => 'error', 'message' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }


    public function destroy(SubCategory $subCategory)
    {
        $subCategory->delete();
        return redirect()->route('admin.sub_category.index')->with(['status' => 'success', 'message' => 'Xóa thành công']);
    }

    // CategoryController.php
    public function massDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:sub_categories,id'
        ]);

        SubCategory::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => 'success',
            'message' => 'Xóa hàng loạt thành công'
        ]);
    }
}
