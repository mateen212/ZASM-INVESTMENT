<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\PartnerDealScope;

class Document extends Model
{
    protected $fillable = [
        'deal_id',
        'offering_id',
        'name',
        'file_name',
        'file_path',
        'file_extension',
        'document_section_id',
        'date_added',
        'shared_with',
        'visible_to_lp',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        // Apply the PartnerDealScope to automatically filter documents for partners
        // static::addGlobalScope(new PartnerDealScope);
    }

    public function section()
    {
        return $this->hasMany(DocumentSection::class, 'document_section_id');
    }
    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }
}
