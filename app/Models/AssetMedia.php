<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'media_url',
        'media_type',
        'media_description',
        'asset_id',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    // public function getMediaUrlAttribute()
    // {
    //     return $this->assetMedia->pluck('media_url');
    // }

    // function to add files to the asset
    // upload the file to the storage and save the file path to the database
    public function addMedia($media)
    {
        $this->assetMedia()->create($media);
    }
}
