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
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn(['meal_name', 'meal_description', 'meal_price']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->string('meal_name')->nullable()->after('reservation_required');
            $table->text('meal_description')->nullable()->after('meal_name');
            $table->decimal('meal_price', 10, 2)->nullable()->after('meal_description');
        });
    }
};
