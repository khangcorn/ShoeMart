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
        Schema::create('orders', function (Blueprint $table) {
            $table->id('order_id');
            $table->string('order_code', 50)->unique();
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->foreignId('address_id')->constrained('user_addresses', 'address_id')->onDelete('cascade');
            $table->decimal('total', 10, 2);
            $table->foreignId('status_id')->constrained('statuses', 'status_id')->onDelete('cascade');
            $table->decimal('shipping_fee', 10, 2)->default(0);
            $table->enum('payment_method', ['cod', 'bank_transfer', 'credit_card', 'paypal']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
