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
        Schema::table('waterfall_hurdles', function (Blueprint $table) {
            $table->string('preferred_return_type')->nullable()->after('classes_values');
        });
        Schema::table('investments', function (Blueprint $table) {
            $table->dropColumn('investment_in_progress');
        });
        Schema::table('investments', function (Blueprint $table) {
            $table->boolean('investment_in_progress')->nullable()->after('investment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('waterfall_hurdles', function (Blueprint $table) {
            $table->dropColumn('preferred_return_type');
        });
        Schema::table('investments', function (Blueprint $table) {
            $table->dropColumn('investment_in_progress');
        });
        Schema::table('investments', function (Blueprint $table) {
            $table->boolean('investment_in_progress')->nullable()->after('investment_status');
        });
    }
};
