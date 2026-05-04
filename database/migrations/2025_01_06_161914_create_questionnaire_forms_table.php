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
        Schema::create('questionnaire_forms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('offering_id');
            $table->string('name')->nullable();
            $table->string('address')->nullable();
            $table->string('social_security_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questionnaire_forms');
    }
};
