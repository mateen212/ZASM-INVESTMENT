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
        Schema::create('admin_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('co_sponser_investor_info')->default(false);
            $table->boolean('co_sponser_member_tab')->default(false);
            $table->string('lead_sponser_investment')->nullable();
            $table->string('lead_sponser_investment_update')->nullable();
            $table->boolean('co_sponser_portal')->default(false);
            $table->string('sponsers_billing_notification')->nullable();
            $table->string('equity_increase_class')->nullable();
            $table->boolean('equity_investment_increment')->default(false);
            $table->boolean('equity_funds_recieved')->default(false);
            $table->boolean('equity_funds_instruction')->default(false);
            $table->boolean('equity_funds_show_instruction')->default(false);
            $table->string('equity_sponser_email')->nullable();
            $table->boolean('equity_ach_details')->default(false);
            $table->boolean('equity_gp_approval')->default(false);
            $table->boolean('metric_ownership_percentage')->default(false);
            $table->boolean('metric_investors_share')->default(false);
            $table->boolean('metric_show_coc')->default(true);
            $table->boolean('metric_investor_liquid')->default(true);
            $table->boolean('metric_capital_balance')->default(true);
            $table->boolean('metric_investor_return')->default(false);
            $table->boolean('metric_investor_cash_balance')->default(false);
            $table->string('distribution_investment_return')->nullable();
            $table->boolean('distribution_reinvestment')->default(false);
            $table->boolean('distribution_tax_percentage')->default(false);
            $table->unsignedBigInteger('deal_id');
            $table->foreign('deal_id')->references('id')->on('deals')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_settings');
    }
};
