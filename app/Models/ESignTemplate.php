<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ESignTemplate extends Model
{
    protected $fillable = [
        'template_name', 
        'template_type', 
        'file_path', 
        'deal_id', 
        'offering_id',
        'documenso_document_id',
    ];

   
    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }
    public function offering()
    {
        return $this->belongsTo(Offering::class);
    }
    public function fields()
    {
        return $this->hasMany(ESignTemplateField::class, 'e_sign_templates_id');
    }
    public function recipients()
    {
        return $this->hasMany(ESignTemplateRecipient::class, 'e_sign_template_document_id');
    }
    
}
