<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\MoneyCast;
use App\Casts\PercentageCast;
class Offering extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'name',
        'internal_name',
        'offering_size',
        'verify_investor_accreditation',
        'deal_id',
        'visibility',
        'status',
        'hard_committed_percent',
        'video_url',
        'overview_metrics',
        'summary',
        'logged_summary',
        'secondary_summary_visibility',
        'offering_capital_call',
        'public_offering',
        // Add other fields here as needed
    ];

    protected $casts = [
        'overview_metrics' => 'array',
        'hard_committed_percent' => PercentageCast::class,
        'offering_size' => MoneyCast::class,
    ];

    public function getVisibilityTextAttribute()
    {
        $map = [
            'show_on_dashboard' => 'Show on dashboard',
            'show_on_deal_investor_dashboard' => "Show on deal investor's Dashboard",
            'only_visible_on_link' => 'Only Visible to Link'
        ];
        return $map[$this->visibility] ?? $this->visibility;
    }

    public function getStatusTextAttribute()
    {
        $map = [
            '1' => 'Draft',
            '2' => "Open to soft commits",
            '3' => 'Open to hard commits',
            '4' => 'Open to investments',
            '5' => 'WaitList',
            '6' => 'Closed',
            '7' => 'Past'
        ];
        return $map[$this->status] ?? $this->status;
    }


    public function getEffectiveInvestmentTypeAttribute()
    {
        $investmentTypes = $this->classes->pluck('investment_type')->unique();

        if ($investmentTypes->count() === 1) {
            $type = $investmentTypes->first();
            return $type === 'debt' ? 'Note' : 'Equity';
        }

        return 'equityNote';
    }
    public function getEffectiveInvestmentAttribute()
    {
        $investmentTypes = $this->classes->pluck('investment_type')->unique();

        if ($investmentTypes->count() === 1) {
            $type = $investmentTypes->first();
            return $type === 'debt' ? 'Debt' : 'Equity';
        }

        return 'Debt/Equity';
    }

    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }

    public function media()
    {
        return $this->hasMany(OfferingMedia::class);
    }

    public function classes()
    {
        return $this->belongsToMany(DealClass::class);
    }

    public function assets()
    {
        return $this->belongsToMany(Asset::class, 'asset_offering');
    }

    public function investments()
    {
        return $this->hasMany(Investment::class);
    }

    public function documents(){
        return $this->hasMany(Document::class, 'offering_id');
    }

    public function funding_info(){
        return $this->hasOne(OfferingFundingInfo::class, 'offering_id');
    }

    public function key_metrics(){
        return $this->hasMany(KeyMetric::class, 'offering_id');
    }

    public function insight(){
        return $this->hasOne(Insight::class, 'offering_id');
    }
    
    public function manageoffering(){
        return $this->hasOne(ManageOffering::class, 'offering_id');
    }

    public function investment_questionnaires()
    {
        return $this->hasMany(InvestmentQuestionnaire::class, 'offering_id');
    }

    public function questionnaire_addresses()
    {
        return $this->hasMany(QuestionnaireAddress::class, 'offering_id');
    }

    public function questionnaire_forms()
    {
        return $this->hasOne(QuestionnaireForm::class, 'offering_id');
    }
    public function esignTemplates() {
        return $this->hasMany(ESignTemplate::class);
    }
    public function esignTemplateRecipients() {
        return $this->hasMany(ESignTemplateRecipient::class , 'offering_id');
    }
}
