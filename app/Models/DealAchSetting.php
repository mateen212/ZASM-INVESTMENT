<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DealAchSetting extends Model
{
    protected $table = "deal_ach_settings";
    protected $fillable = [
        "deal_id",
        'entity_name',
        'entity_type',
        'address_id',
        'ein_letter',
        'controller_id',
        'controller_address',
        'state_registration',
        'ein',
        'first_name',
        'last_name',
        'job_title',
        'ssn',
        'date_of_birth',
        'document_label',
        'does_individual',
        'verify_detail',
        'verify_confirmation',
        'stripe_customer_id',
        'stripe_account_id',
        'ach_payment_method_id',
        'is_verified',
    ];

    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }

    public function address()
    {
        return $this->belongsTo(DealAddress::class, 'address_id', 'id');
    }

}