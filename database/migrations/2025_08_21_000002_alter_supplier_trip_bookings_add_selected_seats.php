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
        if (!Schema::hasTable('supplier_trip_bookings')) {
            return;
        }

        Schema::table('supplier_trip_bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('supplier_trip_bookings', 'selected_seats')) {
                // Use text for broader DB compatibility (cast handles JSON encoding/decoding)
                $table->text('selected_seats')->nullable()->after('passengers_count');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('supplier_trip_bookings')) {
            return;
        }

        Schema::table('supplier_trip_bookings', function (Blueprint $table) {
            if (Schema::hasColumn('supplier_trip_bookings', 'selected_seats')) {
                $table->dropColumn('selected_seats');
            }
        });
    }
};


