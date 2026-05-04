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
        Schema::create('investor_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('investor_id')->nullable();
            $table->string('profile_type')->nullable(); 
            $table->string('profile_fname')->nullable();
            $table->string('profile_mname')->nullable(); 
            $table->string('profile_lname')->nullable(); 
            $table->string('profile_ira_name')->nullable(); 
            $table->string('profile_ira_company')->nullable(); 
            $table->string('profile_company_name')->nullable(); 
            $table->string('profile_ira_account_number')->nullable(); 
            $table->string('profile_email')->unique()->nullable(); 
            $table->string('profile_fname2')->nullable(); 
            $table->string('profile_mname2')->nullable(); 
            $table->string('profile_lname2')->nullable(); 
            $table->string('profile_email2')->unique()->nullable(); 
            $table->string('profile_entity_name')->nullable(); 
            $table->integer('profile_number_of_members')->nullable();
            $table->decimal('profile_distribution', 15, 2)->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investor_profiles');
    }
};
