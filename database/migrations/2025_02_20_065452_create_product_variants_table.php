<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id('variant_id');  // Đảm bảo cột khóa chính là variant_id
            $table->unsignedBigInteger('product_id');  // Khóa ngoại tham chiếu đến products.product_id
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('price_sale', 10, 2)->nullable();
            $table->integer('stock');
            $table->timestamps();
        
            // Khóa ngoại tham chiếu đến product_id trong bảng products
            $table->foreign('product_id')->references('product_id')->on('products')->onDelete('cascade');
        });
        
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
