<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\SubCategory;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        return view('layouts.pages.admin.product.index');
    }

    public function data()
    {
        $query = Product::with('category', 'subcategory', 'brand');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('image', function ($product) {
                $imagePath = optional($product->productImages->where('type', 1)->first())->image;
                return $imagePath
                    ? '<img src="' . asset('storage/' . $imagePath) . '" alt="Ảnh sản phẩm" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">'
                    : '<img src="' . asset('asset/img/no-image.png') . '" alt="Ảnh sản phẩm" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">';
            })
            ->addColumn('brand', function ($product) {
                return $product->brand->name ?? 'N/A';
            })
            ->addColumn('original_price', function ($product) {
                return $product->original_price ? number_format($product->original_price) . ' VNĐ' : 'N/A';
            })
            ->addColumn('discount_percentage', function ($product) {
                return $product->discount_percentage ? $product->discount_percentage . '%' : '0%';
            })
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
            ->editColumn('title', function ($product) {
                return Str::limit($product->title, 30, '...');
            })
            ->editColumn('qty', function ($product) {
                $html = '<span class="badge badge-success">Còn: ' . $product->qty . '</span>';
                return new HtmlString($html);
            })
            ->editColumn('category', function ($product) {
                if ($product->subcategory_id && $product->subcategory && $product->subcategory->categories) {
                    $cate = $product->subcategory->categories[0]->name;
                } elseif ($product->category_id && $product->categories) {
                    $cate = $product->categories[0]->name;
                } else {
                    $cate = "Không có";
                }

                $html = '<span class="badge badge-info">' . $cate . '</span>';
                if ($product->is_featured) {
                    $html .= '<span class="badge badge-warning">Nổi bật</span>';
                }
                if ($product->compare_price > 0 && $product->compare_price < $product->price) {
                    $html .= '<span class="badge badge-danger">Giảm giá</span>';
                }
                return new HtmlString($html);
            })
            ->editColumn('sku', function ($product) {
                return 'SKU-' . $product->sku;
            })
            ->editColumn('price', function ($product) {
                return number_format($product->price) . ' VNĐ';
            })
            ->editColumn('status', function ($product) {
                return $product->status == 1
                    ? '<i class="fa-solid fa-circle-check text-success" style="font-size: 22px"></i>'
                    : '<i class="fa-regular fa-circle-xmark text-danger" style="font-size: 22px"></i>';
            })
            ->rawColumns(['image', 'actions', 'status', 'qty', 'category', 'original_price', 'discount_percentage'])
            ->make(true);
    }

    public function create()
    {
        $categories = Category::orderBy('name', 'asc')->get();
        $brands = Brand::orderBy('name', 'asc')->get();
        $barcode = $this->generateBarcode();
        $image = null;
        $imageThumbnails = collect([]);

        // Tạo danh sách danh mục phân cấp
        $categoryList = $this->getCategoryList();

        return view('layouts.pages.admin.product.upsert', compact('categories', 'brands', 'barcode', 'image', 'imageThumbnails', 'categoryList'));
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name', 'asc')->get();
        $brands = Brand::orderBy('name', 'asc')->get();
        $image = $product->productImages()->where('type', 1)->first();
        $imageThumbnails = $product->productImages()->where('type', 2)->get();

        // Tạo danh sách danh mục phân cấp
        $categoryList = $this->getCategoryList();

        return view('layouts.pages.admin.product.upsert', compact('product', 'categories', 'brands', 'image', 'imageThumbnails', 'categoryList'));
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
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_start_date' => 'nullable|date',
            'discount_end_date' => 'nullable|date|after_or_equal:discount_start_date',
            'status' => 'required|in:0,1',
            'category_id' => 'required|integer',
            'brand_id' => 'nullable|exists:brands,id',
            'image' => 'nullable|string',
            'is_featured' => 'required|in:yes,no',
            'sku' => 'required|string|max:255',
            'barcode' => 'nullable|max:255',
            'qty' => 'nullable|numeric|min:0',
            'specifications' => 'nullable|string',
            'warranty_period' => 'nullable|integer|min:0',
            'warranty_policy' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'variants' => 'nullable|string',
        ], [
            'slug.unique' => 'Slug này đã tồn tại, vui lòng chọn tên khác.',
            'price.required' => 'Vui lòng nhập giá bán.',
            'original_price.required' => 'Vui lòng nhập giá gốc.',
            'discount_percentage.max' => 'Phần trăm giảm giá không được vượt quá 100%.',
            'discount_end_date.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
        ]);

        // Xử lý category_id và subcategory_id
        $selectedCategoryId = $validated['category_id'];
        $subcategory = Subcategory::find($selectedCategoryId);
        $category = Category::find($selectedCategoryId);

        if ($subcategory) {
            $validated['category_id'] = $subcategory->category_id;
            $validated['subcategory_id'] = $selectedCategoryId;
        } elseif ($category) {
            $validated['category_id'] = $selectedCategoryId;
            $validated['subcategory_id'] = null;
        } else {
            return redirect()->back()->withErrors(['category_id' => 'Danh mục không hợp lệ.']);
        }

        $validated['track_qty'] = $track_qty;

        if ($product) {
            $product->update($validated);
            $message = 'Cập nhật thành công';

            // === Xử lý ảnh đại diện ===
            $oldMainImage = $product->productImages->where('type', 1)->first();
            if ($image === '') {
                // Người dùng xóa ảnh đại diện
                if ($oldMainImage) {
                    Storage::disk('public')->delete($oldMainImage->image);
                    $oldMainImage->delete();
                }
            } elseif (!empty($image) && $image !== ($oldMainImage->image ?? '')) {
                // Có ảnh mới và khác với ảnh cũ
                if ($oldMainImage) {
                    Storage::disk('public')->delete($oldMainImage->image);
                    $oldMainImage->delete();
                }
                ProductImage::create([
                    'product_id' => $product->id,
                    'type' => 1,
                    'image' => $image
                ]);
            }
            // Nếu $image là null hoặc giống ảnh cũ, giữ nguyên ảnh hiện tại

            // === Xử lý ảnh thumbnails ===
            $oldThumbnails = $product->productImages->where('type', 2);
            $existingThumbnailPaths = $oldThumbnails->pluck('image')->toArray();
            $newThumbnails = array_filter($imageThumbnails, fn($val) => !empty($val));

            if (empty($newThumbnails) && $request->has('imageThumbnails')) {
                // Người dùng xóa hết thumbnail
                foreach ($oldThumbnails as $thumbnail) {
                    Storage::disk('public')->delete($thumbnail->image);
                    $thumbnail->delete();
                }
            } elseif (!empty($newThumbnails) && $newThumbnails !== $existingThumbnailPaths) {
                // Có thay đổi trong thumbnail
                foreach ($oldThumbnails as $thumbnail) {
                    Storage::disk('public')->delete($thumbnail->image);
                    $thumbnail->delete();
                }
                foreach ($newThumbnails as $thumbnail) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'type' => 2,
                        'image' => $thumbnail
                    ]);
                }
            }
            // Nếu không có thay đổi, giữ nguyên thumbnail hiện tại
        } else {
            $product = Product::create($validated);
            $message = 'Thêm mới thành công';

            if ($image) {
                ProductImage::create(['product_id' => $product->id, 'type' => 1, 'image' => $image]);
            }

            if ($imageThumbnails) {
                foreach ($imageThumbnails as $thumbnail) {
                    if (!empty($thumbnail)) {
                        ProductImage::create(['product_id' => $product->id, 'type' => 2, 'image' => $thumbnail]);
                    }
                }
            }
        }

        return redirect()->route('admin.product.index')->with(['status' => 'success', 'message' => $message]);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.product.index')->with(['status' => 'success', 'message' => 'Xóa thành công']);
    }

    public function massDestroy(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:products,id']);
        Product::whereIn('id', $request->ids)->delete();

        return response()->json(['success' => 'success', 'message' => 'Xóa hàng loạt thành công']);
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
        $filePath = Storage::disk('public')->putFileAs('products', $file, $fileName);

        return response()->json([
            'filePath' => 'products/' . $fileName, // <-- đảm bảo lưu đúng
            'url' => Storage::url('products/' . $fileName),
        ]);
    }

    public function getSubcategories($category_id)
    {
        $subcategories = SubCategory::where('category_id', $category_id)->get();
        return response()->json($subcategories);
    }

    public function generateBarcode($length = 10)
    {
        $barcode = '';
        for ($i = 0; $i < $length; $i++) {
            $barcode .= mt_rand(0, 9);
        }
        return $barcode;
    }

    private function getCategoryList()
    {
        $categories = Category::with('subcategories')->orderBy('name', 'asc')->get();
        $categoryList = [];

        foreach ($categories as $category) {
            // Thêm danh mục cha
            $categoryList[$category->id] = $category->name;

            // Thêm danh mục con (nếu có)
            if ($category->subcategories->isNotEmpty()) {
                foreach ($category->subcategories as $subcategory) {
                    $categoryList[$subcategory->id] = $category->name . ' > ' . $subcategory->name;
                }
            }
        }

        return $categoryList;
    }
}
