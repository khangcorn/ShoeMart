<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CartDetailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Chèn dữ liệu mẫu vào bảng cart_details
        DB::table('cart_details')->insert([
            [
                'cart_id' => 1, // ID giỏ hàng
                'product_id' => 1, // ID sản phẩm
                'variant_id' => 1, // ID variant của sản phẩm, có thể null
                'quantity' => 2, // Số lượng sản phẩm trong giỏ hàng
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'cart_id' => 2,
                'product_id' => 2,
                'variant_id' => null, // Sản phẩm không có variant
                'quantity' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
