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
        Schema::table('e_sign_template_document_recipients', function (Blueprint $table) {
            $table->string('documenso_recipient_id')->nullable()->after('recipient_type');
            $table->string('status')->nullable()->after('documenso_recipient_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('e_sign_template_document_recipients', function (Blueprint $table) {
            $table->dropColumn('documenso_recipient_id');
            $table->dropColumn('status');
        });
    }
};
