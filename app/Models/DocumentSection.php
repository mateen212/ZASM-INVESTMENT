<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentSection extends Model
{
    protected $fillable = [
        'deal_id',
        'name',
        'can_edit'
    ];

    public function deal(){
        return $this->belongsTo(Deal::class);
    }

    public function documents(){
        return $this->hasMany(Document::class);
    }
}
