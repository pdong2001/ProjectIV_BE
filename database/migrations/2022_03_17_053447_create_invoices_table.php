<?php

use App\Models\Invoice;
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
        Schema::create('invoices', function (Blueprint $table) {
            Invoice::Migration($table);
            $table->string('customer_name')->nullable()->fulltext();
            $table->string('phone_number')->nullable()->fulltext();
            $table->string('address')->nullable()->fulltext();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->bigInteger('total')->default(0);
            $table->bigInteger('paid')->default(0);
            $table->tinyInteger('status')->range(1,9)->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
};
