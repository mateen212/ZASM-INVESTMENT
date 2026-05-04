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
        Schema::table('admins', function (Blueprint $table) {
            // Add status column with default value 1 (active)
            // Status values: 1 = active, 0 = inactive, 2 = paused, 3 = terminated
            $table->tinyInteger('status')->default(1)->after('password')->comment('1=active, 0=inactive, 2=paused, 3=terminated');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
