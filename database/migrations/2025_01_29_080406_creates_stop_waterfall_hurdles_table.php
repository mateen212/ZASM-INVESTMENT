<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('stop_waterfall_hurdles', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('waterfall_hurdle_id'); 
        $table->string('preferred_return_type')->nullable();
        $table->json('included_class')->nullable();
        $table->json('classes_values')->nullable();
        $table->text('notes')->nullable();
        $table->string('accrues_on')->default('capital_balance');
        $table->string('payment_type_towards')->nullable();
        $table->string('payments_towards')->default('preferred_return');
        $table->string('split_unpayed')->default('unpaid_accrual');
        $table->string('accrual_cadence')->default('daily');
        $table->string('start_date')->nullable();
        $table->string('end_date')->nullable();
        $table->string('duration')->nullable();
        $table->string('day_count')->default('actual_365');
        $table->string('compounding_frequency')->default('none');
        $table->string('cumulated_return_reach')->nullable();
        $table->foreign('waterfall_hurdle_id')->references('id')->on('waterfall_hurdles')->onDelete('cascade');
        $table->timestamps();

    });
}

public function down()
{
    Schema::dropIfExists('stop_waterfall_hurdles');
}
};
