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
        Schema::create('waterfall_hurdles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('waterfall_id')->nullable();
            $table->string('split')->nullable(); 
            $table->json('included_class')->nullable(); 
            $table->string('a_limited_partner1')->nullable(); 
            $table->string('a_limited_partner2')->nullable(); 
            $table->decimal('cumulated_return_reach', 15, 2)->nullable(); 
            $table->integer('day_count')->nullable(); 
            $table->string('compounding_frequency')->nullable(); 
            $table->date('start_date')->nullable(); 
            $table->date('end_date')->nullable();
            $table->string('duration')->nullable();
            $table->string('accrues_on')->nullable(); 
            $table->string('payment_towards')->nullable(); 
            $table->string('payment_type_towards')->nullable(); 
            $table->string('split_unpayed')->nullable(); 
            $table->string('accrual_cadence')->nullable(); 
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->foreign('waterfall_id')->references('id')->on('waterfalls')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waterfall_hurdles');
    }
};
