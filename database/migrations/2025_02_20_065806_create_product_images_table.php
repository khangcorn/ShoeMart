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
        Schema::create('product_images', function (Blueprint $table) {
            $table->id('image_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('variant_id')->nullable();
            $table->string('image_url', 255);
            $table->enum('type', ['main', 'gallery'])->default('gallery');
            $table->timestamps();

            // Foreign key cho product_id
            $table->foreign('product_id')->references('product_id')->on('products')->onDelete('cascade');

            // Foreign key cho variant_id (nếu có)
            $table->foreign('variant_id')->references('variant_id')->on('product_variants')->onDelete('cascade');
        });
    }

    
    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('product_images');
    }
};
