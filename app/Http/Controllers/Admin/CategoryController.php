<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Yajra\DataTables\DataTables;

class CategoryController extends Controller
{
    public function index()
    {
        return view('layouts.pages.admin.category.index');
    }

    public function data()
    {
        $query = Category::query()->orderBy('sort');
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('actions', function ($category) {
                $editUrl = route('admin.category.edit', $category);
                $deleteUrl = route('admin.category.destroy', $category);

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
            ->addColumn('sort', function ($category) {
                return $category->sort;
            })
            ->rawColumns(['actions', 'status', 'sort'])
            ->make(true);
    }

    public function create()
    {
        $categoriesCount = Category::count();
        $maxPosition = isset($category) ? $categoriesCount : $categoriesCount + 1;
        return view('layouts.pages.admin.category.upsert', compact('maxPosition'));
    }
    public function edit(Category $category)
    {
        $categoriesCount = Category::count();
        $maxPosition = isset($category) ? $categoriesCount : $categoriesCount + 1;

        return view('layouts.pages.admin.category.upsert', compact('category', 'maxPosition'));
    }

    public function storeOrUpdate(Request $request, $id = null)
    {
        try {
            $rules = [
                'name' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:categories,slug,' . $id,
                'status' => 'required|in:0,1',
                'sort' => 'required|integer|min:1',
            ];

            // Chỉ thêm rule hình ảnh nếu có file
            if ($request->hasFile('image')) {
                $rules['image'] = 'image|mimes:jpeg,png,jpg,gif|max:2048';
            }

            $validated = $request->validate($rules, [
                'slug.unique' => 'Slug này đã tồn tại, vui lòng chọn tên khác.',
                'sort.required' => 'Vui lòng chọn vị trí.',
                'image.image' => 'File phải là hình ảnh.',
            ]);

            if ($id) {
                $category = Category::findOrFail($id);
                $oldSort = $category->sort;
                $newSort = $validated['sort'];

                if ($oldSort != $newSort) {
                    if ($newSort < $oldSort) {
                        Category::where('sort', '>=', $newSort)
                            ->where('sort', '<', $oldSort)
                            ->increment('sort');
                    } else {
                        Category::where('sort', '>', $oldSort)
                            ->where('sort', '<=', $newSort)
                            ->decrement('sort');
                    }
                }

                if ($request->hasFile('image')) {
                    if ($category->image) {
                        Storage::disk('public')->delete($category->image);
                    }
                    $validated['image'] = Storage::disk('public')->putFileAs(
                        'categories',
                        $request->file('image'),
                        time() . '_' . $request->file('image')->getClientOriginalName()
                    );
                } else {
                    unset($validated['image']); // tránh override ảnh cũ nếu không có ảnh mới
                }

                $category->update($validated);
                $message = 'Cập nhật danh mục thành công.';
            } else {
                $sort = $validated['sort'];
                Category::where('sort', '>=', $sort)->increment('sort');

                if ($request->hasFile('image')) {
                    $validated['image'] = Storage::disk('public')->putFileAs(
                        'categories',
                        $request->file('image'),
                        time() . '_' . $request->file('image')->getClientOriginalName()
                    );
                }

                $category = Category::create($validated);
                $message = 'Thêm danh mục thành công.';
            }

            return redirect()->back()->with(['status' => 'success', 'message' => $message]);
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
        }
    }


    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.category.index')->with(['status' => 'success', 'message' => 'Xóa thành công']);
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

    public function updateOrder(Request $request)
    {
        $order = $request->input('order');

        foreach ($order as $item) {
            Category::where('id', $item['id'])->update(['sort' => $item['position']]);
        }

        // Sắp xếp lại để đảm bảo không có khoảng trống
        $this->reorderCategories();

        return response()->json(['success' => true, 'message' => 'Cập nhật vị trí thành công']);
    }

    private function reorderCategories()
    {
        $categories = Category::orderBy('sort')->get();
        foreach ($categories as $index => $category) {
            $category->update(['sort' => $index + 1]);
        }
    }
}
