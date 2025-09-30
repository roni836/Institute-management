<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasColumn('admissions', 'discount_type')) {
            Schema::table('admissions', function (Blueprint $table) {
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('admissions', 'discount_type')) {
            Schema::table('admissions', function (Blueprint $table) {
            });
        }
    }
};
