<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('user_id');
            $table->tinyInteger('rating'); // 1–5 sao
            $table->text('comment');
            $table->json('media_paths')->nullable(); // Lưu đường dẫn hình ảnh hoặc video
            $table->unsignedBigInteger('order_detail_id'); // Cột order_detail_id
            $table->unsignedBigInteger('product_id'); // Thêm cột product_id
            $table->unsignedBigInteger('variant_id')->nullable()->change();
            $table->timestamps();

            // Đảm bảo rằng mỗi người dùng chỉ có thể đánh giá mỗi sản phẩm trong đơn hàng một lần
            $table->unique(['order_id', 'user_id', 'order_detail_id']); // Khóa duy nhất

            // Khóa ngoại
            $table->foreign('order_id')->references('order_id')->on('orders')->onDelete('cascade');
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('order_detail_id')->references('order_detail_id')->on('order_details')->onDelete('cascade');
            $table->foreign('product_id')->references('product_id')->on('products')->onDelete('cascade');
            $table->foreign('variant_id')->references('variant_id')->on('product_variants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_reviews');
    }
};
