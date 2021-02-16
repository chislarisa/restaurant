<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SellerOrders extends Model
{
    protected $table = 'sellers_orders';
    protected $fillable = ['user_id', 'closed', 'table_number'];

    public function getNrOfItems() {
        $items = SellerOrdersDetails::where('seller_order_id', $this->id)->get();
        $sum = 0;
        foreach($items as $item) {
            $sum += $item->quantity;
        }

        return $sum;
    }

    public function getOrderPrice() {
        $items = SellerOrdersDetails::where('seller_order_id', $this->id)->get();

        $sum = 0;
        foreach($items as $item) {
            $food = Food::find($item->food_id);
            $sum += $food->price * $item->quantity;
        }

        return $sum;
    }

    public function getItems() {
        $items = SellerOrdersDetails::where('seller_order_id', $this->id)->get();

        foreach ($items as $item) {
            $item->details = Food::find($item->food_id);
        }

        return $items;
    }
}
