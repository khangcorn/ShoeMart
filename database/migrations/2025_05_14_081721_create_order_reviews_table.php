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
            $table->text('admin_response')->nullable(); // phản hồi của admin
            $table->json('media_paths')->nullable(); // Lưu đường dẫn hình ảnh hoặc video
            $table->unsignedBigInteger('order_detail_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('variant_id');
            $table->timestamps();

            // Khóa duy nhất: mỗi người dùng chỉ đánh giá mỗi chi tiết đơn hàng 1 lần
            $table->unique(['order_id', 'user_id', 'order_detail_id']);

            // Khóa ngoại (tham chiếu cột id của bảng tương ứng)
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
