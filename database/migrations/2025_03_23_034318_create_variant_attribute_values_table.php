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
            $table->unsignedBigInteger('variant_id');
            $table->unsignedBigInteger('attribute_id');
            $table->timestamps();

            // Thiết lập khóa ngoại
            $table->foreign('variant_id')->references('variant_id')->on('product_variants')->onDelete('cascade');
            $table->foreign('attribute_id')->references('attribute_id')->on('variant_attributes')->onDelete('cascade');

            // Thiết lập khóa chính hợp nhất
            $table->primary(['variant_id', 'attribute_id']);
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

