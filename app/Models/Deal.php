<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\PartnerDealScope;

class Deal extends Model
{
    protected $fillable = [
        'name',
        'type',
        'deal_stage', 
        'sec_type',
        'close_date',
        'owning_entity_name',
        'funds_received_before_gp_countersigns',
        'send_funding_instructions_after_gp_countersigns',
        'user_id',
        // Add other fields here as needed
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        // Apply the PartnerDealScope to automatically filter deals for partners
        // static::addGlobalScope(new PartnerDealScope);
    }

    public function user()
    {
        return $this->belongsTo(Admin::class, 'user_id');
    }

    public function classes()
    {
        return $this->hasMany(DealClass::class, 'deal_id','id');
    }

    public function buckets()
    {
        return $this->hasMany(Bucket::class);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    public function offerings()
    {
        return $this->hasMany(Offering::class);
    }

    public function waterfalls()
    {
        return $this->hasMany(WaterFall::class);
    }
    public function investments()
    {
        return $this->hasMany(Investment::class);
    }
    public function investors()
    {
        return $this->hasMany(Investor::class);
    }
    // public function profiles()
    // {
    //     return $this->hasMany(Profiles::class);
    // }

    public function distributions()
    {
        return $this->hasMany(Distribution::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'deal_id'); 
    }

    public function document_sections()
    {
        return $this->hasMany(DocumentSection::class, 'deal_id'); 
    }

    public function admin_setting()
    {
        return $this->hasOne(AdminSetting::class, 'deal_id'); 
    }

    public function personal_setting()
    {
        return $this->hasOne(PersonalSetting::class, 'deal_id'); 
    }

    public function settings()
    {
        return $this->hasOne(DealCheckSetting::class);
    }

    // Define a one-to-one relationship for sender address
    public function senderaddresses()
    {
        return $this->hasMany(DealSenderAddress::class);
    }

    // Define a one-to-one relationship for bank account
    public function bankaccounts()
    {
        return $this->hasMany(DealBankAccount::class);
    }
    public function esignTemplates() {
        return $this->hasMany(ESignTemplate::class);
    }
    
    public function achsettings(){
        return $this->hasOne(DealAchSetting::class);
    }

    public function beneficial_owner_details(){
        return $this->hasMany(BeneficialOwnerDetail::class);
    }
    public function addresses(){
        return $this->hasMany(DealAddress::class);
    }

    public function owningEntityDetails()
    {
        return $this->hasOne(DealOwningDetail::class);
    }

    public function members()
    {
        return $this->hasMany(Member::class);
    }

    public function partners()
    {
        return $this->belongsToMany(Admin::class, 'partner_deals', 'deal_id', 'admin_id')
            ->withPivot('is_active', 'activation_key', 'role', 'status', 'invitation_email')
            ->withTimestamps();
    }

    public function getAllmembers()
    {
        $owner = $this->user;
        $owner->is_owner = true;

        $owner->pivot = (object)[
            "deal_id" => $this->id,
            "admin_id" => $owner->id,
            "is_active" => 1,
            "activation_key" => null,
            "role" => 'lead-sponsor',
            "status" => 1,
            "invitation_email" => null,
        ];

        // $owner->name = $owner->name;
        $partners = $this->partners;

        return collect([$owner])->merge($partners);
    }

    public function activePartners()
    {
        return $this->partners()->where('status', 'active');
    }

}
