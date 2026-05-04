<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable; // Import the Notifiable trait

class Admin extends Authenticatable
{
    use HasRoles;
    use Notifiable; // Add the trait
    /**
     * The guard name for the model.
     *
     * @var string
     */
    protected $guard_name = 'admin';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'email_verified_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the partner deals associated with the admin.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function partnerDeals()
    {
        return $this->hasMany('App\Models\PartnerDeal');
    }
    public function deals()
    {
        return $this->hasMany('App\Models\Deal');
    }
    public function routeNotificationForMail()
    {
        return $this->email;
    }
}