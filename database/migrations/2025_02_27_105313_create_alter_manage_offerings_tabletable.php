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
        Schema::table('manage_offerings', function (Blueprint $table) {
            $table->boolean('display_offering')->default(false)->after('require_kyc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('manage_offerings', callback: function (Blueprint $table) {
            $table->dropColumn('display_offering');
        });
    }
};
