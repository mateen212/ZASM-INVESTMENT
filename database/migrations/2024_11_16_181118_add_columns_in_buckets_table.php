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
        Schema::table('buckets', function (Blueprint $table) {
            $table->double('distribution_share', 8, 2)->after('raise_quota')->nullable();
            $table->double('entity_legal_ownership', 8, 2)->after('distribution_share')->nullable();
            $table->string('waitlist_status')->after('entity_legal_ownership')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('buckets', function (Blueprint $table) {
            $table->dropColumn('distribution_share');
            $table->dropColumn('entity_legal_ownership');
            $table->dropColumn('waitlist_status');
        });
    }
};
