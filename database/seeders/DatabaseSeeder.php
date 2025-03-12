<?php

namespace Database\Seeders;

use App\Models\UserAddress;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // 1️⃣ Seed bảng quyền và vai trò trước
            RoleSeeder::class,
            PermissionSeeder::class,
            
            // 2️⃣ Seed bảng trung gian giữa roles & permissions
            RolePermissionSeeder::class,

            // 3️⃣ Seed user
            UserSeeder::class,

            // 4️⃣ Gán role cho user
            UserRoleSeeder::class,

            // 5️⃣ Seed danh mục sản phẩm
            CategorySeeder::class,


            // 6️⃣ Seed sản phẩm & biến thể
            ProductSeeder::class,
            ProductVariantSeeder::class,
            ProductImageSeeder::class,
            VariantAttributeSeeder::class, // ✅ Thêm seed dữ liệu thuộc tính biến thể
            VariantAttributeValuesSeeder::class,

            // 7️⃣ Seed trạng thái đơn hàng & phí vận chuyển
            OrderStatusSeeder::class,
            ShippingFeeSeeder::class,

            // 8️⃣ Seed mã giảm giá
            CouponSeeder::class,

            // 9️⃣ Seed đơn hàng & chi tiết đơn hàng
            OrderSeeder::class,
            OrderDetailSeeder::class,
            OrderCouponSeeder::class,

            // 🔟 Seed giỏ hàng (nếu cần)
            CartSeeder::class,
            CartDetailSeeder::class,

            // 1️⃣1️⃣ Seed bình luận & đánh giá
            CommentSeeder::class,
            CommentVoteSeeder::class,
            CommentVariantSeeder::class,

            // 1️⃣2️⃣ Seed slider
            SliderSeeder::class, // ✅ Thêm seed dữ liệu slider
        ]);
    }
}
