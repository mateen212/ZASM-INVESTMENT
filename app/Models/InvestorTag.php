<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvestorTag extends Model
{
    protected $fillable = [
        'investor_id',
        'name'
    ];
    public function investor()

    {
        return $this->belongsTo(Investor::class);
    }

}
