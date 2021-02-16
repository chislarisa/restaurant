<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserBillingDetails extends Model
{
    protected $table = 'users_billing_details';
    protected $fillable = ['user_id', 'address', 'town', 'postcode', 'phone'];
}
