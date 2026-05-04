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
        Schema::table('class_hurdles', function (Blueprint $table) {
            $table->double('upside_limit')->after('upside_split');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('class_hurdles', function (Blueprint $table) {
            $table->dropColumn('upside_limit');
        });
    }
};
