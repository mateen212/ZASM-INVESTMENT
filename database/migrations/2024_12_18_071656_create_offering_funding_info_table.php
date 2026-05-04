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
        Schema::create('offering_funding_infos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('offering_id');
            $table->string('funding_methods')->nullable();
            $table->string('receiving_bank')->nullable();
            $table->string('bank_address')->nullable();
            $table->string('routing_no')->nullable();
            $table->string('account_no')->nullable();
            $table->string('account_type')->nullable();
            $table->string('beneficiary_account_name')->nullable();
            $table->string('beneficiary_address')->nullable();
            $table->string('reference_info')->nullable();
            $table->string('instruction_info')->nullable();
            $table->string('mail_address')->nullable();
            $table->string('mail_beneficiary')->nullable();
            $table->string('mail_beneficiary_address')->nullable();
            $table->string('mail_instructions')->nullable();
            $table->string('investment_fee_type')->nullable();
            $table->string('investment_fee_method')->nullable();
            $table->decimal('investment_fee_amount', 10, 2)->nullable();  
            $table->foreign('offering_id')->references('id')->on('offerings')->onDelete('cascade');
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offering_funding_infos');
    }
};
