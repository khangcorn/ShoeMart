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
        Schema::table('orders', function (Blueprint $table) {
            // Kiểm tra nếu chưa có cột address_id mới thêm
            if (! Schema::hasColumn('orders', 'address_id')) {
                $table->unsignedBigInteger('address_id')
                    ->nullable()
                    ->after('coupon_id');
                $table->foreign('address_id')
                    ->references('address_id')
                    ->on('user_addresses')
                    ->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'address_id')) {
                // Hạ foreign key trước, sau đó drop cột
                $table->dropForeign(['address_id']);
                $table->dropColumn('address_id');
            }
        });
    }
};
