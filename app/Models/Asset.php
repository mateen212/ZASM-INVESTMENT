<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\MoneyCast;
use App\Casts\PercentageCast;
class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'city',
        'state',
        'zip',
        'country',
        'property_type',
        'property_class',
        'net_asset_value',
        'number_of_units',
        'type_of_units',
        'year_built',
        'year_renovated',
        'acquisition_date',
        'acquisition_price',
        'exit_date',
        'exit_price',
        'deal_id',
        // Add other fields here as needed
    ];

    protected $casts = [
        'acquisition_date' => 'date:Y-m-d',
        'exit_date' => 'date:Y-m-d',
        'net_asset_value' => MoneyCast::class,
        'acquisition_price' => MoneyCast::class,
        'exit_price' => MoneyCast::class,
        
    ];

    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }

    public function assetMedia()
    {
        return $this->hasMany(AssetMedia::class);
    }

}
