<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index()
    {
        return view('layouts.pages.admin.product.index');
    }

    public function data()
    {
        // Lấy danh sách sản phẩm, kèm quan hệ nếu cần
        $query = Product::with('category', 'subcategory', 'brand');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('actions', function ($product) {
                $editUrl = route('admin.product.edit', $product);
                $deleteUrl = route('admin.product.destroy', $product);

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
            ->editColumn('created_at', function ($product) {
                return $product->created_at->format('d/m/Y');
            })
            ->editColumn('qty', function ($product) {
                return 'Số lượng còn lại: '.$product->qty;
            })
            ->editColumn('sku', function ($product) {
                return 'SKU-' . $product->sku;
            })
            ->editColumn('price', function ($product) {
                return $product->price . 'vnđ';
            })
            ->editColumn('image', function ($product) {
                $imagePath = optional($product->productImages->where('type', 1)->first())->image;
                if ($imagePath) {
                    $fullPath = asset('storage/' . $imagePath);
                    return '<img src="' . $fullPath . '" alt="Ảnh sản phẩm" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">';
                }
                return '<span class="text-muted">Không có ảnh</span>';
            })
            ->editColumn('status', function ($product) {
                return $product->status == 1
                    ? '<i class="fa-solid fa-circle-check text-success" style="font-size: 22px"></i>'
                    : '<i class="fa-regular fa-circle-xmark text-danger" style="font-size: 22px"></i>';
            })
            ->rawColumns(['actions', 'status', 'image'])
            ->make(true);
    }

    public function create()
    {
        $categories = Category::orderBy('name', 'asc')->get();
        $brands = Brand::orderBy('name', 'asc')->get();
        return view('layouts.pages.admin.product.upsert', compact('categories', 'brands'));
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name', 'asc')->get();
        $brands = Brand::orderBy('name', 'asc')->get();
        $image = $product->productImages->where('type', 1)->first();
        $imageThumbnail = $product->productImages->where('type', 2);

        return view('layouts.pages.admin.product.upsert', compact('product', 'categories', 'brands', 'image', 'imageThumbnail'));
    }

    public function storeOrUpdate(Request $request, Product $product = null)
    {
        $id = $product ? $product->id : null;
        $track_qty = $request->has('track_qty') ? 'yes' : 'no';
        $image = $request->input('image', null);
        $imageThumbnails = $request->input('imageThumbnails', []);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products')->ignore($id),
            ],
            'description' => 'nullable|string',
            'status' => 'required|in:0,1',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'nullable|exists:sub_categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'image' => 'nullable|string',
            'is_featured' => 'required|in:yes,no',

            // Bổ sung các trường mới
            'price' => 'required|numeric|min:0',
            'compare_price' => 'required|numeric|min:0',
            'sku' => 'required|string|max:255',
            'barcode' => 'nullable|max:255',
            'qty' => 'nullable|numeric|min:0',
        ], [
            'slug.unique' => 'Slug này đã tồn tại, vui lòng chọn tên khác.',
            'price.required' => 'Vui lòng nhập giá bán.',
            'compare_price.required' => 'Vui lòng nhập giá so sánh.',
            'sku.required' => 'Vui lòng nhập mã sản phẩm.',
        ]);

        $validated['track_qty'] = $track_qty;

        if ($product) {
            $product->update($validated);
            $message = 'Cập nhật thành công';
        } else {
            $product = Product::create($validated);
            $message = 'Thêm mới thành công';

            if ($image) {
                $productImage = ProductImage::create([
                    'product_id' => $product->id,
                    'type' => 1,
                    'image' => $image,
                ]);
            }
            // dd($imageThumbnails);
            if ($imageThumbnails) {
                foreach ($imageThumbnails as $thumbnail) {
                    $productImage = ProductImage::create([
                        'product_id' => $product->id,
                        'type' => 2,
                        'image' => $thumbnail,
                    ]);
                }
            }
        }


        return redirect()->route('admin.product.index')->with([
            'status' => 'success',
            'message' => $message
        ]);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.product.index')->with(['status' => 'success', 'message' => 'Xóa thành công']);
    }

    public function massDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:products,id'
        ]);

        Product::whereIn('id', $request->ids)->delete();

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
            $filePath = Storage::disk('public')->putFileAs('products', $file, $fileName);

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

    public function getSubcategories($category_id)
    {
        $subcategories = SubCategory::where('category_id', $category_id)->get();
        return response()->json($subcategories);
    }
}
