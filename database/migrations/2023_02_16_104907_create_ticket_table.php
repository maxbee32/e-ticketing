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
        Schema::create('ticket', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('image')->nullable();
            $table->unsignedBigInteger('firstname');
            $table->unsignedBigInteger('lastname');
            $table->unsignedBigInteger('gender');
            $table->unsignedBigInteger('country');
            $table->date('reservation_date');
            $table->unsignedBigInteger('no_of_ticket');
            $table->double('total');
            $table->string('status');
            $table->timestamps();




            // $table->foreign('user_id')
            // ->references('id')->on('users')
            // ->onDelete('cascade')->onUpdate('cascade');

            // $table->foreign('image')
            // ->references('user_id')->on('bookings')
            // ->onDelete('cascade')->onUpdate('cascade');

            // $table->foreign('firstname')
            // ->references('user_id')->on('bookings')
            // ->onDelete('cascade')->onUpdate('cascade');

            // $table->foreign('lastname')
            // ->references('user_id')->on('bookings')
            // ->onDelete('cascade')->onUpdate('cascade');

            // $table->foreign('gender')
            // ->references('user_id')->on('bookings')
            // ->onDelete('cascade')->onUpdate('cascade');

            // $table->foreign('country')
            // ->references('user_id')->on('bookings')
            // ->onDelete('cascade')->onUpdate('cascade');

            // $table->foreign('no_of_ticket')
            // ->references('user_id')->on('bookings')
            // ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ticket');
    }
};
