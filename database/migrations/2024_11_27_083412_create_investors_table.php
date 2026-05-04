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
        Schema::create('investors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('investment_id')->nullable();
            $table->string('investor_fname')->nullable(); 
            $table->string('investor_lname')->nullable(); 
            $table->string('investor_email')->unique(); 
            $table->string('investor_phone_number')->nullable(); 
            $table->text('investor_note')->nullable(); 
            $table->string('investor_tags')->nullable(); 
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investors');
    }
};
