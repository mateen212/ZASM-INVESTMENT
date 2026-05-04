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
        Schema::create('investment_questionnaires', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('offering_id');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('telephone')->nullable();
            $table->string('address')->nullable();
            $table->double('resident_of_usa', 5, 2)->default(0)->nullable();
            $table->date('birth_date')->nullable();
            $table->boolean('tax_purpose')->nullable();
            $table->string('social_security_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investment_questionnaires');
    }
};
