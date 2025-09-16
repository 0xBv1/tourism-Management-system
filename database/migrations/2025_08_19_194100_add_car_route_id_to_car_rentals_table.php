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
        Schema::table('car_rentals', function (Blueprint $table) {
            $table->foreignId('car_route_id')->nullable()->after('booking_id')->constrained('car_routes')->nullOnDelete();
            $table->index('car_route_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('car_rentals', function (Blueprint $table) {
            $table->dropForeign(['car_route_id']);
            $table->dropColumn('car_route_id');
        });
    }
}; 