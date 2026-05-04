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
        Schema::create('class_hurdles', function (Blueprint $table) {
            $table->id();
            $table->string('upside_split')->nullable();
            $table->string('hurdle_name')->nullable();
            $table->string('preferred_return_type')->nullable();
            $table->string('final_hurdle')->nullable();
            $table->string('catch_up')->nullable();
            $table->string('honor_only')->nullable();
            $table->string('preferred_return')->nullable();
            $table->string('day_count')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->unsignedBigInteger('deal_class_id');

            $table->timestamps();
        
            $table->foreign('deal_class_id')->references('id')->on('deal_classes')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_hurdles');
    }
};
