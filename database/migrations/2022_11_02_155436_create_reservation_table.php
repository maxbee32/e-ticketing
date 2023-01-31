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
    // public function up()
    // {
    //     Schema::create('reservation', function (Blueprint $table) {
    //         $table->id();
    //         $table->foreign('user_id')->references('id')->on('users');
    //         $table->string('gender');
    //         $table->string('firstname');
    //         $table->string('lastname');
    //         $table->string('customer_type');
    //         $table->string('country');
    //         $table->string('contact');
    //         $table->date('book_date');
    //         $table->time("book_time");
    //         $table->string('digital address');
    //         $table->string('city');
    //         $table->timestamps();
    //     });
    // }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reservation');
    }
};
