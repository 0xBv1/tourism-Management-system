<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inquiry_resources', function (Blueprint $table) {
            $table->dateTime('start_at')->nullable()->after('added_by');
            $table->dateTime('end_at')->nullable()->after('start_at');
            $table->string('price_type', 10)->nullable()->after('end_at'); // day|hour
            $table->decimal('original_price', 10, 2)->nullable()->after('price_type');
            $table->decimal('new_price', 10, 2)->nullable()->after('original_price');
            $table->decimal('increase_percent', 5, 2)->nullable()->after('new_price');
            $table->decimal('effective_price', 10, 2)->nullable()->after('increase_percent');
            $table->string('currency', 8)->nullable()->after('effective_price');
            $table->text('price_note')->nullable()->after('currency');
        });
    }

    public function down(): void
    {
        Schema::table('inquiry_resources', function (Blueprint $table) {
            $table->dropColumn([
                'start_at',
                'end_at',
                'price_type',
                'original_price',
                'new_price',
                'increase_percent',
                'effective_price',
                'currency',
                'price_note',
            ]);
        });
    }
};


