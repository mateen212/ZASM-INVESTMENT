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
        Schema::create('key_metrics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('offering_id');
            $table->string('metric_label')->nullable();
            $table->string('metric_class')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('key_metrics');
    }
};
