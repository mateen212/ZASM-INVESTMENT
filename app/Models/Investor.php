<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;


use Illuminate\Database\Eloquent\Model;
use Spatie\Tags\HasTags;

class Investor extends Model
{
    use HasFactory;
    use HasTags;

    
    protected $fillable = [
        'investor_fname',
        'investor_lname',
        'investor_email',
        'investor_phone_number',
        'investor_note',
        'investor_tags',
        'user_id',
    ];
    public function investments()
    {
        return $this->hasMany(Investment::class, 'investor_id');
    }
    public function investor_profiles()
    {
        return $this->hasMany(InvestorProfile::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // public function tag()
    // {
    //     return $this->hasMany(InvestorTag::class);
    // }

    public function investment_questionnaires()
    {
        return $this->hasMany(InvestmentQuestionnaire::class, 'investor_id');
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
        return $this->hasMany(ESignTemplateRecipient::class, 'investor_id');
    }
}
