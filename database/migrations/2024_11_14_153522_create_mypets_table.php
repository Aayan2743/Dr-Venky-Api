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
        // Schema::create('mypets', function (Blueprint $table) {
        //     // $table->id();
        //     // $table->string('petname');
        //     // $table->string('petgender');
        //     // $table->string('petbread');
        //     // $table->unsignedBigInteger('category_id');
        //     // // $table->foreign('category_id')->references('id')->on('patient_categories')->onDelete('casecade')->onUpdate('casecade');


        //     // $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mypets');
    }
};
