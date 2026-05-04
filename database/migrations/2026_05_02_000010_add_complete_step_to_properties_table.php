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
            if (!Schema::hasColumn('properties', 'complete_step')) {
                $table->unsignedTinyInteger('complete_step')->default(0)->after('status')->index();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('properties')) {
            return;
        }

        Schema::table('properties', function (Blueprint $table) {
            if (Schema::hasColumn('properties', 'complete_step')) {
                $table->dropColumn('complete_step');
            }
        });
    }
};
