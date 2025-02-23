<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShippingFeesTable extends Migration
{
    public function up()
    {
        Schema::create('shipping_fees', function (Blueprint $table) {
            $table->id('shipping_id');
            $table->string('region', 100);
            $table->decimal('fee', 10, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('shipping_fees');
    }
}

