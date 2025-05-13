<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('shipping_fees', function (Blueprint $table) {
            $table->id('shipping_id');
            $table->string('province', 100);
            $table->string('district', 100)->nullable();
            $table->string('ward', 100)->nullable();
            $table->decimal('fee', 10, 2);
            $table->timestamps(); // Tạo cả created_at và updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_fees');
    }
};
