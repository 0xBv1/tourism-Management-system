<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Modify restaurants table to make removed fields nullable and remove unused fields.
     */
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            // Make cuisine_type nullable since it was removed from the form
            $table->string('cuisine_type')->nullable()->change();
            
            // Remove unused fields that were removed from the form
            $table->dropColumn([
                'price_per_meal',
                'features',
                'amenities',
                'opening_hours',
                'dress_code',
                'notes',
                'images'
            ]);
        });
    }

    /**
     * Add back the removed fields.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            // Restore cuisine_type as required field
            $table->string('cuisine_type')->nullable(false)->change();
            
            // Add back removed fields
            $table->decimal('price_per_meal', 10, 2)->nullable();
            $table->text('features')->nullable();
            $table->text('amenities')->nullable();
            $table->text('opening_hours')->nullable();
            $table->string('dress_code')->nullable();
            $table->text('notes')->nullable();
            $table->text('images')->nullable();
        });
    }
};
