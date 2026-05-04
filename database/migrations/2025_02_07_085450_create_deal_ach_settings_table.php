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
        Schema::create('deal_ach_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deal_id');
            $table->string('entity_name')->nullable();
            $table->string('entity_type')->nullable();
            $table->string('address')->nullable();
            $table->string('ein_letter')->nullable(); 
            $table->string('controller_id')->nullable(); 
            $table->string('controller_address')->nullable();
            $table->string('state_registration')->nullable();
            $table->string('ein')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('job_title')->nullable();
            $table->string('ssn')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('document_label')->nullable();
            $table->boolean('does_individual')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deal_ach_settings');
    }
};
