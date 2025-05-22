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
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id('address_id');
            $table->foreignId('user_id')
                ->constrained('users', 'user_id')  // tham chiếu tới users.user_id
                ->onDelete('cascade');

            $table->string('address_name')->nullable(); // Ví dụ: "Nhà riêng", "Công ty"
            $table->string('recipient_name'); // Tên người nhận hàng
            $table->string('recipient_phone', 15); // Số điện thoại người nhận
            $table->string('city', 100);
            $table->string('district', 100);
            $table->string('ward', 100);
            $table->string('street_address', 255);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_addresses');
    }
};
