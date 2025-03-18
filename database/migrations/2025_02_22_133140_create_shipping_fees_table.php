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
        Schema::create('shipping_fees', function (Blueprint $table) {
            $table->id('shipping_id'); // Tạo cột shipping_id là khóa chính
            $table->string('province', 100); // Tỉnh thành
            $table->string('district', 100)->nullable(); // Quận huyện (tùy chọn)
            $table->string('ward', 100)->nullable(); // Phường xã (tùy chọn)
            $table->decimal('fee', 10, 2); // Phí vận chuyển
            $table->timestamps(); // created_at và updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shipping_fees');
    }
};
