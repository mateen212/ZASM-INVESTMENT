<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('pages')) {
            return;
        }

        if (!Schema::hasColumn('pages', 'is_default')) {
            Schema::table('pages', function (Blueprint $table) {
                $table->tinyInteger('is_default')->default(0)->after('slug')->comment('1=default,0=not default');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('pages') && Schema::hasColumn('pages', 'is_default')) {
            Schema::table('pages', function (Blueprint $table) {
                $table->dropColumn('is_default');
            });
        }
    }
};
