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
        if (!Schema::hasTable('admins')) {
            return;
        }

        Schema::table('admins', function (Blueprint $table) {
            if (!Schema::hasColumn('admins', 'company_name')) {
                $table->string('company_name')->nullable(); // For partners
            }
            if (!Schema::hasColumn('admins', 'company_description')) {
                $table->text('company_description')->nullable();
            }
            if (!Schema::hasColumn('admins', 'company_logo')) {
                $table->string('company_logo')->nullable();
            }
            if (!Schema::hasColumn('admins', 'company_website')) {
                $table->string('company_website')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('admins')) {
            return;
        }

        Schema::table('admins', function (Blueprint $table) {
            $drops = [];
            if (Schema::hasColumn('admins', 'company_name')) {
                $drops[] = 'company_name';
            }
            if (Schema::hasColumn('admins', 'company_description')) {
                $drops[] = 'company_description';
            }
            if (Schema::hasColumn('admins', 'company_logo')) {
                $drops[] = 'company_logo';
            }
            if (Schema::hasColumn('admins', 'company_website')) {
                $drops[] = 'company_website';
            }
            if (!empty($drops)) {
                $table->dropColumn($drops);
            }
        });
    }
};
