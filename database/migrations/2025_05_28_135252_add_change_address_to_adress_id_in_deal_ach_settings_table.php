<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('deal_ach_settings', function (Blueprint $table) {
            $table->dropColumn('address');
            $table->dropColumn('controller_address');
            $table->unsignedBigInteger('address_id')->nullable()->after('deal_id');
            $table->foreign('address_id')->references('id')->on('deal_addresses')->onDelete('set null');
            $table->unsignedBigInteger('controller_address')->nullable()->after('address_id');
            $table->foreign('controller_address')->references('id')->on('deal_addresses')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('deal_ach_settings', function (Blueprint $table) {
            $table->dropForeign(['address_id']);
            $table->dropForeign(['controller_address']);
            $table->dropColumn('address_id');
            $table->dropColumn('controller_address');
            $table->string('address')->nullable()->after('deal_id');
            $table->string('controller_address')->nullable()->after('entity_type');
        });
    }

};
