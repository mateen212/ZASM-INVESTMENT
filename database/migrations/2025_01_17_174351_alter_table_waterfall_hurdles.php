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
        Schema::table('waterfall_hurdles', function (Blueprint $table) {
            // drop column
            $table->dropColumn('a_limited_partner1');
            $table->dropColumn('a_limited_partner2');

            $table->json('classes_values')->nullable()->after('included_class');
            $table->json('splits')->nullable()->after('split');

            $table->unsignedBigInteger('parent_id')->nullable()->after('waterfall_id');
            $table->foreign('parent_id')->references('id')->on('waterfall_hurdles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('waterfall_hurdles', function (Blueprint $table) {
            // add column
            $table->string('a_limited_partner1')->nullable()->after('included_class');
            $table->string('a_limited_partner2')->nullable()->after('a_limited_partner1');

            $table->dropColumn('classes_values');
            $table->dropColumn('splits');
            
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
        });
    }
};
