<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonalSetting extends Model
{

    // The attributes that are mass assignable
    protected $fillable = [
        'deal_id',
        'email_privacy_investor',
        'email_interception_review',
        'email_interception_sponser',
        'notification_selected_sponser',
        'document_visibility_investors',
    ];

    protected $casts = [
        'email_privacy_investor' => 'boolean',
        'email_interception_review' => 'boolean',
        'email_interception_sponser' => 'boolean',
        'document_visibility_investors' => 'boolean',
    ];

    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }
}
