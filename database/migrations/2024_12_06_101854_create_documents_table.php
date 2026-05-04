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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deal_id')->nullable();
            $table->string('name');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_extension')->nullable();
            $table->unsignedBigInteger('document_section_id');
            $table->string('date_added')->nullable(); 
            $table->date('shared_with')->nullable(); 
            $table->string('visible_to_lp')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
