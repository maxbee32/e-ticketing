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
        Schema::create('bookings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('table_id');
            $table->string('image')->nullable();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('gender');
            $table->string('country');
            $table->string('region');
            $table->string('city');
            $table->string('phone_number');
            $table->dateTime('reservation_date');
            $table->integer('no_of_ticket');
            $table->integer('guest_number');
            $table->timestamps();

            $table->foreign('user_id')
            ->references('id')->on('users')
            ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bookings');
    }
};
