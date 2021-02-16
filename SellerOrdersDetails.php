<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SellerOrdersDetails extends Model
{
    protected $table = "sellers_orders_details";
    protected $fillable = ['seller_order_id', 'food_id', 'quantity'];
}
