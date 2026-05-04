<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class InvestorProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'investor_id',
        'profile_type',
        'profile_fname',
        'profile_mname',
        'profile_lname',
        'profile_ira_name',
        'profile_ira_company',
        'profile_company_name',
        'profile_ira_account_number',
        'profile_email',
        'profile_fname2',
        'profile_mname2',
        'profile_lname2',
        'profile_email2',
        'profile_entity_name',
        'profile_number_of_members',
        'profile_distribution',
    ];

    public function Investor()
    {
        return $this->belongsTo(Investor::class);
    }
}
