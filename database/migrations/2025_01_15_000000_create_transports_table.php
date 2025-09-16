<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transports', function (Blueprint $table) {
            $table->id();
            $table->string('transport_type'); // bus, train, ferry, plane, etc.
            $table->string('vehicle_type')->nullable(); // sedan, bus, train, boat, etc.
            $table->integer('seating_capacity')->default(1);
            $table->string('origin_location');
            $table->string('destination_location');
            $table->text('intermediate_stops')->nullable(); // JSON array of stops
            $table->integer('estimated_travel_time')->nullable(); // in minutes
            $table->decimal('distance', 10, 2)->nullable(); // in kilometers
            $table->string('route_type')->default('direct'); // direct, with_stops, circular
            $table->decimal('price', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('vehicle_registration')->nullable();
            $table->text('amenities')->nullable(); // JSON array
            $table->text('images')->nullable(); // JSON array
            $table->string('featured_image')->nullable();
            $table->boolean('enabled')->default(false);
            $table->string('slug', 191)->unique();
            $table->string('phone_contact')->nullable();
            $table->string('whatsapp_contact')->nullable();
            $table->string('email_contact')->nullable();
            $table->text('contact_notes')->nullable();
            $table->time('departure_time')->nullable();
            $table->time('arrival_time')->nullable();
            $table->string('departure_location')->nullable();
            $table->string('arrival_location')->nullable();
            $table->text('schedule_notes')->nullable();
            $table->decimal('price_per_hour', 10, 2)->nullable();
            $table->decimal('price_per_day', 10, 2)->nullable();
            $table->decimal('price_per_km', 10, 2)->nullable();
            $table->decimal('discount_percentage', 5, 2)->nullable();
            $table->text('discount_conditions')->nullable();
            $table->text('pricing_notes')->nullable();
            $table->text('vehicle_images')->nullable(); // JSON array
            $table->string('route_map')->nullable();
            $table->timestamps();
        });

        Schema::create('transport_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transport_id')->constrained('transports')->cascadeOnDelete();
            $table->string('locale')->index();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->unique(['transport_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transport_translations');
        Schema::dropIfExists('transports');
    }
};
