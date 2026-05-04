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
        Schema::create('manage_offerings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('offering_id');
            $table->boolean('min_investment')->default(true);
            $table->boolean('max_investment')->default(true);
            $table->boolean('account_creation')->default(false);
            $table->string('prompt_lp')->nullable();
            $table->boolean('ira_document')->default(false);
            $table->string('allowed_profile_types')->nullable();
            $table->boolean('questionnaire')->default(true);
            $table->boolean('questionnaire_soft')->default(false);
            $table->boolean('require_w9')->default(true);
            $table->string('signature_text')->nullable();
            $table->boolean('verify_investor')->default(true);
            $table->boolean('verify_accreditation_soft')->default(false);
            $table->boolean('ait_cvl')->default(false);
            $table->boolean('rav_blp')->default(false);
            $table->string('methods')->nullable();
            $table->boolean('verify_accreditation_identity')->default(false);
            $table->boolean('require_kyc')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manage_offerings');
    }
};
