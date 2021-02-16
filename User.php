<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'type',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getBillingInfo() {
        $billingDetails = UserBillingDetails::where('user_id', $this->id)->first();

        if (!$billingDetails) {
            return new UserBillingDetails();
        }
        return $billingDetails;
    }

    public function hasBillingAddress() {
        return UserBillingDetails::where('user_id', $this->id)->exists();
    }
}
