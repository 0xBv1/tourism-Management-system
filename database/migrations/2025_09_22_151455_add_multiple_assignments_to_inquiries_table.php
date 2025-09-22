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
        Schema::table('inquiries', function (Blueprint $table) {
            // Add new assignment fields for each role
            $table->unsignedBigInteger('assigned_reservation_id')->nullable()->after('assigned_to');
            $table->unsignedBigInteger('assigned_operator_id')->nullable()->after('assigned_reservation_id');
            $table->unsignedBigInteger('assigned_admin_id')->nullable()->after('assigned_operator_id');
            
            // Add foreign key constraints
            $table->foreign('assigned_reservation_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('assigned_operator_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('assigned_admin_id')->references('id')->on('users')->onDelete('set null');
            
            // Add indexes for better performance
            $table->index('assigned_reservation_id');
            $table->index('assigned_operator_id');
            $table->index('assigned_admin_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            // Drop foreign key constraints first
            $table->dropForeign(['assigned_reservation_id']);
            $table->dropForeign(['assigned_operator_id']);
            $table->dropForeign(['assigned_admin_id']);
            
            // Drop indexes
            $table->dropIndex(['assigned_reservation_id']);
            $table->dropIndex(['assigned_operator_id']);
            $table->dropIndex(['assigned_admin_id']);
            
            // Drop columns
            $table->dropColumn([
                'assigned_reservation_id',
                'assigned_operator_id', 
                'assigned_admin_id'
            ]);
        });
    }
};