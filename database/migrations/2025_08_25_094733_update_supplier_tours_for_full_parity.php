<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('supplier_tours', function (Blueprint $table) {
            if (!Schema::hasColumn('supplier_tours', 'slug')) {
                $table->string('slug', 191)->nullable()->after('id');
            }
            if (!Schema::hasColumn('supplier_tours', 'code')) {
                $table->string('code')->nullable()->after('slug');
            }
            if (!Schema::hasColumn('supplier_tours', 'display_order')) {
                $table->integer('display_order')->nullable()->default(0)->after('code');
            }
            if (!Schema::hasColumn('supplier_tours', 'featured')) {
                $table->boolean('featured')->default(false)->after('enabled');
            }
            if (!Schema::hasColumn('supplier_tours', 'gallery')) {
                $table->longText('gallery')->nullable()->after('featured_image');
            }
            if (!Schema::hasColumn('supplier_tours', 'duration_in_days')) {
                $table->integer('duration_in_days')->nullable()->after('adult_price');
            }
            if (!Schema::hasColumn('supplier_tours', 'pricing_groups')) {
                $table->longText('pricing_groups')->nullable()->after('child_price');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('supplier_tours', function (Blueprint $table) {
            if (Schema::hasColumn('supplier_tours', 'slug')) {
                $table->dropColumn('slug');
            }
            if (Schema::hasColumn('supplier_tours', 'display_order')) {
                $table->dropColumn('display_order');
            }
            if (Schema::hasColumn('supplier_tours', 'featured')) {
                $table->dropColumn('featured');
            }
            if (Schema::hasColumn('supplier_tours', 'gallery')) {
                $table->dropColumn('gallery');
            }
            if (Schema::hasColumn('supplier_tours', 'duration_in_days')) {
                $table->dropColumn('duration_in_days');
            }
            if (Schema::hasColumn('supplier_tours', 'pricing_groups')) {
                $table->dropColumn('pricing_groups');
            }
        });
    }
};
