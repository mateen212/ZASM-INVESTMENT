<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ESignTemplateField extends Model
{
    protected $table = 'e_sign_templates_fields';

    protected $fillable = [
        'e_sign_templates_id',
        'type',
        'x',
        'y',
        'page',
        'value',
        'pageWidth',
        'pageHeight',
    ];

    public function template()
    {
        return $this->belongsTo(ESignTemplate::class, 'e_sign_templates_id');
    }
}