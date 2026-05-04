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
        Schema::table('partner_deals', function (Blueprint $table) {
            $table->boolean('is_active')->default(false)->after('deal_id');
            $table->string('activation_key')->nullable()->after('is_active');
            $table->string('role')->nullable()->after('activation_key');
            $table->tinyInteger('status')->default(0)->after('role');
            $table->string('invitation_email')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partner_deals', function (Blueprint $table) {
            $table->dropColumn('invitation_email');
            $table->tinyInteger('status')->default(0);
            $table->string('role')->nullable();
            $table->string('activation_key')->nullable();
            $table->boolean('is_active')->default(false);
        });
    }
};
