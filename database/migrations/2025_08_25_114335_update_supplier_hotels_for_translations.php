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
        // Create supplier_hotel_translations table
        Schema::create('supplier_hotel_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_hotel_id')->constrained('supplier_hotels')->cascadeOnDelete();
            $table->string('locale')->index();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->string('city')->nullable();
            $table->unique(['supplier_hotel_id', 'locale']);
        });

        // Update supplier_hotels table to add missing fields
        Schema::table('supplier_hotels', function (Blueprint $table) {
            // Add missing fields similar to Hotel model
            if (!Schema::hasColumn('supplier_hotels', 'banner')) {
                $table->string('banner')->nullable();
            }
            if (!Schema::hasColumn('supplier_hotels', 'gallery')) {
                $table->text('gallery')->nullable();
            }
            if (!Schema::hasColumn('supplier_hotels', 'map_iframe')) {
                $table->text('map_iframe')->nullable();
            }
            if (!Schema::hasColumn('supplier_hotels', 'slug')) {
                $table->string('slug', 191)->unique()->nullable();
            }
            if (!Schema::hasColumn('supplier_hotels', 'phone_contact')) {
                $table->string('phone_contact')->nullable();
            }
            if (!Schema::hasColumn('supplier_hotels', 'whatsapp_contact')) {
                $table->string('whatsapp_contact')->nullable();
            }
            
            // Remove old fields that are now in translations
            if (Schema::hasColumn('supplier_hotels', 'name')) {
                $table->dropColumn('name');
            }
            if (Schema::hasColumn('supplier_hotels', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('supplier_hotels', 'city')) {
                $table->dropColumn('city');
            }
            
            // Remove fields that are no longer needed
            if (Schema::hasColumn('supplier_hotels', 'country')) {
                $table->dropColumn('country');
            }
            if (Schema::hasColumn('supplier_hotels', 'phone')) {
                $table->dropColumn('phone');
            }
            if (Schema::hasColumn('supplier_hotels', 'email')) {
                $table->dropColumn('email');
            }
            if (Schema::hasColumn('supplier_hotels', 'website')) {
                $table->dropColumn('website');
            }
            if (Schema::hasColumn('supplier_hotels', 'amenities')) {
                $table->dropColumn('amenities');
            }
            if (Schema::hasColumn('supplier_hotels', 'images')) {
                $table->dropColumn('images');
            }
        });

        // Create supplier_hotel_amenities pivot table
        Schema::create('supplier_hotel_amenities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_hotel_id')->constrained('supplier_hotels')->cascadeOnDelete();
            $table->foreignId('amenity_id')->constrained('amenities')->cascadeOnDelete();
            $table->timestamps();
            
            $table->unique(['supplier_hotel_id', 'amenity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_hotel_amenities');
        Schema::dropIfExists('supplier_hotel_translations');
        
        Schema::table('supplier_hotels', function (Blueprint $table) {
            // Restore old fields
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->default('Egypt');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->text('amenities')->nullable();
            $table->text('images')->nullable();
            
            // Remove new fields
            $table->dropColumn(['banner', 'gallery', 'map_iframe', 'slug', 'phone_contact', 'whatsapp_contact']);
        });
    }
};
