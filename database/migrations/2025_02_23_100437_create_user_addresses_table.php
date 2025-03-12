<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id('address_id');
            $table->unsignedBigInteger('user_id');

            $table->string('recipient_name', 100);
            $table->string('recipient_phone', 15);
            $table->string('recipient_email', 100)->nullable();
            $table->string('province', 100);
            $table->string('district', 100);
            $table->string('ward', 100);
            $table->string('street_address', 255);
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            // Khóa ngoại
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('user_addresses');
    }
};
