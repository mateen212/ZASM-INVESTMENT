<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;  
use Spatie\Tags\HasTags;
use App\Casts\MoneyCast;
use App\Casts\PercentageCast;
use App\Scopes\PartnerDealScope;

class Investment extends Model
{

    use HasTags;
    use HasFactory;
    
    protected $fillable = [
        'deal_id',
        'investor_id',
        'investor_profile_id',
        'deal_class_id',
        'offering_id',
        'questionnaire_id',
        'status',
        'investment_amount',
        'pcb_ownership',
        'op_ownership',
        'pcb_distribution',
        'op_distribution',
        'investment_tags',
        'date_placed',
        'contribution_method',
        'investment_status',
        'investment_in_progress',
        'canceled_on',
        'inactive_since',
        'primary_sponsor',
        'primary_company_member',
        'initiate_wire_transfer_date',
        'invoice_images',
        'transaction_details',
        'wire_transfer_status',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        // Apply the PartnerDealScope to automatically filter investments for partners
        // static::addGlobalScope(new PartnerDealScope);
    }

    protected $casts = [
        'investment_amount' => MoneyCast::class,
        'pcb_ownership' => PercentageCast::class,
        'op_ownership' => PercentageCast::class,
        'pcb_distribution' => PercentageCast::class,
        'op_distribution' => PercentageCast::class,
        'investment_in_progress' => 'boolean',
        'invoice_images' => 'array',
    ];

    protected $dates = [
        'date_placed',
        'canceled_on',
        'inactive_since',
    ];

    public function getInvestmentStatusTextAttribute()
    {
        $map = [
            'soft_committed'=>"Soft Committed",
            'investment_started'=> "Investment started",
            'document_started'=> "Document Signing Started",
            'signed'=> "Signed",
            'counter_signed'=> "Counter-Signed",
            'funding_instructions'=> "Funding Instructions Sent",
            'fund_received'=> "Fund Fully Received(Complete)",
            'inactive_bought_assign_sold'=> "Inactive (bought out, assigned, or sold)",
            'canceled'=> "Canceled (did not complete)",
        ];
        return $map[$this->investment_status] ?? $this->investment_status;
    }


    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }
    public function investor()
    {
        return $this->belongsTo(Investor::class);
    }
    public function profile()
    {
        return $this->belongsTo(InvestorProfile::class, 'investor_profile_id');
    }
    public function class()
    {
        return $this->belongsTo(DealClass::class, 'deal_class_id');
    }
    public function offering()
    {
        return $this->belongsTo(Offering::class);
    }

    public function investment_questionnaire()
    {
        return $this->belongsTo(InvestmentQuestionnaire::class);
    }
    
    /**
     * Attach tags to the investment for a specific user.
     *
     * @param array|string $tags
     * @param int $userId
     * @return void
     */
    public function attachTagsForUser($tags, $userId)
    {
        foreach ((array) $tags as $tag) {
            $tag = Tag::findOrCreateFromString($tag);
            if (!$this->tags()->where('tag_id', $tag->id)->wherePivot('user_id', $userId)->exists()) {
                $this->tags()->attach($tag, ['user_id' => $userId]);
            }
        }
    }

    /**
     * Get tags for the investment specific to a user.
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function tagsForUser($userId)
    {
        return $this->tags()->wherePivot('user_id', $userId)->get();
    }
    public function eSignTemplateRecipients()
    {
        return $this->hasMany(ESignTemplateRecipient::class, 'investment_id');
    }

}
