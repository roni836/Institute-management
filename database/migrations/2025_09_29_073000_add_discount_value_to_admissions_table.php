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
        if (! Schema::hasColumn('admissions', 'discount_value')) {
            Schema::table('admissions', function (Blueprint $table) {
                // decimal to store fixed amount or percentage value
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('admissions', 'discount_value')) {
            Schema::table('admissions', function (Blueprint $table) {
                $table->dropColumn('discount_value');
            });
        }
    }
};
