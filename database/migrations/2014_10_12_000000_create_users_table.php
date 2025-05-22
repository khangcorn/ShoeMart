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
        Schema::create('users', function (Blueprint $table) {
<<<<<<< HEAD:database/migrations/2014_10_12_000000_create_users_table.php
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
=======
            $table->id('user_id'); // Custom primary key
            $table->string('username', 50)->unique();
            $table->string('password_hash', 255);
            $table->string('email', 100)->unique();
            $table->string('phone', 15)->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_blocked')->default(false)->after('created_by');
            $table->boolean('must_change_password')->default(true)->after('password');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('user_id')->on('users')->onDelete('set null');

>>>>>>> 1bbab0a (Full code DATN):database/migrations/2025_02_23_084100_create_users_table.php
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
