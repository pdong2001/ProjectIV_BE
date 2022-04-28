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
        Schema::create('product_detail_option_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_detail_id');
            $table->foreign('product_detail_id')->references('id')->on('product_details')->cascadeOnDelete()->cascadeOnUpdate();;
            $table->unsignedBigInteger('option_id');
            $table->foreign('option_id')->references('id')->on('product_options')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('value', 50);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_detail_option_values');
    }
};
