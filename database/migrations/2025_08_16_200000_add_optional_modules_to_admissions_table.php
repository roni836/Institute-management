<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admissions', function (Blueprint $table) {
            $table->string('module1')->nullable()->after('stream');
            $table->string('module2')->nullable()->after('module1');
            $table->string('module3')->nullable()->after('module2');
            $table->string('module4')->nullable()->after('module3');
            $table->string('module5')->nullable()->after('module4');
            $table->boolean('id_card_required')->default(false)->after('module5');
        });
    }

    public function down(): void
    {
        Schema::table('admissions', function (Blueprint $table) {
            $table->dropColumn([
                'module1',
                'module2',
                'module3',
                'module4',
                'module5',
                'id_card_required',
            ]);
        });
    }
};
