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
            // Change included_class to json
            $table->json('included_class')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('waterfall_hurdles', function (Blueprint $table) {
            $table->string('included_class')->nullable()->change();
        });
    }
};
