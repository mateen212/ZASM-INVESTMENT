<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $table = "members";

    protected $fillable = [
        'deal_id',
        'contact',
        'first_name',
        'last_name',
        'email_address',
        'role',
        'status',
        'invitation_email',
    ];

    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }

}

