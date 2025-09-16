<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		if (!Schema::hasTable('supplier_tours')) {
			return;
		}

		Schema::table('supplier_tours', function (Blueprint $table) {
			// If currently a pivot, drop unique index and tour_id
			if (Schema::hasColumn('supplier_tours', 'tour_id')) {
				try {
					$table->dropUnique('supplier_tours_supplier_id_tour_id_unique');
				} catch (\Throwable $e) {
					// ignore
				}
				$table->dropColumn('tour_id');
			}

			// Add back entity columns if missing
			if (!Schema::hasColumn('supplier_tours', 'title')) {
				$table->string('title')->nullable()->after('supplier_id');
			}
			if (!Schema::hasColumn('supplier_tours', 'description')) {
				$table->text('description')->nullable()->after('title');
			}
			if (!Schema::hasColumn('supplier_tours', 'highlights')) {
				$table->text('highlights')->nullable()->after('description');
			}
			if (!Schema::hasColumn('supplier_tours', 'included')) {
				$table->text('included')->nullable()->after('highlights');
			}
			if (!Schema::hasColumn('supplier_tours', 'excluded')) {
				$table->text('excluded')->nullable()->after('included');
			}
			if (!Schema::hasColumn('supplier_tours', 'duration')) {
				$table->string('duration')->nullable()->after('excluded');
			}
			if (!Schema::hasColumn('supplier_tours', 'type')) {
				$table->string('type')->nullable()->after('duration');
			}
			if (!Schema::hasColumn('supplier_tours', 'pickup_location')) {
				$table->string('pickup_location')->nullable()->after('type');
			}
			if (!Schema::hasColumn('supplier_tours', 'dropoff_location')) {
				$table->string('dropoff_location')->nullable()->after('pickup_location');
			}
			if (!Schema::hasColumn('supplier_tours', 'adult_price')) {
				$table->decimal('adult_price', 10, 2)->default(0)->after('dropoff_location');
			}
			if (!Schema::hasColumn('supplier_tours', 'child_price')) {
				$table->decimal('child_price', 10, 2)->default(0)->after('adult_price');
			}
			if (!Schema::hasColumn('supplier_tours', 'infant_price')) {
				$table->decimal('infant_price', 10, 2)->default(0)->after('child_price');
			}
			if (!Schema::hasColumn('supplier_tours', 'currency')) {
				$table->string('currency')->default('EGP')->after('infant_price');
			}
			if (!Schema::hasColumn('supplier_tours', 'max_group_size')) {
				$table->integer('max_group_size')->default(20)->after('currency');
			}
			if (!Schema::hasColumn('supplier_tours', 'itinerary')) {
				$table->text('itinerary')->nullable()->after('max_group_size');
			}
			if (!Schema::hasColumn('supplier_tours', 'images')) {
				$table->text('images')->nullable()->after('itinerary');
			}
			if (!Schema::hasColumn('supplier_tours', 'featured_image')) {
				$table->string('featured_image')->nullable()->after('images');
			}
			if (!Schema::hasColumn('supplier_tours', 'enabled')) {
				$table->boolean('enabled')->default(true)->after('featured_image');
			}
			if (!Schema::hasColumn('supplier_tours', 'approved')) {
				$table->boolean('approved')->default(false)->after('enabled');
			}
			if (!Schema::hasColumn('supplier_tours', 'rejection_reason')) {
				$table->text('rejection_reason')->nullable()->after('approved');
			}
		});
	}

	public function down(): void
	{
		// no-op corrective
	}
}; 