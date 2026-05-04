<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionnaireForm extends Model
{
    protected $fillable = [
        'offering_id',        
        'name',
        'address',
        'social_security_number',
        
    ];
    public function offering()
    {
        return $this->belongsTo(Offering::class);
    }
}
