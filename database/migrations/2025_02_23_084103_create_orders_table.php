<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id('order_id');
            $table->unsignedBigInteger('user_id');
            $table->decimal('total_price', 10, 2)->default(0);
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade'); // Tham chiếu đúng khóa chính
            $table->decimal('total', 10, 2)->default(0);
            $table->unsignedBigInteger('status_id');
            $table->foreign('status_id')->references('status_id')->on('order_statuses')->onDelete('cascade');
            $table->decimal('shipping_fee', 10, 2)->default(0);
            $table->unsignedBigInteger('shipping_id');
            $table->foreign('shipping_id')->references('shipping_id')->on('shipping_fees')->onDelete('cascade');
            $table->unsignedBigInteger('coupon_id')->nullable(); // Đảm bảo có nullable()
            $table->foreign('coupon_id')->references('coupon_id')->on('coupons')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
}