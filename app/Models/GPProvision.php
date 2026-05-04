<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Casts\JsonCast;
class GPProvision extends Model
{

    protected $table = 'gp_provisions';
    protected $fillable = [
        'waterfall_hurdle_id',
        'deal_class_id',
        'classes_catch_up',
        'catch_up_splits',
        'classify_payment',
        'notes'
    ];
    protected $casts = [
        'classes_catch_up' => JsonCast::class,
        'catch_up_splits' => JsonCast::class
    ];

    public function hurdle()
    {
        return $this->belongsTo(WaterFallHurdle::class, 'waterfall_hurdle_id');
    }
}
