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
        // remove columns investment_id
        Schema::table('investors', function (Blueprint $table) {
            $table->dropColumn('investment_id');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('investors', function (Blueprint $table) {
            $table->dropForeign(['investment_id']);
            $table->dropColumn('investment_id');
            
        });
    }
};
