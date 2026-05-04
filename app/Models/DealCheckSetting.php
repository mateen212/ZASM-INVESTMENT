<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DealCheckSetting extends Model
{
    // Table name (if different from pluralized model name)
    protected $table = 'deal_check_setting';

    // The attributes that are mass assignable
    protected $fillable = [
        'deal_id',
        'senderAddress',
        'bankAccount',        
    ];

    protected $casts = [  
        // 'pref_return_start_date' => 'date',
        
    ];

    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }
}
