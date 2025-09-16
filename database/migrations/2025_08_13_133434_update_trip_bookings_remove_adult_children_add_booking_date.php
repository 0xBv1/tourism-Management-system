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
            // Check if columns exist before dropping them
            if (Schema::hasColumn('trip_bookings', 'adults_count')) {
                $table->dropColumn('adults_count');
            }
            
            if (Schema::hasColumn('trip_bookings', 'children_count')) {
                $table->dropColumn('children_count');
            }
        });
        
        // Add booking date column if it doesn't exist
        if (!Schema::hasColumn('trip_bookings', 'booking_date')) {
            Schema::table('trip_bookings', function (Blueprint $table) {
                $table->date('booking_date')->after('passenger_phone')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trip_bookings', function (Blueprint $table) {
            // Remove the booking date column if it exists
            if (Schema::hasColumn('trip_bookings', 'booking_date')) {
                $table->dropColumn('booking_date');
            }
            
            // Add back the removed columns if they don't exist
            if (!Schema::hasColumn('trip_bookings', 'adults_count')) {
                $table->integer('adults_count')->default(0)->after('passenger_phone');
            }
            
            if (!Schema::hasColumn('trip_bookings', 'children_count')) {
                $table->integer('children_count')->default(0)->after('adults_count');
            }
        });
    }
};
