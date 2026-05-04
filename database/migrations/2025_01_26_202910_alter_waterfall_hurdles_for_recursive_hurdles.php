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
            $table->tinyInteger('path')->nullable()->after('parent_id');
            $table->tinyInteger('sort_order')->nullable()->after('path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('waterfall_hurdles', function (Blueprint $table) {
            $table->dropColumn('path');
            $table->dropColumn('sort_order');
        });
    }
};
