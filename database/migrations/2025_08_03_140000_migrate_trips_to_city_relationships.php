<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\City;

return new class extends Migration
{
    public function up(): void
    {
        // Add foreign keys
        Schema::table('tours', function (Blueprint $table) {
            $table->foreignId('departure_city_id')->nullable()->constrained('cities')->onDelete('cascade');
            $table->foreignId('arrival_city_id')->nullable()->constrained('cities')->onDelete('cascade');
        });

        // Drop the old columns
        Schema::table('tours', function (Blueprint $table) {
            if (Schema::hasColumn('tours', 'departure_city')) {
                $table->dropColumn('departure_city');
            }

            if (Schema::hasColumn('tours', 'arrival_city')) {
                $table->dropColumn('arrival_city');
            }
        });
    }

    public function down(): void
    {
    }
};
