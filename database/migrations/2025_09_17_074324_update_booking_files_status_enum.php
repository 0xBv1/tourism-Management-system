<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // First, update existing records to use new status values
        DB::table('booking_files')->where('status', 'generated')->update(['status' => 'pending']);
        DB::table('booking_files')->where('status', 'sent')->update(['status' => 'confirmed']);
        DB::table('booking_files')->where('status', 'downloaded')->update(['status' => 'completed']);
        
        // Then modify the column
        Schema::table('booking_files', function (Blueprint $table) {
            $table->string('status')->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert status values
        DB::table('booking_files')->where('status', 'pending')->update(['status' => 'generated']);
        DB::table('booking_files')->where('status', 'confirmed')->update(['status' => 'sent']);
        DB::table('booking_files')->where('status', 'completed')->update(['status' => 'downloaded']);
        
        // Revert column type
        Schema::table('booking_files', function (Blueprint $table) {
            $table->enum('status', ['generated', 'sent', 'downloaded'])->default('generated')->change();
        });
    }
};
