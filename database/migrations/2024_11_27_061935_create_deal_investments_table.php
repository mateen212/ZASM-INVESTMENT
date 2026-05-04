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
        Schema::create('deal_investments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deal_id')->nullable(); 
            $table->string('investor_name')->nullable();
            $table->string('investor_profile')->nullable(); 
            $table->string('deal_class_id')->nullable(); 
            $table->string('offering_id')->nullable(); 
            $table->double('investment_amount', 15, 2)->default(0)->nullable(); 
            $table->double('pcb_ownership', 5, 2)->default(0)->nullable(); 
            $table->double('op_ownership', 5, 2)->default(0)->nullable(); 
            $table->double('pcb_distribution', 5, 2)->default(0)->nullable(); 
            $table->double('op_distribution', 5, 2)->default(0)->nullable();
            $table->string('investment_tags')->nullable(); 
            $table->date('date_placed')->nullable(); 
            $table->string('contribution_method')->nullable(); 
            $table->string('investment_status')->nullable(); 
            $table->boolean('investment_in_progress')->default(false)->nullable(); 
            $table->date('canceled_on')->nullable(); 
            $table->date('inactive_since')->nullable(); 
            $table->string('primary_sponsor')->nullable(); 
            $table->string('primary_company_member')->nullable(); 
            $table->timestamps();
            $table->foreign('deal_id')->references('id')->on('deals')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deal_investments');
    }
};
