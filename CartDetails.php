<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CartDetails extends Model
{
    protected $table = 'carts_details';
    protected $fillable = ['cart_id', 'food_id', 'quantity'];
}
