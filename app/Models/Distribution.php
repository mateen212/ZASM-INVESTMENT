<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\MoneyCast;
use App\Casts\PercentageCast;
use App\Scopes\PartnerDealScope;

class Distribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'deal_id',
        'source',
        'description',
        'amount',
        'distribution_date',
        'distribution_type',
        'approved',
        'is_visible',
        'memo',
        'distribution_waterfall_id',
        'start_date',
        'end_date',
        'count_toward',
        'counts_toward_pref',
        'calculation_method',
        'distribution_waterfall',
        'compounding_period',
        'day_count',
        'preffered_return',
        'amount',
        
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        // Apply the PartnerDealScope to automatically filter distributions for partners
        // static::addGlobalScope(new PartnerDealScope);
    }
    
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'amount' => MoneyCast::class,
        
    ];
    public function getCountTowardTextAttribute()
    {
        $map = [
            'accrued_pref' => "Deducts from Accrued Pref",
            'no_applicable' => "Does not deduct from accruals",
        ];

        return $map[$this->attributes['count_toward']] ?? $this->attributes['count_toward'];
    }

    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }
}
