<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('order_coupons', function (Blueprint $table) {
            $table->id('order_coupon_id');
            $table->foreignId('order_id')->constrained('orders', 'order_id');
            $table->foreignId('coupon_id')->constrained('coupons', 'coupon_id');
            $table->decimal('applied_amount', 10, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_coupons');
    }
};
