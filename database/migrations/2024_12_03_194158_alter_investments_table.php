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
        // reomve columns investor_name, investor_profile
        Schema::table('investments', function (Blueprint $table) {
            $table->dropColumn('investor_name');
            $table->dropColumn('investor_profile');

            $table->unsignedBigInteger('investor_id')->nullable();
            $table->unsignedBigInteger('investor_profile_id')->nullable();

            $table->foreign('investor_id')->references('id')->on('investors')->onDelete('cascade');
            $table->foreign('investor_profile_id')->references('id')->on('investor_profiles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('investments', function (Blueprint $table) {
            $table->string('investor_name')->nullable();
            $table->string('investor_profile')->nullable();

            $table->dropForeign(['investor_id']);
            $table->dropForeign(['investor_profile_id']);

            $table->dropColumn('investor_id');
            $table->dropColumn('investor_profile_id');
        });
    }
};
