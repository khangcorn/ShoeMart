<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('variant_attributes', function (Blueprint $table) {
            $table->id('attribute_id');
            $table->unsignedBigInteger('variant_id'); // Thêm cột variant_id trước khi đặt khóa ngoại
            $table->string('attribute_name', 50);
            $table->string('attribute_value', 100);

            // Định nghĩa khóa ngoại
            $table->foreign('variant_id')->references('variant_id')->on('product_variants')->onDelete('cascade');
        });
    }

    public function down() {
        Schema::dropIfExists('variant_attributes');
    }
};
