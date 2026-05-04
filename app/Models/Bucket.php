<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;
use App\Casts\MoneyCast;
use App\Casts\PercentageCast;
class Bucket extends Model
{
    use HasFactory;

    protected $fillable = [
        'equity_bucket_name',
        'raise_amount_ownership',
        'raise_amount_distributions',
        'raise_quota',
        'distribution_share',
        'entity_legal_ownership',
        'waitlist_status',
        'deal_id',
    ];

    protected $casts = [  
        // 'Money Casts'
        'raise_amount_ownership' => MoneyCast::class,
        'raise_amount_distributions' => MoneyCast::class,
        'raise_quota' => MoneyCast::class,
        // 'Percentage Casts'
        'entity_legal_ownership' => PercentageCast::class,
        'distribution_share' => PercentageCast::class,
        
    ];
    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }
    public function classes()
    {
        return $this->hasMany(DealClass::class);
    }
    
}
