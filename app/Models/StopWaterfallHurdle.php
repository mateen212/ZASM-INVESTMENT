<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Casts\PercentageCast;
use App\Casts\JsonCast;
class StopWaterfallHurdle extends Model
{
    protected $table = "stop_waterfall_hurdles";
    protected $fillable = [
        'waterfall_hurdle_id', 
        'preferred_return_type',
        'included_class',
        'classes_values',
        'notes',
        'accrues_on',
        'payment_type_towards',
        'payments_towards',
        'split_unpayed',
        'accrual_cadence',
        'start_date',
        'end_date',
        'duration',
        'day_count',
        'compounding_frequency',
        'cumulated_return_reach',
    ];
    protected $casts = [
        'cumulated_return_reach' => PercentageCast::class,
        'classes_values' => JsonCast::class,
        "included_class" => JsonCast::class,
    ];

    public function hurdle()
    {
        return $this->belongsTo(WaterFallHurdle::class);
    }
}
