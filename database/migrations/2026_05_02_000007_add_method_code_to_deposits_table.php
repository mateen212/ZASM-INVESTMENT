<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('deposits')) {
            return;
        }

        Schema::table('deposits', function (Blueprint $table) {
            if (!Schema::hasColumn('deposits', 'method_code')) {
                $table->unsignedInteger('method_code')->default(0)->after('method')->index();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('deposits')) {
            return;
        }

        Schema::table('deposits', function (Blueprint $table) {
            if (Schema::hasColumn('deposits', 'method_code')) {
                $table->dropColumn('method_code');
            }
        });
    }
};
