<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVariantAttributeValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('variant_attribute_values', function (Blueprint $table) {
            $table->id('value_id');  // Khóa chính của bảng
            $table->unsignedBigInteger('variant_id');  // Khóa ngoại tham chiếu đến product_variants.variant_id
            $table->unsignedBigInteger('attribute_id');  // Khóa ngoại tham chiếu đến variant_attributes.attribute_id
            $table->string('attribute_value');  // Giá trị thuộc tính
            $table->integer('stock')->default(0);  // Số lượng tồn kho cho từng size
            $table->timestamps();
        
            // Thiết lập khóa ngoại cho variant_id
            $table->foreign('variant_id')->references('variant_id')->on('product_variants')->onDelete('cascade');
            // Thiết lập khóa ngoại cho attribute_id
            $table->foreign('attribute_id')->references('attribute_id')->on('variant_attributes')->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('variant_attribute_values');
    }
}
