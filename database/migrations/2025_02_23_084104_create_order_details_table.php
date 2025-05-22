<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDetailsTable extends Migration
{
    public function up()
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->id('order_detail_id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('variant_id')->nullable();

            $table->foreign('order_id')->references('order_id')->on('orders')->onDelete('cascade');
            $table->foreign('product_id')->references('product_id')->on('products')->onDelete('restrict');
            $table->foreign('variant_id')->references('variant_id')->on('product_variants')->onDelete('restrict');

            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);

            $table->decimal('subtotal', 10, 2);
            $table->decimal('total_price', 10, 2);

            $table->string('status')->default('active'); // trạng thái sản phẩm trong đơn
            $table->string('cancel_reason')->nullable();
            // ➕ Các cột snapshot
            $table->string('product_name')->nullable();
            $table->string('variant_name')->nullable();
            $table->string('attributes')->nullable(); // ví dụ: color: red, size: 42
            $table->decimal('original_price', 10, 2)->nullable();
            $table->decimal('final_price', 10, 2)->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_details');
    }
}
