<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDocumensoDocumentIdToESignTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('e_sign_templates', function (Blueprint $table) {
            $table->string('documenso_document_id')->nullable()->after('offering_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('e_sign_templates', function (Blueprint $table) {
            // Drop the column if rolling back
            $table->dropColumn('documenso_document_id');
        });
    }
}