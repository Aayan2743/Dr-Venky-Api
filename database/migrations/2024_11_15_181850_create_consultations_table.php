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
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('aid');
            $table->string('serviceName',255);
            $table->string('subServiceName',255);
            $table->integer('amount');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('pet_id');
            $table->unsignedInteger('report_id');
            $table->boolean('payment_status')->default(0);
            $table->string('payment_mode');
            $table->unsignedInteger('updated_by');

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
        Schema::dropIfExists('consultations');
    }
};
