<?php

namespace App\Models;

use App\Casts\PercentageCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;
use App\Models\MoneyCast;
class ClassHurdle extends Model
{
    use HasFactory;

    // Specify the table name if it's different from the model name
    protected $table = 'class_hurdles';

    // Mass assignable attributes
    protected $fillable = [
        'upside_split',
        'upside_limit',
        'hurdle_name',
        'preferred_return_type',
        'final_hurdle',
        'catch_up',
        'honor_only',
        'preferred_return',
        'day_count',
        'start_date',
        'end_date',
        'deal_class_id',
    ];

    // Attribute casting
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'upside_split' => PercentageCast::class,
        'upside_limit' => PercentageCast::class,
    ];

    // Define the relationship to the DealClass model
    public function dealClass()
    {
        return $this->belongsTo(DealClass::class);
    }
    
}
