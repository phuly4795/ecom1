<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        return view('layouts.pages.admin.category.index');
    }

    public function data()
    {
        $query = Category::query();
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('actions', function ($category) {
                return view('layouts.include.actions', compact('category'))->render();
            })
            ->editColumn('created_at', function ($category) {
                return $category->created_at->format('d/m/Y');
            })
            ->editColumn('status', function ($category) {
                return $category->status == 1
                    ? '<i class="fa-solid fa-circle-check text-success" style="font-size: 22px"></i>'
                    : '<i class="fa-regular fa-circle-xmark text-danger" style="font-size: 22px"></i>';
            })
            ->rawColumns(['actions', 'status'])
            ->make(true);
    }
    public function create()
    {
        return view('layouts.pages.admin.category.upsert');
    }
    public function edit(Category $category)
    {
        return view('layouts.pages.admin.category.upsert', compact('category'));
    }

    public function storeOrUpdate(Request $request, $id = null)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug,' . $id,
            'status' => 'required|in:0,1',
            'image' => 'nullable|string', // đường dẫn ảnh
        ], [
            'slug.unique' => 'Slug này đã tồn tại, vui lòng chọn tên khác.',
        ]);

        Category::updateOrCreate(
            ['id' => $id],
            $validated
        );

        return redirect()->route('admin.category.index')->with('success', $id ? 'Cập nhật thành công' : 'Thêm mới thành công');
    }


    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.category.index')->with('success', 'Xóa thành công!');
    }

    public function uploadImage(Request $request)
    {
        if ($request->hasFile('image')) {
            $file = $request->file('image');

            // Đặt tên file theo thời gian để tránh trùng
            $fileName = time() . '_' . $file->getClientOriginalName();

            // Lưu file vào thư mục "categories" trong storage/app/public
            $filePath = $file->storeAs('categories', $fileName, 'public');

            // Trả về path để lưu trong DB nếu cần
            return $filePath;
        }

        return null;
    }
}
