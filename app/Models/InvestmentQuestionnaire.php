<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvestmentQuestionnaire extends Model
{
    
    protected $fillable = [
        'investment_id',
        'first_name',
        'last_name',
        'telephone',
        'address',
        'resident_of_usa',
        'birth_date',
        'tax_purpose',
        'social_security_number',
    ];

    public function offering()
    {
        return $this->belongsTo(Offering::class);
    }

    public function investor()
    {
        return $this->belongsTo(Investor::class);
    }
    public function investment()
    {
        return $this->hasOne(Investment::class);
    }
    // public function addresses()
    // {
    //     return $this->hasMany(QuestionnaireAddress::class);
    // }
}
