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
        Schema::create('labreports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('aid');
            $table->unsignedBigInteger('pid');
            $table->unsignedBigInteger('uid');
            $table->unsignedBigInteger('sid');
            $table->unsignedBigInteger('ssid');
            $table->unsignedBigInteger('statusid');
            $table->unsignedBigInteger('updated_id');
            $table->string('filepath');
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
        Schema::dropIfExists('labreports');
    }
};
