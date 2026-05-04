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
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('ach_bank_status')->nullable()->after('stripe_account_id');
        });
        Schema::table('deal_ach_settings', function (Blueprint $table) {
            $table->boolean('is_verified')->default(false)->after('stripe_account_id');
        });
    }

    /**
     * Reverse the migrations.
     */

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('ach_bank_status');
        });
        Schema::table('deal_ach_settings', function (Blueprint $table) {
            $table->dropColumn('is_verified');
        });
    }
};
