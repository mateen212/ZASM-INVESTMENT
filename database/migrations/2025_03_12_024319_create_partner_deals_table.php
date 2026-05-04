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
        Schema::create('partner_deals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id'); // Partner ID (from admins table)
            $table->unsignedBigInteger('deal_id');  // Deal ID
            $table->timestamps();

            // Ensure a deal can only be associated with one partner
            $table->unique('deal_id');

            // Add indexes for performance
            $table->index('admin_id');
        });

        // Add foreign keys only if referenced tables/columns exist to avoid migration failures
        if (Schema::hasTable('partner_deals')) {
            // admin_id -> admins.id
            if (Schema::hasTable('admins') && Schema::hasColumn('partner_deals', 'admin_id')) {
                try {
                    Schema::table('partner_deals', function (Blueprint $table) {
                        $table->foreign('admin_id')->references('id')->on('admins');
                    });
                } catch (\Exception $e) {
                    // ignore failure to add foreign key (will not block migration)
                }
            }

            // deal_id -> deals.id
            if (Schema::hasTable('deals') && Schema::hasColumn('partner_deals', 'deal_id')) {
                try {
                    Schema::table('partner_deals', function (Blueprint $table) {
                        $table->foreign('deal_id')->references('id')->on('deals');
                    });
                } catch (\Exception $e) {
                    // ignore failure to add foreign key
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_deals');
    }
};
