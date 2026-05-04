<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DealBankAccount extends Model
{
    

    // Table name (if different from pluralized model name)
    protected $table = 'deal_bank_account';

    // Specify the primary key if it's not 'id'
    protected $primaryKey = 'id';

    // The attributes that are mass assignable
    protected $fillable = [
        'deal_id',
        'account_nick_name',
        'account_type',
        'ach_account_number',
        'account_number',
        'check_signature',
        
    ];

    protected $casts = [  
        // 'pref_return_start_date' => 'date',
        
    ];

    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }
   
}
