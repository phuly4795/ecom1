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

        if ($brandId = request('brand_id')) {
            $query->where('brand_id', $brandId);
        }

        if (request()->has('status') && request('status') !== null && request('status') !== '') {
            $query->where('status', request('status'));
        }

        if (request('qty_status') === 'in_stock') {
            $query->where(function ($q) {
                $q->where(function ($sub) {
                    $sub->where('product_type', '!=', 'variant')->where('qty', '>', 0);
                })->orWhere(function ($sub) {
                    $sub->where('product_type', 'variant')->whereHas('productVariants', function ($vQ) {
                        $vQ->where('qty', '>', 0);
                    });
                });
            });
        } elseif (request('qty_status') === 'out_of_stock') {
            $query->where(function ($q) {
                $q->where(function ($sub) {
                    $sub->where('product_type', '!=', 'variant')->where(function($sq) {
                        $sq->where('qty', '<=', 0)->orWhereNull('qty');
                    });
                })->orWhere(function ($sub) {
                    $sub->where('product_type', 'variant')->whereDoesntHave('productVariants', function ($vQ) {
                        $vQ->where('qty', '>', 0);
                    });
                });
            });
        }

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
                if ($product->product_type !== 'variant') {
                    $originalPrice = number_format($product->original_price) . ' đ';
                    if ($product->is_on_sale) {
                        $displayPrice = number_format($product->display_price) . ' đ';
                        return '<del class="text-muted small">' . $originalPrice . '</del><br/><strong class="text-danger">' . $displayPrice . '</strong>';
                    }
                    return '<strong>' . $originalPrice . '</strong>';
                } else {
                    if ($product->productVariants->isEmpty()) {
                        return '<span class="text-muted">Chưa cấu hình</span>';
                    }
                    $prices = $product->productVariants->map(function ($v) {
                        return $v->display_price;
                    });
                    $minPrice = $prices->min();
                    $maxPrice = $prices->max();
                    if ($minPrice == $maxPrice) {
                        return '<strong>' . number_format($minPrice) . ' đ</strong>';
                    }
                    return '<strong>' . number_format($minPrice) . ' đ - ' . number_format($maxPrice) . ' đ</strong>';
                }
            })
            ->addColumn('actions', function ($product) {
                $editUrl = route('admin.product.edit', $product);
                $deleteUrl = route('admin.product.destroy', $product);
                $toggleStatusUrl = route('admin.product.toggleStatus', $product);
                $cloneUrl = route('admin.product.clone', $product);

                $html = '<div class="d-flex gap-2">';
                $html .= '<a href="' . $editUrl . '" class="btn btn-sm btn-warning" data-toggle="tooltip" title="Sửa sản phẩm" style="margin-right: 2%;"><i class="fas fa-edit"></i></a>';
                $html .= '<button type="button" class="btn btn-sm btn-info clone-product-btn" data-url="' . $cloneUrl . '" data-toggle="tooltip" title="Nhân bản sản phẩm" style="margin-right: 2%;"><i class="fas fa-copy"></i></button>';
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
                $editUrl = route('admin.product.edit', $product);
                return '<a href="' . $editUrl . '" style="color: #000;font-weight: 700;"><span data-toggle="tooltip" title="' . $product->title . '">' . Str::limit($product->title, 30, '...') . '</span></a>';
            })

            ->editColumn('qty', function ($product) {
                $html = '<div>';
                if ($product->product_type !== "variant") {
                    $html .= '<span class="badge badge-' . ($product->qty > 0 ? 'success' : 'danger') . '">Còn: ' . $product->qty . '</span>';
                    if ($product->is_on_sale) {
                        $html .= '<span class="badge badge-danger ml-1">Khuyến mãi: ' . $product->discount_percentage . '%</span>';
                    }
                } else {
                    foreach ($product->productVariants as $variant) {
                        $html .= '<span class="badge badge-' . ($variant->qty > 0 ? 'success' : 'danger') . ' mb-1">' . $variant->variant_name . ': ' . $variant->qty . '</span>';
                        if ($variant->is_on_sale) {
                            $html .= '<span class="badge badge-danger ml-1">Khuyến mãi: ' . $variant->discount_percentage . '%</span><br/>';
                        }
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

                if ($product->is_featured == 1 || $product->is_featured === 'yes') {
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
        $validated['track_qty'] = $track_qty === 'yes' ? 1 : 0;
        $validated['is_featured'] = $validated['is_featured'] === 'yes' ? 1 : 0;
        $validated['price'] = $validated['original_price'] ?? 0;
        $validated['discount_percentage'] = $validated['discount_percentage'] ?? 0;
        $validated['qty'] = $validated['qty'] ?? 0;

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
                            'discount_percentage' => $existingDiscountedPrices[$variantId] ?? 0,
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
                        'discount_percentage' => $newDiscountedPrices[$index] ?? 0,
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
                        'discount_percentage' => $newDiscountedPrices[$index] ?? 0,
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

        foreach ($request->ids as $id) {
            $product = Product::with('productVariants')->find($id);
            if ($product) {
                $product->productVariants()->delete();
                $product->delete();
            }
        }

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
        $category = Category::with('subCategories')->findOrFail($category_id);
        return response()->json($category->subCategories);
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

    public function clone(Product $product)
    {
        DB::beginTransaction();
        try {
            // 1. Sao chép các thuộc tính sản phẩm
            $newProduct = $product->replicate();
            $newProduct->title = $product->title . ' (Bản sao)';
            
            // Tạo slug mới unique
            $slug = Str::slug($newProduct->title);
            $count = Product::where('slug', 'like', $slug . '%')->count();
            $newProduct->slug = $count ? "{$slug}-{$count}" : $slug;
            
            // Tạo SKU mới
            $newProduct->sku = $product->sku . '-CLONE-' . Str::upper(Str::random(4));
            
            // Reset số lượng về 0 và barcode mới
            $newProduct->qty = 0;
            $newProduct->barcode = $this->generateBarcode();
            
            $newProduct->save();

            // 2. Sao chép ảnh sản phẩm
            foreach ($product->productImages as $image) {
                $newImage = $image->replicate();
                $newImage->product_id = $newProduct->id;
                $newImage->save();
            }

            // 3. Sao chép các biến thể sản phẩm
            foreach ($product->productVariants as $variant) {
                $newVariant = $variant->replicate();
                $newVariant->product_id = $newProduct->id;
                $newVariant->sku = $variant->sku . '-CL-' . Str::upper(Str::random(3));
                $newVariant->qty = 0; // Reset số lượng về 0
                $newVariant->save();
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Nhân bản sản phẩm thành công!',
                'redirect_url' => route('admin.product.edit', $newProduct)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi nhân bản sản phẩm: ' . $e->getMessage()
            ], 500);
        }
    }

    public function crawl(Request $request)
    {
        $request->validate([
            'url' => 'required|url'
        ]);

        $url = $request->input('url');

        // Fix SSRF: Chỉ cho phép URL bên ngoài, chặn IP nội bộ
        $parsedUrl = parse_url($url);
        $host = $parsedUrl['host'] ?? '';
        $hostIp = gethostbyname($host);
        if (filter_var($hostIp, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return response()->json([
                'success' => false,
                'message' => 'URL không hợp lệ: không được phép truy cập địa chỉ nội bộ.'
            ], 400);
        }

        try {
            // Sử dụng cURL để tải nội dung HTML giả lập trình duyệt
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36');
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language: vi-VN,vi;q=0.9,en;q=0.8',
            ]);
            $html = curl_exec($ch);
            curl_close($ch);

            if (!$html) {
                throw new \Exception("Không thể tải nội dung từ đường dẫn này.");
            }

            // Bóc tách dữ liệu
            $title = '';
            $description = '';
            $salePrice = '';    // Giá đã giảm (giá bán thực tế)
            $originalPrice = ''; // Giá gốc (giá niêm yết)
            $images = [];
            $specifications = [];
            $crawledVariants = [];

            // Danh sách từ khóa lọc ảnh rác
            $junkKeywords = ['logo', 'icon', 'banner', 'favicon', 'sprite', 'avatar', 'pixel', 'tracking', 'badge', 'tag', 'label', 'flag', '1x1', 'blank.', 'spacer', 'loading', 'placeholder', 'data:image'];

            // Hàm kiểm tra URL ảnh hợp lệ
            $isValidImageUrl = function($url) use ($junkKeywords) {
                if (empty($url) || strlen($url) < 10) return false;
                if (!str_starts_with($url, 'http')) return false;
                $urlLower = strtolower($url);
                foreach ($junkKeywords as $junk) {
                    if (str_contains($urlLower, $junk)) return false;
                }
                return true;
            };

            // =============================================
            // 1. Quét JSON-LD Schema.org Product
            // =============================================
            $dom = new \DOMDocument();
            @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
            $xpath = new \DOMXPath($dom);
            
            $jsonLdScripts = $xpath->query('//script[@type="application/ld+json"]');
            foreach ($jsonLdScripts as $script) {
                $jsonText = trim($script->nodeValue);
                $jsonData = json_decode($jsonText, true);
                if (!is_array($jsonData)) continue;

                $nodes = isset($jsonData['@graph']) ? $jsonData['@graph'] : [$jsonData];
                foreach ($nodes as $node) {
                    $nodeType = $node['@type'] ?? '';
                    if (is_array($nodeType)) $nodeType = implode(',', $nodeType);
                    if (stripos($nodeType, 'product') === false) continue;

                    $title = $node['name'] ?? $title;
                    $description = $node['description'] ?? $description;
                    
                    // Lấy giá từ JSON-LD (thường là giá bán/sale price)
                    if (isset($node['offers'])) {
                        $offers = $node['offers'];
                        // Xử lý AggregateOffer
                        $offerType = $offers['@type'] ?? '';
                        if (stripos($offerType, 'AggregateOffer') !== false) {
                            $salePrice = $offers['lowPrice'] ?? ($offers['price'] ?? $salePrice);
                            $originalPrice = $offers['highPrice'] ?? $originalPrice;
                        } elseif (isset($offers['price'])) {
                            $salePrice = $offers['price'];
                        } elseif (is_array($offers) && isset($offers[0]['price'])) {
                            $salePrice = $offers[0]['price'];
                        }

                        // Trích xuất tất cả các Offers để tìm biến thể
                        $extractOffers = function($item) use (&$crawledVariants, &$extractOffers) {
                            if (!is_array($item)) return;
                            if (isset($item['price'])) {
                                $name = $item['name'] ?? '';
                                if (empty($name) && isset($item['sku'])) {
                                    $name = 'Phiên bản ' . $item['sku'];
                                }
                                if (!empty($name)) {
                                    $crawledVariants[] = [
                                        'name' => $name,
                                        'price' => floatval($item['price']),
                                        'sku' => $item['sku'] ?? ''
                                    ];
                                }
                            } else {
                                foreach ($item as $val) {
                                    if (is_array($val)) {
                                        $extractOffers($val);
                                    }
                                }
                            }
                        };
                        $extractOffers($offers);
                    }
                    
                    // Lấy ảnh từ JSON-LD
                    if (isset($node['image'])) {
                        $nodeImages = is_array($node['image']) ? $node['image'] : [$node['image']];
                        foreach ($nodeImages as $img) {
                            if (is_string($img) && $isValidImageUrl($img)) {
                                $images[] = $img;
                            } elseif (is_array($img) && isset($img['url'])) {
                                if ($isValidImageUrl($img['url'])) $images[] = $img['url'];
                            }
                        }
                    }
                }
            }

            // =============================================
            // 2. Fallback sang OpenGraph tags nếu thiếu
            // =============================================
            if (empty($title)) {
                $ogTitleNode = $xpath->query('//meta[@property="og:title"]/@content');
                if ($ogTitleNode->length > 0) {
                    $title = $ogTitleNode->item(0)->nodeValue;
                } else {
                    $titleNode = $xpath->query('//title');
                    if ($titleNode->length > 0) {
                        $title = trim($titleNode->item(0)->nodeValue);
                    }
                }
            }

            if (empty($description)) {
                $ogDescNode = $xpath->query('//meta[@property="og:description"]/@content');
                if ($ogDescNode->length > 0) {
                    $description = $ogDescNode->item(0)->nodeValue;
                }
            }

            if (empty($salePrice)) {
                $priceNode = $xpath->query('//meta[@property="og:price:amount"]/@content');
                if ($priceNode->length > 0) {
                    $salePrice = $priceNode->item(0)->nodeValue;
                }
                // Cũng kiểm tra product:price:amount
                if (empty($salePrice)) {
                    $priceNode2 = $xpath->query('//meta[@property="product:price:amount"]/@content');
                    if ($priceNode2->length > 0) {
                        $salePrice = $priceNode2->item(0)->nodeValue;
                    }
                }
            }

            // Lấy danh sách ảnh từ og:image
            $ogImageNode = $xpath->query('//meta[@property="og:image"]/@content');
            foreach ($ogImageNode as $meta) {
                if ($isValidImageUrl($meta->nodeValue)) {
                    $images[] = $meta->nodeValue;
                }
            }

            // =============================================
            // 3. Quét ảnh nâng cao: data-src, data-zoom-image, gallery
            // =============================================
            $imgAttrs = ['src', 'data-src', 'data-zoom-image', 'data-original', 'data-image', 'data-lazy-src', 'data-srcset'];
            foreach ($imgAttrs as $attr) {
                $imgNodes = $xpath->query('//img/@' . $attr);
                foreach ($imgNodes as $imgNode) {
                    $val = trim($imgNode->nodeValue);
                    // Xử lý data-srcset (lấy URL đầu tiên)
                    if ($attr === 'data-srcset' && str_contains($val, ',')) {
                        $val = trim(explode(',', $val)[0]);
                        $val = trim(explode(' ', $val)[0]);
                    }
                    if ($isValidImageUrl($val)) {
                        $images[] = $val;
                    }
                }
            }

            // Quét ảnh từ thẻ <a> có gallery/lightbox attributes
            $gallerySelectors = [
                '//a[contains(@class, "spotlight")]/@href',           // CellphoneS
                '//a[@data-fancybox]/@href',                          // Fancybox
                '//a[@data-lightbox]/@href',                          // Lightbox
                '//a[@data-gallery]/@href',                           // Generic gallery
                '//a[@data-zoom-image]/@href',                        // Zoom
                '//a/@data-zoom-image',                               // data-zoom-image attr
            ];
            foreach ($gallerySelectors as $selector) {
                $galleryNodes = $xpath->query($selector);
                foreach ($galleryNodes as $node) {
                    if ($isValidImageUrl($node->nodeValue)) {
                        $images[] = $node->nodeValue;
                    }
                }
            }

            // Fallback: Quét tất cả thẻ <a> có href trỏ đến file ảnh (.jpg, .png, .webp)
            $allLinks = $xpath->query('//a/@href');
            foreach ($allLinks as $link) {
                $href = trim($link->nodeValue);
                if ($isValidImageUrl($href) && preg_match('/\.(jpe?g|png|webp|gif)(\?.*)?$/i', $href)) {
                    $images[] = $href;
                }
            }

            // Đảm bảo ảnh là duy nhất và hợp lệ, giới hạn 20 ảnh
            $images = array_unique(array_filter($images));
            $images = array_values($images);
            $images = array_slice($images, 0, 20);

            // =============================================
            // 4. Trích xuất GIÁ GỐC từ HTML (nhiều nguồn)
            // =============================================
            
            // 4a. Tìm giá gốc (giá cũ, giá trước giảm) từ HTML elements
            $originalPriceSelectors = [
                // Thẻ gạch ngang truyền thống
                '//del',
                '//strike',
                '//s[not(ancestor::script)]',
                // CellphoneS specific
                '//*[contains(@class, "product__price--through")]',
                '//*[contains(@class, "box-price__old")]',
                '//*[contains(@class, "price-old")]',
                '//*[contains(@class, "tpt-box-price__old")]',
                // Các trang TMDT VN phổ biến
                '//*[contains(@class, "old-price")]',
                '//*[contains(@class, "original-price")]',
                '//*[contains(@class, "list-price")]',
                '//*[contains(@class, "compare-price")]',
                '//*[contains(@class, "regular-price")]',
                '//*[contains(@class, "price-original")]',
                '//*[contains(@class, "price--compare")]',
                '//*[contains(@class, "product-price__list-price")]',
                // FPT Shop
                '//*[contains(@class, "progress-price")]',
                '//*[contains(@class, "strike-price")]',
                // Thegioididong
                '//*[contains(@class, "price-old")]',
            ];
            foreach ($originalPriceSelectors as $selector) {
                $priceNodes = $xpath->query($selector);
                foreach ($priceNodes as $pNode) {
                    $priceText = trim($pNode->nodeValue);
                    $extracted = preg_replace('/[^\d]/', '', $priceText);
                    if (strlen($extracted) >= 5 && intval($extracted) > 0) {
                        $originalPrice = $extracted;
                        break 2;
                    }
                }
            }

            // 4b. Tìm giá bán (giá hiện tại, giá khuyến mãi) từ HTML elements nếu chưa có
            if (empty($salePrice)) {
                $salePriceSelectors = [
                    '//*[contains(@class, "product__price--show")]',
                    '//*[contains(@class, "box-price__present")]',
                    '//*[contains(@class, "tpt-box-price__price-special")]',
                    '//*[contains(@class, "special-price")]',
                    '//*[contains(@class, "price-current")]',
                    '//*[contains(@class, "product-price__current-price")]',
                    '//*[contains(@class, "price--sale")]',
                ];
                foreach ($salePriceSelectors as $selector) {
                    $priceNodes = $xpath->query($selector);
                    foreach ($priceNodes as $pNode) {
                        $priceText = trim($pNode->nodeValue);
                        $extracted = preg_replace('/[^\d]/', '', $priceText);
                        if (strlen($extracted) >= 5 && intval($extracted) > 0) {
                            $salePrice = $extracted;
                            break 2;
                        }
                    }
                }
            }

            // 4c. Fallback: Tìm giá từ regex trong raw HTML (pattern giá VNĐ phổ biến)
            if (empty($originalPrice) && empty($salePrice)) {
                // Tìm tất cả giá dạng xx.xxx.xxxđ hoặc xx,xxx,xxx VNĐ
                preg_match_all('/(\d{1,3}(?:[.,]\d{3})+)\s*(?:đ|₫|VNĐ|vnđ)/u', $html, $allPrices);
                if (!empty($allPrices[1])) {
                    $parsedPrices = [];
                    foreach ($allPrices[1] as $p) {
                        $num = intval(preg_replace('/[^\d]/', '', $p));
                        if ($num >= 10000) { // >= 10,000đ
                            $parsedPrices[] = $num;
                        }
                    }
                    $parsedPrices = array_unique($parsedPrices);
                    sort($parsedPrices);
                    if (count($parsedPrices) >= 2) {
                        // Giá nhỏ nhất là sale price, giá lớn nhất gần nó là original price
                        $salePrice = $parsedPrices[0];
                        $originalPrice = $parsedPrices[count($parsedPrices) - 1];
                    } elseif (count($parsedPrices) === 1) {
                        $originalPrice = $parsedPrices[0];
                    }
                }
            }

            // 4d. Chuẩn hóa: Nếu không tìm được giá gốc riêng, dùng giá sale làm giá gốc
            if (empty($originalPrice) && !empty($salePrice)) {
                $originalPrice = $salePrice;
                $salePrice = ''; // Không có giảm giá
            }
            // Nếu có giá gốc nhưng không có giá sale
            if (!empty($originalPrice) && empty($salePrice)) {
                // Không làm gì — sản phẩm chưa giảm giá
            }

            // =============================================
            // 5. Trích xuất biến thể từ HTML elements
            // =============================================
            if (count($crawledVariants) < 2) {
                // Tìm các container biến thể phổ biến trên trang TMDT VN
                $variantSelectors = [
                    '//*[contains(@class, "box-option") or contains(@class, "product-version") or contains(@class, "variant-selector") or contains(@class, "product-variant")]//a | //*[contains(@class, "box-option") or contains(@class, "product-version") or contains(@class, "variant-selector") or contains(@class, "product-variant")]//button | //*[contains(@class, "box-option") or contains(@class, "product-version") or contains(@class, "variant-selector") or contains(@class, "product-variant")]//label',
                ];
                
                foreach ($variantSelectors as $selector) {
                    $variantNodes = $xpath->query($selector);
                    foreach ($variantNodes as $vNode) {
                        $vText = trim($vNode->nodeValue);
                        // Lọc text quá ngắn hoặc quá dài
                        if (strlen($vText) >= 2 && strlen($vText) <= 80) {
                            // Tìm giá bên trong element
                            $priceInVariant = '';
                            $priceSpans = $xpath->query('.//*[contains(@class, "price") or contains(@class, "gia")]', $vNode);
                            if ($priceSpans->length > 0) {
                                $priceInVariant = preg_replace('/[^\d]/', '', $priceSpans->item(0)->nodeValue);
                            }
                            // Nếu không có giá riêng, thử regex trong text
                            if (empty($priceInVariant)) {
                                preg_match('/[\d.,]+đ/', $vText, $priceMatch);
                                if (!empty($priceMatch)) {
                                    $priceInVariant = preg_replace('/[^\d]/', '', $priceMatch[0]);
                                    // Loại bỏ giá khỏi tên biến thể
                                    $vText = trim(str_replace($priceMatch[0], '', $vText));
                                }
                            }

                            if (!empty($vText)) {
                                // Tách tên và giá nếu có dòng mới
                                $lines = preg_split('/[\n\r]+/', $vText);
                                $variantName = trim($lines[0]);
                                if (empty($priceInVariant) && count($lines) > 1) {
                                    $possiblePrice = preg_replace('/[^\d]/', '', $lines[count($lines) - 1]);
                                    if (strlen($possiblePrice) >= 5) {
                                        $priceInVariant = $possiblePrice;
                                    }
                                }
                                
                                $crawledVariants[] = [
                                    'name' => $variantName,
                                    'price' => $priceInVariant ? floatval($priceInVariant) : 0,
                                    'sku' => ''
                                ];
                            }
                        }
                    }
                    if (count($crawledVariants) >= 2) break;
                }
            }

            // =============================================
            // 6. Trích xuất biến thể từ JavaScript objects nhúng trong <script>
            // =============================================
            if (count($crawledVariants) < 2) {
                // Tìm mảng biến thể trong JavaScript (pattern phổ biến trên e-commerce VN)
                $patterns = [
                    '/(?:variants|product_variants|productVariants|options)\s*[:=]\s*(\[[\s\S]*?\]);/i',
                    '/"variants"\s*:\s*(\[[\s\S]*?\])/i',
                ];
                foreach ($patterns as $pattern) {
                    if (preg_match($pattern, $html, $matches)) {
                        $variantsJson = json_decode($matches[1], true);
                        if (is_array($variantsJson) && count($variantsJson) >= 2) {
                            foreach ($variantsJson as $vItem) {
                                $vName = $vItem['name'] ?? ($vItem['title'] ?? ($vItem['variant_name'] ?? ''));
                                $vPrice = $vItem['price'] ?? ($vItem['original_price'] ?? 0);
                                if (!empty($vName)) {
                                    $crawledVariants[] = [
                                        'name' => $vName,
                                        'price' => floatval($vPrice),
                                        'sku' => $vItem['sku'] ?? ''
                                    ];
                                }
                            }
                            break;
                        }
                    }
                }
            }

            // =============================================
            // 7. Tự động bóc tách thông số kỹ thuật
            // =============================================
            $tables = $xpath->query('//table');
            $specGroups = [];
            $tempItems = [];

            foreach ($tables as $table) {
                $rows = $xpath->query('.//tr', $table);
                foreach ($rows as $row) {
                    $cols = $xpath->query('.//td|.//th', $row);
                    if ($cols->length === 2) {
                        $key = trim($cols->item(0)->nodeValue);
                        $val = trim($cols->item(1)->nodeValue);
                        if (!empty($key) && !empty($val) && strlen($key) < 50 && strlen($val) < 150) {
                            $tempItems[$key] = $val;
                        }
                    }
                }
            }

            if (!empty($tempItems)) {
                $specGroups[] = [
                    'name' => 'Thông số kỹ thuật cào tự động',
                    'items' => $tempItems
                ];
            } else {
                $specListNodes = $xpath->query('//ul[contains(@class, "parameter") or contains(@class, "spec")]/li');
                foreach ($specListNodes as $li) {
                    $spanNodes = $xpath->query('.//span', $li);
                    if ($spanNodes->length === 2) {
                        $key = trim($spanNodes->item(0)->nodeValue);
                        $val = trim($spanNodes->item(1)->nodeValue);
                        if (!empty($key) && !empty($val)) {
                            $tempItems[$key] = $val;
                        }
                    }
                }
                if (!empty($tempItems)) {
                    $specGroups[] = [
                        'name' => 'Thông số kỹ thuật cào tự động',
                        'items' => $tempItems
                    ];
                }
            }

            // Lọc bớt biến thể trùng tên
            $uniqueVariants = [];
            foreach ($crawledVariants as $v) {
                $uniqueVariants[$v['name']] = $v;
            }
            $crawledVariants = array_values($uniqueVariants);

            // =============================================
            // 8. Decode HTML entities và chuẩn hóa output
            // =============================================
            $title = html_entity_decode($title, ENT_QUOTES, 'UTF-8');
            $title = trim($title);
            $description = strip_tags(html_entity_decode($description, ENT_QUOTES, 'UTF-8'));

            // Parse giá thành số
            $parsedOriginalPrice = $originalPrice ? floatval(preg_replace('/[^\d]/', '', $originalPrice)) : '';
            $parsedSalePrice = $salePrice ? floatval(preg_replace('/[^\d]/', '', $salePrice)) : '';

            // Đảm bảo giá gốc luôn >= giá bán
            if ($parsedOriginalPrice && $parsedSalePrice && $parsedSalePrice > $parsedOriginalPrice) {
                $temp = $parsedOriginalPrice;
                $parsedOriginalPrice = $parsedSalePrice;
                $parsedSalePrice = $temp;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'title' => $title,
                    'description' => $description,
                    'original_price' => $parsedOriginalPrice,
                    'sale_price' => $parsedSalePrice ?: '',
                    'images' => $images,
                    'specifications' => $specGroups,
                    'variants' => count($crawledVariants) >= 2 ? $crawledVariants : []
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cào dữ liệu: ' . $e->getMessage()
            ], 500);
        }
    }

    private function isInternalUrl($url)
    {
        $parsed = parse_url($url);
        $host = $parsed['host'] ?? '';
        $ip = gethostbyname($host);
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false;
    }

    public function crawlDownloadImages(Request $request)
    {
        $request->validate([
            'main_image' => 'required|url',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'url'
        ]);

        $mainUrl = $request->input('main_image');
        if ($this->isInternalUrl($mainUrl)) {
            return response()->json(['success' => false, 'message' => 'URL ảnh không hợp lệ.'], 400);
        }
        foreach ($request->input('gallery_images', []) as $galUrl) {
            if ($this->isInternalUrl($galUrl)) {
                return response()->json(['success' => false, 'message' => 'URL ảnh phụ không hợp lệ.'], 400);
            }
        }

        try {
            $localImagePaths = [];

            // 1. Tải ảnh chính
            $mainContent = @file_get_contents($mainUrl);
            if (!$mainContent) {
                throw new \Exception("Không thể tải hình ảnh chính từ: " . $mainUrl);
            }
            $mainExt = pathinfo(parse_url($mainUrl, PHP_URL_PATH), PATHINFO_EXTENSION);
            if (empty($mainExt) || !in_array(strtolower($mainExt), ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                $mainExt = 'jpg';
            }
            $mainFilename = 'crawler_' . time() . '_' . Str::random(5) . '.' . $mainExt;
            $mainSavePath = 'products/' . $mainFilename;
            Storage::disk('public')->put($mainSavePath, $mainContent);
            $localImagePaths['main'] = $mainSavePath;

            // 2. Tải ảnh phụ
            $localImagePaths['gallery'] = [];
            $galleryUrls = $request->input('gallery_images', []);
            foreach ($galleryUrls as $galUrl) {
                $galContent = @file_get_contents($galUrl);
                if ($galContent) {
                    $galExt = pathinfo(parse_url($galUrl, PHP_URL_PATH), PATHINFO_EXTENSION);
                    if (empty($galExt) || !in_array(strtolower($galExt), ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                        $galExt = 'jpg';
                    }
                    $galFilename = 'crawler_' . time() . '_' . Str::random(5) . '.' . $galExt;
                    $galSavePath = 'products/' . $galFilename;
                    Storage::disk('public')->put($galSavePath, $galContent);
                    $localImagePaths['gallery'][] = $galSavePath;
                }
            }

            return response()->json([
                'success' => true,
                'data' => $localImagePaths
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tải hình ảnh về máy chủ: ' . $e->getMessage()
            ], 500);
        }
    }

    public function quickCreateCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sub_name' => 'nullable|string|max:255'
        ]);

        try {
            $catName = $request->input('name');
            $catSlug = Str::slug($catName);
            
            $category = Category::where('slug', $catSlug)->first();
            if (!$category) {
                $category = Category::create([
                    'name' => $catName,
                    'slug' => $catSlug,
                    'status' => 1,
                    'sort' => Category::max('sort') + 1,
                ]);
            }

            $subcatId = null;
            $subcatName = $request->input('sub_name');
            if (!empty($subcatName)) {
                $subcatSlug = Str::slug($subcatName);
                $subcategory = SubCategory::where('slug', $subcatSlug)->first();
                if (!$subcategory) {
                    $subcategory = SubCategory::create([
                        'category_id' => $category->id,
                        'name' => $subcatName,
                        'slug' => $subcatSlug,
                        'status' => 1
                    ]);
                    $category->subcategories()->syncWithoutDetaching([$subcategory->id]);
                }
                $subcatId = $subcategory->id;
            }

            $jsonValue = json_encode([
                'category_id' => $category->id,
                'sub_category_id' => $subcatId
            ]);
            $label = $category->name . ($subcatName ? ' > ' . $subcatName : '');

            return response()->json([
                'success' => true,
                'value' => $jsonValue,
                'label' => $label
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tạo danh mục: ' . $e->getMessage()
            ], 500);
        }
    }

    public function quickCreateBrand(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        try {
            $brandName = $request->input('name');
            $brandSlug = Str::slug($brandName);
            
            $brand = Brand::where('slug', $brandSlug)->first();
            if (!$brand) {
                $brand = Brand::create([
                    'name' => $brandName,
                    'slug' => $brandSlug,
                    'status' => 1
                ]);
            }

            return response()->json([
                'success' => true,
                'id' => $brand->id,
                'name' => $brand->name
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tạo thương hiệu: ' . $e->getMessage()
            ], 500);
        }
    }
}
