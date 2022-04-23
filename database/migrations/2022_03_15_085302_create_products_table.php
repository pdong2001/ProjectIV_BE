<?php

use App\Models\Product;
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
        Schema::create('products', function (Blueprint $table) {
            $table->unsignedBigInteger('provider_id')->nullable();
            $table->string('name')->fulltext();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('default_image')->nullable();
            $table->string('code')->nullable();
            $table->unsignedInteger('option_count')->default(0);
            $table->boolean('visible')->default(true);
            $table->bigInteger('quantity')->default(0);
            $table->text('description')->fulltext()->nullable();
            $table->unsignedBigInteger('default_detail')->nullable();
            Product::Migration($table);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
