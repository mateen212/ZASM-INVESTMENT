<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DealSenderAddress extends Model
{
    // Table name (if different from pluralized model name)
    protected $table = 'deal_sender_address';

    // Specify the primary key if it's not 'id'
    protected $primaryKey = 'id';

    // The attributes that are mass assignable
    protected $fillable = [
        'deal_id',
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

    protected $casts = [  
        // 'pref_return_start_date' => 'date',
        
    ];

    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }
}
