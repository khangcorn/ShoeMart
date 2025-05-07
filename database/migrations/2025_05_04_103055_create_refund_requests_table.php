<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('refund_requests', function (Blueprint $table) {
            $table->id('refund_id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('user_id'); // Người yêu cầu hoàn tiền
            $table->decimal('amount', 15, 2);
            $table->text('reason')->nullable();
            $table->json('attachments')->nullable(); // Lưu các đường dẫn file
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->unsignedBigInteger('approved_by')->nullable(); // admin duyệt
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
    
            $table->foreign('order_id')->references('order_id')->on('orders')->onDelete('cascade');
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('user_id')->on('users')->onDelete('set null');
            
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refund_requests');
    }
};
