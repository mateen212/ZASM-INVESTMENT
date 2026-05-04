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
        Schema::create('gp_provisions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('waterfall_hurdle_id');
            $table->unsignedBigInteger('deal_class_id')->nullable();
            $table->json('classes_catch_up')->nullable();
            $table->json('catch_up_splits')->nullable();
            $table->string('classify_payment')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
        });

        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gp_provisions');
        
    }

    
};
