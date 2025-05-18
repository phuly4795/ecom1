<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        return view('layouts.pages.admin.product.index');
    }

    public function data()
    {
        $query = Category::query();
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('actions', function ($category) {
                $editUrl = route('admin.product.edit', $category);
                $deleteUrl = route('admin.product.destroy', $category);

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
        return view('layouts.pages.admin.product.upsert');
    }
    public function edit(Category $category)
    {
        return view('layouts.pages.admin.product.upsert', compact('category'));
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

        return redirect()->back()->with(['status' => 'success', 'message' => $id ? 'Cập nhật thành công' : 'Thêm mới thành công']);
    }


    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.product.index')->with(['status' => 'success', 'message' => 'Xóa thành công']);
    }

    // CategoryController.php
    public function massDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:categories,id'
        ]);

        Category::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => 'success',
            'message' => 'Xóa hàng loạt thành công'
        ]);
    }


    public function uploadImage(Request $request)
    {
        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }

        $file = $request->file('file');

        if (!$file->isValid()) {
            return response()->json(['error' => 'Invalid file'], 400);
        }

        $fileName = time() . '_' . $file->getClientOriginalName();

        try {
            $filePath = Storage::disk('public')->putFileAs('categories', $file, $fileName);

            return response()->json([
                'filePath' => $filePath,
                'url' => Storage::url($filePath),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Upload failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
