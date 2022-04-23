<?php

use App\Models\InvoiceDetail;
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
        Schema::create('invoice_details', function (Blueprint $table) {
            InvoiceDetail::Migration($table);
            $table->unsignedBigInteger('product_detail_id')->nullable();
            $table->bigInteger('quantity');
            $table->bigInteger('price');
            $table->unsignedBigInteger('invoice_id');
            $table->foreign('product_detail_id')
                ->references('id')
                ->on('product_details')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->foreign('invoice_id')
                ->references('id')
                ->on('invoices')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_details');
    }
};
