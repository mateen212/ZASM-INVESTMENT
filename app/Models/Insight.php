<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Casts\MoneyCast;
use App\Casts\PercentageCast;
class Insight extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'property_manager_name',
        'full_cycle_deals',
        'irr_full_cycle_deals',
        'one_mile_median_income',
        'three_mile_median_income',
        'financing_type',
        'loan_to_value',
        'interest_rate',
        'loan_term',
        'loan_assumption',
        'interest_only_period',
        'acquisition_fee',
        'asset_management_fee',
        'construction_management_fee',
        'disposition_fee',
        'refinance_fee',
        'profit_sharing',
        'offering_id',
    ];

    protected $casts = [
        'loan_assumption' => 'boolean',
        'one_mile_median_income' => MoneyCast::class,
        'three_mile_median_income' => MoneyCast::class,
        'irr_full_cycle_deals' => PercentageCast::class,
        'loan_to_value' => PercentageCast::class,
        'interest_rate' => PercentageCast::class,
        'acquisition_fee' => PercentageCast::class,
        'asset_management_fee' => PercentageCast::class,
        'construction_management_fee' => PercentageCast::class,
        'disposition_fee' => PercentageCast::class,
        'refinance_fee' => PercentageCast::class,
    ];

    
    public function offering()
    {
        return $this->belongsTo(related: Offering::class);
    }
}
