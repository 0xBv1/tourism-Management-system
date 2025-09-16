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
        Schema::table('trip_bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('trip_bookings', 'booking_date')) {
                $table->date('booking_date')->after('passenger_phone')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trip_bookings', function (Blueprint $table) {
            if (Schema::hasColumn('trip_bookings', 'booking_date')) {
                $table->dropColumn('booking_date');
            }
        });
    }
};
