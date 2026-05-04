<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('investments', function (Blueprint $table) {
            $table->unsignedBigInteger('investment_questionnaire_id')->nullable()->after('investment_in_progress'); // Foreign key
            $table->foreign('investment_questionnaire_id')->references('id')->on('investment_questionnaires')->onDelete('cascade'); // Delete related investment_questionnaire when deleting investment record.
        });

        Schema::table('investment_questionnaires', function (Blueprint $table) {
            $table->string('status')->default('in-progress')->after('social_security_number');
            $table->unsignedBigInteger('investor_id')->after('offering_id');
        });
    }

    public function down()
    {
        Schema::table('investments', function (Blueprint $table) {
            $table->dropForeign(['investment_questionnaire_id']);
            $table->dropColumn(['investment_questionnaire_id']);
        });

        Schema::table('investment_questionnaires', function (Blueprint $table) {
            $table->dropColumn(['status']);
            $table->dropColumn(['investor_id']);
        });
    }
};
