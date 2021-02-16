<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['user_id', 'delivered'];

    public function getNrOfItems() {
        $items = OrderDetails::where('order_id', $this->id)->get();
        $sum = 0;
        foreach($items as $item) {
            $sum += $item->quantity;
        }

        return $sum;
    }

    public function getOrderPrice() {
        $items = OrderDetails::where('order_id', $this->id)->get();

        $sum = 0;
        foreach($items as $item) {
            $food = Food::find($item->food_id);
            if (!$food) {
                continue;
            }
            $sum += $food->price * $item->quantity;
        }

        return $sum;
    }

    public function getItems() {
        $items = OrderDetails::where('order_id', $this->id)->get();

        foreach ($items as $item) {
            $item->details = Food::find($item->food_id);
        }

        return $items;
    }

    public function getUserAddress() {
        $user = User::find($this->user_id);

        return UserBillingDetails::where('user_id', $user->id)->first();
    }
}
