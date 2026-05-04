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
        Schema::table('investor_profiles', function (Blueprint $table) {
            // Drop the columns
            $table->dropColumn('profile_email');
            $table->dropColumn('profile_email2');
        });

        Schema::table('investor_profiles', function (Blueprint $table) {
            // Recreate the columns in specific positions
            $table->string('profile_email')->nullable()->after('profile_ira_account_number');
            $table->string('profile_email2')->nullable()->after('profile_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('investor_profiles', function (Blueprint $table) {
            // Drop the newly created columns
            $table->dropColumn('profile_email');
            $table->dropColumn('profile_email2');
        });

        Schema::table('investor_profiles', function (Blueprint $table) {
            // Recreate the original columns with unique constraints
            $table->string('profile_email')->unique()->nullable()->after('profile_ira_account_number');
            $table->string('profile_email2')->unique()->nullable()->after('profile_email');
        });
    }
};
