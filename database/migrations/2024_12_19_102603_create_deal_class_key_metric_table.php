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
        Schema::create('deal_class_key_metric', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deal_class_id');
            $table->unsignedBigInteger('key_metric_id');
            $table->string('value')->nullable();
            $table->foreign('deal_class_id')->references('id')->on('deal_classes')->onDelete('cascade');
            $table->foreign('key_metric_id')->references('id')->on('key_metrics')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deal_class_key_metric');
    }
};
