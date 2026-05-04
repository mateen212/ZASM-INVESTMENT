<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;
use App\Casts\MoneyCast;
use App\Casts\PercentageCast;
use App\Scopes\PartnerDealScope;

class DealClass extends Model
{
    use HasFactory;

    // Table name (if different from pluralized model name)
    protected $table = 'deal_classes';

    // Specify the primary key if it's not 'id'
    protected $primaryKey = 'id';

    // The attributes that are mass assignable
    protected $fillable = [
        'deal_id',
        'class_type',
        'equity_class_name',
        'entity_legal_ownership',
        'preferred_return',
        'preferred_return_type',
        'raise_amount_ownership',
        'raise_amount_distributions',
        'raise_quota',
        'minimum_investment',
        'distribution_share',
        'investment_type',
        'maximum_investment',
        'price_per_unit',
        'target_irr',
        'pref_return_start_date',
        'waitlist_status',
        'preferred_return_accrues_on',
        'day_count',
        'start_date',
        'end_date',

    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        // Apply the PartnerDealScope to automatically filter deal classes for partners
        // static::addGlobalScope(new PartnerDealScope);
    }

    protected $appends = ['total_investments'];

    protected $casts = [  
        // 'Money Casts'
        'raise_amount_ownership' => MoneyCast::class,
        'raise_amount_distributions' => MoneyCast::class,
        'raise_quota' => MoneyCast::class,
        'price_per_unit' => MoneyCast::class,
        'minimum_investment' => MoneyCast::class,
        'maximum_investment' => MoneyCast::class,
        // 'Percentage Casts'
        'preferred_return' => PercentageCast::class,
        'entity_legal_ownership' => PercentageCast::class,
        'distribution_share' => PercentageCast::class,
        'target_irr' => PercentageCast::class,
        
    ];

    public function deal()
    {
        return $this->belongsTo(Deal::class, 'deal_id', 'id');
    }
    public function hurdles()
    {
        return $this->hasMany(ClassHurdle::class);
    }
    
    public function bucket()
    {
        return $this->belongsTo(Bucket::class);
    }
    public function investments()
    {
        return $this->hasMany(Investment::class);
    }

    // get total of all investments and convert string to float and remove symbol and then becom sum then apply symbols
    public function getTotalInvestmentsAttribute()
    {
        $total = $this->investments->sum(function ($investment) {
            $amount = preg_replace('/[^\d.]/', '', $investment->investment_amount);
            return (float) $amount;
        });

        return '$' . number_format($total, 2);
    }

}
