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
        if (! Schema::hasColumn('admissions', 'gst_amount') || ! Schema::hasColumn('admissions', 'gst_rate')) {
            Schema::table('admissions', function (Blueprint $table) {
                if (! Schema::hasColumn('admissions', 'gst_amount')) {
                }
                if (! Schema::hasColumn('admissions', 'gst_rate')) {
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('admissions', 'gst_amount') || Schema::hasColumn('admissions', 'gst_rate')) {
            Schema::table('admissions', function (Blueprint $table) {
                if (Schema::hasColumn('admissions', 'gst_amount')) {
                    $table->dropColumn('gst_amount');
                }
                if (Schema::hasColumn('admissions', 'gst_rate')) {
                    $table->dropColumn('gst_rate');
                }
            });
        }
    }
};
