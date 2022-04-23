<?php

use App\Models\ProductDetail;
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
        Schema::create('product_details', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id');
            $table->string('option_name');
            $table->string('option_value');
            $table->unsignedBigInteger('out_price');
            $table->unsignedBigInteger('in_price')->nullable();
            $table->bigInteger('remaining_quantity');
            $table->unsignedBigInteger('total_quantity');
            $table->boolean('visible')->default(true);
            $table->unsignedBigInteger('default_image')->nullable();
            $table->string('unit', 50);
            ProductDetail::Migration($table);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_details');
    }
};
