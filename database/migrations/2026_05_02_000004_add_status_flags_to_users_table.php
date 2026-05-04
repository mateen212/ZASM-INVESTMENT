<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'status')) {
                $table->tinyInteger('status')->default(1)->after('password');
            }

            if (!Schema::hasColumn('users', 'ev')) {
                $table->tinyInteger('ev')->default(0)->after('status');
            }

            if (!Schema::hasColumn('users', 'sv')) {
                $table->tinyInteger('sv')->default(0)->after('ev');
            }

            if (!Schema::hasColumn('users', 'kv')) {
                $table->tinyInteger('kv')->default(0)->after('sv');
            }

            if (!Schema::hasColumn('users', 'balance')) {
                $table->decimal('balance', 28, 8)->default(0)->after('kv');
            }

            if (!Schema::hasColumn('users', 'ver_code')) {
                $table->string('ver_code')->nullable()->after('balance');
            }

            if (!Schema::hasColumn('users', 'ver_code_send_at')) {
                $table->timestamp('ver_code_send_at')->nullable()->after('ver_code');
            }

            if (!Schema::hasColumn('users', 'kyc_data')) {
                $table->json('kyc_data')->nullable()->after('ver_code_send_at');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'kyc_data')) {
                $table->dropColumn('kyc_data');
            }
            if (Schema::hasColumn('users', 'ver_code_send_at')) {
                $table->dropColumn('ver_code_send_at');
            }
            if (Schema::hasColumn('users', 'ver_code')) {
                $table->dropColumn('ver_code');
            }
            if (Schema::hasColumn('users', 'balance')) {
                $table->dropColumn('balance');
            }
            if (Schema::hasColumn('users', 'kv')) {
                $table->dropColumn('kv');
            }
            if (Schema::hasColumn('users', 'sv')) {
                $table->dropColumn('sv');
            }
            if (Schema::hasColumn('users', 'ev')) {
                $table->dropColumn('ev');
            }
            if (Schema::hasColumn('users', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
