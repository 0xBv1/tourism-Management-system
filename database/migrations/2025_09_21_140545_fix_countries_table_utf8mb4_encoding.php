<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, let's check if the countries table exists and has data
        if (Schema::hasTable('countries')) {
            // Convert the table to use utf8 character set
            DB::statement('ALTER TABLE countries CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci');
            
            // Update the flag column to use country codes instead of emoji
            Schema::table('countries', function (Blueprint $table) {
                $table->string('flag', 10)->charset('utf8')->collation('utf8_unicode_ci')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('countries')) {
            // Revert back to utf8
            DB::statement('ALTER TABLE countries CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci');
            
            Schema::table('countries', function (Blueprint $table) {
                $table->string('flag', 10)->charset('utf8')->collation('utf8_unicode_ci')->nullable()->change();
            });
        }
    }
};