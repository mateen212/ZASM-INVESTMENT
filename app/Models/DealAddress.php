<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DealAddress extends Model
{
    protected $table = 'deal_addresses';

    protected $primaryKey = 'id';

    protected $fillable = [
        'deal_id',
        'country',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'zip_code',
        
    ];

    protected $casts = [  
        // 'pref_return_start_date' => 'date',
        
    ];

    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }
}
