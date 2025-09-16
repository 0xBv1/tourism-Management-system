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
		if (!Schema::hasTable('supplier_hotels')) {
			return;
		}

		Schema::table('supplier_hotels', function (Blueprint $table) {
			// If this table is currently a pivot, drop the unique index and hotel_id
			if (Schema::hasColumn('supplier_hotels', 'hotel_id')) {
				// Drop unique index on [supplier_id, hotel_id] if it exists
				try {
					$table->dropUnique('supplier_hotels_supplier_id_hotel_id_unique');
				} catch (\Throwable $e) {
					// ignore if index does not exist
				}
				$table->dropColumn('hotel_id');
			}

			// Add missing entity columns if not present
			if (!Schema::hasColumn('supplier_hotels', 'name')) {
				$table->string('name')->nullable()->after('supplier_id');
			}
			if (!Schema::hasColumn('supplier_hotels', 'description')) {
				$table->text('description')->nullable()->after('name');
			}
			if (!Schema::hasColumn('supplier_hotels', 'address')) {
				$table->string('address')->nullable()->after('description');
			}
			if (!Schema::hasColumn('supplier_hotels', 'city')) {
				$table->string('city')->nullable()->after('address');
			}
			if (!Schema::hasColumn('supplier_hotels', 'country')) {
				$table->string('country')->default('Egypt')->after('city');
			}
			if (!Schema::hasColumn('supplier_hotels', 'phone')) {
				$table->string('phone')->nullable()->after('country');
			}
			if (!Schema::hasColumn('supplier_hotels', 'email')) {
				$table->string('email')->nullable()->after('phone');
			}
			if (!Schema::hasColumn('supplier_hotels', 'website')) {
				$table->string('website')->nullable()->after('email');
			}
			if (!Schema::hasColumn('supplier_hotels', 'stars')) {
				$table->integer('stars')->default(3)->after('website');
			}
			if (!Schema::hasColumn('supplier_hotels', 'price_per_night')) {
				$table->decimal('price_per_night', 10, 2)->default(0)->after('stars');
			}
			if (!Schema::hasColumn('supplier_hotels', 'currency')) {
				$table->string('currency')->default('EGP')->after('price_per_night');
			}
			if (!Schema::hasColumn('supplier_hotels', 'amenities')) {
				$table->text('amenities')->nullable()->after('currency');
			}
			if (!Schema::hasColumn('supplier_hotels', 'images')) {
				$table->text('images')->nullable()->after('amenities');
			}
			if (!Schema::hasColumn('supplier_hotels', 'featured_image')) {
				$table->string('featured_image')->nullable()->after('images');
			}
			if (!Schema::hasColumn('supplier_hotels', 'enabled')) {
				$table->boolean('enabled')->default(true)->after('featured_image');
			}
			if (!Schema::hasColumn('supplier_hotels', 'approved')) {
				$table->boolean('approved')->default(false)->after('enabled');
			}
			if (!Schema::hasColumn('supplier_hotels', 'rejection_reason')) {
				$table->text('rejection_reason')->nullable()->after('approved');
			}

			// Helpful indexes
			try {
				$table->index(['supplier_id', 'enabled']);
				$table->index(['city', 'enabled']);
				$table->index('approved');
			} catch (\Throwable $e) {
				// ignore if indexes already exist
			}
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		// No-op: this migration is corrective and non-destructive to existing data beyond optional column drops above
	}
}; 