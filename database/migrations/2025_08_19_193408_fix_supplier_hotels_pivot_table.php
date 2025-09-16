<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop the existing table with hotel data fields
        Schema::dropIfExists('supplier_hotels');
        
        // Recreate as a proper pivot table
        Schema::create('supplier_hotels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->foreignId('hotel_id')->constrained('hotels')->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Unique constraint to prevent duplicate relationships
            $table->unique(['supplier_id', 'hotel_id']);
            
            // Indexes for better performance
            $table->index('supplier_id');
            $table->index('hotel_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop the pivot table
        Schema::dropIfExists('supplier_hotels');
        
        // Recreate the original table structure
        Schema::create('supplier_hotels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('address');
            $table->string('city');
            $table->string('country')->default('Egypt');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->integer('stars')->default(3);
            $table->decimal('price_per_night', 10, 2);
            $table->string('currency')->default('EGP');
            $table->text('amenities')->nullable();
            $table->text('images')->nullable();
            $table->string('featured_image')->nullable();
            $table->boolean('enabled')->default(true);
            $table->boolean('approved')->default(false);
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['supplier_id', 'enabled']);
            $table->index(['city', 'enabled']);
            $table->index('approved');
        });
    }
};
