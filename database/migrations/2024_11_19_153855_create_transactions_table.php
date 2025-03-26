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
        Schema::create('clinictransactions', function (Blueprint $table) {
            $table->id();
            $table->integer('payment_mode');
            $table->string('payment_for');
            $table->integer('amount');
            $table->text('transactionid');
            $table->unsignedBigInteger('paid_by_id');
            $table->unsignedBigInteger('aid');
            $table->unsignedBigInteger('uid');
            $table->unsignedBigInteger('pid');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
