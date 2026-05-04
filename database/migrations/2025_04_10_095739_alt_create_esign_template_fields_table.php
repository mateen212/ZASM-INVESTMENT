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
        Schema::table('e_sign_templates_fields', function (Blueprint $table) {
            $table->float('pageWidth')->after('page')->nullable();
            $table->float('pageHeight')->after('pageWidth')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('e_sign_templates_fields', function (Blueprint $table) {
            $table->dropColumn('pageWidth');
            $table->dropColumn('pageHeight');
        });
    }
};
