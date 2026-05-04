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
        Schema::create('e_sign_templates', function (Blueprint $table) {
            $table->id();
            $table->string('template_name');
            $table->string('template_type');
            $table->string('file_path');
            $table->unsignedBigInteger('deal_id');
            $table->unsignedBigInteger('offering_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('e_sign_templates');
    }
};
