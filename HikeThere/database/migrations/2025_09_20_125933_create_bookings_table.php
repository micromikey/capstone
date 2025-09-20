<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // who booked
            $table->string('fullname');            // name of the person booking
            $table->string('email')->nullable();   // email contact
            $table->string('phone')->nullable();   // phone contact
            $table->date('hike_date');             // hike date
            $table->integer('participants');       // number of participants
            $table->string('status')->default('pending'); // booking status
            $table->timestamps();

            // Optional: add foreign key to users table if you have users
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};