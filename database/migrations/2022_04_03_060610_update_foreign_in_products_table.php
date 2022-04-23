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
        Schema::table('products', function (Blueprint $table) {
            $table->foreign('default_detail')
                ->references('id')
                ->on('product_details')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->foreign('provider_id')
                ->references('id')
                ->on('providers')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->foreign('default_image')
                ->references('id')
                ->on('blobs')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('default_detail');
            $table->dropConstrainedForeignId('default_image');
            $table->dropConstrainedForeignId('category_id');
            $table->dropConstrainedForeignId('provider_id');
        });
    }
};
