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
        Schema::create('deal_classes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deal_id')->nullable();  
            $table->string('class_type')->nullable();  
            $table->string('equity_class_name')->nullable(); 
            $table->string('entity_legal_ownership')->nullable();  
            $table->string('preferred_return_type')->nullable(); 
            $table->decimal('raise_amount_ownership', 15, 2)->nullable();  
            $table->decimal('raise_amount_distributions', 15, 2)->nullable();  
            $table->integer('raise_quota')->nullable(); 
            $table->decimal('minimum_investment', 15, 2)->nullable();  
            $table->decimal('distribution_share', 5, 2)->nullable(); 
            $table->string('investment_type')->nullable(); 
            $table->decimal('maximum_investment', 15, 2)->nullable();  
            $table->decimal('price_per_unit', 15, 2)->nullable();  
            $table->decimal('target_irr', 5, 2)->nullable();   
            $table->date('pref_return_start_date')->nullable();  
            $table->tinyInteger('waitlist_status')->default(0)->nullable();  
            $table->timestamps();

            
            $table->foreign('deal_id')->references('id')->on('deals')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deal_classes');
    }
};
