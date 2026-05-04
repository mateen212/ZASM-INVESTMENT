<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Admin;
use App\Models\Deal;

class PartnerDeal extends Model
{
    protected $table = "partner_deals";
    
    protected $fillable = [
        "admin_id",
        "deal_id",
        "status",
        "role",
        "is_active",
        "invitation_email",
    ];
    
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
    
    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }
}
