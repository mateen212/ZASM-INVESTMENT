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
        Schema::table('distributions', function (Blueprint $table) {
            $table->unsignedBigInteger('distribution_waterfall')->nullable(); 
            $table->string('compounding_period')->nullable(); 
            $table->string('day_count')->nullable(); 
            $table->string('preffered_return')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('distributions', function (Blueprint $table) {
            $table->unsignedBigInteger('distribution_waterfall')->nullable(); 
            $table->string('compounding_period')->nullable(); 
            $table->string('day_count')->nullable(); 
            $table->string('preffered_return')->nullable();

            
        });
    }
};
