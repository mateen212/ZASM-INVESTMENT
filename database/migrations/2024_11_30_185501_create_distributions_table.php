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
        // Data object 
        
        Schema::create('distributions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deal_id');
            $table->string('source');
            $table->text('description')->nullable();

            // amout
            $table->decimal('amount', 10, 2)->nullable();
            $table->date('distribution_date')->nullable();
            $table->string('distribution_type')->nullable();
            $table->boolean('approved')->default(false);
            $table->boolean('is_visible')->default(true);
            $table->string('memo')->nullable();

            $table->string('distribution_waterfall_id')->nullable();
            $table->string('calculation_method')->nullable();

            // Start and end dates
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            // count towards the total
            $table->string('count_toward')->nullable()->default("none");
            // counts_toward_pref
            $table->boolean('counts_toward_pref')->default(false);

            $table->foreign('deal_id')->references('id')->on('deals')->onDelete('cascade');

            $table->timestamps();
        });

        // Many to Many relation with classes
        Schema::create('deal_class_distribution', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('distribution_id');
            $table->unsignedBigInteger('deal_class_id');
            $table->foreign('distribution_id')->references('id')->on('distributions')->onDelete('cascade');
            $table->foreign('deal_class_id')->references('id')->on('deal_classes')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deal_class_distribution');
        Schema::dropIfExists('distributions');
    }
};
