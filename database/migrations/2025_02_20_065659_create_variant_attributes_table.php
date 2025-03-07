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
        Schema::create('variant_attributes', function (Blueprint $table) {
            $table->id('attribute_id');  // Cột khóa chính
            $table->unsignedBigInteger('variant_id');  // Cột khóa ngoại tham chiếu đến variant_id trong bảng product_variants
            $table->string('attribute_name');
            $table->string('attribute_value');
            $table->timestamps();
    
            // Thiết lập khóa ngoại cho variant_id
            $table->foreign('variant_id')->references('variant_id')->on('product_variants')->onDelete('cascade');
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variant_attributes');
    }
};
