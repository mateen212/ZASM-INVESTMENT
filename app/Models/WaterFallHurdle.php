<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Casts\MoneyCast;
use App\Casts\PercentageCast;
use App\Casts\JsonCast;

class WaterFallHurdle extends Model
{
    // Specify the table name if it's different from the model name
    protected $table = 'waterfall_hurdles';

    // Define hurdle types constants
    const HURDLE_TYPE_CASH_ON_CASH = 'cash_on_cash';
    const HURDLE_TYPE_INTERNAL_RATE = 'internal_rate';
    const HURDLE_TYPE_RETURN_ON_INVESTMENT = 'return_on_investment';
    const HURDLE_TYPE_SPLIT = 'split';
    const HURDLE_TYPE_MANAGEMENT_FEE = 'management_fee';
    const HURDLE_TYPE_RETURN_OF_CAPITAL = 'return_of_capital';
    const HURDLE_TYPE_CUMULATIVE_RETURN = 'cumulative_return';
    const HURDLE_TYPE_INTEREST = 'interest';

    // Mass assignable attributes
    protected $fillable = [
        'waterfall_id',
        'parent_id',
        'path',
        'sort_order',
        'split',
        'splits',
        'hurdle_type',
        'included_class',
        'classes_values',
        'preferred_return_type',
        'cumulated_return_reach',
        'day_count',
        'compounding_frequency',
        'start_date',
        'end_date',
        'duration',
        'accrues_on',
        'payment_towards',
        'payment_type_towards',
        'split_unpayed',
        'accrual_cadence',
        'notes',
    ];
    protected $casts = [
        'cumulated_return_reach' => PercentageCast::class,
        'classes_values' => JsonCast::class,
        "included_class" => JsonCast::class,
        'payment_type_towards' => JsonCast::class,
        'splits' => JsonCast::class,
    ];

    protected $with = ['stop_hurdle', 'gp_provision'];

    // Define the relationship to the Water faLL model
    public function waterfall()
    {
        return $this->belongsTo(WaterFall::class);
    }

    public function children()
    {
        return $this->hasMany(WaterFallHurdle::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(WaterFallHurdle::class, 'parent_id');
    }

    public function getHasChildrenAttribute()
    {
        return $this->children()->count() > 0;
    }

    public function stop_hurdle()
    {
        return $this->hasOne(StopWaterfallHurdle::class, 'waterfall_hurdle_id');
    }

    public function gp_provision()
    {
        return $this->hasOne(GPProvision::class, 'waterfall_hurdle_id');
    }
}
