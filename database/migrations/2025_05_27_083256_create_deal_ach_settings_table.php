<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('deal_ach_settings', function (Blueprint $table) {
            $table->string('stripe_customer_id')->nullable()->after('verify_confirmation');
            $table->string('stripe_account_id')->nullable()->after('stripe_customer_id');
        });
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn(['stripe_customer_id', 'stripe_account_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deal_ach_settings', function (Blueprint $table) {
            $table->dropColumn(['stripe_customer_id', 'stripe_account_id']);
        });

        Schema::table('admins', function (Blueprint $table) {
            $table->string('stripe_customer_id')->nullable();
            $table->string('stripe_account_id')->nullable();
        });
    }
};
