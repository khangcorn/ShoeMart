<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('variant_attributes', function (Blueprint $table) {
            $table->id('attribute_id');
            $table->unsignedBigInteger('variant_id');

            $table->foreign('variant_id')->references('variant_id')->on('product_variants')->onDelete('cascade');
            $table->string('attribute_name', 50);
            $table->string('attribute_value', 100);
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
