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
        // Add the whatsapp_no column only if it doesn't exist already
        if (!Schema::hasColumn('students', 'whatsapp_no')) {
            Schema::table('students', function (Blueprint $table) {
                $table->string('whatsapp_no')->nullable()->after('phone');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('students', 'whatsapp_no')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropColumn('whatsapp_no');
            });
        }
    }
};
