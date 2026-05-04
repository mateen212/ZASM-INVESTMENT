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
        Schema::table('questionnaire_addresses', function (Blueprint $table) {
            $table->unsignedBigInteger('offering_id')->after('id');
            $table->dropColumn('questionnaire_id');
            $table->foreign('offering_id')->references('id')->on('offerings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questionnaire_addresses', function (Blueprint $table) {
            $table->dropForeign(['offering_id']);
            $table->dropColumn('offering_id');
            $table->unsignedBigInteger('questionnaire_id');
        });
    }
};
