<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManageOffering extends Model
{
    
    protected $fillable = [
        'offering_id',
        'min_investment',
        'max_investment',
        'account_creation',
        'prompt_lp',
        'ira_document',
        'allowed_profile_types',
        'questionnaire',
        'questionnaire_soft',
        'require_w9',
        'signature_text',
        'verify_investor',
        'verify_accreditation_soft',
        'ait_cvl',
        'rav_blp',
        'methods',
        'verify_accreditation_identity',
        'require_kyc',
        'display_offering',
    ];

    protected $casts = [
        'min_investment' => 'boolean',
        'max_investment' => 'boolean',
        'account_creation' => 'boolean',
        'ira_document' => 'boolean',
        'questionnaire' => 'boolean',
        'questionnaire_soft' => 'boolean',
        'require_w9' => 'boolean',
        'verify_investor' => 'boolean',
        'verify_accreditation_soft' => 'boolean',
        'ait_cvl' => 'boolean',
        'rav_blp' => 'boolean',
        'verify_accreditation_identity' => 'boolean',
        'require_kyc' => 'boolean',
        'display_offering' => 'boolean',
    ];

    public function offering()
    {
        return $this->belongsTo(Offering::class);
    }
}
