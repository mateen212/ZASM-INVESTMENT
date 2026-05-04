<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class ApiIntegration extends Model
{
    use GlobalStatus;

    protected $guarded = ['id'];

    protected $casts = [
        'credentials' => 'object',
        'settings' => 'object',
    ];

    protected $hidden = ['credentials'];

    /**
     * Get a credential value by key
     *
     * @param string $key
     * @return mixed|null
     */
    public function getCredentialValue($key)
    {
        $credentials = json_decode(json_encode($this->credentials), true);
        return $credentials[$key]['value'] ?? null;
    }
}
