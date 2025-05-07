<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserBanksTable extends Migration
{
    public function up()
    {
        Schema::create('user_banks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade'); 
            $table->string('bank_name');
            $table->string('account_number');
            $table->timestamps();
        });
    }
    

    public function down()
    {
        Schema::dropIfExists('user_banks');
    }
}
