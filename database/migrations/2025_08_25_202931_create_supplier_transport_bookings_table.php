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
        Schema::create('supplier_transport_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_transport_id')->constrained('supplier_transports')->onDelete('cascade');
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->date('travel_date');
            $table->time('pickup_time');
            $table->string('pickup_location');
            $table->string('dropoff_location');
            $table->integer('passengers')->default(1);
            $table->decimal('price_per_passenger', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->decimal('commission_amount', 10, 2);
            $table->decimal('supplier_amount', 10, 2);
            $table->string('currency')->default('EGP');
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->text('special_requests')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['supplier_transport_id', 'status']);
            $table->index(['client_id', 'status']);
            $table->index(['supplier_id', 'status']);
            $table->index('travel_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_transport_bookings');
    }
};
