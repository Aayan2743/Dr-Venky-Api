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
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('aid');
            $table->unsignedBigInteger('pid');
            $table->unsignedBigInteger('uid');
            $table->unsignedBigInteger('dr_id');
            $table->longText('inhouse');
            $table->longText('grooming');
            $table->longText('lab');
            $table->longText('services');
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
        Schema::dropIfExists('prescriptions');
    }
};
