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
            $table->unsignedBigInteger('status_id');
            $table->unsignedBigInteger('shipping_id');
           
            $table->string('order_code', 50)->unique(); // Mã đơn hàng duy nhất
            $table->decimal('total', 10, 2)->default(0);
            $table->decimal('shipping_fee', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0); // Lưu số tiền giảm giá từ coupon
            $table->decimal('total_price', 10, 2)->default(0); // Tổng tiền sau giảm giá
            $table->enum('payment_method', ['cod', 'bank_transfer', 'credit_card', 'paypal']);
            $table->timestamps();

            // Khóa ngoại
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('status_id')->references('status_id')->on('order_statuses')->onDelete('cascade');
            $table->foreign('shipping_id')->references('shipping_id')->on('shipping_fees')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
