<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('sliders', function (Blueprint $table) {
            $table->id('slider_id');
            $table->string('image_url', 255);
            $table->text('caption')->nullable();
            $table->string('link', 255)->nullable();
            $table->integer('position')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sliders');
    }
};
