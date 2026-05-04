<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeyMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'offering_id',
        'metric_label',
        'metric_class',
        'can_del',
    ];

    public function offering(){
        return $this->belongsTo(Offering::class);
    }

    public function classes()
    {
        return $this->belongsToMany(DealClass::class)->withPivot('value');
    }
 
}
