<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\MoneyCast;
use App\Casts\PercentageCast;
class OfferingFundingInfo extends Model
{
    use HasFactory;

    // protected $appends = ['funding_methods'];

    protected $fillable = [
        'offering_id',
        'receiving_bank',
        'bank_address',
        'routing_no',
        'account_no',
        'account_type',
        'beneficiary_account_name',
        'beneficiary_address',
        'reference_info',
        'instruction_info',
        'mail_address',
        'mail_beneficiary',
        'mail_beneficiary_address',
        'mail_instructions',
        'investment_fee_type',
        'investment_fee_method',
        'investment_fee_amount',
        'funding_methods',
    ];

    protected $casts = [
        // 'funding_methods' => 'array',
        'investment_fee_amount' => MoneyCast::class,
    ];

    public function offering()
    {
        return $this->belongsTo(Offering::class, 'offering_id');
    }

    // public function getFundingMethodsAttribute($value)
    // {
    //     if (empty($value)) {
    //         return [];
    //     }
    //     return json_decode($value, true);
    // }
    
}
