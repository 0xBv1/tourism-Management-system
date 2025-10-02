<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inquiry_resources', function (Blueprint $table) {
            $table->date('check_in')->nullable()->after('end_at');
            $table->date('check_out')->nullable()->after('check_in');
            $table->unsignedInteger('number_of_rooms')->nullable()->after('check_out');
            $table->unsignedInteger('number_of_adults')->nullable()->after('number_of_rooms');
            $table->unsignedInteger('number_of_children')->nullable()->after('number_of_adults');
            $table->decimal('rate_per_adult', 10, 2)->nullable()->after('number_of_children');
            $table->decimal('rate_per_child', 10, 2)->nullable()->after('rate_per_adult');
        });
    }

    public function down(): void
    {
        Schema::table('inquiry_resources', function (Blueprint $table) {
            $table->dropColumn([
                'check_in',
                'check_out',
                'number_of_rooms',
                'number_of_adults',
                'number_of_children',
                'rate_per_adult',
                'rate_per_child',
            ]);
        });
    }
};



