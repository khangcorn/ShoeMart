<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id('order_id');
            $table->string('order_code', 50)->unique();
            $table->foreignId('user_id')->constrained('users', 'user_id');
            $table->foreignId('address_id')->constrained('user_addresses', 'address_id');
            $table->decimal('total', 10, 2);
            $table->foreignId('status_id')->constrained('order_statuses', 'status_id');
            $table->decimal('shipping_fee', 10, 2)->default(0);
            $table->enum('payment_method', ['cod', 'bank_transfer', 'credit_card', 'paypal']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
