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
        Schema::create('settlement_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('settlement_id');
            $table->unsignedBigInteger('resource_booking_id')->nullable();
            $table->unsignedBigInteger('booking_file_id')->nullable();
            $table->date('booking_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->integer('duration_hours')->default(0);
            $table->integer('duration_days')->default(0);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('client_name')->nullable();
            $table->string('tour_name')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('settlement_id')->references('id')->on('settlements')->onDelete('cascade');
            $table->foreign('resource_booking_id')->references('id')->on('resource_bookings')->onDelete('set null');
            $table->foreign('booking_file_id')->references('id')->on('booking_files')->onDelete('set null');
            
            // Indexes
            $table->index('settlement_id');
            $table->index('booking_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settlement_items');
    }
};
