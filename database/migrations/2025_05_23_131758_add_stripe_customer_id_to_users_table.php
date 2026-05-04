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
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'stripe_customer_id')) {
                // add without 'after' if referenced column missing
                if (Schema::hasColumn('users', 'signature')) {
                    $table->string('stripe_customer_id')->nullable()->after('signature');
                } else {
                    $table->string('stripe_customer_id')->nullable();
                }
            }

            if (!Schema::hasColumn('users', 'stripe_account_id')) {
                if (Schema::hasColumn('users', 'stripe_customer_id')) {
                    $table->string('stripe_account_id')->nullable()->after('stripe_customer_id');
                } else {
                    $table->string('stripe_account_id')->nullable();
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $drops = [];
            if (Schema::hasColumn('users', 'stripe_customer_id')) {
                $drops[] = 'stripe_customer_id';
            }
            if (Schema::hasColumn('users', 'stripe_account_id')) {
                $drops[] = 'stripe_account_id';
            }
            if (!empty($drops)) {
                $table->dropColumn($drops);
            }
        });
    }
};
