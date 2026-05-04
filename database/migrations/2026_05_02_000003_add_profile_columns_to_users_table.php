<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'country_code')) {
                $table->string('country_code', 10)->nullable()->after('dial_code');
            }
            if (!Schema::hasColumn('users', 'address')) {
                $table->string('address')->nullable()->after('country_code');
            }
            if (!Schema::hasColumn('users', 'city')) {
                $table->string('city')->nullable()->after('address');
            }
            if (!Schema::hasColumn('users', 'state')) {
                $table->string('state')->nullable()->after('city');
            }
            if (!Schema::hasColumn('users', 'zip')) {
                $table->string('zip')->nullable()->after('state');
            }
            if (!Schema::hasColumn('users', 'country_name')) {
                $table->string('country_name')->nullable()->after('zip');
            }
            if (!Schema::hasColumn('users', 'profile_complete')) {
                $table->tinyInteger('profile_complete')->default(0)->after('country_name');
            }
        });
    }

    public function down()
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'profile_complete')) {
                $table->dropColumn('profile_complete');
            }
            if (Schema::hasColumn('users', 'country_name')) {
                $table->dropColumn('country_name');
            }
            if (Schema::hasColumn('users', 'zip')) {
                $table->dropColumn('zip');
            }
            if (Schema::hasColumn('users', 'state')) {
                $table->dropColumn('state');
            }
            if (Schema::hasColumn('users', 'city')) {
                $table->dropColumn('city');
            }
            if (Schema::hasColumn('users', 'address')) {
                $table->dropColumn('address');
            }
            if (Schema::hasColumn('users', 'country_code')) {
                $table->dropColumn('country_code');
            }
        });
    }
};
