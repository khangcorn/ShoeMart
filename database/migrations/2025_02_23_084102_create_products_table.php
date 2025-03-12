<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_products_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('products', function (Blueprint $table) {
            $table->id('product_id');
            $table->string('name', 255)->notNull();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->notNull();
            $table->decimal('price_sale', 10, 2)->nullable(); // Giá khuyến mãi, có thể null
            $table->integer('stock')->default(0); // Tổng số lượng tồn kho
            $table->unsignedBigInteger('category_id')->nullable();
            $table->timestamps();

            // Khóa ngoại
            $table->foreign('category_id')->references('category_id')->on('categories')->onDelete('cascade'); // Tham chiếu đúng khóa chính
        });
    }

    public function down(): void {
        Schema::dropIfExists('products');
    }
};
