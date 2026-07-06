<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use App\Models\Province;
use App\Models\District;
use App\Models\Ward;
use App\Models\Brand;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductImage;
use App\Models\Setting;
use App\Models\ShippingFee;
use App\Models\Coupon;
use App\Models\Page;
use App\Models\Contact;
use App\Models\Warehouse;
use App\Models\WarehouseDetail;
use App\Models\FavoriteProduct;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Roles
        $adminRole = Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Admin']);
        $customerRole = Role::firstOrCreate(['slug' => 'customer'], ['name' => 'Customer']);

        // 2. Seed Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('12345678'),
                'phone' => '0987654321',
                'is_active' => true,
            ]
        );
        $admin->roles()->sync([$adminRole->id]);

        $customer = User::firstOrCreate(
            ['email' => 'customer@gmail.com'],
            [
                'name' => 'Customer User',
                'password' => Hash::make('12345678'),
                'phone' => '0123456789',
                'is_active' => true,
            ]
        );
        $customer->roles()->sync([$customerRole->id]);

        // 3. Seed Provinces, Districts, Wards
        $p1 = Province::firstOrCreate(['code' => '79'], ['name' => 'Hồ Chí Minh', 'full_name' => 'Thành phố Hồ Chí Minh']);
        $p2 = Province::firstOrCreate(['code' => '01'], ['name' => 'Hà Nội', 'full_name' => 'Thành phố Hà Nội']);
        $p3 = Province::firstOrCreate(['code' => '48'], ['name' => 'Đà Nẵng', 'full_name' => 'Thành phố Đà Nẵng']);

        $d1 = District::firstOrCreate(['code' => '760'], ['name' => 'Quận 1', 'full_name' => 'Quận 1', 'city_code' => '79']);
        $d2 = District::firstOrCreate(['code' => '001'], ['name' => 'Ba Đình', 'full_name' => 'Quận Ba Đình', 'city_code' => '01']);
        $d3 = District::firstOrCreate(['code' => '490'], ['name' => 'Hải Châu', 'full_name' => 'Quận Hải Châu', 'city_code' => '48']);

        $w1 = Ward::firstOrCreate(['code' => '26734'], ['name' => 'Bến Nghé', 'full_name' => 'Phường Bến Nghé', 'district_code' => '760']);
        $w2 = Ward::firstOrCreate(['code' => '00001'], ['name' => 'Phúc Xá', 'full_name' => 'Phường Phúc Xá', 'district_code' => '001']);
        $w3 = Ward::firstOrCreate(['code' => '20197'], ['name' => 'Hòa Cường Bắc', 'full_name' => 'Phường Hòa Cường Bắc', 'district_code' => '490']);

        // 4. Seed Shipping Fees
        ShippingFee::firstOrCreate(['province_id' => '79', 'district_id' => '760'], ['fee' => 20000]);
        ShippingFee::firstOrCreate(['province_id' => '01', 'district_id' => '001'], ['fee' => 25000]);
        ShippingFee::firstOrCreate(['province_id' => '48', 'district_id' => '490'], ['fee' => 15000]);

        // 5. Seed Settings
        Setting::firstOrCreate(['key' => 'site_logo'], ['value' => 'logo.png']);
        Setting::firstOrCreate(['key' => 'contact_email'], ['value' => 'contact@ecom1.com']);
        Setting::firstOrCreate(['key' => 'default_shipping_fee'], ['value' => '30000']);

        // 6. Seed Brands
        $apple = Brand::firstOrCreate(['slug' => 'apple'], ['name' => 'Apple', 'status' => 1]);
        $samsung = Brand::firstOrCreate(['slug' => 'samsung'], ['name' => 'Samsung', 'status' => 1]);
        $xiaomi = Brand::firstOrCreate(['slug' => 'xiaomi'], ['name' => 'Xiaomi', 'status' => 1]);

        // 7. Seed Categories
        $catPhones = Category::firstOrCreate(['slug' => 'dien-thoai'], ['name' => 'Điện thoại', 'status' => 1, 'sort' => 1]);
        $catLaptops = Category::firstOrCreate(['slug' => 'laptop'], ['name' => 'Laptop', 'status' => 1, 'sort' => 2]);
        $catAccessories = Category::firstOrCreate(['slug' => 'phu-kien'], ['name' => 'Phụ kiện', 'status' => 1, 'sort' => 3]);

        // 8. Seed SubCategories
        $subIphone = SubCategory::firstOrCreate(['slug' => 'iphone'], ['name' => 'iPhone', 'status' => 1]);
        $subGalaxy = SubCategory::firstOrCreate(['slug' => 'samsung-galaxy'], ['name' => 'Samsung Galaxy', 'status' => 1]);
        $subMacbook = SubCategory::firstOrCreate(['slug' => 'macbook'], ['name' => 'MacBook', 'status' => 1]);
        $subOplung = SubCategory::firstOrCreate(['slug' => 'op-lung'], ['name' => 'Ốp lưng', 'status' => 1]);

        // 9. Sync Category-SubCategory
        $catPhones->subCategories()->syncWithoutDetaching([$subIphone->id, $subGalaxy->id]);
        $catLaptops->subCategories()->syncWithoutDetaching([$subMacbook->id]);
        $catAccessories->subCategories()->syncWithoutDetaching([$subOplung->id]);

        // 10. Seed Products & Variants & Images
        // Product 1: iPhone 15 Pro Max
        $p1 = Product::firstOrCreate(
            ['slug' => 'iphone-15-pro-max'],
            [
                'title' => 'iPhone 15 Pro Max',
                'description' => 'Siêu phẩm iPhone 15 Pro Max mới nhất từ Apple.',
                'price' => 30000000,
                'compare_price' => 32000000,
                'original_price' => 30000000,
                'discount_percentage' => 0,
                'category_id' => $catPhones->id,
                'subcategory_id' => $subIphone->id,
                'brand_id' => $apple->id,
                'is_featured' => true,
                'product_type' => 'physical',
                'sku' => 'IP15PM-BASE',
                'barcode' => '88888888',
                'track_qty' => true,
                'qty' => 50,
                'status' => 1,
            ]
        );
        ProductImage::firstOrCreate(['product_id' => $p1->id, 'type' => '1'], ['image' => 'products/iphone15.jpg', 'sort_order' => 0]);
        ProductVariant::firstOrCreate(
            ['product_id' => $p1->id, 'variant_name' => '256GB'],
            [
                'discount_percentage' => 0,
                'original_price' => 30000000,
                'sku' => 'IP15PM-256',
                'qty' => 30,
            ]
        );
        ProductVariant::firstOrCreate(
            ['product_id' => $p1->id, 'variant_name' => '512GB'],
            [
                'discount_percentage' => 0,
                'original_price' => 35000000,
                'sku' => 'IP15PM-512',
                'qty' => 20,
            ]
        );

        // Product 2: Samsung Galaxy S24 Ultra
        $p2 = Product::firstOrCreate(
            ['slug' => 'samsung-galaxy-s24-ultra'],
            [
                'title' => 'Samsung Galaxy S24 Ultra',
                'description' => 'Điện thoại AI cao cấp nhất của Samsung.',
                'price' => 26000000,
                'compare_price' => 28000000,
                'original_price' => 26000000,
                'discount_percentage' => 0,
                'category_id' => $catPhones->id,
                'subcategory_id' => $subGalaxy->id,
                'brand_id' => $samsung->id,
                'is_featured' => true,
                'product_type' => 'physical',
                'sku' => 'S24U-BASE',
                'barcode' => '77777777',
                'track_qty' => true,
                'qty' => 30,
                'status' => 1,
            ]
        );
        ProductImage::firstOrCreate(['product_id' => $p2->id, 'type' => '1'], ['image' => 'products/s24u.jpg', 'sort_order' => 0]);
        ProductVariant::firstOrCreate(
            ['product_id' => $p2->id, 'variant_name' => '256GB'],
            [
                'discount_percentage' => 0,
                'original_price' => 26000000,
                'sku' => 'S24U-256',
                'qty' => 20,
            ]
        );

        // Product 3: MacBook Pro M3
        $p3 = Product::firstOrCreate(
            ['slug' => 'macbook-pro-m3'],
            [
                'title' => 'MacBook Pro M3',
                'description' => 'Laptop mạnh mẽ dành cho dân chuyên nghiệp.',
                'price' => 40000000,
                'compare_price' => 42000000,
                'original_price' => 40000000,
                'discount_percentage' => 0,
                'category_id' => $catLaptops->id,
                'subcategory_id' => $subMacbook->id,
                'brand_id' => $apple->id,
                'is_featured' => true,
                'product_type' => 'physical',
                'sku' => 'MBP-M3',
                'barcode' => '66666666',
                'track_qty' => true,
                'qty' => 15,
                'status' => 1,
            ]
        );
        ProductImage::firstOrCreate(['product_id' => $p3->id, 'type' => '1'], ['image' => 'products/mbpm3.jpg', 'sort_order' => 0]);

        // 11. Seed Coupons
        Coupon::firstOrCreate(
            ['code' => 'GIAM10'],
            [
                'description' => 'Giảm 10% cho mọi đơn hàng',
                'type' => 'percent',
                'value' => 10,
                'start_date' => Carbon::now()->subDay(),
                'end_date' => Carbon::now()->addYear(),
                'usage_limit' => 100,
                'used' => 0,
                'is_active' => true,
            ]
        );
        Coupon::firstOrCreate(
            ['code' => 'FREESHIP'],
            [
                'description' => 'Giảm 30.000 VNĐ phí vận chuyển',
                'type' => 'fixed',
                'value' => 30000,
                'start_date' => Carbon::now()->subDay(),
                'end_date' => Carbon::now()->addYear(),
                'usage_limit' => 200,
                'used' => 0,
                'is_active' => true,
            ]
        );

        // 12. Seed Pages
        Page::updateOrCreate(
            ['slug' => 'about-us'],
            [
                'title' => 'Giới thiệu',
                'content_json' => json_encode([
                    [
                        'type' => 'text',
                        'content' => 'Chào mừng bạn đến với Cửa hàng E-commerce của chúng tôi. Chúng tôi cung cấp các sản phẩm chính hãng với giá tốt nhất.'
                    ]
                ]),
                'is_active' => true,
            ]
        );
        Page::updateOrCreate(
            ['slug' => 'privacy-policy'],
            [
                'title' => 'Chính sách bảo mật',
                'content_json' => json_encode([
                    [
                        'type' => 'text',
                        'content' => 'Chính sách bảo mật của cửa hàng nhằm bảo vệ tuyệt đối thông tin khách hàng.'
                    ]
                ]),
                'is_active' => true,
            ]
        );

        // 13. Seed Contacts
        Contact::firstOrCreate(
            ['email' => 'customer_contact@gmail.com'],
            [
                'name' => 'Nguyễn Văn Liên Hệ',
                'content' => 'Tôi cần tư vấn thêm về chế độ bảo hành của MacBook Pro M3.',
                'is_read' => false,
            ]
        );

        // 14. Seed Warehouses & WarehouseDetails
        $warehouse = Warehouse::firstOrCreate(
            ['name' => 'Kho chính Hà Nội'],
            [
                'user_id' => $admin->id,
                'created_by' => $admin->id,
            ]
        );
        WarehouseDetail::firstOrCreate(
            ['warehouse_id' => $warehouse->id, 'product_id' => $p1->id, 'product_variant_id' => null],
            [
                'qty' => 100,
                'price' => 28000000,
                'created_by' => $admin->id,
            ]
        );

        // 15. Seed Favorite Products
        FavoriteProduct::firstOrCreate([
            'user_id' => $customer->id,
            'product_id' => $p1->id,
            'product_variant_id' => null
        ]);

        // 16. Seed Notifications
        Notification::firstOrCreate(
            ['title' => 'Đơn hàng mới'],
            [
                'type' => 'order',
                'message' => 'Người dùng customer@gmail.com đã đặt đơn hàng ORD-000001.',
                'reference_id' => 1,
                'is_read' => false,
            ]
        );

        // 17. Seed sample Orders for Dashboard graphs/charts
        $order1 = Order::firstOrCreate(
            ['order_code' => 'ORD-000001'],
            [
                'user_id' => $customer->id,
                'shipping_address_id' => null,
                'billing_full_name' => 'Customer User',
                'billing_email' => 'customer@gmail.com',
                'billing_address' => '123 Đường Bến Nghé, Quận 1',
                'billing_province_id' => '79',
                'billing_district_id' => '760',
                'billing_ward_id' => '26734',
                'billing_telephone' => '0123456789',
                'payment_method' => 'cash',
                'shipping_fee' => 20000,
                'total_amount' => 30020000,
                'status' => 'completed',
                'created_at' => Carbon::now()->subDays(5),
            ]
        );
        OrderDetail::firstOrCreate(
            ['order_id' => $order1->id, 'product_id' => $p1->id],
            [
                'product_variant_id' => null,
                'product_name' => 'iPhone 15 Pro Max',
                'price' => 30000000,
                'quantity' => 1,
                'total_price' => 30000000,
            ]
        );

        $order2 = Order::firstOrCreate(
            ['order_code' => 'ORD-000002'],
            [
                'user_id' => $customer->id,
                'shipping_address_id' => null,
                'billing_full_name' => 'Customer User',
                'billing_email' => 'customer@gmail.com',
                'billing_address' => '123 Đường Bến Nghé, Quận 1',
                'billing_province_id' => '79',
                'billing_district_id' => '760',
                'billing_ward_id' => '26734',
                'billing_telephone' => '0123456789',
                'payment_method' => 'paypal',
                'shipping_fee' => 20000,
                'total_amount' => 26020000,
                'status' => 'processing',
                'created_at' => Carbon::now()->subDays(2),
            ]
        );
        OrderDetail::firstOrCreate(
            ['order_id' => $order2->id, 'product_id' => $p2->id],
            [
                'product_variant_id' => null,
                'product_name' => 'Samsung Galaxy S24 Ultra',
                'price' => 26000000,
                'quantity' => 1,
                'total_price' => 26000000,
            ]
        );
    }
}
