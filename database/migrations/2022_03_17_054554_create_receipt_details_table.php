<?php

use App\Models\ReceiptDetail;
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
        Schema::create('receipt_details', function (Blueprint $table) {
            ReceiptDetail::Migration($table);
            $table->unsignedBigInteger('quantity');
            $table->unsignedBigInteger('price');
            $table->unsignedBigInteger('product_detail_id')->nullable();
            $table->unsignedBigInteger('receipt_id');
            $table->foreign('receipt_id')
            ->references('id')
            ->on('receipts')
            ->cascadeOnUpdate()
            ->cascadeOnDelete();
            $table->foreign('product_detail_id')
            ->references('id')
            ->on('product_details')
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
        Schema::dropIfExists('receipt_details');
    }
};
