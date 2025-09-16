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
            // Schedule fields
            $table->time('departure_time')->nullable()->after('estimated_travel_time');
            $table->time('arrival_time')->nullable()->after('departure_time');
            $table->string('departure_location')->nullable()->after('arrival_time');
            $table->string('arrival_location')->nullable()->after('departure_location');
            $table->text('schedule_notes')->nullable()->after('arrival_location');
            
            // Pricing fields
            $table->decimal('price_per_hour', 10, 2)->nullable()->after('price');
            $table->decimal('price_per_day', 10, 2)->nullable()->after('price_per_hour');
            $table->decimal('price_per_km', 10, 2)->nullable()->after('price_per_day');
            $table->decimal('discount_percentage', 5, 2)->nullable()->after('price_per_km');
            $table->string('discount_conditions')->nullable()->after('discount_percentage');
            $table->text('pricing_notes')->nullable()->after('discount_conditions');
            
            // Contact fields
            $table->string('contact_person')->nullable()->after('pricing_notes');
            $table->string('phone_contact')->nullable()->after('contact_person');
            $table->string('whatsapp_contact')->nullable()->after('phone_contact');
            $table->string('email_contact')->nullable()->after('whatsapp_contact');
            $table->text('contact_notes')->nullable()->after('email_contact');
            
            // Media fields
            $table->text('vehicle_images')->nullable()->after('featured_image');
            $table->string('route_map')->nullable()->after('vehicle_images');
            
            // SEO fields
            $table->string('meta_title')->nullable()->after('route_map');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->string('meta_keywords')->nullable()->after('meta_description');
            $table->string('slug')->nullable()->after('meta_keywords');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_transports', function (Blueprint $table) {
            $table->dropColumn([
                'departure_time',
                'arrival_time',
                'departure_location',
                'arrival_location',
                'schedule_notes',
                'price_per_hour',
                'price_per_day',
                'price_per_km',
                'discount_percentage',
                'discount_conditions',
                'pricing_notes',
                'contact_person',
                'phone_contact',
                'whatsapp_contact',
                'email_contact',
                'contact_notes',
                'vehicle_images',
                'route_map',
                'meta_title',
                'meta_description',
                'meta_keywords',
                'slug'
            ]);
        });
    }
};
