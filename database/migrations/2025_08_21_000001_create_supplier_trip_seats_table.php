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
        Schema::create('supplier_trip_seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_trip_id')->constrained('supplier_trips')->onDelete('cascade');
            $table->unsignedInteger('seat_number');
            $table->boolean('is_available')->default(true);
            $table->foreignId('booking_id')->nullable()->constrained('supplier_trip_bookings')->onDelete('set null');
            $table->timestamps();

            $table->unique(['supplier_trip_id', 'seat_number']);
            $table->index(['supplier_trip_id', 'is_available']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_trip_seats');
    }
};


