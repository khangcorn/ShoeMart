<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id('coupon_id'); // Tạo cột coupon_id là khóa chính
            $table->string('code', 50)->unique(); // Mã giảm giá, duy nhất
            $table->enum('discount_type', ['fixed', 'percentage']); // Loại giảm giá
            $table->decimal('discount_value', 10, 2); // Giá trị giảm giá
            $table->decimal('max_discount_value', 10, 2)->nullable(); // Giới hạn giảm giá tối đa
            $table->date('expiration_date')->nullable(); // Ngày hết hạn
            $table->integer('usage_limit')->nullable(); // Giới hạn sử dụng
            $table->integer('usage_count')->default(0); // Số lần đã sử dụng
            $table->enum('status', ['active', 'expired', 'disabled'])->default('active'); // Trạng thái mã giảm giá
            $table->timestamps(); // created_at và updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coupons');
    }
};

