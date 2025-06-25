<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\SubCategory;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ProductController extends Controller
{
    public function index()
    {
        return view('layouts.pages.admin.product.index');
    }

    public function data()
    {
        $query = Product::with('category', 'subcategory', 'brand', 'productVariants');

        if ($categoryId = request('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($status = request('status')) {
            $query->where('product.status', $status);
        }

        // if (!is_null(request('is_featured'))) {
        //     $query->where('is_featured', request('is_featured'));
        // }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('image', function ($product) {
                $imagePath = optional($product->productImages->where('type', 1)->first())->image;
                return $imagePath
                    ? '<img src="' . asset('storage/' . $imagePath) . '" alt="Ảnh sản phẩm" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">'
                    : '<img src="' . asset('asset/img/no-image.png') . '" alt="Ảnh sản phẩm" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">';
            })
            ->addColumn('brand', function ($product) {
                return $product->brand ? '<span class="badge badge-primary">' . $product->brand->name . '</span>' : '<span class="badge badge-secondary">N/A</span>';
            })
            ->addColumn('price_info', function ($product) {
                $html = '<div>';

                if ($product->product_type == 'single') {
                    $html .= '<span class="text-primary font-weight-bold">' . number_format($product->original_price) . ' VNĐ</span>';
                    if ($product->original_price && $product->original_price > $product->original_price) {
                        $html .= '<br><del class="text-muted">' . number_format($product->original_price) . ' VNĐ</del>';
                        $discount = $product->discount_percentage ? $product->discount_percentage . '%' : 'N/A';
                        $html .= '<span class="badge badge-danger ml-1">' . $discount . '</span>';
                    }
                } elseif ($product->product_type == 'variant') {
                    $variants = $product->productVariants;
                    if ($variants->isNotEmpty()) {
                        foreach ($variants as $variant) {
                            $price = $variant->price ?? 0;
                            $originalPrice = $variant->original_price ?? $price; // Giá gốc, nếu không có thì dùng giá hiện tại
                            $discountedPrice = $variant->discount_percentage ?? $price; // Giá sau giảm, nếu không có thì dùng giá hiện tại
                            $discountPercentage = $variant->discount_percentage ?? 0;

                            $html .= '<div class="mb-1">';
                            $html .= '<span class="text-primary font-weight-bold">' . number_format($discountedPrice) . ' VNĐ</span>';
                            if ($originalPrice > $discountedPrice) {
                                $html .= '<br><del class="text-muted">' . number_format($originalPrice) . ' VNĐ</del>';
                                $html .= '<span class="badge badge-danger ml-1">' . ($discountPercentage > 0 ? $discountPercentage . '%' : 'N/A') . '</span>';
                            }
                            $html .= '<br><small class="text-info">' . ($variant->variant_name ?? 'Không có tên') . '</small>';
                            $html .= '</div>';
                        }
                    } else {
                        $html .= '<span class="text-muted">Chưa có biến thể</span>';
                    }
                }

                $html .= '</div>';
                return new HtmlString($html);
            })
            ->addColumn('actions', function ($product) {
                $editUrl = route('admin.product.edit', $product);
                $deleteUrl = route('admin.product.destroy', $product);
                $toggleStatusUrl = route('admin.product.toggleStatus', $product);

                $html = '<div class="d-flex gap-2">';
                $html .= '<a href="' . $editUrl . '" class="btn btn-sm btn-warning" data-toggle="tooltip" title="Sửa sản phẩm" style="margin-right: 2%;"><i class="fas fa-edit"></i></a>';
                $html .= '<form action="' . $deleteUrl . '" method="POST" class="d-inline" style="margin-right: 2%;">';
                $html .= csrf_field();
                $html .= method_field('DELETE');
                $html .= '<button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Bạn có chắc chắn muốn xóa?\')" data-toggle="tooltip" title="Xóa sản phẩm"><i class="fas fa-trash"></i></button>';
                $html .= '</form>';
                $html .= '<form action="' . $toggleStatusUrl . '" method="POST" class="d-inline">';
                $html .= csrf_field();
                $html .= method_field('PATCH');
                $html .= '<button type="submit" class="btn btn-sm ' . ($product->status == 1 ? 'btn-success' : 'btn-secondary') . '" data-toggle="tooltip" title="' . ($product->status == 1 ? 'Hiển thị sản phẩm' : 'Ẩn sản phẩm') . '"><i class="fas ' . ($product->status == 1 ? 'fa-eye' : 'fa-eye-slash') . '"></i></button>';
                $html .= '</form>';
                $html .= '</div>';

                return new HtmlString($html);
            })
            ->editColumn('created_at', function ($product) {
                return $product->created_at->format('d/m/Y');
            })
            ->editColumn('title', function ($product) {
                return '<span data-toggle="tooltip" title="' . $product->title . '">' . Str::limit($product->title, 30, '...') . '</span>';
            })
            ->editColumn('qty', function ($product) {
                $html = '<div>';
                if ($product->product_type == "single") {
                    $html .= '<span class="badge badge-' . ($product->qty > 0 ? 'success' : 'danger') . '">Còn: ' . $product->qty . '</span>';
                }
                if ($product->product_type == "variant") {
                    foreach ($product->productVariants as $variant) {
                        $html .= '<span class="badge badge-' . ($variant->qty > 0 ? 'success' : 'danger') . ' mb-1">' . $variant->variant_name . ': ' . $variant->qty . '</span><br>';
                    }
                }
                $html .= '</div>';
                return new HtmlString($html);
            })
            ->editColumn('category', function ($product) {
                $html = '<div>';

                $categoryName = 'Không có';

                if ($product->subcategory && $product->subcategory->categories->isNotEmpty()) {
                    // Danh mục con + cha
                    $parentCategory = $product->subcategory->categories->first();
                    $categoryName = $parentCategory->name . ' > ' . $product->subcategory->name;
                } elseif ($product->category && is_object($product->category)) {
                    // Chỉ có danh mục cha
                    $categoryName = $product->category->name;
                }

                $html .= '<span class="badge badge-info">' . e($categoryName) . '</span>';

                if ($product->is_featured === 'yes') {
                    $html .= '<span class="badge badge-warning ml-1">Nổi bật</span>';
                }

                $html .= '</div>';

                return new HtmlString($html);
            })
            ->editColumn('sku', function ($product) {
                return '<span class="badge badge-dark">SKU-' . Str::limit($product->sku, 10, '...') . '</span>';
            })
            ->editColumn('status', function ($product) {
                return $product->status == 1
                    ? '<span class="badge badge-success">Hiển thị</span>'
                    : '<span class="badge badge-danger">Ẩn</span>';
            })
            ->rawColumns(['image', 'actions', 'status', 'qty', 'category', 'price_info', 'title', 'sku', 'brand'])
            ->make(true);
    }

    public function create()
    {
        $categories = Category::orderBy('name', 'asc')->get();
        $brands = Brand::where('status', 1)->orderBy('name', 'asc')->get();
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
        $brands = Brand::where('status', 1)->orderBy('name', 'asc')->get();
        $image = $product->productImages()->where('type', 1)->first();
        $imageThumbnails = $product->productImages()->where('type', 2)->get();
        $categoryList = $this->getCategoryList();

        return view('layouts.pages.admin.product.upsert', compact('product', 'categories', 'brands', 'image', 'imageThumbnails', 'categoryList'));
    }

    public function storeOrUpdate(Request $request, Product $product = null)
    {
        $id = $product ? $product->id : null;
        $track_qty = $request->has('track_qty') ? 'yes' : 'no';
        $image = $request->input('image', null);
        $imageThumbnails = $request->input('imageThumbnails', []);

        // Chuyển image thành chuỗi nếu là mảng (lấy giá trị đầu tiên)
        if (is_array($image)) {
            $image = !empty($image) ? $image[0] : null;
        }

        // Lọc các giá trị không rỗng từ imageThumbnails
        $imageThumbnails = array_filter($imageThumbnails, fn($val) => !empty($val));

        // Kiểm tra và giải mã specifications
        $specifications = $request->input('specifications');
        $specificationsArray = [];
        if ($specifications) {
            $specificationsArray = json_decode($specifications, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return redirect()->back()->withErrors(['specifications' => 'Dữ liệu thông số kỹ thuật không hợp lệ (JSON không đúng định dạng).']);
            }
        }

        // Validation
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => ['required', 'string', 'max:255', Rule::unique('products')->ignore($id)],
            'description' => 'nullable|string',
            // 'price' => $request->input('product_type') == 'single' ? 'required|numeric|min:0' : 'nullable|numeric|min:0',
            'original_price' => $request->input('product_type') == 'single' ? 'nullable|numeric|min:0' : 'nullable|numeric|min:0',
            'discount_percentage' => $request->input('product_type') == 'single' ? 'nullable|numeric|min:0|max:100' : 'nullable|numeric|min:0|max:100',
            'discount_start_date' => $request->input('product_type') == 'single' ? 'nullable|date' : 'nullable|date',
            'discount_end_date' => $request->input('product_type') == 'single' ? 'nullable|date|after_or_equal:discount_start_date' : 'nullable|date|after_or_equal:discount_start_date',
            'qty' => $request->input('product_type') == 'single' ? 'nullable|numeric|min:0' : 'nullable|numeric|min:0',
            'status' => 'required|in:0,1',
            'category_json' => 'required|json',
            'brand_id' => 'nullable|exists:brands,id',
            'image' => 'nullable|string',
            'is_featured' => 'required|in:yes,no',
            'sku' => 'required|string|max:255',
            'barcode' => 'nullable|max:255',
            'specifications' => 'nullable|string', // Có thể thêm validation JSON nếu cần
            'warranty_period' => 'nullable|integer|min:0',
            'warranty_policy' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'imageThumbnails' => 'nullable|array',
            'imageThumbnails.*' => 'nullable|string',
            'variants.new.name' => $request->input('product_type') == 'variant' ? 'nullable|array' : 'nullable|array',
            'variants.new.original_price' => $request->input('product_type') == 'variant' ? 'nullable|array' : 'nullable|array',
            'variants.new.discount_percentage' => $request->input('product_type') == 'variant' ? 'nullable|array' : 'nullable|array',
            'variants.new.discount_start_date' => $request->input('product_type') == 'variant' ? 'nullable|array' : 'nullable|array',
            'variants.new.discount_end_date' => $request->input('product_type') == 'variant' ? 'nullable|array' : 'nullable|array',
            'variants.new.sku' => $request->input('product_type') == 'variant' ? 'nullable|array' : 'nullable|array',
            'variants.new.qty' => $request->input('product_type') == 'variant' ? 'nullable|array' : 'nullable|array',
            'variants.existing.name' => $request->input('product_type') == 'variant' ? 'nullable|array' : 'nullable|array',
            'variants.existing.original_price' => $request->input('product_type') == 'variant' ? 'nullable|array' : 'nullable|array',
            'variants.existing.discount_percentage' => $request->input('product_type') == 'variant' ? 'nullable|array' : 'nullable|array',
            'variants.existing.discount_start_date' => $request->input('product_type') == 'variant' ? 'nullable|array' : 'nullable|array',
            'variants.existing.discount_end_date' => $request->input('product_type') == 'variant' ? 'nullable|array' : 'nullable|array',
            'variants.existing.sku' => $request->input('product_type') == 'variant' ? 'nullable|array' : 'nullable|array',
            'variants.existing.qty' => $request->input('product_type') == 'variant' ? 'nullable|array' : 'nullable|array',
            'product_type' => 'required|in:single,variant',
        ], [
            'slug.unique' => 'Slug này đã tồn tại, vui lòng chọn tên khác.',
            // 'price.required' => 'Vui lòng nhập giá bán.',
            'original_price.required' => 'Vui lòng nhập giá gốc.',
            'discount_percentage.max' => 'Phần trăm giảm giá không được vượt quá 100%.',
            'discount_end_date.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
        ]);

        // Xử lý category_id và subcategory_id

        $categoryJson = $request->input('category_json');
        $categoryData = json_decode($categoryJson, true);

        if ($categoryData) {
            $validated['category_id'] = $categoryData['category_id'];
            $validated['subcategory_id'] = $categoryData['sub_category_id'];
        } else {
            return redirect()->back()->withErrors(['category_json' => 'Danh mục không hợp lệ.']);
        }

        // Gán specifications vào validated
        $validated['specifications'] = $specificationsArray; // Lưu mảng đã giải mã thay vì chuỗi JSON
        $validated['track_qty'] = $track_qty;

        if ($product) {
            // Cập nhật sản phẩm
            $product->update($validated);
            $message = 'Cập nhật thành công';

            // === Xử lý ảnh đại diện ===
            $oldMainImage = $product->productImages()->where('type', 1)->first();
            if ($image === '') {
                if ($oldMainImage) {
                    Storage::disk('public')->delete($oldMainImage->image);
                    $oldMainImage->delete();
                }
            } elseif (!empty($image) && $image !== ($oldMainImage->image ?? '')) {
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

            // === Xử lý ảnh thumbnails ===
            $oldThumbnails = $product->productImages()->where('type', 2)->get();
            $existingThumbnailPaths = $oldThumbnails->pluck('image')->toArray();

            if (empty($imageThumbnails) && $request->has('imageThumbnails')) {
                foreach ($oldThumbnails as $thumbnail) {
                    Storage::disk('public')->delete($thumbnail->image);
                    $thumbnail->delete();
                }
            } elseif (!empty($imageThumbnails) && array_diff($imageThumbnails, $existingThumbnailPaths)) {
                foreach ($oldThumbnails as $thumbnail) {
                    Storage::disk('public')->delete($thumbnail->image);
                    $thumbnail->delete();
                }
                foreach ($imageThumbnails as $thumbnail) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'type' => 2,
                        'image' => $thumbnail
                    ]);
                }
            }

            // === Xử lý biến thể hiện có ===
            if ($request->has('variants.existing.name')) {
                $existingVariants = $product->productVariants->keyBy('id');
                $existingNames = $request->input('variants.existing.name', []);
                $existingOriginalPrices = $request->input('variants.existing.original_price', []);
                $existingDiscountedPrices = $request->input('variants.existing.discount_percentage', []);
                $existingDiscountStartDates = $request->input('variants.existing.discount_start_date', []);
                $existingDiscountEndDates = $request->input('variants.existing.discount_end_date', []);
                $existingSkus = $request->input('variants.existing.sku', []);
                $existingQtys = $request->input('variants.existing.qty', []);

                foreach ($existingNames as $variantId => $name) {
                    if (isset($existingVariants[$variantId]) && !empty($name)) {
                        $existingVariants[$variantId]->update([
                            'variant_name' => $name,
                            'original_price' => $existingOriginalPrices[$variantId] ?? 0,
                            'discount_percentage' => $existingDiscountedPrices[$variantId] ?? null,
                            'discount_start_date' => $existingDiscountStartDates[$variantId] ?? null,
                            'discount_end_date' => $existingDiscountEndDates[$variantId] ?? null,
                            'sku' => $existingSkus[$variantId] ?? '',
                            'qty' => $existingQtys[$variantId] ?? 0,
                        ]);
                    }
                }
                $existingVariantIds = $existingVariants->keys()->toArray();
                $keptVariantIds = array_keys(array_filter($existingNames, 'strlen'));
                $variantsToDelete = array_diff($existingVariantIds, $keptVariantIds);
                $product->productVariants()->whereIn('id', $variantsToDelete)->delete();
            }

            // === Xử lý biến thể mới ===
            if ($request->has('variants.new.name')) {
                $newNames = $request->input('variants.new.name', []);
                $newOriginalPrices = $request->input('variants.new.original_price', []);
                $newDiscountedPrices = $request->input('variants.new.discount_percentage', []);
                $newDiscountStartDates = $request->input('variants.new.discount_start_date', []);
                $newDiscountEndDates = $request->input('variants.new.discount_end_date', []);
                $newSkus = $request->input('variants.new.sku', []);
                $newQtys = $request->input('variants.new.qty', []);

                foreach (array_filter($newNames) as $index => $name) {
                    $product->productVariants()->create([
                        'variant_name' => $name,
                        'original_price' => $newOriginalPrices[$index] ?? 0,
                        'discount_percentage' => $newDiscountedPrices[$index] ?? null,
                        'discount_start_date' => $newDiscountStartDates[$index] ?? null,
                        'discount_end_date' => $newDiscountEndDates[$index] ?? null,
                        'sku' => $newSkus[$index] ?? '',
                        'qty' => $newQtys[$index] ?? 0,
                    ]);
                }
            }
        } else {
            // Tạo mới sản phẩm
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

            if ($request->has('variants.new.name')) {
                $newNames = $request->input('variants.new.name', []);
                $newOriginalPrices = $request->input('variants.new.original_price', []);
                $newDiscountedPrices = $request->input('variants.new.discount_percentage', []);
                $newDiscountStartDates = $request->input('variants.new.discount_start_date', []);
                $newDiscountEndDates = $request->input('variants.new.discount_end_date', []);
                $newSkus = $request->input('variants.new.sku', []);
                $newQtys = $request->input('variants.new.qty', []);

                foreach (array_filter($newNames) as $index => $name) {
                    $product->productVariants()->create([
                        'variant_name' => $name,
                        'original_price' => $newOriginalPrices[$index] ?? 0,
                        'discount_percentage' => $newDiscountedPrices[$index] ?? null,
                        'discount_start_date' => $newDiscountStartDates[$index] ?? null,
                        'discount_end_date' => $newDiscountEndDates[$index] ?? null,
                        'sku' => $newSkus[$index] ?? '',
                        'qty' => $newQtys[$index] ?? 0,
                    ]);
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

        ProductVariant::whereIn('product_id', $request->ids)->delete();
        Product::whereIn('id', $request->ids)->delete();

        return response()->json(['success' => 'success', 'message' => 'Xóa hàng loạt thành công']);
    }

    // public function uploadImage(Request $request)
    // {
    //     if (!$request->hasFile('file')) {
    //         return response()->json(['error' => 'No file uploaded'], 400);
    //     }

    //     $file = $request->file('file');
    //     if (!$file->isValid()) {
    //         return response()->json(['error' => 'Invalid file'], 400);
    //     }

    //     $fileName = time() . '_' . $file->getClientOriginalName();
    //     $filePath = Storage::disk('public')->putFileAs('products', $file, $fileName);

    //     return response()->json([
    //         'filePath' => 'products/' . $fileName, // <-- đảm bảo lưu đúng
    //         'url' => Storage::url('products/' . $fileName),
    //     ]);
    // }

    public function uploadImage(Request $request)
    {
        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }

        $file = $request->file('file');
        if (!$file->isValid()) {
            return response()->json(['error' => 'Invalid file'], 400);
        }

        // Định nghĩa kích thước cố định (ví dụ: 800x600 pixel)
        $width = 600;
        $height = 600;

        // Tạo tên file duy nhất
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = 'products/' . $fileName;

        // Resize và lưu ảnh
        $image = Image::make($file)->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio(); // Giữ tỷ lệ khung hình
            $constraint->upsize(); // Không phóng to nếu ảnh nhỏ hơn
        })->encode(null, 90); // Chất lượng 90%

        // Lưu vào storage
        Storage::disk('public')->put($filePath, (string) $image);

        return response()->json([
            'filePath' => $filePath,
            'url' => Storage::url($filePath),
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
            // Danh mục cha
            $categoryList[json_encode(['category_id' => $category->id, 'sub_category_id' => null])] = $category->name;

            // Danh mục con
            foreach ($category->subcategories as $subcategory) {
                $categoryList[json_encode([
                    'category_id' => $category->id,
                    'sub_category_id' => $subcategory->id
                ])] = $category->name . ' > ' . $subcategory->name;
            }
        }

        return $categoryList;
    }

    public function toggleStatus(Product $product)
    {
        $product->update([
            'status' => $product->status == 1 ? 0 : 1
        ]);

        return redirect()->route('admin.product.index')->with(['status' => 'success', 'message' => 'Cập nhật trạng thái thành công']);
    }
}
