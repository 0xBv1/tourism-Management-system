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
        Schema::table('supplier_hotels', function (Blueprint $table) {
            if (Schema::hasColumn('supplier_hotels', 'price_per_night')) {
                $table->dropColumn('price_per_night');
            }
            if (Schema::hasColumn('supplier_hotels', 'currency')) {
                $table->dropColumn('currency');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_hotels', function (Blueprint $table) {
            if (!Schema::hasColumn('supplier_hotels', 'price_per_night')) {
                $table->decimal('price_per_night', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('supplier_hotels', 'currency')) {
                $table->string('currency', 3)->nullable();
            }
        });
    }
};
