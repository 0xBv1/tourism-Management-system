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
        Schema::create('supplier_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique()->nullable();
            $table->string('featured_image')->nullable();
            $table->string('banner')->nullable();
            $table->unsignedBigInteger('supplier_hotel_id');
            $table->text('gallery')->nullable();
            $table->boolean('enabled')->default(true);
            $table->integer('bed_count')->default(1);
            $table->string('room_type')->nullable();
            $table->integer('max_capacity')->nullable();
            $table->string('bed_types')->nullable();
            $table->decimal('night_price', 10, 2)->default(0.00);
            $table->boolean('extra_bed_available')->default(false);
            $table->decimal('extra_bed_price', 10, 2)->nullable();
            $table->integer('max_extra_beds')->default(1);
            $table->text('extra_bed_description')->nullable();
            $table->boolean('approved')->default(false);
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->foreign('supplier_hotel_id')->references('id')->on('supplier_hotels')->onDelete('cascade');
            $table->index(['supplier_hotel_id', 'enabled']);
            $table->index(['approved', 'enabled']);
        });

        // Create pivot table for room amenities
        Schema::create('supplier_room_amenities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_room_id');
            $table->unsignedBigInteger('amenity_id');
            $table->timestamps();

            $table->foreign('supplier_room_id')->references('id')->on('supplier_rooms')->onDelete('cascade');
            $table->foreign('amenity_id')->references('id')->on('amenities')->onDelete('cascade');
            $table->unique(['supplier_room_id', 'amenity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_room_amenities');
        Schema::dropIfExists('supplier_rooms');
    }
};
