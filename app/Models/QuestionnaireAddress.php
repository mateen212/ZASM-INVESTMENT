<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionnaireAddress extends Model
{
    protected $fillable = [
        'offering_id',        
        'company_name',
        'country',
        'address_line_1',
        'address_line_2',
        'city',
        'province',
        'postal_code',
        'state',
        'zip_code',
    ];

    // public function questionnaire()
    // {
    //     return $this->belongsTo(InvestmentQuestionnaire::class);
    // }
    public function offering()
    {
        return $this->belongsTo(Offering::class);
    }
}
