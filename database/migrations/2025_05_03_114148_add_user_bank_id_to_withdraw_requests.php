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
        Schema::table('withdraw_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('user_bank_id')->nullable(); // Nếu không bắt buộc, có thể nullable()
            $table->foreign('user_bank_id')->references('id')->on('user_banks')->onDelete('cascade'); // Thay 'id' bằng khóa chính của bảng user_banks nếu cần
        });
    }
    
    public function down()
    {
        Schema::table('withdraw_requests', function (Blueprint $table) {
            $table->dropForeign(['user_bank_id']);
            $table->dropColumn('user_bank_id');
        });
    }
    
};
