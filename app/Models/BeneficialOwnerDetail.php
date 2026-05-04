<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeneficialOwnerDetail extends Model
{
    protected $table = "beneficial_owner_details";
    protected $fillable = [
        'deal_id',
        'first_name',
        'last_name',
        'dob',
        'social_security_number',
        'address_1',
        'address_2',
        'address_lookup',
        'city',
        'state',
        'zipcode'
    ];
    
    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }
}
