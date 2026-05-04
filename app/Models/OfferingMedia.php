<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferingMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'media_type',
        'media_url',
        'offering_id',
        // Add other fields here as needed
    ];

    public function offering()
    {
        return $this->belongsTo(Offering::class);
    }
}
