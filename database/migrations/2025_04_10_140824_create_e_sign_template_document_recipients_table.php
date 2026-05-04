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
        Schema::create('e_sign_template_document_recipients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('e_sign_templates_id')->constrained('e_sign_template_documents')->onDelete('cascade');
            $table->unsignedBigInteger('investment_id')->constrained('investments')->onDelete('cascade');
            $table->unsignedBigInteger('investor_id')->constrained('investors')->onDelete('cascade');
            $table->string('name')->nullable();
            $table->string('role')->nullable(); 
            $table->string('email')->nullable();
            $table->string('signing_order')->nullable(); 
            $table->string('recipient_type')->nullable(); // e.g., 'signer', 'carbon_copy', etc.
            $table->string('token')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('e_sign_template_document_recipients');
    }
};
