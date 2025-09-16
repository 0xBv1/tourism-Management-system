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
        Schema::table('supplier_transports', function (Blueprint $table) {
            $table->foreignId('origin_city_id')->nullable()->after('supplier_id')->constrained('cities')->onDelete('set null');
            $table->foreignId('destination_city_id')->nullable()->after('origin_city_id')->constrained('cities')->onDelete('set null');
            $table->string('vehicle_registration')->nullable()->after('seating_capacity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_transports', function (Blueprint $table) {
            $table->dropForeign(['origin_city_id']);
            $table->dropForeign(['destination_city_id']);
            $table->dropColumn(['origin_city_id', 'destination_city_id', 'vehicle_registration']);
        });
    }
};
