<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DealOwningDetail extends Model
{
    
    protected $table = 'deal_owning_entity_details';
    protected $fillable = [
        'deal_id',
        'owning_entity_name',
        'executive_name',
        'executive_title',
        'jurisdiction',
        'taxpayer_id',
        'email',
        'date_formed',
        'address_1',
        'address_2',
        'city',
        'province',
        'postal_code',
        'country',
    ];

    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }
}
