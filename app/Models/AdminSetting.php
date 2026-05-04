<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Casts\MoneyCast;
use App\Casts\PercentageCast;
class AdminSetting extends Model
{

    // The attributes that are mass assignable
    protected $fillable = [
        'co_sponser_investor_info',
        'co_sponser_member_tab',
        'lead_sponser_investment',
        'lead_sponser_distribution',
        'lead_sponser_investment_update',
        'co_sponser_portal',
        'sponsers_billing_notification',
        'equity_increase_class',
        'equity_investment_increment',
        'equity_funds_recieved',
        'equity_funds_instruction',
        'equity_funds_show_instruction',
        'equity_sponser_email',
        'equity_ach_details',
        'equity_gp_approval',
        'metric_ownership_percentage',
        'metric_investors_share',
        'metric_show_coc',
        'metric_investor_liquid',
        'metric_capital_balance',
        'metric_investor_return',
        'metric_investor_cash_balance',
        'distribution_investment_return',
        'distribution_reinvestment',
        'distribution_tax_percentage',
        'deal_id',
        'min_amount',
        'max_amount'
    ];

    protected $casts = [
        'co_sponser_investor_info' => 'boolean',
        'co_sponser_member_tab' => 'boolean',
        'lead_sponser_investment_update' => 'boolean',
        'co_sponser_portal' => 'boolean',
        'equity_investment_increment' => 'boolean',
        'equity_funds_recieved' => 'boolean',
        'equity_funds_instruction' => 'boolean',
        'equity_funds_show_instruction' => 'boolean',
        'equity_ach_details' => 'boolean',
        'equity_gp_approval' => 'boolean',
        'metric_ownership_percentage' => 'boolean',
        'metric_investors_share' => 'boolean',
        'metric_show_coc' => 'boolean',
        'metric_investor_liquid' => 'boolean',
        'metric_capital_balance' => 'boolean',
        'metric_investor_return' => 'boolean',
        'metric_investor_cash_balance' => 'boolean',
        'distribution_reinvestment' => 'boolean',
        'distribution_tax_percentage' => 'boolean',
        'equity_increase_class' => 'array',
        'min_amount' => MoneyCast::class,
        'max_amount' => MoneyCast::class,
        
    ];

    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }
}
