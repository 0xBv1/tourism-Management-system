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
        Schema::create('supplier_trip_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_trip_id')->constrained('supplier_trips')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('travel_date');
            $table->integer('passengers_count')->default(1);
            $table->text('selected_seats')->nullable();
            $table->integer('seats_booked')->default(0);
            $table->decimal('total_price', 10, 2);
            $table->string('currency', 3)->default('EGP');
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->string('booking_reference')->unique();
            $table->text('special_requests')->nullable();
            $table->string('guest_name');
            $table->string('guest_email');
            $table->string('guest_phone');
            $table->timestamps();
            
            $table->index(['supplier_trip_id', 'travel_date']);
            $table->index(['user_id', 'status']);
            $table->index('booking_reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_trip_bookings');
    }
};
