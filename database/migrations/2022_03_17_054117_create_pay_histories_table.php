<?php

use App\Models\PayHistory;
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
        Schema::create('pay_histories', function (Blueprint $table) {
            PayHistory::Migration($table);
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('money')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pay_histories');
    }
};
