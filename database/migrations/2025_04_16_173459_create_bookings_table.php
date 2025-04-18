<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('attendee_id')->constrained()->onDelete('cascade');
            $table->string('booking_reference')->unique();
            $table->enum('status', ['confirmed', 'cancelled', 'waiting'])->default('confirmed');
            $table->timestamps();

            $table->unique(['event_id', 'attendee_id']);
            $table->index('booking_reference');
        });
    }

    public function down()
    {
        Schema::dropIfExists('bookings');
    }
};