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
        if (!Schema::hasTable('partner_deals')) {
            Schema::create('partner_deals', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('admin_id')->nullable(); // Partner ID
                $table->unsignedBigInteger('deal_id')->nullable();  // Deal ID
                $table->timestamps();
            });
        } else {
            // Table exists — ensure required columns exist
            Schema::table('partner_deals', function (Blueprint $table) {
                if (!Schema::hasColumn('partner_deals', 'admin_id')) {
                    $table->unsignedBigInteger('admin_id')->nullable()->after('id');
                }
                if (!Schema::hasColumn('partner_deals', 'deal_id')) {
                    $table->unsignedBigInteger('deal_id')->nullable()->after('admin_id');
                }
            });
        }

        // Add foreign keys if referenced tables & columns exist
        if (Schema::hasTable('partner_deals')) {
            if (Schema::hasTable('admins') && Schema::hasColumn('partner_deals', 'admin_id')) {
                try {
                    Schema::table('partner_deals', function (Blueprint $table) {
                        // avoid adding duplicate foreign key by checking index
                        $sm = Schema::getConnection()->getDoctrineSchemaManager();
                    });
                    Schema::table('partner_deals', function (Blueprint $table) {
                        $table->foreign('admin_id')->references('id')->on('admins');
                    });
                } catch (\Exception $e) {
                    // ignore errors adding FK
                }
            }

            if (Schema::hasTable('deals') && Schema::hasColumn('partner_deals', 'deal_id')) {
                try {
                    Schema::table('partner_deals', function (Blueprint $table) {
                        $table->foreign('deal_id')->references('id')->on('deals');
                    });
                } catch (\Exception $e) {
                    // ignore errors adding FK
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('partner_deals')) {
            return;
        }

        // Drop foreign keys if present then drop columns or table
        try {
            Schema::table('partner_deals', function (Blueprint $table) {
                if (Schema::hasColumn('partner_deals', 'admin_id')) {
                    try {
                        $table->dropForeign(['admin_id']);
                    } catch (\Exception $e) {
                    }
                }
                if (Schema::hasColumn('partner_deals', 'deal_id')) {
                    try {
                        $table->dropForeign(['deal_id']);
                    } catch (\Exception $e) {
                    }
                }
            });
        } catch (\Exception $e) {
        }

        Schema::dropIfExists('partner_deals');
    }
};
