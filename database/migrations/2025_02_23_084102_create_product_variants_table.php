<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_product_variants_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateProductVariantsTable extends Migration
{
    public function up()
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id('variant_id');
            $table->unsignedBigInteger('product_id');

            $table->foreign('product_id')->references('product_id')->on('products')->onDelete('cascade');
            $table->decimal('price', 10, 2)->notNull();
            $table->decimal('price_sale', 10, 2)->nullable(); // Giá khuyến mãi, có thể null
            $table->integer('stock')->default(0);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_variants');
    }
}
