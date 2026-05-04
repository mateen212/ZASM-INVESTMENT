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
        Schema::table('deal_classes', function (Blueprint $table) {
            // Make distribution_share nullable
            $table->decimal('distribution_share', 5, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deal_classes', function (Blueprint $table) {
            //
            $table->decimal('distribution_share', 5, 2)->change();
        });
    }
};
