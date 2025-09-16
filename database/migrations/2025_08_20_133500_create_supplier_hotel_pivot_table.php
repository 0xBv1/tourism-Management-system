<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('supplier_hotel_pivot', function (Blueprint $table) {
			$table->id();
			$table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
			$table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
			$table->boolean('is_active')->default(true);
			$table->timestamps();
			$table->unique(['supplier_id', 'hotel_id']);
			$table->index('supplier_id');
			$table->index('hotel_id');
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('supplier_hotel_pivot');
	}
}; 