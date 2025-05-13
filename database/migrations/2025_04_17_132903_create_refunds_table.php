<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('refunds', function (Blueprint $table) {
            $table->id('refund_id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->decimal('amount', 15, 2);
            $table->string('reason')->nullable();
            $table->text('note')->nullable(); // admin ghi chú nếu duyệt/từ chối
            $table->json('attachments')->nullable(); // ✅ file ảnh/video từ người dùng
            $table->unsignedBigInteger('approved_by')->nullable(); // ✅ ai duyệt
            $table->timestamp('approved_at')->nullable(); // ✅ thời gian duyệt
            $table->timestamps();

            // Foreign keys
            $table->foreign('order_id')->references('order_id')->on('orders')->onDelete('cascade');
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('user_id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};
