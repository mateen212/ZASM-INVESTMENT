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
            $table->double('preferred_return')->nullable()->after('preferred_return_type');
            $table->string('preferred_return_accrues_on')->nullable()->after('preferred_return');
            $table->string('day_count')->nullable()->after('preferred_return_accrues_on');
            $table->date('start_date')->nullable()->after('day_count');
            $table->date('end_date')->nullable()->after('start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deal_classes', function (Blueprint $table) {
            $table->dropColumn('preferred_return');
            $table->dropColumn('preferred_return_accrues_on');
            $table->dropColumn('day_count');
            $table->dropColumn('start_date');
            $table->dropColumn('end_date'); 
        });
    }
};
