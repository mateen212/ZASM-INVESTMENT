<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ESignTemplateRecipient extends Model
{
    protected $table = 'e_sign_template_document_recipients';
    protected $fillable = [
        'e_sign_templates_id',
        'investment_id',
        'investor_id',
        'name',
        'email',
        'role',
        'signing_order',
        'token',
        'recipient_type',
        'documenso_recipient_id',
    ];
    public function eSignTemplateDocument()
    {
        return $this->belongsTo(ESignTemplate::class, 'e_sign_template_document_id');
    }
    public function investment()
    {
        return $this->belongsTo(Investment::class, 'investment_id');
    }
    public function investor()
    {
        return $this->belongsTo(Investor::class, 'investor_id');
    }
}
