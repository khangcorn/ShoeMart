<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('product_id'); // Nếu khóa chính là số nguyên
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('price_sale', 10, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->unsignedBigInteger('category_id');  // Cột này tham chiếu tới 'id' trong bảng 'categories'
            $table->timestamps();
    
            // Khóa ngoại tham chiếu đến cột 'id' trong bảng categories
            $table->foreign('category_id')->references('category_id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_images', function (Blueprint $table) {
            $table->dropForeign(['product_id']); // Xóa khóa ngoại
        });
    
        Schema::dropIfExists('product_images'); // Xóa bảng product_images
        Schema::dropIfExists('products'); // Xóa bảng products
    }
    
};
