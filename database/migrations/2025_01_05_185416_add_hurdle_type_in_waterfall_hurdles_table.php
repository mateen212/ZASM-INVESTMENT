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
            $table->string('hurdle_type')->nullable()->after('waterfall_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('waterfall_hurdles', function (Blueprint $table) {
            $table->dropColumn('hurdle_type');
        });
    }
};
