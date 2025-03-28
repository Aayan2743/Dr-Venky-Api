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
        Schema::create('vaccination_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Link to the user table
            $table->unsignedBigInteger('msg_id'); // Link to the user table
            $table->date('start_date');
            $table->enum('frequency', ['daily', 'weekly', 'monthly', 'quarterly', 'half_yearly', 'yearly']);
            // $table->text('message');
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
        Schema::dropIfExists('vaccination_schedules');
    }
};
