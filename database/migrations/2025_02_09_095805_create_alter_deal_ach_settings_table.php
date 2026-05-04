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
        Schema::table('deal_ach_settings', function (Blueprint $table) {
            $table->boolean('verify_detail')->default(false)->after('does_individual')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deal_ach_settings', function (Blueprint $table) {
            $table->dropColumn('verify_detail');
        });
    }
};
