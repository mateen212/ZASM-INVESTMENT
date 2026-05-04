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
        Schema::create('insights', function (Blueprint $table) {
            $table->id();

            // Property Manager Section
            $table->string('property_manager_name')->nullable();

            // Sponsorship Team Section
            $table->integer('full_cycle_deals')->nullable();
            $table->string('irr_full_cycle_deals')->nullable();

            // Market Section
            $table->decimal('one_mile_median_income', 10, 2)->nullable();
            $table->decimal('three_mile_median_income', 10, 2)->nullable();

            // Debt Financing Section
            $table->string('financing_type')->nullable();
            $table->decimal('loan_to_value', 5, 2)->nullable();
            $table->decimal('interest_rate', 5, 2)->nullable();
            $table->integer('loan_term')->nullable();
            $table->decimal('loan_assumption', 5, 2)->nullable();
            $table->integer('interest_only_period')->nullable();

            // Terms and Fees Section
            $table->decimal('acquisition_fee', 5, 2)->nullable();
            $table->decimal('asset_management_fee', 5, 2)->nullable();
            $table->decimal('construction_management_fee', 5, 2)->nullable();
            $table->decimal('disposition_fee', 5, 2)->nullable();
            $table->decimal('refinance_fee', 5, 2)->nullable();
            $table->string('profit_sharing')->nullable();

            $table->unsignedBigInteger('offering_id');
            $table->foreign('offering_id')->references('id')->on('offerings')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insights');
    }
};
