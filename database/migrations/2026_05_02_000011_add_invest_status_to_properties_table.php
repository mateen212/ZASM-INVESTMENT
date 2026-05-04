<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('properties')) {
            return;
        }

        Schema::table('properties', function (Blueprint $table) {
            if (!Schema::hasColumn('properties', 'invest_status')) {
                $table->unsignedTinyInteger('invest_status')->default(0)->after('complete_step')->index();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('properties')) {
            return;
        }

        Schema::table('properties', function (Blueprint $table) {
            if (Schema::hasColumn('properties', 'invest_status')) {
                $table->dropColumn('invest_status');
            }
        });
    }
};
