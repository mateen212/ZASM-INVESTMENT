<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaterFall extends Model
{

    
    protected $table = 'waterfalls';

    // Specify the primary key if it's not 'id'
    protected $primaryKey = 'id';

    // The attributes that are mass assignable
    protected $fillable = [
        'deal_id',
        'waterfall_name',
        'is_default',
        'is_basic',
    ];

    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }
    public function hurdles()
    {
        return $this->hasMany(WaterFallHurdle::class, 'waterfall_id')->where('parent_id', null);
    }
}
